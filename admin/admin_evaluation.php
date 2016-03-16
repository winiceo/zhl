<?php
 /*
 * 74cms �˲Ų���ģ��
*/
define('IN_QISHI',true);
require_once(dirname(__FILE__).'/../data/config.php');
require_once(dirname(__FILE__).'/include/admin_common.inc.php');
require_once(ADMIN_ROOT_PATH.'include/admin_evaluation_fun.php');
require_once(ADMIN_ROOT_PATH.'include/upload.php');
$act = !empty($_GET['act']) ? trim($_GET['act']) : 'list';
$smarty->assign('act',$act);
check_permissions($_SESSION['admin_purview'],"set_evaluation");
//�Ծ��б�
if($act == 'list')
{
	get_token();
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	$oederbysql=" order BY id  DESC";
	$key=isset($_GET['key'])?trim($_GET['key']):"";
	$key_type=isset($_GET['key_type'])?intval($_GET['key_type']):"";
	if ($key && $key_type>0)
	{
		if($key_type===1)$wheresql=" WHERE title like '%{$key}%'";
		$oederbysql="";
	}
	else
	{
		//��������
		$type_id=intval($_GET['type_id']);
		if ($type_id>0)
		{
			$wheresql.=empty($wheresql)?" WHERE ":" AND  ";
			$wheresql.=" type_id = {$type_id} ";	
		}
	}
	$total_sql="SELECT COUNT(*) AS num FROM ".table('evaluation_paper').$wheresql;
	$total=$db->get_total($total_sql);
	$page = new page(array('total'=>$total, 'perpage'=>$perpage));
	$currenpage=$page->nowindex;
	$offset=($currenpage-1)*$perpage;
	$list = get_evaluation_paper($offset, $perpage,$wheresql.$oederbysql);
	$smarty->assign('list',$list);
	$smarty->assign('total',$total);
	$smarty->assign('page',$page->show(3));
	//��������
	$evaluat_type = get_evaluation_category();
	$smarty->assign('evaluat_type',$evaluat_type);
	$smarty->assign('pageheader',"�˲Ų���");
	$smarty->display('evaluation/admin_evaluation_list.htm');
}
//��������
elseif($act == 'type_list')
{
	get_token();
	//��������
	$evaluat_type = get_evaluation_category();
	$smarty->assign('evaluat_type',$evaluat_type);
	$smarty->assign('pageheader',"�˲Ų���");
	$smarty->display('evaluation/admin_evaluation_type_list.htm');
}
// �޸Ĳ�������
elseif($act == 'type_edit')
{
	get_token();
	$id=intval($_GET['id']);
	$smarty->assign('pageheader',"�˲Ų��� ");
	$smarty->assign('show',get_evaluation_category_one($id));
	$eval_dir=$_CFG['site_dir']."data/eval/logo/";
	$smarty->assign('eval_dir',$eval_dir);
	$smarty->display('evaluation/admin_evaluation_type_edit.htm');
}
// �����������
elseif($act == 'save_type')
{
	check_token();
	$id=intval($_POST['id']);
	//LOGO
	if (!empty($_FILES['logo']['name']))
	{
		$eval_updir="../data/eval/logo/".date("Y/m/d/");
		make_dir($eval_updir);
		$setarr['logo']=_asUpFiles($eval_updir,"logo",1000,'gif/jpg/bmp/png',true);
		if (empty($setarr['logo']))
		{
			adminmsg('�ϴ��ļ�ʧ�ܣ�',1);
		}
		$setarr['logo']=date("Y/m/d/").$setarr['logo'];
	}
	$setarr["suitable_crowd"]=$_POST["suitable_crowd"]?trim($_POST["suitable_crowd"]):adminmsg("������������Ⱥ��");
	$setarr["description"]=$_POST["description"]?trim($_POST["description"]):adminmsg("����������������");
	//�޸�
	$link[0]['text'] = "���ز��������б�";
	$link[0]['href'] = '?act=type_list';
	!$db->updatetable(table("evaluation_type"),$setarr,array("id"=>$id))?adminmsg("�޸�ʧ�ܣ�"):adminmsg("�޸ĳɹ���",2,$link);	
}
// ����Ծ�
elseif($act == 'paper_add')
{
	get_token();
	//��������
	$evaluat_type = get_evaluation_category();
	$smarty->assign('evaluat_type',$evaluat_type);
	$smarty->assign('pageheader',"�˲Ų��� ");
	$smarty->display('evaluation/admin_evaluation_add.htm');
}
// �޸��Ծ�
elseif($act == 'paper_edit')
{
	get_token();
	$id=intval($_GET['id']);
	$smarty->assign('pageheader',"�˲Ų��� ");
	$smarty->assign('show',get_evaluation_one($id));
	//��������
	$evaluat_type = get_evaluation_category();
	$smarty->assign('evaluat_type',$evaluat_type);
	$eval_dir=$_CFG['site_dir']."data/eval/img/";
	$smarty->assign('eval_dir',$eval_dir);
	$smarty->display('evaluation/admin_evaluation_add.htm');
}
// �����Ծ�
elseif($act == 'paper_save')
{
	check_token();
	$id=intval($_POST['id']);
	$setarr["title"]=$_POST["title"]?trim($_POST["title"]):adminmsg("�������Ծ����");
	$setarr["type_id"]=$_POST["type_id"]?intval($_POST["type_id"]):adminmsg("��ѡ��������������");
	$setarr["timelimit"]=$_POST["timelimit"]?intval($_POST["timelimit"]):adminmsg("���������ʱ��");
	$setarr["price"]=$_POST["price"]?intval($_POST["price"]):0;
	$setarr["keywords"]=$_POST["keywords"]?trim($_POST["keywords"]):adminmsg("������ؼ���");
	$setarr["suitable_crowd"]=$_POST["suitable_crowd"]?trim($_POST["suitable_crowd"]):adminmsg("����ʹ����Ⱥ");
	$setarr["description"]=$_POST["description"]?trim($_POST["description"]):adminmsg("�������Ծ�����");
	$setarr["explain"]=$_POST["explain"]?trim($_POST["explain"]):adminmsg("������÷�˵��");
	$setarr["result_type"]=$_POST["result_type"]?trim($_POST["result_type"]):adminmsg("�����Ͳ���Ϊ��");
	//�޸�
	if($id>0)
	{
		//LOGO
		if (!empty($_FILES['img']['name']))
		{
			$eval_updir="../data/eval/img/".date("Y/m/d/");
			make_dir($eval_updir);
			$setarr['img']=_asUpFiles($eval_updir,"img",1000,'gif/jpg/bmp/png',true);
			if (empty($setarr['img']))
			{
				adminmsg('�ϴ��ļ�ʧ�ܣ�',1);
			}
			$setarr['img']=date("Y/m/d/").$setarr['img'];
		}
		write_log("��̨�޸��Ծ���Ϣ", $_SESSION['admin_name'],3);
		!$db->updatetable(table("evaluation_paper"),$setarr,array("id"=>$id))?adminmsg("�޸�ʧ�ܣ�"):adminmsg("�޸ĳɹ���",2);
	}
	//���
	else
	{
		//LOGO
		if (empty($_FILES['img']['name']))
		{
			adminmsg('���ϴ�ͼƬ��',1);
		}
		else
		{
			$eval_updir="../data/eval/img/".date("Y/m/d/");
			make_dir($eval_updir);
			$setarr['img']=_asUpFiles($eval_updir,"img",1000,'gif/jpg/bmp/png',true);
			if (empty($setarr['img']))
			{
				adminmsg('�ϴ��ļ�ʧ�ܣ�',1);
			}
			$setarr['img']=date("Y/m/d/").$setarr['img'];
		}
		$link[0]['text'] = "�����б�";
		$link[0]['href'] = '?act=list';
		write_log("��̨����Ծ���Ϣ", $_SESSION['admin_name'],3);
		//�����Ծ���  1=>��    2=>��
		set_paper_num('1',$setarr["type_id"],1);
		!$db->inserttable(table("evaluation_paper"),$setarr)?adminmsg("���ʧ�ܣ�"):adminmsg("��ӳɹ���",2,$link);	
	}
}
// ɾ���Ծ�
elseif($act=="paper_del")
{
	$id =!empty($_REQUEST['id'])?$_REQUEST['id']:adminmsg("��û��ѡ���Ծ�",1);
	$num=del_paper($id);
	if ($num>0)
	{
	write_log("��̨ɾ���Ծ�,��ɾ��".$return."��", $_SESSION['admin_name'],3);
	adminmsg("ɾ���ɹ�����ɾ��".$num."��",2);
	}
	else
	{
	adminmsg("ɾ��ʧ�ܣ�",0);
	}
}
// �Ծ������б�
elseif($act == 'question_list')
{
	get_token();
	// �Ծ�id 
	$paper_id=intval($_GET['id']);
	if($paper_id <= 0)
	{
		adminmsg("�Ծ��Ŷ�ʧ��",0);
	}
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	$wheresql=" WHERE paper_id=".$paper_id;
	$oederbysql=" order BY id  DESC";
	$key=isset($_GET['key'])?trim($_GET['key']):"";
	$key_type=isset($_GET['key_type'])?intval($_GET['key_type']):"";
	if ($key && $key_type>0)
	{
		if($key_type===1)$wheresql.=" AND title like '%{$key}%'";
	}
	$total_sql="SELECT COUNT(*) AS num FROM ".table('evaluation_question').$wheresql;
	$total=$db->get_total($total_sql);
	$page = new page(array('total'=>$total, 'perpage'=>$perpage));
	$currenpage=$page->nowindex;
	$offset=($currenpage-1)*$perpage;
	$list = get_paper_question($offset, $perpage,$wheresql.$oederbysql);
	$smarty->assign('list',$list);
	$paper_info = get_evaluation_one($paper_id);
	$smarty->assign('paper_info',$paper_info);
	$smarty->assign('total',$total);
	$smarty->assign('page',$page->show(3));
	$smarty->assign('pageheader',"�˲Ų���");
	$smarty->display('evaluation/admin_question_list.htm');
}
// ��������
elseif($act == 'question_add')
{
	get_token();
	// �Ծ�id 
	$paper_id=intval($_GET['paper_id']);
	$smarty->assign('paper_id',$paper_id);
	$smarty->assign('pageheader',"�˲Ų���");
	$smarty->display('evaluation/admin_question_add.htm');
}
// ���� ��������
elseif($act == 'add_question_save')
{
	check_token();
	$paper_id=intval($_POST['paper_id']);
	if(empty($paper_id))
	{
		adminmsg("�Ծ���Ϣ��ʧ��");
	}
	//���������
	if (is_array($_POST['title']) && count($_POST['title'])>0)
	{
		for ($i =0; $i <count($_POST['title']);$i++){
			if (!empty($_POST['title'][$i]))
			{	
				$setsqlarr['title']=trim($_POST['title'][$i]);
				$setsqlarr['paper_id']=$paper_id;
				!$db->inserttable(table('evaluation_question'),$setsqlarr)?adminmsg("����ʧ�ܣ�",0):"";
				$num=$num+$db->affected_rows();
			}
		}
	}
	$link[0]['text'] = "�����Ծ������б�";
	$link[0]['href'] = '?act=question_list&id='.$paper_id;
	//�����Ծ���Ŀ��  1=>��    2=>��
	set_paper_question_num('1',$paper_id,$num);
	//��д����Ա��־
	write_log("��̨�ɹ�����Ծ���⣡���������".$num."������", $_SESSION['admin_name'],3);
	adminmsg("��ӳɹ������������".$num."������",2,$link);	
}
// �޸Ĳ���
elseif($act == 'question_edit')
{
	get_token();
	$smarty->assign('pageheader',"�˲Ų���");
	$smarty->assign('question',get_question_one($_GET['id']));
	$smarty->display('evaluation/admin_question_edit.htm');
}
// �޸ı������
elseif($act == 'edit_question_save')
{
	check_token();
	$setsqlarr['title']=!empty($_POST['title']) ?trim($_POST['title']) : adminmsg("����д��Ŀ",1);
	!$db->updatetable(table('evaluation_question'),$setsqlarr," id=".intval($_POST['id']))?adminmsg("�޸�ʧ�ܣ�",0):"";
	$link[0]['text'] = "�����б�";
	$link[0]['href'] = '?act=question_list&id='.$_POST['paper_id'];
	//��д����Ա��־
	write_log("��̨�ɹ��޸��Ծ���⣡", $_SESSION['admin_name'],3);
	adminmsg("����ɹ���",2,$link);
}
// ɾ������
elseif($act=="question_del")
{
	$id =!empty($_REQUEST['id'])?$_REQUEST['id']:adminmsg("��û��ѡ����⣡",1);
	$paper_id = $_REQUEST['paper_id'];
	$num=del_question($id);
	if ($num>0)
	{
		//�����Ծ���Ŀ��  1=>��    2=>��
		set_paper_question_num('2',$paper_id,$num);
		write_log("��̨ɾ������,��ɾ��".$return."��", $_SESSION['admin_name'],3);
		adminmsg("ɾ���ɹ�����ɾ��".$num."��",2);
	}
	else
	{
		adminmsg("ɾ��ʧ�ܣ�",0);
	}
}
// ����ѡ���б�
elseif($act == 'option_list')
{
	get_token();
	// ����id 
	$question_id=intval($_GET['id']);
	if($question_id <= 0)
	{
		adminmsg("�����Ŷ�ʧ��",0);
	}
	$question_info = get_question_one($question_id);
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	$wheresql=" WHERE question_id=".$question_id." AND paper_id=".$question_info['paper_id'];
	$oederbysql=" order BY id  DESC";
	$total_sql="SELECT COUNT(*) AS num FROM ".table('evaluation_option').$wheresql;
	$total=$db->get_total($total_sql);
	$page = new page(array('total'=>$total, 'perpage'=>$perpage));
	$currenpage=$page->nowindex;
	$offset=($currenpage-1)*$perpage;
	$list = get_question_option($offset, $perpage,$wheresql.$oederbysql);
	$smarty->assign('list',$list);
	$question_info = get_question_one($question_id);
	$smarty->assign('question_info',$question_info);
	$smarty->assign('total',$total);
	$smarty->assign('page',$page->show(3));
	$smarty->assign('pageheader',"�˲Ų���");
	$smarty->display('evaluation/admin_option_list.htm');
}
// ����ѡ��
elseif($act == 'option_add')
{
	get_token();
	// ����id 
	$question_id=intval($_GET['question_id']);
	$smarty->assign('question_id',$question_id);
	$smarty->assign('pageheader',"�˲Ų���");
	$smarty->display('evaluation/admin_option_add.htm');
}
// ����ѡ��
elseif($act == 'add_option_save')
{
	check_token();
	$question_id=intval($_POST['question_id']);
	if(empty($question_id))
	{
		adminmsg("������Ϣ��ʧ��");
	}
	$question_info = get_question_one($question_id);
	if (is_array($_POST['name']) && count($_POST['name'])>0)
	{
		for ($i =0; $i <count($_POST['name']);$i++){
			if (!empty($_POST['name'][$i]))
			{	
				$setsqlarr['question_id']=$question_id;
				$setsqlarr['name']=trim($_POST['name'][$i]);
				$setsqlarr['score']=($_POST['score'][$i]);
				$setsqlarr['paper_id']=$question_info['paper_id'];
				!$db->inserttable(table('evaluation_option'),$setsqlarr)?adminmsg("����ʧ�ܣ�",0):"";
				$num=$num+$db->affected_rows();
			}
		}
	}
	$link[0]['text'] = "����ѡ���б�";
	$link[0]['href'] = '?act=option_list&id='.$question_id;
	//��д����Ա��־
	write_log("��̨�ɹ���Ӳ���ѡ����������".$num."��ѡ��", $_SESSION['admin_name'],3);
	adminmsg("��ӳɹ������������".$num."��ѡ��",2,$link);	
}
// �޸�ѡ��
elseif($act == 'option_edit')
{
	get_token();
	$smarty->assign('pageheader',"�˲Ų���");
	$smarty->assign('option',get_option_one($_GET['id']));
	$smarty->display('evaluation/admin_option_edit.htm');
}
// �޸ı���ѡ��
elseif($act == 'edit_option_save')
{
	check_token();
	$setsqlarr['name']=!empty($_POST['name']) ?trim($_POST['name']) : adminmsg("����дѡ������",1);
	$setsqlarr['score']=!empty($_POST['score']) ?trim($_POST['score']) : adminmsg("����дѡ���ֵ",1);
	!$db->updatetable(table('evaluation_option'),$setsqlarr," id=".intval($_POST['id']))?adminmsg("�޸�ʧ�ܣ�",0):"";
	$link[0]['text'] = "�����б�";
	$link[0]['href'] = '?act=option_list&id='.$_POST['question_id'];
	//��д����Ա��־
	write_log("��̨�ɹ��޸Ĳ���ѡ�", $_SESSION['admin_name'],3);
	adminmsg("����ɹ���",2,$link);
}
// ɾ��ѡ��
elseif($act=="option_del")
{
	$id =!empty($_REQUEST['id'])?$_REQUEST['id']:adminmsg("��û��ѡ��ѡ�",1);
	$question_id = $_REQUEST['question_id'];
	$num=del_option($id);
	if ($num>0)
	{
		write_log("��̨ɾ������ѡ��,��ɾ��".$return."��", $_SESSION['admin_name'],3);
		adminmsg("ɾ���ɹ�����ɾ��".$num."��",2);
	}
	else
	{
		adminmsg("ɾ��ʧ�ܣ�",0);
	}
}
// ������¼
elseif($act=="record_list")
{
	//��������
	$evaluat_type = get_evaluation_category();
	$smarty->assign('evaluat_type',$evaluat_type);
	
	$typeid=intval($_GET['typeid']) ? intval($_GET['typeid']) : 0;
	if($typeid > 0)
	{
		$recordsql .= " AND type_id=".$typeid;
		$wheresql = " where type_id=".$typeid;
	}
	$papertype = $db->getall("select id,title from ".table('evaluation_paper').$wheresql);
	$smarty->assign('papertype',$papertype);
	// ɸѡ����
	$paperid=intval($_GET['paperid']) ? intval($_GET['paperid']) : 0;
	if($paperid > 0)
	{
		$recordsql .= " AND paper_id=".$paperid;
	}
	//����ʱ��
	$addsettr=intval($_GET['addsettr']) ? intval($_GET['addsettr']) : 0;
	if($addsettr > 0)
	{
		$settr=strtotime("-".intval($_GET['addsettr'])." day");
		$recordsql .= " AND addtime>".$settr;
	}
	if (!empty($recordsql))
	{
		$recordsql=" WHERE ".ltrim(ltrim($recordsql),'AND');
	}
	$key=isset($_GET['key'])?trim($_GET['key']):"";
	$key_type=isset($_GET['key_type'])?intval($_GET['key_type']):"";
	if (!empty($key) && $key_type>0)
	{
		if     ($key_type===1)$recordsql=" WHERE paper_title like '%{$key}%'";
	}
	$oederbysql=" order BY addtime  DESC";
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	$total_sql="SELECT COUNT(*) AS num FROM ".table('evaluation_record').$recordsql;
	$total=$db->get_total($total_sql);
	$page = new page(array('total'=>$total, 'perpage'=>$perpage));
	$currenpage=$page->nowindex;
	$offset=($currenpage-1)*$perpage;
	$record_sql = "select r.*,m.username as username,t.name as type_name from ".table('evaluation_record')." as r  LEFT JOIN  ".table('members')." AS m  ON r.uid=m.uid  LEFT JOIN  ".table('evaluation_type')." AS t  ON r.type_id=t.id  ".$recordsql.$oederbysql;
	$record = $db->getall($record_sql);
	$smarty->assign('record',$record);
	$smarty->assign('total',intval($total));
	$smarty->assign('page',$page->show(3));
	$smarty->assign('pageheader',"�˲Ų���");
	$smarty->display('evaluation/admin_record_list.htm');
}
// ɾ��������¼
elseif($act=="record_del")
{
	$id =!empty($_REQUEST['id'])?$_REQUEST['id']:adminmsg("��û��ѡ��ѡ�",1);
	$num=del_record($id);
	if ($num>0)
	{
		write_log("��̨ɾ��������¼,��ɾ��".$return."��", $_SESSION['admin_name'],3);
		adminmsg("ɾ���ɹ�����ɾ��".$num."��",2);
	}
	else
	{
		adminmsg("ɾ��ʧ�ܣ�",0);
	}
}
// �Ծ�������
elseif($act == 'result_list')
{
	get_token();
	// �Ծ�id
	$paper_id=intval($_GET['id']);
	if($paper_id <= 0)
	{
		adminmsg("�Ծ��Ŷ�ʧ��",0);
	}
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	$wheresql=" WHERE paper_id=".$paper_id;
	$oederbysql=" order BY id  DESC";

	$total_sql="SELECT COUNT(*) AS num FROM ".table('evaluation_result').$wheresql;
	$total=$db->get_total($total_sql);
	$perpage=50;
	$page = new page(array('total'=>$total, 'perpage'=>$perpage));
	$currenpage=$page->nowindex;
	$offset=($currenpage-1)*$perpage;
	$list = get_paper_result($offset, $perpage,$wheresql.$oederbysql);
	$smarty->assign('list',$list);

	$paper_info = get_evaluation_one($paper_id);
	$smarty->assign('paper_info',$paper_info);
	$smarty->assign('total',$total);
	$smarty->assign('page',$page->show(3));
	$smarty->assign('pageheader',"�˲Ų���");
	$smarty->display('evaluation/admin_result_list.htm');
}
// ������

