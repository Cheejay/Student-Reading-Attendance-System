<?php
namespace app\excel\controller;

use think\controller;
use think\db;
use PHPExcel;
use PHPExcel_IOFactory;

Class IndexController extends controller
{
    public function index(){
        if (cmf_get_current_admin_id())
        return $this->fetch();
    }
    //$excelData = Db::name('user')->limit(10)->select();
    public function export(){
        if (cmf_get_current_admin_id()){
        $objPHPExcel = new PHPExcel();
        // Set document properties
        $objPHPExcel->getProperties()->setCreator("ZFVS")
                    ->setLastModifiedBy("ZFVS")
                    ->setTitle("导出")
                    ->setSubject("标题")
                    ->setDescription("文件描述")
                    ->setKeywords("文件关键词 貌似是用空格隔开")
                    ->setCategory("Test result file");

        // 添加第一栏数据
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', 'ID')
                    ->setCellValue('B1', '用户名')
                    ->setCellValue('C1', '密码')
                    ->setCellValue('D1', '班级')
                    ->setCellValue('E1', '学号')
                    ->setCellValue('F1', '姓名')
                    ->setCellValue('G1', '邮箱')
                    ->setCellValue('H1', '毕业年份');
        //二维数组 从数据库拿出来
        $num =Db::query('select count(*) from cmf_user');
        $excelData = Db::name('user')->limit($num[0]['count(*)'])->select();
        for ($i=0;$i<count($excelData);$i++){
            $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A'.($i+2), $excelData[$i]['id'])
                        ->setCellValue('B'.($i+2), $excelData[$i]['user_login'])
                        ->setCellValue('C'.($i+2), $excelData[$i]['user_pass'])
                        ->setCellValue('D'.($i+2), $excelData[$i]['class'])
                        ->setCellValue('E'.($i+2), $excelData[$i]['student_number'])
                        ->setCellValue('F'.($i+2), $excelData[$i]['user_nickname'])
                        ->setCellValue('G'.($i+2), $excelData[$i]['user_email'])
                        ->setCellValue('H'.($i+2), $excelData[$i]['more']);
        }

        // 设置下边的文章标题 我也不知道叫啥
        $objPHPExcel->getActiveSheet()->setTitle('用户信息表');
        // 设置打开文件浏览的页面（0为第一页）
        $objPHPExcel->setActiveSheetIndex(0);
        // 保存为Excel 2007 文件
        $callStartTime = microtime(true);
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save(str_replace('.php', '.xls', '../data/runtime/temp/outputcache'));
        $callEndTime = microtime(true);
        $callTime = $callEndTime - $callStartTime;
        ob_end_clean();
        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Userinfo.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');
        // If you're serving to IE over SSL, then the following may be needed
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
        }else $this->error("非法访问！");
    }

public function upload() {
        if (!empty ($_FILES ['file-data'] ['name'])) {
            $tmp_file = $_FILES ['file-data'] ['tmp_name'];
            $file_types = explode(".", $_FILES ['file-data'] ['name']);
            $file_type = $file_types [count($file_types) - 1];

            /*判别是不是.xls文件，判别是不是excel文件*/
            if (strtolower($file_type) != "xls" && strtolower($file_type) != "xlsx") {
                $this->error('不是Excel文件，重新上传');
            }
            /*设置上传路径*/
            $savePath = './Public';

            /*以时间来命名上传的文件*/
            $str = date('Ymdhis');
            $file_name = $str . "." . $file_type;

            /*是否上传成功*/
            if (!copy ( $_FILES['file-data']['tmp_name'], $savePath . $file_name )) {
                echo "上传失败";
            }
            $this->importExcel($savePath,$file_name);
      //获取上传文件信息
   //   $info = $upload->getUploadFileInfo();
      //获取上传保存文件名
   //   $fileName = $info[0]['savename'];
      //重定向,把$fileName文件名传给importExcel()方法
  //    $this->redirect('Index/importExcel', array($filename => $file_name), 1, '上传成功！');
    }
  }
   
  /**
   *
   * 导入Excel文件
   */
  public function importExcel($path,$name) {
    header("content-type:text/html;charset=utf-8");
    //引入PHPExcel类
    vendor('PHPExcel');
    vendor('PHPExcel.IOFactory');
    vendor('PHPExcel.Reader.Excel5');
   
    //redirect传来的文件名
    $fileName = $name;
   
    //文件路径
    $filePath = $path.$fileName ;
    //实例化PHPExcel类
    $PHPExcel = new PHPExcel();
    //默认用excel2007读取excel，若格式不对，则用之前的版本进行读取
    $PHPReader = new \PHPExcel_Reader_Excel2007();
    if (!$PHPReader->canRead($filePath)) {
      $PHPReader = new \PHPExcel_Reader_Excel5();
      if (!$PHPReader->canRead($filePath)) {
        echo 'no Excel';
        return;
      }
    } 
    
    //读取Excel文件
    $PHPExcel = $PHPReader->load($filePath);
    //读取excel文件中的第一个工作表
    $sheet = $PHPExcel->getSheet(0);
    //取得最大的列号
    $allColumn = $sheet->getHighestColumn();
    //取得最大的行号
    $allRow = $sheet->getHighestRow();
    //从第二行开始插入,第一行是列名
    for ($currentRow = 2; $currentRow <= $allRow; $currentRow++) {
      //获取B列的值
      $user = $PHPExcel->getActiveSheet()->getCell("B" . $currentRow)->getValue();
      //获取C列的值
      $password = $PHPExcel->getActiveSheet()->getCell("C" . $currentRow)->getValue();
      $pwd=cmf_password($password);
      //获取D列的值
      $class = $PHPExcel->getActiveSheet()->getCell("D" . $currentRow)->getValue();
      //获取E列的值
      $stu_id =  $PHPExcel->getActiveSheet()->getCell("E" . $currentRow)->getValue();
      //获取F列的值
      $name = $PHPExcel->getActiveSheet()->getCell("F" . $currentRow)->getValue();
      //获取G列的值
      $email = $PHPExcel->getActiveSheet()->getCell("G" . $currentRow)->getValue();
      //获取H列的值
      $year = $PHPExcel->getActiveSheet()->getCell("H" . $currentRow)->getValue();
      $num=Db::execute("insert into cmf_user(user_login,user_pass,class,student_number,user_nickname,user_email,more) values ('$user','$pwd','$class','$stu_id','$name','$email','$class')");
    }
    if ($num) {
      unlink($filePath);
      $this->success("添加成功！");
    } else {
      $this->error("非法访问！");
    }
  }

}