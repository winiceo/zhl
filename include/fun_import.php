<?php
 /*
 * 74cms ���˻�Ա�����������
*/
if(!defined('IN_QISHI'))
{
 die('Access Denied!');
}
function match_experience($experience_cn){
	switch(intval($experience_cn)){
		case '0':
			return array('75','1������');break;
		case '1':
		case '2':
		case '3':
			return array('76','1-3��');break;
		case '4':
		case '5':
			return array('77','3-5��');break;
		case '6':
		case '7':
		case '8':
		case '9':
		case '10':
			return array('78','5-10��');break;
		default:
			return array('79','10������');break;
	}
}
function unicode_decode($unistr, $encoding = 'GBK', $prefix = '&#', $postfix = ';') {
    $arruni = explode($prefix, $unistr);
    $unistr = '';
    for($i = 1, $len = count($arruni); $i < $len; $i++) {
        if (strlen($postfix) > 0) {
            $arruni[$i] = substr($arruni[$i], 0, strlen($arruni[$i]) - strlen($postfix));
        } 
        $temp = intval($arruni[$i]);
        $unistr .= ($temp < 256) ? chr(0) . chr($temp) : chr($temp / 256) . chr($temp % 256);
    } 
    return iconv('UCS-2', $encoding, $unistr);
}
function iconv_to_gbk($arr){
	foreach ($arr as $key => $value) {
		if($key=='fullname'){
			continue;
		}
		$arr[$key] = iconv('utf-8','gbk',$value);
	}
	return $arr;
}
//ƥ����ҵ
function match_trade($str=NULL)
{	
	global $db;
	if (empty($str))
	{
		return false;
	}
	else
	{
		$sql = "select c_id,c_name from ".table('category')." where  c_alias='QS_trade'";
		$info=$db->getall($sql);
		$return=match_search_str($info,$str,"c_name");
		if ($return)
		{
		return array("id"=>$return['c_id'],"cn"=>$return['c_name']);
		}
		else
		{
		return false;
		}
	}
}
function match_nature($str=NULL){
	global $db;
	if (empty($str))
	{
		return false;
	}
	else
	{
		$sql = "select c_id,c_name from ".table('category')." where  c_alias='QS_jobs_nature'";
		$info=$db->getall($sql);
		$return=match_search_str($info,$str,"c_name");
		if ($return)
		{
		return array("id"=>$return['c_id'],"cn"=>$return['c_name']);
		}
		else
		{
		return false;
		}
	}
}
function match_education($str=NULL){
	global $db;
	if (empty($str))
	{
		return false;
	}
	else
	{
		$sql = "select c_id,c_name from ".table('category')." where  c_alias='QS_education'";
		$info=$db->getall($sql);
		$return=match_search_str($info,$str,"c_name");
		if ($return)
		{
		return array("id"=>$return['c_id'],"cn"=>$return['c_name']);
		}
		else
		{
		return false;
		}
	}
}
function match_language($str=NULL){
	global $db;
	if (empty($str))
	{
		return false;
	}
	else
	{
		$sql = "select c_id,c_name from ".table('category')." where  c_alias='QS_language'";
		$info=$db->getall($sql);
		$return=match_search_str($info,$str,"c_name");
		if ($return)
		{
		return array("id"=>$return['c_id'],"cn"=>$return['c_name']);
		}
		else
		{
		return false;
		}
	}
}
function match_language_level($str=NULL){
	global $db;
	if (empty($str))
	{
		return false;
	}
	else
	{
		$sql = "select c_id,c_name from ".table('category')." where  c_alias='QS_language_level'";
		$info=$db->getall($sql);
		$return=match_search_str($info,$str,"c_name");
		if ($return)
		{
		return array("id"=>$return['c_id'],"cn"=>$return['c_name']);
		}
		else
		{
		return false;
		}
	}
}
function match_current($str=NULL)
{	
	global $db;
	if (empty($str))
	{
		return false;
	}
	else
	{
		$sql = "select c_id,c_name from ".table('category')." where  c_alias='QS_current'";
		$info=$db->getall($sql);
		$return=match_search_str($info,$str,"c_name");
		if ($return)
		{
		return array("id"=>$return['c_id'],"cn"=>$return['c_name']);
		}
		else
		{
		return false;
		}
	}
}
//ƥ��ְλ����
function match_jobs_category($str=NULL)
{
	global $db;
	if (empty($str))
	{
		return false;
	}
	else
	{
		$sql = "select id,parentid,categoryname from ".table('category_jobs');
		$info=$db->getall($sql);
		$return=match_search_str($info,$str,"categoryname");
		//ƥ�䵽��
		if(!empty($return)){
			//����һ�������
			if($return['parentid']==0){
				return array("topclass"=>$return['id'],"category"=>0,"subclass"=>0,"category_cn"=>$return['categoryname']);
			}else{
				$sql2 = "select id,parentid,categoryname from ".table('category_jobs')." where id=".intval($return['parentid']);
				$info2=$db->getone($sql2);
				//���������
				if($info2['parentid']==0){
					return array("topclass"=>$info2['id'],"category"=>$return['id'],"subclass"=>0,"category_cn"=>$return['categoryname']);
				}
				//���������
				else{
					$sql3 = "select id,parentid,categoryname from ".table('category_jobs')." where id=".intval($info2['parentid']);
					$info3=$db->getone($sql3);
					return array("topclass"=>$info3['id'],"category"=>$info2['id'],"subclass"=>$return['id'],"category_cn"=>$return['categoryname']);
				}
				
			}

		}
		//û��ƥ�䵽
		else{
			return false;
		}
		
	}
}
function match_district($str=NULL)
{
	global $db;
	if (empty($str))
	{
		return false;
	}
	else
	{
		$sql = "select id,parentid,categoryname from ".table('category_district')." ";
		$info=$db->getall($sql);
		$return=match_search_str($info,$str,"categoryname");
		if ($return)
		{
		return array("district"=>$return['parentid'],"sdistrict"=>$return['id'],"district_cn"=>$return['categoryname']);
		}
		else
		{
		return false;
		}
	}
}
function match_wage($str=NULL)
{
	if (empty($str))
	{
		return false;
	}
	else
	{
		$str = trim($str);
		switch($str){
			case '1000Ԫ/������':
				return array('id'=>56,'cn'=>'1000~1500Ԫ/��');break;
			case '1000-2000Ԫ/��':
				return array('id'=>57,'cn'=>'1500~2000Ԫ/��');break;
			case '2001-4000Ԫ/��':
				return array('id'=>58,'cn'=>'2000~3000Ԫ/��');break;
			case '4001-6000Ԫ/��':
				return array('id'=>59,'cn'=>'3000~5000Ԫ/��');break;
			case '6001-8000Ԫ/��':
			case '8001-10000Ԫ/��':
				return array('id'=>60,'cn'=>'5000~10000Ԫ/��');break;
			default:
				return array('id'=>61,'cn'=>'һ������/��');break;
		}
	}
}
function match_search_str($arr,$str,$arrinname)
{
		foreach ($arr as $key =>$list)
		{
			similar_text($list[$arrinname],$str,$percent);
			$od[$percent]=$key;
		}
			krsort($od);
			foreach ($od as $key =>$li)
			{
				if ($key>=55)
				{
				return $arr[$li];
				}
				else
				{
				return false;
				}
			}	
}
?>