<?php
 /*
 * 74cms ��ҵ�ƹ�
*/
define('IN_QISHI', true);
require_once(dirname(__FILE__).'/../data/config.php');
require_once(dirname(__FILE__).'/include/admin_common.inc.php');
require_once(ADMIN_ROOT_PATH.'include/admin_company_fun.php');
$act = !empty($_GET['act']) ? trim($_GET['act']) : 'list';
check_permissions($_SESSION['admin_purview'],"com_promotion");
$smarty->assign('pageheader',"��ҵ�ƹ�");
if($act == 'list')
{
	get_token();
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	$oderbysql=" order BY p.cp_id DESC ";
	$joinsql = " INNER JOIN ".table('jobs')." AS j INNER JOIN ".table('promotion_category')." AS c  ON p.cp_jobid=j.id AND p.cp_promotionid=c.cat_id ";
	$key=isset($_GET['key'])?trim($_GET['key']):"";
	$key_type=isset($_GET['key_type'])?intval($_GET['key_type']):"";
	if (!empty($key) && $key_type>0)
	{
		
		if     ($key_type===1)$wheresql=" WHERE j.jobs_name like '%{$key}%'";
		elseif ($key_type===2)$wheresql=" WHERE j.companyname like '%{$key}%'";
		elseif ($key_type===3)$wheresql=" WHERE j.id =".intval($key);
		elseif ($key_type===4)$wheresql=" WHERE p.cp_uid=".intval($key);
		elseif ($key_type===5)$wheresql=" WHERE p.cp_uid=".intval($key);
		$oederbysql="";
	}
	$settr=$_GET['settr'];
	if ($settr<>"")
	{
		$wheresql.=empty($wheresql)?" WHERE ":" AND  ";
		$days=intval($settr);
		$settr=strtotime($days." day");
		if ($days===0)
		{
		$wheresql.=" p.cp_endtime< ".time()." ";
		}
		else
		{
		$wheresql.=" p.cp_endtime< ".$settr." ";
		}		
	}
	$promotionid=isset($_GET['promotionid'])?intval($_GET['promotionid']):"";
	if ($promotionid>0)
	{
	$wheresql.=empty($wheresql)?" WHERE p.cp_promotionid={$promotionid} ":" AND p.cp_promotionid={$promotionid} ";
	}
	$total_sql="SELECT COUNT(*) AS num FROM ".table('promotion')." AS p ".$joinsql.$wheresql;
	$total_val=$db->get_total($total_sql);
	$page = new page(array('total'=>$total_val, 'perpage'=>$perpage,'getarray'=>$_GET));
	$currenpage=$page->nowindex;
	$offset=($currenpage-1)*$perpage;
	$list = get_promotion($offset,$perpage,$joinsql.$wheresql.$oderbysql);
	$smarty->assign('navlabel',"list");
	$smarty->assign('list',$list);
	$smarty->assign('cat',get_promotion_cat(1));
	$smarty->assign('page',$page->show(3));
	$smarty->display('promotion/admin_promotion_list.htm');
}
elseif($act == 'promotion_add')
{
	get_token();
	$smarty->assign('navlabel',"add");
	$smarty->assign('list',get_promotion_cat());
	$smarty->assign('cat',get_promotion_cat(1));	
	$smarty->display('promotion/admin_promotion_add.htm');
}
elseif($act == 'promotion_save')
{
	check_token();
	$setsqlarr['cp_days']=intval($_POST['days']);
	if ($setsqlarr['cp_days']==0)
	{
		adminmsg("����д�ƹ�����",1);
	}
	$setsqlarr['cp_jobid']=intval($_POST['jobid']);
	$setsqlarr['cp_promotionid']=intval($_POST['promotionid']);
	if (check_promotion($setsqlarr['cp_jobid'],$setsqlarr['cp_promotionid']))
	{
		adminmsg("��ְλ����ִ�д��ƹ㣡��ѡ������ְλ���������ƹ㷽��",1);
	}
	else
	{
		if ($setsqlarr['cp_promotionid']=="4")
		{
		$setsqlarr['cp_val']=!empty($_POST['val'])?$_POST['val']:adminmsg("��ѡ����ɫ",1);
		}
		$setsqlarr['cp_starttime']=time();
		$setsqlarr['cp_endtime']=strtotime("{$setsqlarr['cp_days']} day");
		$setsqlarr['cp_available']=1;
		$setsqlarr['cp_hour_cn']=trim($_POST['hour']);
		$setsqlarr['cp_hour']=intval($_POST['hour']);
		$jobs=get_jobs_one($setsqlarr['cp_jobid']);
		$setsqlarr['cp_uid']=$jobs['uid'];
		if ($db->inserttable(table('promotion'),$setsqlarr))
		{
		$u=get_user($setsqlarr['cp_uid']);
		$promotion=get_promotion_cat_one($setsqlarr['cp_promotionid']);
		write_memberslog($u['uid'],1,3004,$u['username'],"����Ա�����ƹ㣺{$promotion['cat_name']},ְλID��{$setsqlarr['cp_jobid']}");
		set_job_promotion($setsqlarr['cp_jobid'],$setsqlarr['cp_promotionid'],$setsqlarr['cp_val']);
		write_log("����ƹ㣺{$promotion['cat_name']},ְλID��{$setsqlarr['cp_jobid']}", $_SESSION['admin_name'],3);
		$link[0]['text'] = "�����б�";
		$link[0]['href'] = "?act=list";
		adminmsg("��ӳɹ�",2,$link);		
		}
	}
}
elseif($act == 'promotion_edit')
{
	get_token();
	$id=intval($_GET['id']);
	$show = get_promotion_one($id);
	$jobs = get_jobs_one($show['cp_jobid']);
	$promotion = get_promotion_cat_one($show['cp_promotionid']);
	$smarty->assign('time',time());
	$smarty->assign('show',$show);
	$smarty->assign('jobs',$jobs);
	$smarty->assign('promotion',$promotion);
	$smarty->display('promotion/admin_promotion_edit.htm');
}
elseif($act == 'promotion_edit_save')
{
	check_token();
	$setsqlarr['cp_id']=intval($_POST['id']);
	$setsqlarr['cp_promotionid']=intval($_POST['promotionid']);
	$setsqlarr['cp_hour_cn']=trim($_POST['hour']);
	$setsqlarr['cp_hour']=intval($_POST['hour']);
	$days=intval($_POST['days']);	
	if ($setsqlarr['cp_promotionid']=="4")
	{
	$setsqlarr['cp_val']=trim($_POST['val']);
	}
	if ($days>0)
	{
	$endtime=intval($_POST['endtime']);
	$setsqlarr['cp_endtime']=$endtime>time()?$endtime+($days*(60*60*24)):strtotime("".$days." day");
	}
	$wheresql=" cp_id='{$setsqlarr['cp_id']}' ";
	if ($db->updatetable(table('promotion'),$setsqlarr,$wheresql))
	{
		if ($setsqlarr['cp_promotionid']=="4")
		{
			$jobid=intval($_POST['jobid']);
		 	$db->query("UPDATE ".table('jobs')." SET highlight='{$setsqlarr['cp_val']}' WHERE id='{$jobid}' ");
			$db->query("UPDATE ".table('jobs_tmp')." SET highlight='{$setsqlarr['cp_val']}' WHERE id='{$jobid}' ");
		}
		write_log("�޸��ƹ�idΪ".$setsqlarr['cp_id']."���ƹ�", $_SESSION['admin_name'],3);
		$link[0]['text'] = "�ƹ��б�";
		$link[0]['href'] ="?act=list";
		adminmsg("�޸ĳɹ���",2,$link);
	}	
}
elseif($act == 'promotion_del')
{
	get_token();
	if ($n=del_promotion($_POST['id']))
	{
	adminmsg("ȡ���ɹ�����ȡ�� {$n} ��",2);
	}
	else
	{
	adminmsg("ȡ��ʧ�ܣ�",0);
	}
}
elseif($act == 'category')
{
	get_token();
	$smarty->assign('navlabel',"category");
	$smarty->assign('list',get_promotion_cat());	
	$smarty->display('promotion/admin_promotion_category.htm');
}
elseif($act == 'edit_category')
{
	get_token();
	$id=intval($_GET['id']);
	$smarty->assign('navlabel',"category");
    $show=get_promotion_cat_one($id);
	$smarty->assign('show',$show);

    if($id==5){
        $rs=(array) json_decode($show["cp_json"]);

        $smarty->assign('interv',$rs);

        $smarty->display('promotion/admin_promotion_category_edit5.htm');

    }else{
        $smarty->display('promotion/admin_promotion_category_edit.htm');
    }
}
elseif($act=='edit_category_save')
{	
	check_token();
	$setsqlarr['cat_name']=trim($_POST['cat_name'])?trim($_POST['cat_name']):adminmsg('��û����д�������ƣ�',1);


    $setsqlarr['cat_available']=intval($_POST['cat_available']);
    if(intval($_POST['id'])==5){
        $setsqlarr["cp_json"]=json_encode($_POST["interv"]);

    }else{
        	$setsqlarr['cat_minday']=intval($_POST['cat_minday']);
	$setsqlarr['cat_maxday']=intval($_POST['cat_maxday']);
	$setsqlarr['cat_points']=intval($_POST['cat_points']);
	$setsqlarr['cat_order']=intval($_POST['cat_order']);
    }
	$setsqlarr['cat_notes']=trim($_POST['cat_notes']);
	$wheresql=" cat_id='".intval($_POST['id'])."'";
		if ($db->updatetable(table('promotion_category'),$setsqlarr,$wheresql))
		{
		$link[0]['text'] = "�����б�";
		$link[0]['href'] ="?act=category";
		adminmsg("�޸ĳɹ���",2,$link);
		}
		else
		{
		adminmsg("�޸�ʧ�ܣ�",0);
		}
}
?>