elseif($act == 'result_add')
{
	get_token();
	// �Ծ�id
	$paper_id=intval($_GET['paper_id']);
	$paper=get_evaluation_one($paper_id);
	$smarty->assign('paper_id',$paper_id);
	$smarty->assign('paper',$paper);
	$smarty->assign('pageheader',"�˲Ų���");
	$smarty->display('evaluation/admin_result_add.htm');
}
// ���� ���
elseif($act == 'add_result_save')
{
	check_token();
	$paper_id=intval($_POST['paper_id']);
	if(empty($paper_id))
	{
		adminmsg("�Ծ���Ϣ��ʧ��");
	}

	$setarr["result_type"]=$_POST["result_type"]?intval($_POST["result_type"]):adminmsg("�����Ͳ���Ϊ��");
	$setarr["paper_id"]=$paper_id;
	if($setarr["result_type"]==1){
		$setarr["result_options"]=$_POST["result_options"]?($_POST["result_options"]):adminmsg("ѡ���Ϊ��");
		$setarr["result_num"]=$_POST["result_num"]?intval($_POST["result_num"]):adminmsg("��Ŀ����Ϊ��");

	}elseif($setarr["result_type"]==2){
		$setarr["result_score"]=$_POST["result_score"]?intval($_POST["result_score"]):adminmsg("�÷ֲ���Ϊ��");

	}
	$setarr["result_description"]=$_POST["result_description"]?($_POST["result_description"]):adminmsg("�������Ϊ��");


	$link[0]['text'] = "�����Ծ���б�";
	$link[0]['href'] = '?act=result_list&id='.$paper_id;
	!$db->inserttable(table("evaluation_result"),$setarr)?adminmsg("���ʧ�ܣ�"):adminmsg("��ӳɹ���",2,$link);



	//��д����Ա��־
	write_log("��̨�ɹ�����Ծ�𰸣�", $_SESSION['admin_name'],3);
 }
