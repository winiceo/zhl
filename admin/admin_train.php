<?php
 /*
 * 74cms ��ѵ�û����
*/
define('IN_QISHI', true);
require_once(dirname(__FILE__).'/../data/config.php');
require_once(dirname(__FILE__).'/include/admin_common.inc.php');
require_once(ADMIN_ROOT_PATH.'include/admin_train_fun.php');
$act = !empty($_GET['act']) ? trim($_GET['act']) : 'list';
if($act == 'list')
{
	check_permissions($_SESSION['admin_purview'],"cou_show");
	$audit=intval($_GET['audit']);
	$recom=intval($_GET['recom']);
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	$oederbysql=" order BY id DESC ";
	$wheresqlarr=array();
	$key=isset($_GET['key'])?trim($_GET['key']):"";
	$key_type=isset($_GET['key_type'])?intval($_GET['key_type']):"";
	$oederbysql=' order BY refreshtime DESC ';
	if (!empty($key) && $key_type>0)
	{
		if     ($key_type===1)$wheresql=" WHERE course_name like '%{$key}%'";
		elseif ($key_type===2)$wheresql=" WHERE trainname like '%{$key}%'";
		elseif ($key_type===3 && intval($key)>0)$wheresql=" WHERE id =".intval($key);
		elseif ($key_type===4 && intval($key)>0)$wheresql=" WHERE train_id =".intval($key);
		elseif ($key_type===5 && intval($key)>0)$wheresql=" WHERE uid =".intval($key);
		$oederbysql="";
	}
	else
	{
			if ($audit>0)
			{
			$wheresqlarr['audit']=$audit;
			}
			if ($recom>0)
			{
			$wheresqlarr['recommend']=$recom;
			}
			if (!empty($wheresqlarr)) $wheresql=wheresql($wheresqlarr);
			if (!empty($_GET['settr']))
			{
				$settr=strtotime("-".intval($_GET['settr'])." day");
				$wheresql=empty($wheresql)?" WHERE addtime> ".$settr:$wheresql." AND addtime> ".$settr;
				$oederbysql=" order BY addtime DESC ";
			}
			if (!empty($_GET['refre']))
			{
				$settr=strtotime("-".intval($_GET['refre'])." day");
				$wheresql=empty($wheresql)?" WHERE refreshtime> ".$settr:$wheresql." AND refreshtime> ".$settr;
				$oederbysql=" order BY refreshtime DESC ";
			}
			if(!empty($_GET['deadline']))
			{
			
				$settr=strtotime("+".intval($_GET['deadline'])." day");
				$wheresql=empty($wheresql)?" WHERE deadline< {$settr}":$wheresql." AND deadline<{$settr} ";
				$oederbysql=" order BY deadline DESC ";
			}
	}
	if (intval($_CFG['subsite_id'])>0)
	{
		$wheresql.=empty($wheresql)?" WHERE ":" AND ";
		$wheresql.=" subsite_id=".intval($_CFG['subsite_id'])." ";
	}
	$total_sql="SELECT COUNT(*) AS num FROM ".table('course').$wheresql;
	$total_val=$db->get_total($total_sql);
	$page = new page(array('total'=>$total_val, 'perpage'=>$perpage,'getarray'=>$_GET));
	$currenpage=$page->nowindex;
	$offset=($currenpage-1)*$perpage;
	$getsql="SELECT * FROM ".table('course').$wheresql.$oederbysql;
	$course = get_course($offset,$perpage,$getsql);
	$smarty->assign('pageheader',"�γ̹���");
	$smarty->assign('course',$course);
	$smarty->assign('now',time());
	$smarty->assign('total',$total);
	$smarty->assign('page',$page->show(3));
	$smarty->assign('total_val',$total_val);
	get_token();
	$smarty->display('train/admin_train_course.htm');
}
elseif($act == 'teacher_list')
{
	check_permissions($_SESSION['admin_purview'],"tea_show");
	$audit=intval($_GET['audit']);
	$recom=intval($_GET['recom']);
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	$oederbysql=" order BY id DESC ";
	$wheresqlarr=array();
	$key=isset($_GET['key'])?trim($_GET['key']):"";
	$key_type=isset($_GET['key_type'])?intval($_GET['key_type']):"";
	if (!empty($key) && $key_type>0)
	{
		
		if     ($key_type===1)$wheresql=" WHERE teachername like '%{$key}%'";
		elseif ($key_type===2)$wheresql=" WHERE trainname like '%{$key}%'";
		elseif ($key_type===3 && intval($key)>0)$wheresql=" WHERE id =".intval($key);
		elseif ($key_type===4 && intval($key)>0)$wheresql=" WHERE train_id =".intval($key);
		elseif ($key_type===5 && intval($key)>0)$wheresql=" WHERE uid =".intval($key);
		$oederbysql="";
	}
	else
	{
			if ($audit>0)
			{
			$wheresqlarr['audit']=$audit;
			}
			if ($recom>0)
			{
			$wheresqlarr['recommend']=$recom;
			}
			if (!empty($wheresqlarr)) $wheresql=wheresql($wheresqlarr);
			if (!empty($_GET['settr']))
			{
				$settr=strtotime("-".intval($_GET['settr'])." day");
				$wheresql=empty($wheresql)?" WHERE addtime> ".$settr:$wheresql." AND addtime> ".$settr;
				$oederbysql=" order BY addtime DESC ";
			}
			if (!empty($_GET['refre']))
			{
				$settr=strtotime("-".intval($_GET['refre'])." day");
				$wheresql=empty($wheresql)?" WHERE refreshtime> ".$settr:$wheresql." AND refreshtime> ".$settr;
				$oederbysql=" order BY refreshtime DESC ";
			}

	}
	$total_sql="SELECT COUNT(*) AS num FROM ".table('train_teachers').$wheresql;
	$total_val=$db->get_total($total_sql);
	$page = new page(array('total'=>$total_val, 'perpage'=>$perpage,'getarray'=>$_GET));
	$currenpage=$page->nowindex;
	$offset=($currenpage-1)*$perpage;
	$getsql="SELECT * FROM ".table('train_teachers').$wheresql.$oederbysql;
	$teacher = get_teacher($offset,$perpage,$getsql);
	$smarty->assign('pageheader',"��ʦ����");
	$smarty->assign('teacher',$teacher);
	$smarty->assign('total',$total);
	$smarty->assign('page',$page->show(3));
	$smarty->assign('total_val',$total_val);
	get_token();
	$smarty->display('train/admin_train_teacher.htm');
}
elseif($act == 'teacher_perform')
{
		check_token();
		$yid =!empty($_REQUEST['y_id'])?$_REQUEST['y_id']:adminmsg("��û��ѡ��ʦ��",1);
		if (!empty($_POST['delete'])){
			check_permissions($_SESSION['admin_purview'],"tea_del");
			if ($_POST['delete_teacher']=='yes')
			{
			!del_train_idteacher($yid)?adminmsg("ɾ����ʦʧ�ܣ�",0):"";
			}
			if ($_POST['delete_course']=='yes')
			{
			!del_train_teacourse($yid)?adminmsg("ɾ���γ�ʧ�ܣ�",0):"";
			}
			if ($_POST['delete_teacher']<>'yes' && $_POST['delete_course']<>'yes')
			{
			adminmsg("δѡ��ɾ�����ͣ�",1);
			}
			adminmsg("ɾ���ɹ���",2);
		}
		if (!empty($_POST['set_audit']))
		{
			check_permissions($_SESSION['admin_purview'],"tea_audit");
			$audit=intval($_POST['audit']);
			$pms_notice=intval($_POST['pms_notice']);
			$reason=trim($_POST['reason']);
			if ($n=edit_teachers_audit($yid,$audit,$reason,$pms_notice))
			{
			adminmsg("��˳ɹ�����Ӧ���� {$n}",2);			
			}
			else
			{
			adminmsg("��˳ɹ�����Ӧ���� 0",1);
			}
		}
		if (!empty($_REQUEST['set_refre']))
		{
			check_permissions($_SESSION['admin_purview'],"tea_edit");
			if ($n=refresh_teacher($yid))
			{
			adminmsg("ˢ�³ɹ�����Ӧ���� {$n}",2);			
			}
			else
			{
			adminmsg("ˢ�³ɹ�����Ӧ���� 0",1);
			}
		}
		if (!empty($_POST['set_recom']))
		{
		check_permissions($_SESSION['admin_purview'],"tra_promotion");
			$rec_notice=intval($_POST['rec_notice']);
			$recommend=intval($_POST['recommend']);
			$notice=trim($_POST['notice']);
			if($n=recom_teacher($yid,$recommend,$rec_notice,$notice))
			{
				adminmsg("��ʦ���óɹ�����Ӧ���� {$n}",2);
			}
			else
			{
				adminmsg("��ʦ����ʧ�ܣ�",0);
			}
		}

}

