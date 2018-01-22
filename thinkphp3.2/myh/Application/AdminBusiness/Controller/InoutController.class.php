<?php
namespace AdminBusiness\Controller;
use Think\Controller;
class InoutController extends Controller {
    public function index() {
        $this->assign('current',1);
        $this->display();
    }
    public function exportExcel($expTitle,$expCellName,$expTableData){
        $xlsTitle = iconv('utf-8', 'gb2312', $expTitle);//文件名称
        $fileName = $expTitle.date('_YmdHis');//or $xlsTitle 文件名称可根据自己情况设定
        $cellNum = count($expCellName);
        $dataNum = count($expTableData);
        vendor("PHPExcel.PHPExcel");
        $objPHPExcel = new \PHPExcel();
        $cellName = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ');

        $objPHPExcel->getActiveSheet(0)->mergeCells('A1:'.$cellName[$cellNum-1].'1');//合并单元格
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', $expTitle.'  Export time:'.date('Y-m-d H:i:s'));
        for($i=0;$i<$cellNum;$i++){
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cellName[$i].'2', $expCellName[$i][1]);
        }
        // Miscellaneous glyphs, UTF-8
        for($i=0;$i<$dataNum;$i++){
            for($j=0;$j<$cellNum;$j++){
                $objPHPExcel->getActiveSheet(0)->setCellValue($cellName[$j].($i+3), $expTableData[$i][$expCellName[$j][0]]);
            }
        }
        header('pragma:public');
        header('Content-type:application/vnd.ms-excel;charset=utf-8;name="'.$xlsTitle.'.xls"');
        header("Content-Disposition:attachment;filename=$fileName.xls");//attachment新窗口打印inline本窗口打印
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit;
    }

    /**
     *
     * 导出Excel
     */
    function expUser(){//导出Excel
        $xlsName  = "comment";
        $xlsCell  = array(
        array('id','账号序列'),
        array('user_name','姓名'),
        array('score','所属乡镇'),
        array('title','单位'),
        array('content','电话')
        );
        $xlsModel = M('Comment');
        $xlsData  = $xlsModel->Field('id,user_name,score,title,content')->select();
//        foreach ($xlsData as $k => $v)
//        {
//            $xlsData[$k]['tname']=Gettname($v['tid']);
//            array_splice($xlsData[$k]['tid']);
//        }
        $this->exportExcel($xlsName,$xlsCell,$xlsData);
    }

    function excelToArray(){
        vendor("PHPExcel.PHPExcel");

        //加载excel文件
        $filename = dirname(__FILE__).'/result.xlsx';
        $objPHPExcelReader = PHPExcel_IOFactory::load($filename);

        $reader = $objPHPExcelReader->getWorksheetIterator();
        //循环读取sheet
        foreach($reader as $sheet) {
            //读取表内容
            $content = $sheet->getRowIterator();
            //逐行处理
            $res_arr = array();
            foreach($content as $key => $items) {

                $rows = $items->getRowIndex();              //行
                $columns = $items->getCellIterator();       //列
                $row_arr = array();
                //确定从哪一行开始读取
                if($rows < 2){
                    continue;
                }
                //逐列读取
                foreach($columns as $head => $cell) {
                    //获取cell中数据
                    $data = $cell->getValue();
                    $row_arr[] = $data;
                }
                $res_arr[] = $row_arr;
            }

        }

        return $res_arr;
    }
}

?>