// �޸Ĳ���
elseif($act == 'result_edit')
{
	get_token();
	$result=get_result_one($_GET['id']);
	$paper=get_evaluation_one($result["paper_id"]);

	$smarty->assign('paper',$paper);
	$smarty->assign('pageheader',"�˲Ų���");
	$smarty->assign('result',$result);
	$smarty->display('evaluation/admin_result_edit.htm');
}
// �޸ı������
elseif($act == 'edit_result_save')
{
	check_token();



	$rid=intval($_POST['rid']);
	$paper_id=intval($_POST['paper_id']);
	if(empty($paper_id))
	{
		adminmsg("�Ծ���Ϣ��ʧ��");
	}

	$setarr["result_type"]=$_POST["result_type"]?intval($_POST["result_type"]):adminmsg("�����Ͳ���Ϊ��");
	$setarr["paper_id"]=$paper_id;
	if($setarr["result_type"]==1){
		$setarr["result_options"]=$_POST["result_options"]?($_POST["result_options"]):adminmsg("ѡ���Ϊ��");
		$setarr["result_num"]=$_POST["result_num"]?intval($_POST["result_num"]):adminmsg("��Ŀ����Ϊ��");

	}elseif($setarr["result_type"]==2){
		$setarr["result_score"]=$_POST["result_score"]?intval($_POST["result_score"]):adminmsg("�÷ֲ���Ϊ��");

	}
	$setarr["result_description"]=$_POST["result_description"]?($_POST["result_description"]):adminmsg("�������Ϊ��");


	$link[0]['text'] = "�����Ծ���б�";
	$link[0]['href'] = '?act=result_list&id='.$paper_id;
	!$db->updatetable(table("evaluation_result"),$setarr," id=".$rid)?adminmsg("���ʧ�ܣ�"):adminmsg("���³ɹ���",2,$link);



	//��д����Ա��־
	write_log("��̨�ɹ��޸��Ծ�𰸣�", $_SESSION['admin_name'],3);

	adminmsg("����ɹ���",2,$link);


}
// ɾ������
elseif($act=="result_del")
{
	$id =!empty($_REQUEST['id'])?$_REQUEST['id']:adminmsg("��û��ѡ����⣡",1);
	$paper_id = $_REQUEST['paper_id'];
	$num=del_result($id);
	if ($num>0)
	{

		write_log("��̨ɾ������,��ɾ��".$return."��", $_SESSION['admin_name'],3);
		adminmsg("ɾ���ɹ�����ɾ��".$num."��",2);
	}
	else
	{
		adminmsg("ɾ��ʧ�ܣ�",0);
	}
}
?>