elseif($act == 'edit_teacher')
{
	get_token();
	check_permissions($_SESSION['admin_purview'],"tea_edit");
	$id =!empty($_REQUEST['id'])?intval($_REQUEST['id']):adminmsg("��û��ѡ��ʦ��",1);
	$smarty->assign('pageheader',"��ʦ����");
	$teacher=get_teacher_one($id);
	$smarty->assign('url',$_SERVER["HTTP_REFERER"]);
	$smarty->assign('teacher',$teacher);
	$smarty->display('train/admin_train_teacher_edit.htm');
}
elseif ($act=='editteacher_save'){
	get_token();
	check_permissions($_SESSION['admin_purview'],"tea_edit");
	
	$setsqlarr['id']=intval($_POST['id']);
	$setsqlarr['audit']=intval($_POST['audit']);
	
	$setsqlarr['teachername']=!empty($_POST['teachername'])?trim($_POST['teachername']):adminmsg('��û����д��ʦ������',1);
	$setsqlarr['sex']=trim($_POST['sex'])?intval($_POST['sex']):adminmsg('��ѡ���Ա�',1);
	$setsqlarr['sex_cn']=trim($_POST['sex_cn']);
	$setsqlarr['birthdate']=intval($_POST['birthdate'])>1945?intval($_POST['birthdate']):adminmsg('����ȷ��д�������',1);
	$setsqlarr['district']=!empty($_POST['district'])?intval($_POST['district']):adminmsg('��ѡ�����ڵ�����',1);
	$setsqlarr['sdistrict']=intval($_POST['sdistrict']);
	$setsqlarr['district_cn']=trim($_POST['district_cn']);
	$setsqlarr['education']=!empty($_POST['education'])?intval($_POST['education']):adminmsg('��ѡ�����רҵ��',1);
	$setsqlarr['education_cn']=trim($_POST['education_cn']);
	$setsqlarr['speciality']=!empty($_POST['speciality'])?trim($_POST['speciality']):adminmsg('����д��ѧרҵ��',1);
	$setsqlarr['positionaltitles']=!empty($_POST['positionaltitles'])?trim($_POST['positionaltitles']):adminmsg('����д����ְ�ƣ�',1);
	$setsqlarr['graduated_school']=trim($_POST['graduated_school'])?trim($_POST['graduated_school']):adminmsg('����д��ҵԺУ��',1);
	$setsqlarr['work_unit']=trim($_POST['work_unit'])?trim($_POST['work_unit']):adminmsg('����д������λ��',1);
	$setsqlarr['work_position']=trim($_POST['work_position']);
	$setsqlarr['contents']=!empty($_POST['contents'])?trim($_POST['contents']):adminmsg('��û����д���˼�飡',1);
	check_word($_CFG['filter'],$_POST['contents'])?adminmsg($_CFG['filter_tips'],0):'';
	$setsqlarr['achievements']=!empty($_POST['achievements'])?trim($_POST['achievements']):adminmsg('��û����д���˳ɾͣ�',1);
	check_word($_CFG['filter'],$_POST['achievements'])?adminmsg($_CFG['filter_tips'],0):'';
	
	$setsqlarr['telephone']=trim($_POST['telephone'])?trim($_POST['telephone']):adminmsg('����д��ϵ�绰��',1);
	$setsqlarr['email']=trim($_POST['email']);
	$setsqlarr['address']=trim($_POST['address'])?trim($_POST['address']):adminmsg('����дͨѶ��ַ��',1);
	$setsqlarr['website']=trim($_POST['website']);
	$setsqlarr['qq']=trim($_POST['qq']);
	$setsqlarr['refreshtime']=$timestamp;

	
	$setsqlarr['email_show']=intval($_POST['email_show']);
	$setsqlarr['telephone_show']=intval($_POST['telephone_show']);
	$setsqlarr['address_show']=intval($_POST['address_show']);
	$setsqlarr['qq_show']=intval($_POST['qq_show']);

	$wheresql=" id='".$setsqlarr['id']."' ";
	if (!$db->updatetable(table('train_teachers'),$setsqlarr,$wheresql)) adminmsg("����ʧ�ܣ�",0);
	$teacherarr['teacher_cn']=$setsqlarr['teachername'];
	$wheresql=" teacher_id=".$setsqlarr['id'];
	if (!$db->updatetable(table('course'),$teacherarr,$wheresql)) adminmsg("����ʧ�ܣ�",0);
	write_log("�޸Ľ�ʦidΪ".$setsqlarr['id']."�Ľ�ʦ��Ϣ", $_SESSION['admin_name'],3);
	$link[0]['text'] = "���ؽ�ʦ�б�";
	$link[0]['href'] = $_POST['url'];
	adminmsg("�޸ĳɹ���",2,$link);
	
}

