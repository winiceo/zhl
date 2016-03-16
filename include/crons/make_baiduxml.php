<?php
 /*
 * 74cms �ƻ����� �������
*/
if(!defined('IN_QISHI'))
{
die('Access Denied!');
}
	global $_CFG,$db,$baiduxml;
	$xmlset=get_cache('baiduxml');
	$xmlorder=$xmlset['order'];
	require_once(QISHI_ROOT_PATH.'include/baiduxml.class.php');
	$baiduxml = new BaiduXML();
	makebaiduxml($xmlorder,0,$xmlset['xmlpagesize'],1,0,$xmlset);
	function makebaiduxml($xmlorder,$start,$size,$li=1,$t=0,$xmlset)
	{
		global $db,$baiduxml,$_CFG;
		$xmldir = QISHI_ROOT_PATH.$xmlset['xmldir'];
	 	if ($xmlorder=='1')
		{
		$sqlorder=" ORDER BY `addtime` DESC ";
		}
		else
		{
		$sqlorder=" ORDER BY `refreshtime` DESC ";
		}
		$sqllimit=" LIMIT {$start},{$size}";
    	$result = $db->query("select * from ".table('jobs').$sqlorder.$sqllimit);
		while($row = $db->fetch_array($result))
		{
			$t++;
			$contact=$db->getone("SELECT * from ".table('jobs_contact')." where pid = '{$row['id']}' LIMIT 1");
			$com=$db->getone("SELECT * from ".table('company_profile')." where id = '{$row['company_id']}' LIMIT 1");
			$category=$db->getone("SELECT * FROM ".table('category_jobs')." where id=".$row['category']." LIMIT 1");
			$subclass=$db->getone("SELECT * FROM ".table('category_jobs')." where id=".$row['subclass']." LIMIT 1");

			$city =$db->getone("SELECT * FROM ".table('category_district')." where id=".$row['district']." LIMIT 1");
			$city_ =$db->getone("SELECT * FROM ".table('category_district')." where id=".$row['sdistrict']." LIMIT 1");
			if(empty($city_))
			{
				$city_['categoryname']=$city['categoryname'];
			}
			$row['jobs_url']=url_rewrite('QS_jobsshow',array('id'=>$row['id']),1,$row['subsite_id']);
			$row['m_jobs_url']=$_CFG['site_domain'].$_CFG['site_dir']."m/jobs-show.php?id=".$row['id'];
			$com['com_url']=url_rewrite('QS_companyshow',array('id'=>$com['id']));
			$x=array($row['jobs_url'],date("Y-m-d",$row['refreshtime']),$row['jobs_name'],$category['categoryname'],$subclass['categoryname'],$row['amount'],$row['age'],$row['sex_cn'],$row['contents'],$row['education_cn'],$row['experience_cn'],date("Y-m-d",$row['addtime']),date("Y-m-d",$row['deadline']),str_replace('~','-',$row['wage_cn']),$city['categoryname'],$city_['categoryname'],$contact['address'],$row['nature_cn'],$row['jobs_name'],$row['companyname'],$com['com_url'],$com['address'],$com['nature_cn'],$com['scale_cn'],$com['contents'],$contact['email'],$com['trade_cn'],$com['trade_cn'],$com['id'],$_CFG['site_name'],$_CFG['site_domain'].$_CFG['site_dir'],$row['jobs_url'],$row['m_jobs_url']);

			foreach ($x as $key => $value) {
				$x[$key] = strip_tags(str_replace("&","&amp;",$value));
			}
			$baiduxml->XML_url($x);
			$rowid=$row['id'];
			if ($xmlset['xmlmax']>0 && $t>=$xmlset['xmlmax'])
			{
				for($b=1;$b<$li;$b++)
				{
					$xml_dir = '../../'.$xmlset['xmldir'];
					$xmlfile=$xml_dir.$xmlset['xmlpre'].$b.'.xml';
					$xmlfile=ltrim($xmlfile,'../');
					$xmlfile=ltrim($xmlfile,'..\\');
					$atime=filemtime($xmldir.$xmlset['xmlpre'].$b.'.xml');
					$atime=date("Y-m-d",$atime);
					$index[]=array($_CFG['site_domain'].$_CFG['site_dir'].$xmlfile,$atime);
				}
			$baiduxml->XML_index_put($xmldir.$xmlset['indexname'],$index);
			return true;
			}
		}
		if (empty($rowid))
		{
			for($b=1;$b<$li;$b++)
			{
				$xml_dir = '../../'.$xmlset['xmldir'];
				$xmlfile=$xml_dir.$xmlset['xmlpre'].$b.'.xml';
				$xmlfile=ltrim($xmlfile,'../');
				$xmlfile=ltrim($xmlfile,'..\\');
				$atime=filemtime($xmldir.$xmlset['xmlpre'].$b.'.xml');
				$atime=date("Y-m-d",$atime);
				$index[]=array($_CFG['site_domain'].$_CFG['site_dir'].$xmlfile,$atime);
			}
			$baiduxml->XML_index_put($xmldir.$xmlset['indexname'],$index);
			return true;
		}
		else
		{
			$xmlname=$xmldir.$xmlset['xmlpre'].$li.'.xml';
			if ($baiduxml->XML_put($xmlname))
			{
			$li++;
			return makebaiduxml($xmlorder,$t,$xmlset['xmlpagesize'],$li,$t,$xmlset);
			}
		}
	}
	//��������ʱ���
	if ($crons['weekday']>=0)
	{
	$weekday=array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');
	$nextrun=strtotime("Next ".$weekday[$crons['weekday']]);
	}
	elseif ($crons['day']>0)
	{
	$nextrun=strtotime('+1 months'); 
	$nextrun=mktime(0,0,0,date("m",$nextrun),$crons['day'],date("Y",$nextrun));
	}
	else
	{
	$nextrun=time();
	}
	if ($crons['hour']>=0)
	{
	$nextrun=strtotime('+1 days',$nextrun); 
	$nextrun=mktime($crons['hour'],0,0,date("m",$nextrun),date("d",$nextrun),date("Y",$nextrun));
	}
	if (intval($crons['minute'])>0)
	{
	$nextrun=strtotime('+1 hours',$nextrun); 
	$nextrun=mktime(date("H",$nextrun),$crons['minute'],0,date("m",$nextrun),date("d",$nextrun),date("Y",$nextrun));
	}
	$setsqlarr['nextrun']=$nextrun;
	$setsqlarr['lastrun']=time();
	$db->updatetable(table('crons'), $setsqlarr," cronid ='".intval($crons['cronid'])."'");	
?>