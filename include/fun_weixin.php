<?php
 /*
 * 74cms ΢�����ѷ���
*/
 if(!defined('IN_QISHI'))
 {
 	die('Access Denied!');
 }
  /*
 * ΢�����ѣ�����ְλ 
 * $uid    					��ҵ��Աuid 
 * $resumeid 				����id
 * $jobs_name 				ְλ����
 * $personal_fullname 		��������
 * $resume_experience_cn 	������������
 * $notes 					����˵��
*/
function set_applyjobs($uid,$resumeid,$jobs_name,$personal_fullname,$resume_experience_cn,$notes)
{
	global $db,$_CFG;
	$weixinconfig=get_cache('weixin_config');
	if(intval($_CFG['weixin_apiopen'])==1 && $weixinconfig['set_applyjobs']=='1')
	{
		$user=$db->getone("select weixin_openid from ".table('members')." where uid = {$uid} limit 1");
		if($user['weixin_openid']!="")
		{
			$resume_url=$_CFG['wap_domain']."/resume-show.php?id=".$resumeid;
			$template = array(
				'touser' => $user['weixin_openid'],
				'template_id' => "u_yoFifHb-ryYXMtNSlATj_Wfm1CWTKEjf8EkiM6dvY",
				'url' => $resume_url,
				'topcolor' => "#7B68EE",
				'data' => array(
					'first' => array('value' => urlencode(gbk_to_utf8("���յ���һ���¼������뼰ʱ��¼".$_CFG['site_name']."����")),
									'color' => "#743A3A",
						),
					'job' => array('value' => urlencode(gbk_to_utf8($jobs_name)),
									'color' => "#743A3A",
						),
					'resuname' => array('value' => urlencode(gbk_to_utf8("--")),
									'color' => "#743A3A",
						),
					'realname' => array('value' => urlencode(gbk_to_utf8($personal_fullname)),
									'color' => "#743A3A",
						),
					'exp' => array('value' => urlencode(gbk_to_utf8($resume_experience_cn)),
									'color' => "#743A3A",
						),
					'lastjob' => array('value' => urlencode(gbk_to_utf8("--")),
									'color' => "#743A3A",
						),
					'remark' => array('value' => urlencode("\\n".$notes),
									'color' => "#743A3A",
						)
					)
				);
			send_template_message(urldecode(json_encode($template)));
		}
	}
}
  /*
 * ΢�����ѣ���������
 * $uid    					���˻�Աuid 
 * $jobsid 					ְλid
 * $companyname 			��˾����
 * $jobs_name 				ְλ����
 * $jobs_address 			ְλ��ַ
 * $jobs_contact 			ְλ��ϵ��
 * $jobs_telephone 			ְλ��ϵ�绰
 * $notes 					����˵��
*/
function set_invite($uid,$jobsid,$companyname,$jobs_name,$jobs_address,$jobs_contact,$jobs_telephone,$notes)
{
	global $db,$_CFG;
	$weixinconfig=get_cache('weixin_config');
	if(intval($_CFG['weixin_apiopen'])==1 && $weixinconfig['set_invite']=='1')
	{
		$user=$db->getone("select weixin_openid from ".table('members')." where uid ={$uid} limit 1");
		if($user['weixin_openid']!="")
		{
			$jobs_url=$_CFG['wap_domain']."/jobs-show.php?id=".$jobsid;
			$template = array(
				'touser' => $user['weixin_openid'],
				'template_id' => "sdjPV1l3vyv_9mclCe6_Fm8UzyAadMI_w5iIC1DPFPE",
				'url' => $jobs_url,
				'topcolor' => "#7B68EE",
				'data' => array(
					'first' => array('value' => urlencode(gbk_to_utf8($companyname."�������μӹ�˾����")),
									'color' => "#743A3A",
						),
					'job' => array('value' => urlencode(gbk_to_utf8($jobs_name)),
									'color' => "#743A3A",
						),
					'company' => array('value' => urlencode(gbk_to_utf8($companyname)),
									'color' => "#743A3A",
						),
					'time' => array('value' => urlencode(gbk_to_utf8("�����鿴")),
									'color' => "#743A3A",
						),
					'address' => array('value' => urlencode(gbk_to_utf8($jobs_address)),
									'color' => "#743A3A",
						),
					'contact' => array('value' => urlencode(gbk_to_utf8($jobs_contact)),
									'color' => "#743A3A",
						),
					'tel' => array('value' => urlencode($jobs_telephone),
									'color' => "#743A3A",
						),
					'remark' => array('value' => urlencode("\\n".$notes),
									'color' => "#743A3A",
						)
					)
				);
			send_template_message(urldecode(json_encode($template)));
		}
	}
}
 /*
 * ΢�����ѣ���ͨ���ӵ�ͼ
 * $uid    					��ҵ��Աuid 
 * $address 					��ַ
*/
function set_addmap($uid,$address)
{
	global $db,$_CFG;
	$weixinconfig=get_cache('weixin_config');
	if(intval($_CFG['weixin_apiopen'])==1 && $weixinconfig['set_addmap']=='1')
	{
		$user=$db->getone("select weixin_openid from ".table('members')." where uid ={$uid} limit 1");
		if($user['weixin_openid']!="")
		{
			$template = array(
				'touser' => $user['weixin_openid'],
				'template_id' => "RMJ-itxpYQHpZp6Zlr7c9qCpg8LUFU3svJoyq4pDiXk",
				'url' => '',
				'topcolor' => "#7B68EE",
				'data' => array(
					'first' => array('value' => urlencode(gbk_to_utf8("�û���ͨ���ӵ�ͼ����")),
									'color' => "#743A3A",
						),
					'keyword1' => array('value' => urlencode(gbk_to_utf8("�𾴵��û������ڡ�".date('Y-m-d',time())."�����뿪ͨ���ӵ�ͼ��")),
									'color' => "#743A3A",
						),
					'keyword2' => array('value' => urlencode(gbk_to_utf8("�����ӵ�ͼ��ע�ľ���λ��Ϊ : ".$address)),
									'color' => "#743A3A",
						),
					'remark' => array('value' => urlencode("\\n��л����ʹ��"),
									'color' => "#743A3A",
						)
					)
				);
			send_template_message(urldecode(json_encode($template)));
		}
	}
}
/*
 * ΢�����ѣ�ְλ���֪ͨ
 * $uid    					��Աuid 
 * $weixinconfig 			���� : ����ְλ���
 * $first 					���ѱ���
 * $keyword1 				ְλ����
 * $keyword2 				��˽��
 * $keyword3 				ԭ��
*/
function set_jobsallow($uid,$weixinconfig,$first,$keyword1,$keyword2,$keyword3)
{
	global $db,$_CFG;
	//����
	$weixinconfig = trim($weixinconfig);
	if(intval($_CFG['weixin_apiopen'])==1 && $weixinconfig=='1')
	{
		$user=$db->getone("select weixin_openid from ".table('members')." where uid ={$uid} limit 1");
		if($user['weixin_openid']!="")
		{
			$template = array(
				'touser' => $user['weixin_openid'],
				'template_id' => "y-YiAT3ugGu0DC9KTbiugJmFGrdsdOirAFFfBYMmIjw",
				'url' => '',
				'topcolor' => "#7B68EE",
				'data' => array(
					'first' => array('value' => urlencode(gbk_to_utf8("�𾴵��û������ã�����������ְλ��Ϣ".$first)),
									'color' => "#743A3A",
						),
					'keyword1' => array('value' => urlencode(gbk_to_utf8($keyword1)),
									'color' => "#743A3A",
						),
					'keyword2' => array('value' => urlencode(gbk_to_utf8($keyword2)),
									'color' => "#743A3A",
						),
					'keyword3' => array('value' => urlencode(gbk_to_utf8($keyword3)),
									'color' => "#743A3A",
						),
					'remark' => array('value' => urlencode("\\n�����޸������Ϣ�������ύ��лл��"),
									'color' => "#743A3A",
						)
					)
				);
			send_template_message(urldecode(json_encode($template)));
		}
	}
}
/*
 * ΢�����ѣ��������֪ͨ
 * $uid    					��Աuid 
 * $weixinconfig 			���� : ����������
 * $keyword1 				��������
 * $keyword3 				��˽��
 * $keyword4 				ԭ��
*/
function set_resumeallow($uid,$weixinconfig,$keyword1,$keyword2,$keyword3)
{
	global $db,$_CFG;
	//����
	$weixinconfig = trim($weixinconfig);
	if(intval($_CFG['weixin_apiopen'])==1 && $weixinconfig=='1')
	{
		$user=$db->getone("select weixin_openid from ".table('members')." where uid ={$uid} limit 1");
		if($user['weixin_openid']!="")
		{
			$template = array(
				'touser' => $user['weixin_openid'],
				'template_id' => "PL9gZ6pm6qORiDq15yUAySA0n1lcrcoVncGdwENAmHU",
				'url' => '',
				'topcolor' => "#7B68EE",
				'data' => array(
					'first' => array('value' => urlencode(gbk_to_utf8("��ã���ļ�����˽�����£��������⣡")),
									'color' => "#743A3A",
						),
					'keyword1' => array('value' => urlencode(gbk_to_utf8($keyword1)),
									'color' => "#743A3A",
						),
					'keyword2' => array('value' => urlencode(gbk_to_utf8(date('Y-m-d',time()))),
									'color' => "#743A3A",
						),
					'keyword3' => array('value' => urlencode(gbk_to_utf8($keyword3)),
									'color' => "#743A3A",
						),
					'keyword4' => array('value' => urlencode(gbk_to_utf8($keyword4)),
									'color' => "#743A3A",
						),
					'remark' => array('value' => urlencode("\\nһ����������ʵ�ļ�����������ְҵδ��������ש��"),
									'color' => "#743A3A",
						)
					)
				);
			send_template_message(urldecode(json_encode($template)));
		}
	}
}
 /*
 * ΢�����ѣ����֪ͨ
 * $uid    					��Աuid 
 * $weixinconfig 			���� : Ӫҵִ����˲�ͨ��
 * $first 					���ѱ���
 * $keyword1 				��˽��
 * $keyword2 				ԭ��
*/
function set_licenseallow($uid,$weixinconfig,$first,$keyword1,$keyword2)
{
	global $db,$_CFG;
	//����
	$weixinconfig = trim($weixinconfig);
	if(intval($_CFG['weixin_apiopen'])==1 && $weixinconfig=='1')
	{
		$user=$db->getone("select weixin_openid from ".table('members')." where uid ={$uid} limit 1");
		if($user['weixin_openid']!="")
		{
			$template = array(
				'touser' => $user['weixin_openid'],
				'template_id' => "zXQIq9OAEOLMa7HUM6q39wmti95tjhbmlWq_Vb9PiRA",
				'url' => '',
				'topcolor' => "#7B68EE",
				'data' => array(
					'first' => array('value' => urlencode(gbk_to_utf8($first)),
									'color' => "#743A3A",
						),
					'keyword1' => array('value' => urlencode(gbk_to_utf8($keyword1)),
									'color' => "#743A3A",
						),
					'keyword2' => array('value' => urlencode(gbk_to_utf8($keyword2)),
									'color' => "#743A3A",
						),
					'remark' => array('value' => urlencode("\\n��л����".$_CFG['site_name']."��֧�֣�"),
									'color' => "#743A3A",
						)
					)
				);
			send_template_message(urldecode(json_encode($template)));
		}
	}
}
/*
 * ΢�����ѣ��޸�����
 * $uid    					��Աuid 
 * $keyword1 				��¼�˺�
 * $keyword2 				����
*/
function set_editpwd($uid,$keyword1,$keyword2)
{
	global $db,$_CFG;
	$weixinconfig=get_cache('weixin_config');
	if(intval($_CFG['weixin_apiopen'])==1 && $weixinconfig['set_editpwd']=='1')
	{
		$user=$db->getone("select weixin_openid from ".table('members')." where uid ={$uid} limit 1");
		if($user['weixin_openid']!="")
		{
			$template = array(
				'touser' => $user['weixin_openid'],
				'template_id' => "Q6LCTh109qdER7HR-kAu6egWbGiwuuyIw4xBX8Rwtq4",
				'url' => '',
				'topcolor' => "#7B68EE",
				'data' => array(
					'first' => array('value' => urlencode(gbk_to_utf8("����".$_CFG['site_name']."�ĵ�¼��Ϣ���£�")),
									'color' => "#743A3A",
						),
					'keyword1' => array('value' => urlencode(gbk_to_utf8($keyword1)),
									'color' => "#743A3A",
						),
					'keyword2' => array('value' => urlencode(gbk_to_utf8($keyword2)),
									'color' => "#743A3A",
						),
					'remark' => array('value' => urlencode("\\n�����Ʊ��ܡ�"),
									'color' => "#743A3A",
						)
					)
				);
			send_template_message(urldecode(json_encode($template)));
		}
	}
}
/*
 * ΢�����ѣ���ֵ�ɹ�
 * $uid    					��Աuid 
 * $keyword1 				��Ʒ����
 * $keyword2 				���
 * $keyword3 				���
*/
function set_payment($uid,$keyword1,$keyword2,$keyword3)
{
	global $db,$_CFG;
	$weixinconfig=get_cache('weixin_config');
	if(intval($_CFG['weixin_apiopen'])==1 && $weixinconfig['set_payment']=='1')
	{
		$user=$db->getone("select weixin_openid from ".table('members')." where uid ={$uid} limit 1");
		if($user['weixin_openid']!="")
		{
			$template = array(
				'touser' => $user['weixin_openid'],
				'template_id' => "ibrGQdiK_o4r8sNSQT7Fx1f5UZwiE6Zshqff5VZ8ayY",
				'url' => '',
				'topcolor' => "#7B68EE",
				'data' => array(
					'first' => array('value' => urlencode(gbk_to_utf8("�𾴵Ŀͻ������ѳ�ֵ�ɹ�")),
									'color' => "#743A3A",
						),
					'keyword1' => array('value' => urlencode(gbk_to_utf8($keyword1)),
									'color' => "#743A3A",
						),
					'keyword2' => array('value' => urlencode(gbk_to_utf8($keyword2)),
									'color' => "#743A3A",
						),
					'keyword3' => array('value' => urlencode(gbk_to_utf8($keyword3)),
									'color' => "#743A3A",
						),
					'keyword4' => array('value' => urlencode(gbk_to_utf8(date('Y-m-d',time()))),
									'color' => "#743A3A",
						),
					'remark' => array('value' => urlencode("\\n��л���Ĺ��١�"),
									'color' => "#743A3A",
						)
					)
				);
			send_template_message(urldecode(json_encode($template)));
		}
	}
}
/*
 * ΢�����ѣ���������
 * $uid    					��Աuid 
 * $keyword1 				�������
 * $keyword2 				��������
 * $keyword3 				���
*/
function set_order_msg($uid,$keyword1,$keyword2,$keyword3)
{
	global $db,$_CFG;
	$weixinconfig=get_cache('weixin_config');
	if(intval($_CFG['weixin_apiopen'])==1 && $weixinconfig['set_payment']=='1')
	{
		$user=$db->getone("select weixin_openid from ".table('members')." where uid ={$uid} limit 1");
		if($user['weixin_openid']!="")
		{
			$template = array(
				'touser' => $user['weixin_openid'],
				'template_id' => "UxurRCK19QNDb3_g2lO5GSQ4lHO48-d1idxBYvutbBc",
				'url' => '',
				'topcolor' => "#7B68EE",
				'data' => array(
					'first' => array('value' => urlencode(gbk_to_utf8("�𾴵��û����������¶�����Ҫ���֧����")),
									'color' => "#743A3A",
						),
					'keyword1' => array('value' => urlencode(gbk_to_utf8($keyword1)),
									'color' => "#743A3A",
						),
					'keyword2' => array('value' => urlencode(gbk_to_utf8($keyword2)),
									'color' => "#743A3A",
						),
					'keyword3' => array('value' => urlencode(gbk_to_utf8($keyword3)),
									'color' => "#743A3A",
						),
					'keyword4' => array('value' => urlencode(gbk_to_utf8(date('Y-m-d',time()))),
									'color' => "#743A3A",
						),
					'remark' => array('value' => urlencode("\\n�����Բ鿴�ö�����֧����"),
									'color' => "#743A3A",
						)
					)
				);
			send_template_message(urldecode(json_encode($template)));
		}
	}
}


?>