elseif($act == 'edit_course')
{
	get_token();
	check_permissions($_SESSION['admin_purview'],"cou_edit");
	$id =!empty($_REQUEST['id'])?intval($_REQUEST['id']):adminmsg("��û��ѡ��γ̣�",1);
	$train_id =!empty($_REQUEST['train_id'])?intval($_REQUEST['train_id']):adminmsg("��û��ѡ��γ̣�",1);
	$smarty->assign('pageheader',"�γ̹���");
	$course=get_course_one($id);
	$teachers=get_audit_teachers($train_id);
	$smarty->assign('url',$_SERVER["HTTP_REFERER"]);
	$smarty->assign('course',$course);
	$smarty->assign('teachers',$teachers);
	$smarty->display('train/admin_train_course_edit.htm');
}
elseif ($act=='editcourse_save')
{
	check_token();
	check_permissions($_SESSION['admin_purview'],"cou_edit");
	$id=intval($_POST['id']);
	$train_id=intval($_POST['train_id']);
	$train_profile=get_train_one_id($train_id);
	$setsqlarr['display']=intval($_POST['display']);
	$setsqlarr['audit']=intval($_POST['audit']);
		
	$setsqlarr['course_name']=!empty($_POST['course_name'])?trim($_POST['course_name']):adminmsg('��û����д�γ����ƣ�',1);
	$setsqlarr['category']=!empty($_POST['category'])?intval($_POST['category']):adminmsg('��ѡ��γ����',1);
	$setsqlarr['category_cn']=trim($_POST['category_cn']);
	$setsqlarr['classtype']=!empty($_POST['classtype'])?intval($_POST['classtype']):adminmsg('��ѡ���Ͽΰ��ƣ�',1);
	$setsqlarr['classtype_cn']=trim($_POST['classtype_cn']);
	$setsqlarr['teacher_id']=!empty($_POST['teacher_id'])?intval($_POST['teacher_id']):adminmsg('��ѡ�������ˣ�',1);
	$setsqlarr['teacher_cn']=trim($_POST['teacher_cn']);
	$setsqlarr['starttime']=intval(convert_datefm($_POST['starttime'],2));
	if (empty($setsqlarr['starttime']))
	{
	adminmsg('����д����ʱ�䣡ʱ���ʽ��YYYY-MM-DD',1);
	}	
	$setsqlarr['train_object']=!empty($_POST['train_object'])?trim($_POST['train_object']):adminmsg('��û����д�ڿζ���',1);
	$setsqlarr['train_certificate']=!empty($_POST['train_certificate'])?trim($_POST['train_certificate']):'';
	$setsqlarr['classhour']=!empty($_POST['classhour'])?intval($_POST['classhour']):adminmsg('��û����д�ڿ�ѧʱ��',1);
	$setsqlarr['train_expenses']=!empty($_POST['train_expenses'])?intval($_POST['train_expenses']):adminmsg('��û����д��ѵ���ã�',1);
	$setsqlarr['favour_expenses']=!empty($_POST['favour_expenses'])?intval($_POST['favour_expenses']):adminmsg('��û����д�Żݼ۸�',1);
	
	$setsqlarr['contents']=!empty($_POST['contents'])?trim($_POST['contents']):adminmsg('��û����д�γ�������',1);
	check_word($_CFG['filter'],$_POST['contents'])?adminmsg($_CFG['filter_tips'],0):'';

	$setsqlarr['refreshtime']=$timestamp;
	$setsqlarr['key']=$setsqlarr['course_name'].$train_profile['trainname'].$setsqlarr['teacher_cn'].$setsqlarr['train_certificate'].$setsqlarr['category_cn'].$setsqlarr['contents'];
	require_once(QISHI_ROOT_PATH.'include/splitword.class.php');
	$sp = new SPWord();
	$setsqlarr['key']="{$setsqlarr['course_name']} {$train_profile['trainname']} {$setsqlarr['teacher_cn']} {$setsqlarr['train_certificate']} ".$sp->extracttag($setsqlarr['key']);
	$setsqlarr['key']=$sp->pad($setsqlarr['key']);
	$setsqlarr['likekey']="{$setsqlarr['course_name']},{$train_profile['trainname']},{$setsqlarr['teacher_cn']},{$setsqlarr['train_certificate']}";
	
	$days=intval($_POST['days']);
	if ($days>0 && (intval($_POST['olddeadline'])-time())>0) $setsqlarr['deadline']=intval($_POST['olddeadline'])+($days*(60*60*24));
	if ($days>0 && (intval($_POST['olddeadline'])-time())<0) $setsqlarr['deadline']=strtotime("".$days." day");
	$setsqlarr_contact['contact']=trim($_POST['contact']);
	$setsqlarr_contact['qq']=trim($_POST['qq']);
	$setsqlarr_contact['telephone']=trim($_POST['telephone']);
	$setsqlarr_contact['address']=trim($_POST['address']);
	$setsqlarr_contact['email']=trim($_POST['email']);
	$setsqlarr_contact['notify']=trim($_POST['notify']);

	$setsqlarr_contact['contact_show']=intval($_POST['contact_show']);
	$setsqlarr_contact['email_show']=intval($_POST['email_show']);
	$setsqlarr_contact['telephone_show']=intval($_POST['telephone_show']);
	$setsqlarr_contact['address_show']=intval($_POST['address_show']);
	$setsqlarr_contact['qq_show']=intval($_POST['qq_show']);
	$wheresql=" id='".$id."' ";
	if (!$db->updatetable(table('course'),$setsqlarr,$wheresql)) adminmsg("����ʧ�ܣ�",0);
	$wheresql=" pid=".$id;
	if (!$db->updatetable(table('course_contact'),$setsqlarr_contact,$wheresql)) adminmsg("����ʧ�ܣ�",0);
	write_log("�޸Ŀγ�idΪ".$id."�Ŀγ���Ϣ", $_SESSION['admin_name'],3);
	$link[0]['text'] = "���ؿγ��б�";
	$link[0]['href'] = $_POST['url'];
	adminmsg("�޸ĳɹ���",2,$link);
}
elseif($act == 'course_perform')
{
		check_token();
		$yid =!empty($_REQUEST['y_id'])?$_REQUEST['y_id']:adminmsg("��û��ѡ��γ̣�",1);
		if (!empty($_REQUEST['delete']))
		{
			check_permissions($_SESSION['admin_purview'],"cou_del");
			$num=del_course($yid);
			if ($num>0)
			{
			adminmsg("ɾ���ɹ�����ɾ��".$num."��",2);
			}
			else
			{
			adminmsg("ɾ��ʧ�ܣ�",0);
			}
		}
		elseif (!empty($_POST['set_audit']))
		{
			check_permissions($_SESSION['admin_purview'],"cou_audit");
			$audit=intval($_POST['audit']);
			$pms_notice=intval($_POST['pms_notice']);
			$reason=trim($_POST['reason']);
			if ($n=edit_course_audit($yid,$audit,$reason,$pms_notice))
			{
			adminmsg("��˳ɹ�����Ӧ���� {$n}",2);			
			}
			else
			{
			adminmsg("��˳ɹ�����Ӧ���� 0",1);
			}
		}
		elseif (!empty($_GET['refresh']))
		{
			if($n=refresh_course($yid))
			{
			adminmsg("ˢ�³ɹ�����Ӧ���� {$n}",2);
			}
			else
			{
			adminmsg("ˢ��ʧ�ܣ�",0);
			}
		}
		elseif (!empty($_POST['set_delay']))
		{
			$days=intval($_POST['days']);
			if (empty($days))
			{
			adminmsg("����дҪ�ӳ���������",0);
			}
			if($n=delay_course($yid,$days))
			{
			adminmsg("�ӳ���Ч�ڳɹ�����Ӧ���� {$n}",2);
			}
			else
			{
			adminmsg("����ʧ�ܣ�",0);
			}
		}
		elseif (!empty($_POST['set_recom']))
		{
		check_permissions($_SESSION['admin_purview'],"tra_promotion");		
		$rec_notice=intval($_POST['rec_notice']);
			$recommend=intval($_POST['recommend']);
			$notice=trim($_POST['notice']);
			if($n=recom_course($yid,$recommend,$rec_notice,$notice))
			{
				adminmsg("�γ����óɹ�����Ӧ���� {$n}",2);
			}
			else
			{
				adminmsg("�γ�����ʧ�ܣ�",0);
			}
		}
}
elseif($act == 'train_list')
{
	get_token();
	check_permissions($_SESSION['admin_purview'],"tra_show");
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	$oederbysql=" order BY t.id DESC ";
	$key=isset($_GET['key'])?trim($_GET['key']):"";
	$key_type=isset($_GET['key_type'])?intval($_GET['key_type']):"";
	if ($key && $key_type>0)
	{
		if     ($key_type===1)$wheresql=" WHERE t.trainname like '%{$key}%'";
		elseif ($key_type===2)$wheresql=" WHERE t.id ='".intval($key)."'";
		elseif ($key_type===3)$wheresql=" WHERE m.username like '{$key}%'";
		elseif ($key_type===4)$wheresql=" WHERE t.uid ='".intval($key)."'";
		elseif ($key_type===5)$wheresql=" WHERE t.address  like '%{$key}%'";
		elseif ($key_type===6)$wheresql=" WHERE t.telephone  like '{$key}%'";		
		$oederbysql="";
	}
	$_GET['audit']<>""? $wheresqlarr['t.audit']=intval($_GET['audit']):'';
	$_GET['yellowpages']<>""? $wheresqlarr['t.yellowpages']=intval($_GET['yellowpages']):'';
	$_GET['recom']<>""? $wheresqlarr['t.recommend']=intval($_GET['recom']):'';
	if (is_array($wheresqlarr)) $wheresql=wheresql($wheresqlarr);
	if (!empty($_GET['settr']))
	{
		$settr=strtotime("-".intval($_GET['settr'])." day");
		$wheresql=empty($wheresql)?" WHERE addtime> ".$settr:$wheresql." AND addtime> ".$settr;
	}
	$operation_mode=$_CFG['operation_train_mode'];
	if($operation_mode=='1'){
		$joinsql=" LEFT JOIN ".table('members')." AS m ON t.uid=m.uid  LEFT JOIN ".table('members_points')." AS p ON t.uid=p.uid";
	}else{
		$joinsql=" LEFT JOIN ".table('members')." AS m ON t.uid=m.uid  LEFT JOIN ".table('members_train_setmeal')." AS p ON t.uid=p.uid";
	}
	$total_sql="SELECT COUNT(*) AS num FROM ".table('train_profile')." AS t".$joinsql.$wheresql;
	$total_val=$db->get_total($total_sql);
	$page = new page(array('total'=>$total_val, 'perpage'=>$perpage,'getarray'=>$_GET));
	$currenpage=$page->nowindex;
	$offset=($currenpage-1)*$perpage;
	$clist = get_train($offset,$perpage,$joinsql.$wheresql.$oederbysql,$operation_mode);
	$smarty->assign('pageheader',"��������");
	$smarty->assign('clist',$clist);
	$smarty->assign('certificate_train_dir',$certificate_train_dir);
	$smarty->assign('page',$page->show(3));
	$smarty->display('train/admin_train_list.htm');
}
elseif($act == 'train_perform')
{
	check_token();
	$u_id =!empty($_POST['y_id'])?$_POST['y_id']:adminmsg("��û��ѡ�������",1);
	if ($_POST['delete'])
	{
		check_permissions($_SESSION['admin_purview'],"tra_del");
		if ($_POST['delete_train']=='yes')
		{
		!del_train($u_id)?adminmsg("ɾ����������ʧ�ܣ�",0):"";
		}
		if ($_POST['delete_teacher']=='yes')
		{
		!del_train_allteacher($u_id)?adminmsg("ɾ����ʦʧ�ܣ�",0):"";
		}
		if ($_POST['delete_course']=='yes')
		{
		!del_train_allcourse($u_id)?adminmsg("ɾ���γ�ʧ�ܣ�",0):"";
		}
		if ($_POST['delete_course']<>'yes' && $_POST['delete_train']<>'yes' && $_POST['delete_teacher']<>'yes')
		{
		adminmsg("δѡ��ɾ�����ͣ�",1);
		}
		adminmsg("ɾ���ɹ���",2);
	}
	if (trim($_POST['set_audit']))
	{
		check_permissions($_SESSION['admin_purview'],"tra_audit");
		$audit=$_POST['audit'];
		$pms_notice=intval($_POST['pms_notice']);
		$reason=trim($_POST['reason']);
		!edit_train_audit($u_id,intval($audit),$reason,$pms_notice)?adminmsg("����ʧ�ܣ�",0):adminmsg("���óɹ���",2);
	}
	elseif (!empty($_POST['set_refresh']))
		{
			if (empty($_POST['refresh_course']))
			{
			$refrescou=false;
			}
			else
			{
			$refrescou=true;
			}
			if($n=refresh_train($u_id,$refrescou))
			{
			adminmsg("ˢ�³ɹ�����Ӧ���� {$n} ��",2);
			}
			else
			{
			adminmsg("ˢ��ʧ�ܣ�",0);
			}
		}
	elseif (!empty($_POST['set_recom']))
		{
			check_permissions($_SESSION['admin_purview'],"tra_promotion");
			$rec_notice=intval($_POST['rec_notice']);
			$recommend=intval($_POST['recommend']);
			$notice=trim($_POST['notice']);
			if($n=recom_train($u_id,$recommend,$rec_notice,$notice))
			{
				adminmsg("�������óɹ�����Ӧ���� {$n}",2);
			}
			else
			{
				adminmsg("��������ʧ�ܣ�",0);
			}
		}

}
elseif($act == 'edit_train_profile')
{
	get_token();
	check_permissions($_SESSION['admin_purview'],"tra_edit");
	$yid =!empty($_REQUEST['id'])?intval($_REQUEST['id']):adminmsg("��û��ѡ�������",1);
	$smarty->assign('pageheader',"��������");
	$train_profile=get_train_one_id($yid);
	$smarty->assign('url',$_SERVER["HTTP_REFERER"]);
	$smarty->assign('train_profile',$train_profile);
	$smarty->assign('certificate_train_dir',$certificate_train_dir);//Ӫҵִ��·��
	$smarty->display('train/admin_train_profile_edit.htm');
}
elseif ($act=='train_profile_save')
{
	check_token();
	check_permissions($_SESSION['admin_purview'],"train_edit");
	$id=intval($_POST['id']);
	$setsqlarr['uid']=intval($_POST['tuid']);
	$setsqlarr['trainname']=trim($_POST['trainname'])?trim($_POST['trainname']):adminmsg('��û������������ƣ�',1);
	$setsqlarr['nature']=trim($_POST['nature'])?intval($_POST['nature']):adminmsg('��ѡ��������ʣ�',1);
	$setsqlarr['nature_cn']=trim($_POST['nature_cn']);
	$setsqlarr['founddate']=intval(convert_datefm($_POST['founddate'],2));
	if (empty($setsqlarr['founddate']))
	{
	adminmsg('����д����ʱ�䣡ʱ���ʽ��YYYY-MM-DD',1);
	}	
	if ($setsqlarr['founddate']>=time())
	{
	adminmsg('����ʱ�䲻�ܴ��ڽ���',1);
	}	
	$setsqlarr['audit']=intval($_POST['audit']);
	$setsqlarr['district']=intval($_POST['district'])>0?intval($_POST['district']):adminmsg('��ѡ������������',1);
	$setsqlarr['sdistrict']=intval($_POST['sdistrict']);
	$setsqlarr['district_cn']=trim($_POST['district_cn']);
	$setsqlarr['address']=trim($_POST['address'])?trim($_POST['address']):adminmsg('����дͨѶ��ַ��',1);
	$setsqlarr['contact']=trim($_POST['contact'])?trim($_POST['contact']):adminmsg('����д��ϵ�ˣ�',1);
	$setsqlarr['telephone']=trim($_POST['telephone'])?trim($_POST['telephone']):adminmsg('����д��ϵ�绰��',1);
	$setsqlarr['email']=trim($_POST['email'])?trim($_POST['email']):adminmsg('����д��ϵ���䣡',1);
	$setsqlarr['website']=trim($_POST['website']);
	$setsqlarr['contents']=trim($_POST['contents'])?trim($_POST['contents']):adminmsg('����д��˾��飡',1);
	$setsqlarr['teacherpower']=trim($_POST['teacherpower'])?trim($_POST['teacherpower']):adminmsg('����дʦ��������',1);
	$setsqlarr['achievement']=trim($_POST['achievement'])?trim($_POST['achievement']):adminmsg('����д��Ҫҵ����',1);
	$setsqlarr['yellowpages']=intval($_POST['yellowpages']);
	
	$setsqlarr['contact_show']=intval($_POST['contact_show']);
	$setsqlarr['email_show']=intval($_POST['email_show']);
	$setsqlarr['telephone_show']=intval($_POST['telephone_show']);
	$setsqlarr['address_show']=intval($_POST['address_show']);
	$wheresql=" id='{$id}' ";
	$link[0]['text'] = "�����б�";
	$link[0]['href'] = $_POST['url'];
		if ($db->updatetable(table('train_profile'),$setsqlarr,$wheresql))
		{
				$coursearr['trainname']=$setsqlarr['trainname'];
				if (!$db->updatetable(table('course'),$coursearr," uid=".$setsqlarr['uid']."")) adminmsg('�޸���ѵ�������Ƴ���',0);
				if (!$db->updatetable(table('train_teachers'),$coursearr," uid=".$setsqlarr['uid']."")) adminmsg('�޸���ѵ�������Ƴ���',0);
				write_log("�޸Ļ�ԱuidΪ".$setsqlarr['uid']."����ѵ������Ϣ", $_SESSION['admin_name'],3);
				unset($setsqlarr);
				adminmsg("����ɹ���",2,$link);
		}
		else
		{
		unset($setsqlarr);
		adminmsg("����ʧ�ܣ�",0);
		}
}
elseif($act == 'meal_members')
{
	get_token();
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	$wheresql=" WHERE a.effective=1 ";
	$oederbysql=" order BY a.uid DESC ";
	$key=isset($_GET['key'])?trim($_GET['key']):"";
	$key_type=isset($_GET['key_type'])?intval($_GET['key_type']):"";
	if ($key && $key_type>0)
	{
		if     ($key_type===1)$wheresql.=" AND b.username = '{$key}'";
		elseif ($key_type===2)$wheresql.=" AND b.uid = '".intval($key)."' ";
		elseif ($key_type===3)$wheresql.=" AND b.email = '{$key}'";
		elseif ($key_type===4)$wheresql.=" AND b.mobile like '{$key}%'";
		elseif ($key_type===5)$wheresql.=" AND c.trainname like '{$key}%'";
		$oederbysql="";
	}
	else
	{	
		if (!empty($_GET['setmeal_id']))
		{
			$setmeal_id=intval($_GET['setmeal_id']);
			$wheresql.=" AND a.setmeal_id=".$setmeal_id;
		}
		if (!empty($_GET['settr']))
		{
			$settr=intval($_GET['settr']);
			if ($settr==-1)
			{
			$wheresql.=" AND a.endtime<".time()." AND a.endtime>0 ";
			}
			else
			{
			$settr=strtotime("{$settr} day");
			$wheresql.="  AND a.endtime>".time()." AND a.endtime< {$settr}";
			}			
		}
	}
	$joinsql=" LEFT JOIN ".table('members')." as b ON a.uid=b.uid  LEFT JOIN ".table('train_profile')." as c ON a.uid=c.uid ";
	$total_sql="SELECT COUNT(*) AS num FROM ".table('members_train_setmeal')." as a ".$joinsql.$wheresql;
	$total_val=$db->get_total($total_sql);
	$page = new page(array('total'=>$total_val, 'perpage'=>$perpage,'getarray'=>$_GET));
	$currenpage=$page->nowindex;
	$offset=($currenpage-1)*$perpage;
	$member = get_meal_members_list($offset,$perpage,$joinsql.$wheresql.$oederbysql);
	$smarty->assign('pageheader',"��������");
	$smarty->assign('navlabel','meal_members');
	$smarty->assign('member',$member);
	$smarty->assign('setmeal',get_setmeal());	
	$smarty->assign('page',$page->show(3));
	$smarty->display('train/admin_train_meal_members.htm');
}
elseif($act == 'members_list')
{
	get_token();
	check_permissions($_SESSION['admin_purview'],"tra_user_show");
		require_once(QISHI_ROOT_PATH.'include/page.class.php');
	$wheresql=" WHERE m.utype=4 ";
	$oederbysql=" order BY m.uid DESC ";
	$key=isset($_GET['key'])?trim($_GET['key']):"";
	$key_type=isset($_GET['key_type'])?intval($_GET['key_type']):"";
	if ($key && $key_type>0)
	{
		if     ($key_type===1)$wheresql.=" AND m.username = '{$key}'";
		elseif ($key_type===2)$wheresql.=" AND m.uid = '".intval($key)."' ";
		elseif ($key_type===3)$wheresql.=" AND m.email = '{$key}'";
		elseif ($key_type===4)$wheresql.=" AND m.mobile like '{$key}%'";
		elseif ($key_type===5)$wheresql.=" AND t.trainname like '%{$key}%'";
		$oederbysql="";
	}
	else
	{	
		if (!empty($_GET['settr']))
		{
			$settr=strtotime("-".intval($_GET['settr'])." day");
			$wheresql.=" AND m.reg_time> ".$settr;
		}
		if (!empty($_GET['verification']))
		{
			if ($_GET['verification']=="1")
			{
			$wheresql.=" AND m.email_audit = 1";
			}
			elseif ($_GET['verification']=="2")
			{
			$wheresql.=" AND m.email_audit = 0";
			}
			elseif ($_GET['verification']=="3")
			{
			$wheresql.=" AND m.mobile_audit = 1";
			}
			elseif ($_GET['verification']=="4")
			{
			$wheresql.=" AND m.mobile_audit = 0";
			}
		}
	}
	$joinsql=" LEFT JOIN ".table('train_profile')." as t ON m.uid=t.uid ";
	$total_sql="SELECT COUNT(*) AS num FROM ".table('members')." as m ".$joinsql.$wheresql;
	$total_val=$db->get_total($total_sql);
	$page = new page(array('total'=>$total_val, 'perpage'=>$perpage,'getarray'=>$_GET));
	$currenpage=$page->nowindex;
	$offset=($currenpage-1)*$perpage;
	$member = get_member_list($offset,$perpage,$joinsql.$wheresql.$oederbysql);
	$smarty->assign('pageheader',"��ѵ��Ա");
	$smarty->assign('member',$member);
	$smarty->assign('page',$page->show(3));
	$smarty->display('train/admin_train_user_list.htm');
}
elseif($act == 'user_edit')
{
	get_token();
	check_permissions($_SESSION['admin_purview'],"tra_user_edit");
	$train_user=get_user($_GET['tuid']);
	$smarty->assign('pageheader',"������Ա");
	$train_profile=get_train_one_uid($train_user['uid']);
	$train_user['tpl']=$train_profile['tpl'];
	$smarty->assign('train_user',$train_user);
	$smarty->assign('userpoints',get_user_points($train_user['uid']));
	$smarty->assign('setmeal',get_user_setmeal($train_user['uid']));
	$smarty->assign('givesetmeal',get_setmeal(false));
	$smarty->assign('url',$_SERVER["HTTP_REFERER"]);
	$smarty->display('train/admin_train_user_edit.htm');
}
elseif($act == 'set_account_save')
{
	check_token();
	check_permissions($_SESSION['admin_purview'],"tra_user_edit");
	require_once(ADMIN_ROOT_PATH.'include/admin_user_fun.php');
	$setsqlarr['username']=trim($_POST['username']);
	$setsqlarr['email']=trim($_POST['email']);
	$setsqlarr['email_audit']=intval($_POST['email_audit']);
	$setsqlarr['mobile']=trim($_POST['mobile']);
	$setsqlarr['mobile_audit']=intval($_POST['mobile_audit']);
	if ($_POST['qq_openid']=="1")
	{
	$setsqlarr['qq_openid']='';
	}
	$thisuid=intval($_POST['train_uid']);	
	if (strlen($setsqlarr['username'])<3) adminmsg('�û�������Ϊ3λ���ϣ�',1);
	$getusername=get_user_inusername($setsqlarr['username']);
	if (!empty($getusername)  && $getusername['uid']<>$thisuid)
	{
	adminmsg("�û��� {$setsqlarr['username']}  �Ѿ����ڣ�",1);
	}
	if (empty($setsqlarr['email']) || !preg_match("/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/",$setsqlarr['email']))
	{
	adminmsg('���������ʽ����',1);
	}
	$getemail=get_user_inemail($setsqlarr['email']);
	if (!empty($getemail)  && $getemail['uid']<>$thisuid)
	{
	adminmsg("Email  {$setsqlarr['email']}  �Ѿ����ڣ�",1);
	}
	if (!empty($setsqlarr['mobile']) && !preg_match("/^(13|15|14|17|18)\d{9}$/",$setsqlarr['mobile']))
	{
	adminmsg('�ֻ��������',1);
	}
	$getmobile=get_user_inmobile($setsqlarr['mobile']);
	if (!empty($setsqlarr['mobile']) && !empty($getmobile)  && $getmobile['uid']<>$thisuid)
	{
	adminmsg("�ֻ��� {$setsqlarr['mobile']}  �Ѿ����ڣ�",1);
	}
	if ($_POST['tpl'])
	{
		$tplarr['tpl']=trim($_POST['tpl']);
		$db->updatetable(table('train_profile'),$tplarr," uid='{$thisuid}'");
		$db->updatetable(table('course'),$tplarr," uid='{$thisuid}'");
		$db->updatetable(table('train_teachers'),$tplarr," uid='{$thisuid}'");
		unset($tplarr);
	}
	if ($db->updatetable(table('members'),$setsqlarr," uid=".$thisuid.""))
	{
	write_log("�޸Ļ�ԱuidΪ".$thisuid."�Ļ�����Ϣ", $_SESSION['admin_name'],3);
	$link[0]['text'] = "�����б�";
	$link[0]['href'] = $_POST['url'];
	adminmsg('�޸ĳɹ���',2,$link);
	}
	else
	{
	adminmsg('�޸�ʧ�ܣ�',1);
	}
}
elseif($act == 'userpass_edit')
{
	check_token();
	check_permissions($_SESSION['admin_purview'],"tra_user_edit");
	if (strlen(trim($_POST['password']))<6) adminmsg('���������Ϊ6λ���ϣ�',1);
	require_once(ADMIN_ROOT_PATH.'include/admin_user_fun.php');
	$user_info=get_user_inusername($_POST['username']);
	$pwd_hash=$user_info['pwd_hash'];
	$md5password=md5(md5(trim($_POST['password'])).$pwd_hash.$QS_pwdhash);	
	if ($db->query( "UPDATE ".table('members')." SET password = '$md5password'  WHERE uid='".$user_info['uid']."'"))
	{
			if(defined('UC_API'))
			{
			include_once(QISHI_ROOT_PATH.'uc_client/client.php');
			uc_user_edit($user_info['username'],trim($_POST['password']),trim($_POST['password']),"",1);
			}
	write_log("�޸Ļ�ԱuidΪ".$user_info['uid']."������", $_SESSION['admin_name'],3);
	$link[0]['text'] = "�����б�";
	$link[0]['href'] = $_POST['url'];
	adminmsg('�����ɹ���',2,$link);
	}
	else
	{
	adminmsg('����ʧ�ܣ�',1);
	}
}
elseif($act == 'userstatus_edit')
{
	check_token();
	check_permissions($_SESSION['admin_purview'],"tra_user_edit");
	if ($db->query( "UPDATE ".table('members')." SET status = '".intval($_POST['status'])."'  WHERE uid='".intval($_POST['userstatus_uid'])."'"))
	{
	write_log("�޸Ļ�ԱuidΪ".intval($_POST['userstatus_uid'])."��״̬", $_SESSION['admin_name'],3);
	$link[0]['text'] = "�����б�";
	$link[0]['href'] = $_POST['url'];
	adminmsg('�����ɹ���',2,$link);
	}
	else
	{
	adminmsg('����ʧ�ܣ�',1);
	}
}
elseif($act == 'userpoints_edit')
{
	check_token();
	check_permissions($_SESSION['admin_purview'],"tra_user_edit");
	if (intval($_POST['points'])<1) adminmsg('��������֣�',1);
	if (trim($_POST['points_notes'])=='') adminmsg('����д���ֲ���˵����',1);
	$link[0]['text'] = "�����б�";
	$link[0]['href'] = $_POST['url'];
	$user=get_user($_POST['train_uid']);
	$points_type=intval($_POST['points_type']);	
	$t=$points_type==1?"+":"-";
	report_deal($user['uid'],$points_type,intval($_POST['points']));
	$points=get_user_points($user['uid']);
	write_memberslog(intval($_POST['train_uid']),4,9101,$user['username']," ����Ա��������({$t}{$_POST['points']})��(ʣ��:{$points})����ע��".$_POST['points_notes']);
	$user=get_user($_POST['train_uid']);
		if(intval($_POST['is_money']) && $_POST['log_amount']){
			$amount=round($_POST['log_amount'],2);
			$ismoney=2;
		}else{
			$amount='0.00';
			$ismoney=1;
		}
		$notes="�����ˣ�{$_SESSION['admin_name']},˵�����޸Ļ�Ա {$user['username']} ���� ({$t}{$_POST['points']})����ȡ���ֽ�{$amount} Ԫ����ע��{$_POST['points_notes']}";
		write_setmeallog($_POST['train_uid'],$user['username'],$notes,3,$amount,$ismoney,1,4);
		write_log("�޸Ļ�ԱuidΪ".$_POST['train_uid']."����", $_SESSION['admin_name'],3);
	adminmsg('����ɹ���',2);
}
elseif($act == 'edit_setmeal_save')
{
	check_token();
    check_permissions($_SESSION['admin_purview'],"tra_user_edit");
	$setsqlarr['teachers_num']=$_POST['teachers_num'];
	$setsqlarr['course_num']=$_POST['course_num'];
	$setsqlarr['down_apply']=$_POST['down_apply'];
	$setsqlarr['change_templates']=intval($_POST['change_templates']);
	$setsqlarr['map_open']=intval($_POST['map_open']);
	$setsqlarr['added']=$_POST['added'];
	if ($_POST['setendtime']<>"")
	{
		$setendtime=convert_datefm($_POST['setendtime'],2);
		if ($setendtime=='')
		{
		adminmsg('���ڸ�ʽ����',0);	
		}
		else
		{
		$setsqlarr['endtime']=$setendtime;
		}
	}
	else
	{
	$setsqlarr['endtime']=0;
	}
	if ($_POST['days']<>"")
	{
			if (intval($_POST['days'])<>0)
			{
				$oldendtime=intval($_POST['oldendtime']);
				$setsqlarr['endtime']=strtotime("".intval($_POST['days'])." days",$oldendtime==0?time():$oldendtime);
			}
			if (intval($_POST['days'])=="0")
			{
				$setsqlarr['endtime']=0;
			}
	}
	$setmealtime=$setsqlarr['endtime'];
	$train_uid=intval($_POST['train_uid']);
	if ($train_uid)
	{
			$setmeal=get_user_setmeal($train_uid);
			if (!$db->updatetable(table('members_train_setmeal'),$setsqlarr," uid=".$train_uid."")) adminmsg('�޸ĳ���',0);

		//��Ա�ײͱ����¼������Ա��̨�޸Ļ�Ա�ײͣ��޸Ļ�Ա��3��ʾ������Ա��̨�޸�
			$setmeal['endtime']=date('Y-m-d',$setmeal['endtime']);
			$setsqlarr['endtime']=date('Y-m-d',$setsqlarr['endtime']);
			$setsqlarr['log_amount']=round($_POST['log_amount']);
			$notes=edit_setmeal_notes($setsqlarr,$setmeal);
			if($notes){
				$user=get_user($_POST['train_uid']);
				$ismoney=round($_POST['log_amount'])?2:1;
				write_setmeallog($train_uid,$user['username'],$notes,3,$setsqlarr['log_amount'],$ismoney,2,4);
			}
			if ($setsqlarr['endtime']<>"")
			{
				$setmeal_deadline['setmeal_deadline']=$setmealtime;
				if (!$db->updatetable(table('course'),$setmeal_deadline," uid='{$train_uid}' AND add_mode='2' "))adminmsg('�޸ĳ���',0);
			}
	}
	write_log("�޸Ļ�ԱuidΪ".$train_uid."�ײ�", $_SESSION['admin_name'],3);
	$link[0]['text'] = "�����б�";
	$link[0]['href'] = $_POST['url'];
	adminmsg('�����ɹ���',2,$link);
}
elseif($act == 'set_setmeal_save')
{
	check_token();
	check_permissions($_SESSION['admin_purview'],"tra_user_edit");
    if (intval($_POST['reg_service'])>0)
	{
		if (set_members_setmeal($_POST['train_uid'],$_POST['reg_service']))
		{
		$link[0]['text'] = "�����б�";
		$link[0]['href'] = $_POST['url'];

		//��Ա�ײͱ����¼������Ա��̨�޸Ļ�Ա�ײͣ����¿�ͨ�ײ͡�3��ʾ������Ա��̨�޸�
		$user=get_user($_POST['train_uid']);
		if(intval($_POST['is_money']) && $_POST['log_amount']){
			$amount=round($_POST['log_amount'],2);
			$ismoney=2;
		}else{
			$amount='0.00';
			$ismoney=1;
		}
		$notes="�����ˣ�{$_SESSION['admin_name']},˵����Ϊ��Ա {$user['username']} ���¿�ͨ������ȡ�����{$amount}Ԫ������ID��{$_POST['reg_service']}��";
		write_setmeallog($_POST['train_uid'],$user['username'],$notes,4,$amount,$ismoney,2,4);
		write_log("�༭��ԱuidΪ".$_POST['train_uid']."�ײ���Ϣ", $_SESSION['admin_name'],3);
		adminmsg('�����ɹ���',2,$link);
		}
		else
		{
		adminmsg('����ʧ�ܣ�',1);
		}
	}
	else
	{
	adminmsg('��ѡ������ײͣ�',1);
	}	
}
elseif($act == 'order_list')
{	
	get_token();
	check_permissions($_SESSION['admin_purview'],"ord_show");
		require_once(QISHI_ROOT_PATH.'include/page.class.php');
		require_once(ADMIN_ROOT_PATH.'include/admin_pay_fun.php');
	$wheresql=" WHERE o.utype=4 ";
	$oederbysql=" order BY o.addtime DESC ";
	$key=isset($_GET['key'])?trim($_GET['key']):"";
	$key_type=isset($_GET['key_type'])?intval($_GET['key_type']):"";
	if ($key && $key_type>0)
	{
		if     ($key_type===1)$wheresql=" WHERE o.utype=4 AND c.trainname like '%{$key}%'";
		elseif ($key_type===2)$wheresql=" WHERE o.utype=4 AND m.username = '{$key}'";
		elseif ($key_type===3)$wheresql=" WHERE o.utype=4 AND o.oid ='".trim($key)."'";
		$oederbysql="";
	}
	else
	{	
		$wheresqlarr['o.utype']='4';
		!empty($_GET['is_paid'])? $wheresqlarr['o.is_paid']=intval($_GET['is_paid']):'';
		!empty($_GET['typename'])?$wheresqlarr['o.payment_name']=trim($_GET['typename']):'';
		if (is_array($wheresqlarr)) $wheresql=wheresql($wheresqlarr);
		
		if (!empty($_GET['settr']))
		{
			$settr=strtotime("-".intval($_GET['settr'])." day");
			$wheresql.=empty($wheresql)?" WHERE ": " AND ";
			$wheresql.="o.addtime> ".$settr;
		}
	}
	$joinsql=" left JOIN ".table('members')." as m ON o.uid=m.uid LEFT JOIN  ".table('train_profile')." as c ON o.uid=c.uid ";
	$total_sql="SELECT COUNT(*) AS num FROM ".table('order')." as o ".$joinsql.$wheresql;
	$total_val=$db->get_total($total_sql);
	$page = new page(array('total'=>$total_val, 'perpage'=>$perpage,'getarray'=>$_GET));
	$currenpage=$page->nowindex;
	$offset=($currenpage-1)*$perpage;
	$orderlist = get_order_list($offset,$perpage,$joinsql.$wheresql.$oederbysql);
	$smarty->assign('pageheader',"��������");
	$smarty->assign('payment_list',get_payment(2));
	$smarty->assign('orderlist',$orderlist);
	$smarty->assign('page',$page->show(3));
	$smarty->display('train/admin_order_list.htm');
}
elseif($act == 'show_order')
{
	get_token();
	check_permissions($_SESSION['admin_purview'],"ord_show");
	$smarty->assign('pageheader',"��������");
	$smarty->assign('url',$_SERVER["HTTP_REFERER"]);
	$smarty->assign('payment',get_order_one($_GET['id']));
	$smarty->display('train/admin_order_show.htm');
}
elseif($act == 'order_notes_save')
{
	check_token();
	$link[0]['text'] = "�����б�";
	$link[0]['href'] = $_POST['url'];
	!$db->query("UPDATE ".table('order')." SET  notes='".$_POST['notes']."' WHERE id='".intval($_GET['id'])."'")?adminmsg('����ʧ��',1):adminmsg("�����ɹ���",2,$link);
}
elseif($act == 'order_set')
{
	get_token();
	check_permissions($_SESSION['admin_purview'],"ord_set");
	$smarty->assign('pageheader',"��������");
	$smarty->assign('url',$_SERVER["HTTP_REFERER"]);
	$smarty->assign('payment',get_order_one($_GET['id']));
	$smarty->display('train/admin_order_set.htm');
}
elseif($act == 'order_set_save')
{
	check_token();
	check_permissions($_SESSION['admin_purview'],"ord_set");
		if (order_paid(trim($_POST['oid'])))
		{
		$link[0]['text'] = "�����б�";
		$link[0]['href'] = $_POST['url'];
		!$db->query("UPDATE ".table('order')." SET notes='".$_POST['notes']."' WHERE id=".intval($_GET['id'])."  LIMIT 1 ")?adminmsg('����ʧ��',1):adminmsg("�����ɹ���",2,$link);
		}
		else
		{
		adminmsg('����ʧ��',1);
		}
}
//ȡ����Ա��ֵ����
elseif($act == 'order_del')
{
	check_token();
	check_permissions($_SESSION['admin_purview'],"ord_del");
	$id =!empty($_REQUEST['id'])?$_REQUEST['id']:adminmsg("��û��ѡ����Ŀ��",1);
	if (del_order($id))
	{
	adminmsg("ȡ���ɹ���",2,$link);
	}
	else
	{
	adminmsg("ȡ��ʧ�ܣ�",1);
	}
}
elseif($act == 'meal_log')
{
	get_token();
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	$oederbysql=" order BY a.log_id DESC ";
	$key_uid=isset($_GET['key_uid'])?trim($_GET['key_uid']):"";
	$key=isset($_GET['key'])?trim($_GET['key']):"";
	$key_type=isset($_GET['key_type'])?intval($_GET['key_type']):"";
	$operation_mode=$_CFG['operation_train_mode'];
	//���֡��ײ�����ģʽ�����¼
	if($operation_mode=='1')
	{
		$wheresql=" WHERE a.log_mode=1 AND a.log_utype=4";
	}
	elseif($operation_mode=='2')
	{
		$wheresql=" WHERE a.log_mode=2 AND a.log_utype=4";
	}
	//������Ա(uid)�鿴�����¼
	if ($key_uid)
	{
		$wheresql.="  AND a.log_uid = '".intval($key_uid)."' ";
		//������ʶ�������ѯ������Ա�Ļ� ��ô���½ǵ�����������
		$smarty->assign('sign','1');
	}
	//����������� : ����ĳ����Ա�ı����¼
	elseif ($key && $key_type>0)
	{
		if     ($key_type===1)$wheresql.="  AND a.log_username = '{$key}'";
		elseif ($key_type===2)$wheresql.="  AND a.log_uid = '".intval($key)."' ";
		elseif ($key_type===3)$wheresql.=" AND c.huntername like '{$key}%'";
		$oederbysql=" order BY a.log_id DESC ";
	}
	//��������ɸѡ��1->ϵͳ���͡�2->��Ա����3->����Ա�޸ġ�4->����Ա��ͨ����ɸѡ
	if (!empty($_GET['log_type']))
	{
		$log_type=intval($_GET['log_type']);
		$wheresql.=" AND  a.log_type=".$log_type;
	}
	if (!empty($_GET['settr']))
	{
		$settr=intval($_GET['settr']);
		$settr=strtotime("-{$settr} day");
		$wheresql.=" AND a.log_addtime> ".$settr;
	}
	if (!empty($_GET['is_money']))
	{
		$is_money=intval($_GET['is_money']);
		$wheresql.= " AND a.log_ismoney={$is_money}";
	}
	if($operation_mode=='1')
	{
		$joinsql=" LEFT JOIN ".table('members_points')." as b ON a.log_uid=b.uid  LEFT JOIN ".table('train_profile')." as c ON a.log_uid=c.uid ";
	}
	else
	{
		$joinsql=" LEFT JOIN ".table('members_train_setmeal')." as b ON a.log_uid=b.uid  LEFT JOIN ".table('train_profile')." as c ON a.log_uid=c.uid ";
	}
	$total_sql="SELECT COUNT(*) AS num FROM ".table('members_charge_log')." as a ".$joinsql.$wheresql;
	$total_val=$db->get_total($total_sql);
	$page = new page(array('total'=>$total_val, 'perpage'=>$perpage,'getarray'=>$_GET));
	$currenpage=$page->nowindex;
	$offset=($currenpage-1)*$perpage;
	$meallog = get_meal_members_log($offset,$perpage,$joinsql.$wheresql.$oederbysql,$operation_mode);
	$smarty->assign('pageheader','��������');
	$smarty->assign('navlabel','meal_log');
	$smarty->assign('meallog',$meallog);
	$smarty->assign('page',$page->show(3));
	$smarty->display('train/admin_train_meal_log.htm');
}
elseif($act == 'meal_log_pie')
{
	require_once(ADMIN_ROOT_PATH.'include/admin_flash_statement_fun.php');
	$pie_type=!empty($_GET['pie_type'])?intval($_GET['pie_type']):1;
	meal_train_log_pie($pie_type,4);	
	$smarty->assign('pageheader',"��������");
	$smarty->assign('navlabel','meal_log_pie');
	$smarty->display('train/admin_train_meal_log_pie.htm');
}
elseif($act == 'meallog_del')
{
	check_permissions($_SESSION['admin_purview'],"meallog_del");
	check_token();
	$id =!empty($_REQUEST['id'])?$_REQUEST['id']:adminmsg("��û��ѡ���¼��",1);
	$num=del_meal_log($id);
	if ($num>0){adminmsg("ɾ���ɹ�����ɾ��".$num."��",2);}else{adminmsg("ɾ��ʧ�ܣ�",0);}
}
elseif($act == 'delete_user')
{	
	check_token();
	check_permissions($_SESSION['admin_purview'],"tra_user_del");
	$tuid =!empty($_REQUEST['tuid'])?$_REQUEST['tuid']:adminmsg("��û��ѡ���Ա��",1);
	if ($_POST['delete'])
	{
		if (!empty($_POST['delete_user']))
		{
		!delete_train_user($tuid)?adminmsg("ɾ����Աʧ�ܣ�",0):"";
		}
		if (!empty($_POST['delete_train']))
		{
		!del_train($tuid)?adminmsg("ɾ����������ʧ�ܣ�",0):"";
		}
		if (!empty($_POST['delete_teachers']))
		{
		!del_train_allteacher($tuid)?adminmsg("ɾ��������ʦʧ�ܣ�",0):"";
		}
		if (!empty($_POST['delete_course']))
		{
		!del_train_allcourse($tuid)?adminmsg("ɾ�������γ�ʧ�ܣ�",0):"";
		}
	adminmsg("ɾ���ɹ���",2);
	}
}
elseif($act == 'members_add')
{
	get_token();
	check_permissions($_SESSION['admin_purview'],"tra_user_add");
	$smarty->assign('pageheader',"������Ա");
	$smarty->assign('givesetmeal',get_setmeal(false));
	$smarty->assign('points',get_cache('points_rule'));
	$smarty->display('train/admin_train_user_add.htm');
}
elseif($act == 'members_add_save')
{
	check_token();
	check_permissions($_SESSION['admin_purview'],"tra_user_add");
	require_once(ADMIN_ROOT_PATH.'include/admin_user_fun.php');
	if (strlen(trim($_POST['username']))<3) adminmsg('�û�������Ϊ3λ���ϣ�',1);
	if (strlen(trim($_POST['password']))<6) adminmsg('�������Ϊ6λ���ϣ�',1);
	$sql['username'] = !empty($_POST['username']) ? trim($_POST['username']):adminmsg('����д�û�����',1);
	$sql['password'] = !empty($_POST['password']) ? trim($_POST['password']):adminmsg('����д���룡',1);	
	if ($sql['password']<>trim($_POST['password1']))
	{
	adminmsg('������������벻��ͬ��',1);
	}
	$sql['utype'] = !empty($_POST['member_type']) ? intval($_POST['member_type']):adminmsg('��û��ѡ��ע�����ͣ�',1);
	if (empty($_POST['email']) || !preg_match("/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/",$_POST['email']))
	{
	adminmsg('���������ʽ����',1);
	}
	$sql['email']= trim($_POST['email']);
	if (get_user_inusername($sql['username']))
	{
	adminmsg('���û����Ѿ���ʹ�ã�',1);
	}
	if (get_user_inemail($sql['email']))
	{
	adminmsg('�� Email �Ѿ���ע�ᣡ',1);
	}
	if(defined('UC_API'))
	{
		include_once(QISHI_ROOT_PATH.'uc_client/client.php');
		if (uc_user_checkname($sql['username'])<>"1")
		{
		adminmsg('���û����Ѿ���ʹ�û����û����Ƿ���',1);
		exit();
		}
		elseif (uc_user_checkemail($sql['email'])<>"1")
		{
			adminmsg('�� Email�Ѿ���ʹ�û��߷Ƿ���',1);
			exit();
		}
		else
		{
			uc_user_register($sql['username'],$sql['password'],$sql['email']);
		}
	}
	$sql['pwd_hash'] = randstr();
	$sql['password'] = md5(md5($sql['password']).$sql['pwd_hash'].$QS_pwdhash);
	$sql['reg_time']=time();
	$sql['reg_ip']=$online_ip;
	$insert_id=$db->inserttable(table('members'),$sql,true);
			if($sql['utype']=="4")
			{
			$db->query("INSERT INTO ".table('members_points')." (uid) VALUES ('{$insert_id}')");
			$db->query("INSERT INTO ".table('members_train_setmeal')." (uid) VALUES ('{$insert_id}')");
				if(intval($_POST['is_money']) && $_POST['log_amount']){
					$amount=round($_POST['log_amount'],2);
					$ismoney=2;
				}else{
					$amount='0.00';
					$ismoney=1;
				}
				$regpoints_num=intval($_POST['regpoints_num']);
				if ($_POST['regpoints']=="y")
				{
				write_memberslog($insert_id,4,9101,$sql['username'],"<span style=color:#FF6600>ע���Աϵͳ�Զ�����!(+{$regpoints_num})</span>");
				$notes="�����ˣ�{$_SESSION['admin_name']},˵������̨��ӻ�����Ա������(+{$regpoints_num})���֣���ȡ���ã�{$amount}Ԫ";
				write_setmeallog($insert_id,$sql['username'],$notes,4,$amount,$ismoney,1,4);
				report_deal($insert_id,1,$regpoints_num);
				}
				$reg_service=intval($_POST['train_reg_service']);
				if ($reg_service>0)
				{
				$service=get_setmeal_one($reg_service);
				write_memberslog($insert_id,4,9102,$sql['username'],"��ͨ����({$service['setmeal_name']})");
				set_members_setmeal($insert_id,$reg_service);
				$notes="�����ˣ�{$_SESSION['admin_name']},˵������̨��ӻ�����Ա����ͨ����({$service['setmeal_name']})����ȡ���ã�{$amount}Ԫ";
				write_setmeallog($insert_id,$sql['username'],$notes,4,$amount,$ismoney,2,4);
				}
				if(intval($_POST['is_money']) && $_POST['log_amount'] && !$notes){
				$notes="�����ˣ�{$_SESSION['admin_name']},˵������̨��ӻ�����Ա��δ���ͻ��֣�δ��ͨ�ײͣ���ȡ���ã�{$amount}Ԫ";
				write_setmeallog($insert_id,$sql['username'],$notes,4,$amount,2,2,4);
				}			
			}
			
	write_log("��ӻ�Ա��".$sql['username'], $_SESSION['admin_name'],3);
	$link[0]['text'] = "�����б�";
	$link[0]['href'] = "?act=members_list";
	$link[1]['text'] = "�������";
	$link[1]['href'] = "?act=members_add";
	adminmsg('��ӳɹ���',2,$link);
}

