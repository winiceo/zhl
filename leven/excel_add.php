<?php
/*
* 74cms ΢��Ƹ
* ============================================================================
* ��Ȩ����: ��ʿ���磬����������Ȩ����
* ��վ��ַ: http://www.74cms.com��
* ----------------------------------------------------------------------------
* �ⲻ��һ�������������ֻ���ڲ�������ҵĿ�ĵ�ǰ���¶Գ����������޸ĺ�
* ʹ�ã�������Գ���������κ���ʽ�κ�Ŀ�ĵ��ٷ�����
* ============================================================================
*/
define('IN_QISHI', true);
$alias="QS_leven_list";
require_once(dirname(__FILE__).'/../include/common.inc.php');
require_once("lib.php");
require_once 'Classes/PHPExcel.php';
require_once 'Classes/PHPExcel/IOFactory.php';
require_once 'Classes/PHPExcel/Reader/Excel5.php';



//������������phpExcel����
//$objReader = PHPExcel_IOFactory::createReader('Excel5');//use excel2007 for 2007 format
$file_path="qcwy.xls";//ָ��excel�ļ�
$objReader = new PHPExcel_Reader_Excel5(); //use excel2007



$objReader = new PHPExcel_Reader_Excel5(); //use excel2007
$PHPExcel = $objReader->load($file_path); //ָ�����ļ�

 $currentSheet = $PHPExcel->getSheet(0);//��ǰ�sheet
$allColumn = $currentSheet->getHighestColumn();//excel�����ֵ
PHPExcel_Cell::columnIndexFromString($allColumn);//excel�ļ�������
$allRow = $currentSheet->getHighestRow();//excel������,����Ҫע��հ��е�Ӱ��
$inMonth = $currentSheet->getCellByColumnAndRow(0,2);//���������ִ������� eg,0,2����ڶ��е�һ�� //������forѭ����ȡ��Ԫ���ֵ,����������Ҫ������
$inMonth = PHPExcel_Style_NumberFormat::toFormattedString($inMonth->getCalculatedValue(), 'YYYY-MM');//����ȡ��ʱ��ֵת��Ϊ������Ҫ�ĸ�ʽ
//eg:
//һ���һ���Ǳ�ͷ,���ǿ������������洢��ֵ
//$KeyArr = array('month','name','sex','age');//ͬʱ����ά�����±�ֵ
//���������������forǶ��ѭ����ȡ

$cols=array();
for ($j = 65; $j > 32; $j--) {//��ѭ�� 65ΪA��ӦASCII�� 32�Ǹ���excel������,���ݵ�һ����A[65]�������
    $selcolumn = ord('A') - $j;//����

    $value=Leven::utf8_gbk(trim($currentSheet->getCellByColumnAndRow($selcolumn, 1)->getValue()));

    if($value){

        $colinfo=\ORM::for_table(table('relation_title'))->where_equal("name", $value)->find_one()->as_array();

        if($colinfo){
            $cols[$selcolumn]=$colinfo;
        }

    }


}


for ($currentRow = 2; $currentRow <= $allRow; $currentRow++) {//��ѭ�� && $allRow ����
    $obj = \ORM::for_table(table('resume_temp'))->create();
    for ($j = 65; $j > 32; $j--) {//��ѭ�� 65ΪA��ӦASCII�� 32�Ǹ���excel������,���ݵ�һ����A[65]�������
        $selcolumn = ord('A') - $j;//����
        $col=$cols[$selcolumn]["value"];
       // dump($col);
        $regfunc=$cols[$selcolumn]["regfunc"]==""?"trim":$cols[$selcolumn]["regfunc"];
        if($col){
            $colvalue=Leven::utf8_gbk($currentSheet->getCellByColumnAndRow($selcolumn, $currentRow)->getValue());

            $obj[$col]=call_user_func_array(array("Leven",$regfunc  ), array($colvalue));
            dump( $obj[$col]);

        }
    }
    $obj->save();

}


exit;


?>