elseif($act == 'train_img')
{
	get_token();
	check_permissions($_SESSION['admin_purview'],"tra_img_show");
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	$oederbysql=" order BY i.id DESC ";
	$key=isset($_GET['key'])?trim($_GET['key']):"";
	$key_type=isset($_GET['key_type'])?intval($_GET['key_type']):"";
	if ($key && $key_type>0)
	{
		
		if ($key_type===1)$wheresql=" WHERE t.trainname like '%{$key}%'";
		elseif ($key_type===2)$wheresql=" WHERE t.id ='".intval($key)."'";
		elseif ($key_type===3)$wheresql=" WHERE i.id ='".intval($key)."'";
		elseif ($key_type===4)$wheresql=" WHERE i.title like '{$key}%'";
		$oederbysql="";
	}
	$_GET['audit']<>""? $wheresqlarr['i.audit']=intval($_GET['audit']):'';
	if (is_array($wheresqlarr)) $wheresql=wheresql($wheresqlarr);
	if (!empty($_GET['settr']))
	{
		$settr=strtotime("-".intval($_GET['settr'])." day");
		$wheresql=empty($wheresql)?" WHERE i.addtime> ".$settr:$wheresql." AND i.addtime> ".$settr;
	}
	$joinsql=" LEFT JOIN ".table('train_profile')." AS t ON i.train_id=t.id  ";
	$total_sql="SELECT COUNT(*) AS num FROM ".table('train_img')." AS i".$joinsql.$wheresql;
	$total_val=$db->get_total($total_sql);
	$page = new page(array('total'=>$total_val, 'perpage'=>$perpage,'getarray'=>$_GET));
	$currenpage=$page->nowindex;
	$offset=($currenpage-1)*$perpage;
	$clist = get_train_img($offset,$perpage,$joinsql.$wheresql.$oederbysql);
	$smarty->assign('pageheader',"����ͼƬ");
	$smarty->assign('clist',$clist);
	$smarty->assign('page',$page->show(3));
	$smarty->display('train/admin_train_img.htm');
}
elseif($act == 'del_train_img')
{
	check_permissions($_SESSION['admin_purview'],"tra_img_del");
	check_token();
	$id =!empty($_REQUEST['id'])?$_REQUEST['id']:adminmsg("��û��ѡ��ͼƬ��",1);
	$num=del_train_img($id);
	if ($num>0){adminmsg("ɾ���ɹ�����ɾ��".$num."��",2);}else{adminmsg("ɾ��ʧ�ܣ�",0);}
}
elseif($act == 'edit_img_audit')
{
	check_permissions($_SESSION['admin_purview'],"tra_img_audit");
	check_token();
	$id =!empty($_REQUEST['id'])?$_REQUEST['id']:adminmsg("��û��ѡ��ͼƬ��",1);
	$audit=intval($_POST['audit']);
	$pms_notice=intval($_POST['pms_notice']);
	$reason=trim($_POST['reason']);
	$num=edit_img_audit($id,$audit,$reason,$pms_notice);
	if ($num>0){adminmsg("��˳ɹ��������".$num."��",2);}else{adminmsg("��˳ɹ�!��Ӱ��{$num}��",0);}
}
elseif($act == 'train_news')
{
	get_token();
	check_permissions($_SESSION['admin_purview'],"tra_news_show");
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	$oederbysql=" order BY n.id DESC ";
	$key=isset($_GET['key'])?trim($_GET['key']):"";
	$key_type=isset($_GET['key_type'])?intval($_GET['key_type']):"";
	if ($key && $key_type>0)
	{
		
		if ($key_type===1)$wheresql=" WHERE t.trainname like '%{$key}%'";
		elseif ($key_type===2)$wheresql=" WHERE t.id ='".intval($key)."'";
		elseif ($key_type===3)$wheresql=" WHERE n.id ='".intval($key)."'";
		elseif ($key_type===4)$wheresql=" WHERE n.title like '{$key}%'";
		$oederbysql="";
	}
	$_GET['audit']<>""? $wheresqlarr['n.audit']=intval($_GET['audit']):'';
	if (is_array($wheresqlarr)) $wheresql=wheresql($wheresqlarr);
	if (!empty($_GET['settr']))
	{
		$settr=strtotime("-".intval($_GET['settr'])." day");
		$wheresql=empty($wheresql)?" WHERE n.addtime> ".$settr:$wheresql." AND n.addtime> ".$settr;
	}
	$joinsql=" LEFT JOIN ".table('train_profile')." AS t ON n.train_id=t.id  ";
	$total_sql="SELECT COUNT(*) AS num FROM ".table('train_news')." AS n".$joinsql.$wheresql;
	$total_val=$db->get_total($total_sql);
	$page = new page(array('total'=>$total_val, 'perpage'=>$perpage,'getarray'=>$_GET));
	$currenpage=$page->nowindex;
	$offset=($currenpage-1)*$perpage;
	$clist = get_train_news($offset,$perpage,$joinsql.$wheresql.$oederbysql);
	$smarty->assign('pageheader',"��������");
	$smarty->assign('clist',$clist);
	$smarty->assign('page',$page->show(3));
	$smarty->display('train/admin_train_news.htm');
}
elseif($act == 'edit_news_audit')
{
	check_permissions($_SESSION['admin_purview'],"tra_news_audit");
	check_token();
	$id =!empty($_REQUEST['id'])?$_REQUEST['id']:adminmsg("��û��ѡ�����ţ�",1);
	$audit=intval($_POST['audit']);
	$pms_notice=intval($_POST['pms_notice']);
	$reason=trim($_POST['reason']);
	$num=edit_news_audit($id,$audit,$reason,$pms_notice);
	if ($num>0){adminmsg("��˳ɹ��������".$num."��",2);}else{adminmsg("��˳ɹ�!��Ӱ��{$num}��",0);}
}
elseif($act == 'del_train_news')
{
	check_permissions($_SESSION['admin_purview'],"tra_news_del");
	check_token();
	$id =!empty($_REQUEST['id'])?$_REQUEST['id']:adminmsg("��û��ѡ�����ţ�",1);
	$num=del_train_news($id);
	if ($num>0){adminmsg("ɾ���ɹ�����ɾ��".$num."��",2);}else{adminmsg("ɾ��ʧ�ܣ�",0);}
}
elseif($act == 'edit_train_news')
{
	check_permissions($_SESSION['admin_purview'],"tra_news_edit");
	get_token();
	$id =!empty($_REQUEST['id'])?$_REQUEST['id']:adminmsg("��û��ѡ�����ţ�",1);
	$news=get_news_one($id);
	$smarty->assign('news',$news);
	$smarty->assign('url',$_SERVER["HTTP_REFERER"]);
	$smarty->assign('pageheader',"��������");
	$smarty->display('train/admin_train_news_edit.htm');
}
elseif($act == 'train_news_save')
{
	check_token();
    check_permissions($_SESSION['admin_purview'],"tra_news_edit");
	$id=intval($_POST['id']);
	$setsqlarr['title']=$_POST['title']?trim($_POST['title']):adminmsg("��û����д���ű��⣡",1);
	$setsqlarr['content']=$_POST['content']?trim($_POST['content']):adminmsg("��û����д�������ݣ�",1);
	$setsqlarr['click']=intval($_POST['click']);
	$setsqlarr['order']=intval($_POST['order']);
	$setsqlarr['audit']=intval($_POST['audit']);
	$setsqlarr['addtime']=time();
	$link[1]['text'] = "���������б�";
	$link[1]['href'] = '?act=train_news';
	$link[0]['text'] = '�鿴�޸Ľ��';
	$link[0]['href'] = '?act=edit_train_news&id='.$id;
	!$db->updatetable(table('train_news'),$setsqlarr,' id='.$id.' ')?adminmsg("�޸Ļ�������ʧ�ܣ�",1,$link):adminmsg("�޸Ļ������ųɹ���",2,$link);
}
elseif($act == 'management')
{	
	$id=intval($_GET['id']);
	$u=get_user($id);
	if (!empty($u))
	{
		unset($_SESSION['uid']);
		unset($_SESSION['username']);
		unset($_SESSION['utype']);
		unset($_SESSION['uqqid']);
		setcookie("QS[uid]","",time() - 3600,$QS_cookiepath, $QS_cookiedomain);
		setcookie("QS[username]","",time() - 3600,$QS_cookiepath, $QS_cookiedomain);
		setcookie("QS[password]","",time() - 3600,$QS_cookiepath, $QS_cookiedomain);
		setcookie("QS[utype]","",time() - 3600,$QS_cookiepath, $QS_cookiedomain);
		unset($_SESSION['activate_username']);
		unset($_SESSION['activate_email']);
		
		$_SESSION['uid']=$u['uid'];
		$_SESSION['username']=$u['username'];
		$_SESSION['utype']=$u['utype'];
		$_SESSION['uqqid']="1";
		setcookie('QS[uid]',$u['uid'],0,$QS_cookiepath,$QS_cookiedomain);
		setcookie('QS[username]',$u['username'],0,$QS_cookiepath,$QS_cookiedomain);
		setcookie('QS[password]',$u['password'],0,$QS_cookiepath,$QS_cookiedomain);
		setcookie('QS[utype]',$u['utype'], 0,$QS_cookiepath,$QS_cookiedomain);
		header("Location:".get_member_url($u['utype']));
	}	
} 
?>