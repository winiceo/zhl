<?php
 /*
 * ajax ��ϵ��ʽ
*/
define('IN_QISHI', true);
require_once(dirname(dirname(__FILE__)).'/include/plus.common.inc.php');
$act = !empty($_GET['act']) ? trim($_GET['act']) : ''; 

if($act == 'jobs_contact')
{
	$id=intval($_GET['id']);
	if ($id>0)
	{
		$jobs_one=$db->getone("select * from ".table("jobs")." where id=$id ");
		$jobs_tmp=$db->getone("select * from ".table("jobs_tmp")." where id=$id ");
		$jobs = empty($jobs_one)?$jobs_tmp:$jobs_one;
		$show=false;
		if($_CFG['showjobcontact']=='0')
		{
		$show=true;
		}
		elseif($_CFG['showjobcontact']=='1')
		{
			if ($_SESSION['uid'] && $_SESSION['username'] && $_SESSION['utype']=='2')
			{
			$show=true;
			}
			else
			{
			$show=false;
			$html='<div class="no-login">
									<span class="border-item topleft"></span>
									<span class="border-item topright"></span>
									<span class="border-item bottomleft"></span>
									<span class="border-item bottomright"></span>
									<p>������ע�Ტ��¼����ܲ鿴��ҵ����ϵ��ʽ������<a href="javascript:void(0);" class="ajax_user_login" style="color:#ff7400">[������¼]</a>����<a href="'.$_CFG['site_dir'].'user/user_reg.php" style="color:#ff7400">[���ע��]</a></p>
								</div>';
			}
		}
		elseif($_CFG['showjobcontact']=='2')
		{
			if ($_SESSION['uid'] && $_SESSION['username'] && $_SESSION['utype']=='2')
			{
				$val=$db->getone("select uid from ".table('resume')." where uid='{$_SESSION['uid']}' LIMIT 1");
			 	if (!empty($val))
				{
				$show=true;
				}
				else
				{
				$show=false;
				$html='<div class="no-login">
									<span class="border-item topleft"></span>
									<span class="border-item topright"></span>
									<span class="border-item bottomleft"></span>
									<span class="border-item bottomright"></span>
									<p>��û�з����������߼�����Ч������������ſ��Բ鿴��ϵ��ʽ��<a href="'.get_member_url($_SESSION['utype'],true).'personal_resume.php?act=resume_list">[�鿴�ҵļ���]</a></p>
								</div>';
				}
			}
			else
			{
			$show=false;
			$html='<div class="no-login">
									<span class="border-item topleft"></span>
									<span class="border-item topright"></span>
									<span class="border-item bottomleft"></span>
									<span class="border-item bottomright"></span>
									<p>������ע�Ტ��¼����ܲ鿴��ҵ����ϵ��ʽ������<a href="javascript:void(0);" class="ajax_user_login" style="color:#ff7400">[������¼]</a>����<a href="'.$_CFG['site_dir'].'user/user_reg.php" style="color:#ff7400">[���ע��]</a></p>
								</div>';
			}
		}
		if($_SESSION['uid'] && $_SESSION['username'] && $_SESSION['utype']=='1' && $show==false)
		{
			if($jobs['uid']==$_SESSION['uid'])
			{
				$show=true;
			}
			else
			{
				$show=false;
			}
		}
		if ($show)
		{
		$sql = "select * from ".table('jobs_contact')." where pid='{$id}' LIMIT 1";
		$val=$db->getone($sql);
			if ($_CFG['contact_img_job']=='2')
			{
			$hashstr=$_GET['hashstr'];
			$token=md5($val['contact'].$id.$val['telephone']);
			$contact=$val['contact_show']=='1'?"<img src=\"{$_CFG['site_dir']}plus/contact_img.php?act=jobs_contact&type=1&id={$id}&token={$token}\"  border=\"0\" align=\"absmiddle\"/></li>":"��ҵ���ò����⹫��";
			if($val['telephone_show']=='1')
			{
				$telephone=empty($val['telephone'])?'':"<img src=\"{$_CFG['site_dir']}plus/contact_img.php?act=jobs_contact&type=2&id={$id}&token={$token}&hashstr={$hashstr}\"  border=\"0\" align=\"absmiddle\"/><a style=\"color:#017fcf\" id=\"tel_show_pic\" href=\"javascript:;\" >[�鿴]</a> <span  id=\"show_detail\" style='color:#666;display:none'>[��ϵ��ʱ��˵������&nbsp;".$_CFG['site_name']."&nbsp;�Ͽ�����]</span>";
				$landline_tel=$val['landline_tel'] == '0-0-0'?'':"<img src=\"{$_CFG['site_dir']}plus/contact_img.php?act=jobs_contact&type=6&id={$id}&token={$token}&hashstr={$hashstr}\"  border=\"0\" align=\"absmiddle\"/>";
			}
			else
			{
				$telephone="��ҵ���ò����⹫��";
				$landline_tel="��ҵ���ò����⹫��";
			}
			$email=$val['email_show']=='1'?"<img src=\"{$_CFG['site_dir']}plus/contact_img.php?act=jobs_contact&type=3&id={$id}&token={$token}\"  border=\"0\" align=\"absmiddle\"/>":"��ҵ���ò����⹫��";

			$html='<div class="c-contact">
									<div class="contact-item clearfix">
										<div class="contact-type f-left">�� ϵ �ˣ�</div>
										<div class="contact-content f-left">'.$contact.'</div>
									</div>';
			if(!empty($telephone))
			{
				$html.='<div class="contact-item clearfix">
										<div class="contact-type f-left">��ϵ�ֻ���</div>
										<div class="contact-content f-left">'.$telephone.'</div>
									</div>';
			}
			if(!empty($landline_tel))
			{
				$landline_tel='<div class="contact-item clearfix">
										<div class="contact-type f-left">�̶��绰��</div>
										<div class="contact-content f-left">'.$landline_tel.'</div>
									</div>';
			}
			$html.=$landline_tel;
			$html.='<div class="contact-item clearfix">
										<div class="contact-type f-left">��ϵ���䣺</div>
										<div class="contact-content f-left">'.$email.'</div>
									</div>
									<div class="contact-item clearfix">
										<div class="contact-type f-left">��˾��ַ��</div>
										<div class="contact-content f-left">'.$val['address'].'</div>
									</div>
								</div>';
			}
			else
			{
				//���������зָ�
				$telarray = explode('-',$val['landline_tel']);
				if(intval($telarray[0]) > 0)
				{
					$landline_tel = $telarray[0];
				}
				if(intval($telarray[1]) > 0)
				{
					$landline_tel = empty($landline_tel)?$telarray[1]:$landline_tel."-".$telarray[1];
				}
				if(intval($telarray[2]) > 0)
				{
					$landline_tel = empty($landline_tel)?$telarray[2]:$landline_tel."-".$telarray[2];
				}
			$contact=$val['contact_show']=='1'?"{$val['contact']}":"��ҵ���ò����⹫��";
			$telephone=$val['telephone_show']=='1'?"{$val['telephone']}":"��ҵ���ò����⹫��</li>";
			$landline_tel=$val['telephone_show']=='1'?"{$landline_tel}":"��ҵ���ò����⹫��</li>";
			$email=$val['email_show']=='1'?"{$val['email']}":"��ҵ���ò����⹫��</li>";
						$html='<div class="c-contact">
									<div class="contact-item clearfix">
										<div class="contact-type f-left">�� ϵ �ˣ�</div>
										<div class="contact-content f-left">'.$contact.'</div>
									</div>';
						if(!empty($telephone))
						{	
							$html.='<div class="contact-item clearfix">
										<div class="contact-type f-left">��ϵ�ֻ���</div>
										<div class="contact-content f-left"><span>'.$telephone.'</span></div>
									</div>';
						}		
						if(!empty($landline_tel))
						{
							$landline_tel = '<div class="contact-item clearfix">
										<div class="contact-type f-left">�̶��绰��</div>
										<div class="contact-content f-left"><span>'.$landline_tel.'</span></div>
									</div>';
						}
						$html.=$landline_tel;		
						$html.='<div class="contact-item clearfix">
										<div class="contact-type f-left">��ϵ���䣺</div>
										<div class="contact-content f-left"><span>'.$email.'</span></div>
									</div>
									<div class="contact-item clearfix">
										<div class="contact-type f-left">��˾��ַ��</div>
										<div class="contact-content f-left">'.$val['address'].'</div>
									</div>
								</div>';
			}
		exit($html);
		}
		else
		{		
		exit($html);
		}
	}
		
}
elseif($act == 'company_contact')
{
	$id=intval($_GET['id']);
	$company_profile = $db->getone("SELECT uid FROM ".table('company_profile')." WHERE id=$id");
	if ($id>0)
	{
		$show=false;
		if($_CFG['showjobcontact']=='0')
		{
		$show=true;
		}
		elseif($_CFG['showjobcontact']=='1')
		{
			if ($_SESSION['uid'] && $_SESSION['username'] && $_SESSION['utype']=='2')
			{
			$show=true;
			}
			else
			{
			$show=false;
			$html='<div class="no-login">
									<span class="border-item topleft"></span>
									<span class="border-item topright"></span>
									<span class="border-item bottomleft"></span>
									<span class="border-item bottomright"></span>
									<p>������ע�Ტ��¼����ܲ鿴��ҵ����ϵ��ʽ������<a href="javascript:void(0);" class="ajax_user_login" style="color:#ff7400">[������¼]</a>����<a href="'.$_CFG['site_dir'].'user/user_reg.php" style="color:#ff7400">[���ע��]</a></p>
								</div>';
			}
		}
		elseif($_CFG['showjobcontact']=='2')
		{
			if ($_SESSION['uid'] && $_SESSION['username'] && $_SESSION['utype']=='2')
			{
				$val=$db->getone("select uid from ".table('resume')." where uid='{$_SESSION['uid']}' LIMIT 1");
			 	if (!empty($val))
				{
				$show=true;
				}
				else
				{
				$show=false;
				$html='<div class="no-login">
									<span class="border-item topleft"></span>
									<span class="border-item topright"></span>
									<span class="border-item bottomleft"></span>
									<span class="border-item bottomright"></span>
									<p>��û�з����������߼�����Ч������������ſ��Բ鿴��ϵ��ʽ��<a href="'.get_member_url($_SESSION['utype'],true).'personal_resume.php?act=resume_list">[�鿴�ҵļ���]</a></p>
								</div>';
				}
			}
			else
			{
			$show=false;
			$html='<div class="no-login">
									<span class="border-item topleft"></span>
									<span class="border-item topright"></span>
									<span class="border-item bottomleft"></span>
									<span class="border-item bottomright"></span>
									<p>������ע�Ტ��¼����ܲ鿴��ҵ����ϵ��ʽ������<a href="javascript:void(0);" class="ajax_user_login" style="color:#ff7400">[������¼]</a>����<a href="'.$_CFG['site_dir'].'user/user_reg.php" style="color:#ff7400">[���ע��]</a></p>
								</div>';
			}
		}
		if($_SESSION['uid'] && $_SESSION['username'] && $_SESSION['utype']=='1' && $show==false)
		{
			if($company_profile['uid']==$_SESSION['uid'])
			{
				$show=true;
			}
			else
			{
				$show=false;
			}
		}
		if ($show)
		{
		$sql = "select contact,contact_show,telephone,telephone_show,email,email_show,address,address_show,website,landline_tel FROM ".table('company_profile')." where id='{$id}' LIMIT 1";
		$val=$db->getone($sql);
			if ($_CFG['contact_img_com']=='2')
			{
			$token=md5($val['contact'].$id.$val['telephone']);
			$contact=$val['contact_show']=='1'?"<img src=\"{$_CFG['site_dir']}plus/contact_img.php?act=company_contact&type=1&id={$id}&token={$token}\"  border=\"0\" align=\"absmiddle\"/></li>":"��ҵ���ò����⹫��";
			if($val['telephone_show']=='1')
			{
				$telephone=empty($val['telephone'])?'':"<img src=\"{$_CFG['site_dir']}plus/contact_img.php?act=company_contact&type=2&id={$id}&token={$token}\"  border=\"0\" align=\"absmiddle\"/>";
				$landline_tel=$val['landline_tel'] == '0-0-0'?'':"<img src=\"{$_CFG['site_dir']}plus/contact_img.php?act=company_contact&type=5&id={$id}&token={$token}\"  border=\"0\" align=\"absmiddle\"/>";
			}
			else
			{
				$telephone="��ҵ���ò����⹫��";
				$landline_tel="��ҵ���ò����⹫��";
			}
			$email=$val['email_show']=='1'?"<img src=\"{$_CFG['site_dir']}plus/contact_img.php?act=company_contact&type=3&id={$id}&token={$token}\"  border=\"0\" align=\"absmiddle\"/>":"��ҵ���ò����⹫��";

			$html='<div class="c-contact">
									<div class="contact-item clearfix">
										<div class="contact-type f-left">�� ϵ �ˣ�</div>
										<div class="contact-content f-left">'.$contact.'</div>
									</div>';
			if(!empty($telephone))
			{
				$html.='<div class="contact-item clearfix">
										<div class="contact-type f-left">��ϵ�ֻ���</div>
										<div class="contact-content f-left">'.$telephone.'</div>
									</div>';
			}					
			if(!empty($landline_tel))
			{
				$landline_tel='<div class="contact-item clearfix">
										<div class="contact-type f-left">�̶��绰��</div>
										<div class="contact-content f-left">'.$landline_tel.'</div>
									</div>';
			}
			$html.=$landline_tel;
			$html.='<div class="contact-item clearfix">
										<div class="contact-type f-left">��ϵ���䣺</div>
										<div class="contact-content f-left">'.$email.'</div>
									</div>
									<div class="contact-item clearfix">
										<div class="contact-type f-left">��˾��ַ��</div>
										<div class="contact-content f-left">'.$val['address'].'</div>
									</div>
								</div>';
			}
			else
			{
				//���������зָ�
				$telarray = explode('-',$val['landline_tel']);
				if(intval($telarray[0]) > 0)
				{
					$landline_tel = $telarray[0];
				}
				if(intval($telarray[1]) > 0)
				{
					$landline_tel = empty($landline_tel)?$telarray[1]:$landline_tel."-".$telarray[1];
				}
				if(intval($telarray[2]) > 0)
				{
					$landline_tel = empty($landline_tel)?$telarray[2]:$landline_tel."-".$telarray[2];
				}
			$contact=$val['contact_show']=='1'?"{$val['contact']}":"��ҵ���ò����⹫��";
			$telephone=$val['telephone_show']=='1'?"{$val['telephone']}":"��ҵ���ò����⹫��</li>";
			$landline_tel=$val['telephone_show']=='1'?"{$landline_tel}":"��ҵ���ò����⹫��</li>";
			$email=$val['email_show']=='1'?"{$val['email']}":"��ҵ���ò����⹫��</li>";
						$html='<div class="c-contact">
									<div class="contact-item clearfix">
										<div class="contact-type f-left">�� ϵ �ˣ�</div>
										<div class="contact-content f-left">'.$contact.'</div>
									</div>';
						if(!empty($telephone))
						{
							$html.='<div class="contact-item clearfix">
										<div class="contact-type f-left">��ϵ�ֻ���</div>
										<div class="contact-content f-left"><span>'.$telephone.'</span></div>
									</div>';
						}		
						if(!empty($landline_tel))
						{
							$landline_tel = '<div class="contact-item clearfix">
										<div class="contact-type f-left">�̶��绰��</div>
										<div class="contact-content f-left"><span>'.$landline_tel.'</span></div>
									</div>';
						}
						$html.=$landline_tel;	
						$html.='<div class="contact-item clearfix">
										<div class="contact-type f-left">��ϵ���䣺</div>
										<div class="contact-content f-left"><span>'.$email.'</span></div>
									</div>
									<div class="contact-item clearfix">
										<div class="contact-type f-left">��˾��ַ��</div>
										<div class="contact-content f-left">'.$val['address'].'</div>
									</div>
								</div>';
			}
			exit($html);
		}
		else
		{		
		exit($html);
		}
	}
}
elseif($act == 'resume_contact')
{
		$id=intval($_GET['id']);
		$show=false;
		$resume_sql="select * from ".table("resume")." where id=$id ";
		$resume_one=$db->getone($resume_sql);
		if($_SESSION["utype"]==2 && $_SESSION["uid"]==$resume_one["uid"]){
			$show=true;
		}
		if($_SESSION['uid'] && $_SESSION['username'] && $_SESSION['utype']=='1' && $_CFG['showapplycontact']=='1'){
			$has = $db->getone("select 1 from ".table('personal_jobs_apply')." where company_uid=".intval($_SESSION['uid'])." and resume_id=".$id);
			if($has){
				$show = true;
			}
		}
		if($show==false){
			if($_CFG['showresumecontact']=='0')
			{
			$show=true;
			}
			elseif($_CFG['showresumecontact']=='1')
			{
				if ($_SESSION['uid'] && $_SESSION['username'] && ($_SESSION['utype']=='1' || $_SESSION['utype']=='3'))
				{
				$show=true;
				}
				else
				{
				$show=false;
				$html="<div class=\"contact-box\"><input type=\"button\" value=\"�鿴��ϵ��ʽ\" class=\"contact-btn download\" /></div>";
				}
			}
			elseif($_CFG['showresumecontact']=='2')
			{
				if ($_SESSION['uid'] && $_SESSION['username'] && $_SESSION['utype']=='1')
				{
					$sql = "select did from ".table('company_down_resume')." WHERE company_uid = {$_SESSION['uid']} AND resume_id='{$id}' LIMIT 1";
					$info=$db->getone($sql);
					if (!empty($info))
					{
					$show=true;
					}
					else
					{
					$show=false;
					$html="<div class=\"contact-box\"><input type=\"button\" value=\"�鿴��ϵ��ʽ\" class=\"contact-btn download\" /></div>";
					}
				}
				elseif ($_SESSION['uid'] && $_SESSION['username'] && $_SESSION['utype']=='3')
				{
					$sql = "select did from ".table('hunter_down_resume')." WHERE hunter_uid = {$_SESSION['uid']} AND resume_id='{$id}' LIMIT 1";
					$info=$db->getone($sql);
					if (!empty($info))
					{
					$show=true;
					}
					else
					{
					$show=false;
					$html="<div class=\"contact-box\"><input type=\"button\" value=\"�鿴��ϵ��ʽ\" class=\"contact-btn download\" /></div>";
					}
				}
				else
				{
				$show=false;
				$html="<div class=\"contact-box\"><input type=\"button\" value=\"�鿴��ϵ��ʽ\" class=\"contact-btn download\" /></div>";
				}
			}
		}
		if ($show)
		{
			$val=$db->getone("select fullname,telephone,email,word_resume from ".table('resume')." WHERE  id='{$id}'  LIMIT 1");
			/*
				���������״̬
			*/
			if($_SESSION['uid'] && $_SESSION['utype']==1)
			{
				$resume_state=$db->getone("select resume_state from ".table("company_label_resume")." where resume_id=$id and uid=$_SESSION[uid]");
				switch ($resume_state['resume_state']) {
					case 1:
						$state_htm="<input type=\"button\" value=\"��δ��ͨ\" class=\"interview-state\" state=\"4\" resume_id=\"{$id}\"/><input type=\"button\" value=\"����\" class=\"interview-state\" state=\"3\" resume_id=\"{$id}\"/><input type=\"button\" value=\"����\" class=\"interview-state selected\" state=\"1\" resume_id=\"{$id}\"/><input type=\"button\" value=\"������\" class=\"interview-state\" state=\"2\" resume_id=\"{$id}\"/>";
						break;
					case 2:
						$state_htm="<input type=\"button\" value=\"��δ��ͨ\" class=\"interview-state\" state=\"4\" resume_id=\"{$id}\"/><input type=\"button\" value=\"����\" class=\"interview-state\" state=\"3\" resume_id=\"{$id}\"/><input type=\"button\" value=\"����\" class=\"interview-state\" state=\"1\" resume_id=\"{$id}\"/><input type=\"button\" value=\"������\" class=\"interview-state selected\" state=\"2\" resume_id=\"{$id}\"/>";
						break;
					case 3:
						$state_htm="<input type=\"button\" value=\"��δ��ͨ\" class=\"interview-state\" state=\"4\" resume_id=\"{$id}\"/><input type=\"button\" value=\"����\" class=\"interview-state selected\" state=\"3\" resume_id=\"{$id}\"/><input type=\"button\" value=\"����\" class=\"interview-state\" state=\"1\" resume_id=\"{$id}\"/><input type=\"button\" value=\"������\" class=\"interview-state\" state=\"2\" resume_id=\"{$id}\"/>";
						break;
					case 4:
						$state_htm="<input type=\"button\" value=\"��δ��ͨ\" class=\"interview-state selected\" state=\"4\" resume_id=\"{$id}\"/><input type=\"button\" value=\"����\" class=\"interview-state\" state=\"3\" resume_id=\"{$id}\"/><input type=\"button\" value=\"����\" class=\"interview-state\" state=\"1\" resume_id=\"{$id}\"/><input type=\"button\" value=\"������\" class=\"interview-state\" state=\"2\" resume_id=\"{$id}\"/>";
						break;
					default:
						$state_htm="<input type=\"button\" value=\"��δ��ͨ\" class=\"interview-state\" state=\"4\" resume_id=\"{$id}\"/><input type=\"button\" value=\"����\" class=\"interview-state\" state=\"3\" resume_id=\"{$id}\"/><input type=\"button\" value=\"����\" class=\"interview-state\" state=\"1\" resume_id=\"{$id}\"/><input type=\"button\" value=\"������\" class=\"interview-state\" state=\"2\" resume_id=\"{$id}\"/>";
						break;
				}
			}
			else
			{
				$state_htm="<input type=\"button\" value=\"��δ��ͨ\" class=\"interview-state\" state=\"4\" resume_id=\"{$id}\"/><input type=\"button\" value=\"����\" class=\"interview-state\" state=\"3\" resume_id=\"{$id}\"/><input type=\"button\" value=\"����\" class=\"interview-state\" state=\"1\" resume_id=\"{$id}\"/><input type=\"button\" value=\"������\" class=\"interview-state\" state=\"2\" resume_id=\"{$id}\"/>";
			}
			// word ����
			if($val['word_resume'])
			{
				$word_resume="<a class=\"word_resume\" href=\"".$_CFG['site_dir']."data/word/".$val['word_resume']."\"><img src=\"".$_CFG['site_template']."/images/word_resume.png\"> word����</a>";
			}
			if ($_CFG['contact_img_resume']=='2')
			{
			$token=md5($val['fullname'].$id.$val['telephone']);
			$html="<div class=\"contact-interview\">";
			$html.="<div class=\"contact-text\">��ϵ��ʽ��<span><img src=\"{$_CFG['site_dir']}plus/contact_img.php?act=resume_contact&type=1&id={$id}&token={$token}\"  border=\"0\" align=\"absmiddle\"/><em>|</em><img src=\"{$_CFG['site_dir']}plus/contact_img.php?act=resume_contact&type=2&id={$id}&token={$token}\"  border=\"0\" align=\"absmiddle\"/><em>|</em><img src=\"{$_CFG['site_dir']}plus/contact_img.php?act=resume_contact&type=3&id={$id}&token={$token}\"  border=\"0\" align=\"absmiddle\"/>".$word_resume."</span></div>";
			$html.="<div class=\"interview-block\">";
			$html.="<input id=\"invited\" type=\"button\" value=\"������������\" class=\"contact-btn\" resume_id=\"{$id}\" />".$state_htm;
			$html.="</div>"; 
			$html.="</div>";
			}
			else
			{
			$html="<div class=\"contact-interview\">";
			$html.="<div class=\"contact-text\">��ϵ��ʽ��<span>".$val['fullname']."<em>|</em>".$val['telephone']."<em>|</em>".$val['email'].$word_resume."</span></div>";
			$html.="<div class=\"interview-block\">";
			$html.="<input id=\"invited\" type=\"button\" value=\"������������\" class=\"contact-btn\" resume_id=\"{$id}\" />".$state_htm;
			$html.="</div>"; 
			$html.="</div>";
			}
			exit($html);
		}
		else
		{		
		exit($html);
		}
}
//3.4
elseif($act == 'course_contact')
{
	$id=intval($_GET['id']);
	if ($id>0)
	{
		$show=false;
		if($_CFG['showcoursecontact']=='0')
		{
		$show=true;
		}
		elseif($_CFG['showcoursecontact']=='1')
		{
		//��¼�󣬲鿴Ȩ��
			if ($_SESSION['uid'] && $_SESSION['username'] && $_SESSION['utype']=='2')
			{
			$show=true;
			}
			else
			{
			$show=false;
			$html="<div class=\"c\">�� <a href=\"".$_CFG['site_dir']."user/login.php\" class=\"ajax_user_login\"> ��¼ </a>��<a href=\"".$_CFG['site_dir']."user/user_reg.php\"> ע�� </a> ���˻�Ա���鿴�û�����ϵ��ʽ��</div>";
			}
		}
		if ($show)
		{
		$sql = "select * from ".table('course_contact')." where pid='{$id}' LIMIT 1";
		$val=$db->getone($sql);
			if ($_CFG['contact_img_course']=='2')
			{
			$token=md5($val['contact'].$id.$val['telephone']);
			$ul="<ul>";
			$contact=$val['contact_show']=='1'?"<div class=\"c\">�� ϵ �ˣ�<img src=\"{$_CFG['site_dir']}plus/contact_img.php?act=course_contact&type=1&id={$id}&token={$token}\"  border=\"0\" align=\"absmiddle\"/></div>":"<div class=\"c\">�� ϵ �ˣ��������ò����⹫��</div>";
			$telephone=$val['telephone_show']=='1'?"<div class=\"c\">��ϵ�绰��<img src=\"{$_CFG['site_dir']}plus/contact_img.php?act=course_contact&type=2&id={$id}&token={$token}\"  border=\"0\" align=\"absmiddle\"/></div>":"<div class=\"c\">��ϵ�绰���������ò����⹫��</div>";
			$email=$val['email_show']=='1'?"<div class=\"c\">��ϵ���䣺<img src=\"{$_CFG['site_dir']}plus/contact_img.php?act=course_contact&type=3&id={$id}&token={$token}\"  border=\"0\" align=\"absmiddle\"/></div>":"<div class=\"c\">��ϵ���䣺�������ò����⹫��</div>";
			$ull="</ul>";
			$html=$ul.$contact.$telephone.$email.$ull;
			}
			else
			{
			$ul="<ul>";
			$contact=$val['contact_show']=='1'?"<div class=\"c\">�� ϵ �ˣ�{$val['contact']}</div>":"<div class=\"c\">�� ϵ �ˣ��������ò����⹫��</div>";
			$telephone=$val['telephone_show']=='1'?"<div class=\"c\">��ϵ�绰��{$val['telephone']}</div>":"<div class=\"c\">��ϵ�绰���������ò����⹫��</div>";
			$email=$val['email_show']=='1'?"<div class=\"c\">��ϵ���䣺{$val['email']}</div>":"<div class=\"c\">��ϵ���䣺�������ò����⹫��</div>";
			$ull="</ul>";
			$html=$ul.$contact.$telephone.$email.$ull;
			}
		exit($html);
		}
		else
		{		
		exit($html);
		}
	}
}
elseif($act == 'train_contact')
{
	$id=intval($_GET['id']);
	if ($id>0)
	{
		$show=false;
		if($_CFG['showcoursecontact']=='0')
		{
		$show=true;
		}
		elseif($_CFG['showcoursecontact']=='1')
		{
		//��Ҫע��
			if ($_SESSION['uid'] && $_SESSION['username'] && $_SESSION['utype']=='2')
			{
			$show=true;
			}
			else
			{
			$show=false;
			$html="<div class=\"c\">�� <a href=\"".$_CFG['site_dir']."user/login.php\" class=\"ajax_user_login\"> ��¼ </a>��<a href=\"".$_CFG['site_dir']."user/user_reg.php\"> ע�� </a> ���˻�Ա���鿴�û�����ϵ��ʽ��</div>";
			}
		}
		if ($show)
		{
		$sql = "select contact,contact_show,telephone,telephone_show,email,email_show,address,address_show,website FROM ".table('train_profile')." where id='{$id}' LIMIT 1";
		$val=$db->getone($sql);
			if ($_CFG['contact_img_train']=='2')
			{
			$token=md5($val['contact'].$id.$val['telephone']);
			$ul="<ul>";
			$contact=$val['contact_show']=='1'?"<div class=\"c\">�� ϵ �ˣ�<img src=\"{$_CFG['site_dir']}plus/contact_img.php?act=train_contact&type=1&id={$id}&token={$token}\"  border=\"0\" align=\"absmiddle\"/></div>":"<div class=\"c\">�� ϵ �ˣ��������ò����⹫��</div>";
			$telephone=$val['telephone_show']=='1'?"<div class=\"c\">��ϵ�绰��<img src=\"{$_CFG['site_dir']}plus/contact_img.php?act=train_contact&type=2&id={$id}&token={$token}\"  border=\"0\" align=\"absmiddle\"/></div>":"<div class=\"c\">��ϵ�绰���������ò����⹫��</div>";
			$email=$val['email_show']=='1'?"<div class=\"c\">��ϵ���䣺<img src=\"{$_CFG['site_dir']}plus/contact_img.php?act=train_contact&type=3&id={$id}&token={$token}\"  border=\"0\" align=\"absmiddle\"/></div>":"<div class=\"c\">��ϵ���䣺�������ò����⹫��</div>";
			$address=$val['address_show']=='1'?"<div class=\"c\">��ϵ��ַ��<img src=\"{$_CFG['site_dir']}plus/contact_img.php?act=train_contact&type=4&id={$id}&token={$token}\"  border=\"0\" align=\"absmiddle\"/></div>":"<div class=\"c\">��ϵ��ַ���������ò����⹫��</div>";
			$website.="<div class=\"c\">������ַ��<img src=\"{$_CFG['site_dir']}plus/contact_img.php?act=train_contact&type=5&id={$id}&token={$token}\"  border=\"0\" align=\"absmiddle\"/></div>";		
			$ull="</ul>";
			$html=$ul.$contact.$telephone.$email.$address.$website.$ull;
			}
			else
			{
			$ul="<ul>";
			$contact=$val['contact_show']=='1'?"<div class=\"c\">�� ϵ �ˣ�{$val['contact']}</div>":"<div class=\"c\">�� ϵ �ˣ��������ò����⹫��</div>";
			$telephone=$val['telephone_show']=='1'?"<div class=\"c\">��ϵ�绰��{$val['telephone']}</div>":"<div class=\"c\">��ϵ�绰���������ò����⹫��</div>";
			$email=$val['email_show']=='1'?"<div class=\"c\">��ϵ���䣺{$val['email']}</div>":"<div class=\"c\">��ϵ���䣺�������ò����⹫��</div>";
			$address=$val['address_show']=='1'?"<div class=\"c\">��ϵ��ַ��{$val['address']}</div>":"<div class=\"c\">��ϵ��ַ���������ò����⹫��</div>";
			$website=$val['website']==""?"":"<div class=\"c\">������ַ��{$val['website']}</div>";
			$ull.="</ul>";
			$html=$ul.$contact.$telephone.$email.$address.$website.$ull;
			}
			exit($html);
		}
		else
		{		
		exit($html);
		}
	}
}
elseif($act == 'teacher_contact')
{
	$id=intval($_GET['id']);
	if ($id>0)
	{
		$show=false;
		if($_CFG['showcoursecontact']=='0')
		{
		$show=true;
		}
		elseif($_CFG['showcoursecontact']=='1')
		{
		//��Ҫע��
			if ($_SESSION['uid'] && $_SESSION['username'] && $_SESSION['utype']=='2')
			{
			$show=true;
			}
			else
			{
			$show=false;
			$html="<div class=\"c\">�� <a href=\"".$_CFG['site_dir']."user/login.php\" class=\"ajax_user_login\"> ��¼ </a>��<a href=\"".$_CFG['site_dir']."user/user_reg.php\"> ע�� </a> ���˻�Ա���鿴�ý�ʦ��ϵ��ʽ��</div>";
			}
		}
		if ($show)
		{
		$sql = "select telephone,telephone_show,email,email_show,address,address_show,qq,qq_show,website FROM ".table('train_teachers')." where id='{$id}' LIMIT 1";
		$val=$db->getone($sql);
			if ($_CFG['contact_img_train']=='2')
			{
			$token=md5($val['contact'].$id.$val['telephone']);
			$ul="<ul>";
			$telephone=$val['telephone_show']=='1'?"<div class=\"c\">��ϵ�绰��<img src=\"{$_CFG['site_dir']}plus/contact_img.php?act=teacher_contact&type=2&id={$id}&token={$token}\"  border=\"0\" align=\"absmiddle\"/></div>":"<div class=\"c\">��ϵ�绰����ʦ���ò����⹫��</div>";
			$email=$val['email_show']=='1'?"<div class=\"c\">��ϵ���䣺<img src=\"{$_CFG['site_dir']}plus/contact_img.php?act=teacher_contact&type=3&id={$id}&token={$token}\"  border=\"0\" align=\"absmiddle\"/></div>":"<div class=\"c\">��ϵ���䣺��ʦ���ò����⹫��</div>";
			$address=$val['address_show']=='1'?"<div class=\"c\">��ϵ��ַ��<img src=\"{$_CFG['site_dir']}plus/contact_img.php?act=teacher_contact&type=4&id={$id}&token={$token}\"  border=\"0\" align=\"absmiddle\"/></div>":"<div class=\"c\">��ϵ��ַ����ʦ���ò����⹫��</div>";
			$ull="</ul>";
			$html=$ul.$contact.$telephone.$email.$address.$ull;
			}
			else
			{
			$ul="<ul>";
			$telephone=$val['telephone_show']=='1'?"<div class=\"c\">��ϵ�绰��{$val['telephone']}</div>":"<div class=\"c\">��ϵ�绰����ʦ���ò����⹫��</div>";
			$email=$val['email_show']=='1'?"<div class=\"c\">��ϵ���䣺{$val['email']}</div>":"<div class=\"c\">��ϵ���䣺��ʦ���ò����⹫��</div>";
			$address=$val['address_show']=='1'?"<div class=\"c\">��ϵ��ַ��{$val['address']}</div>":"<div class=\"c\">��ϵ��ַ����ʦ���ò����⹫��</div>";
			$ull.="</ul>";
			$html=$ul.$contact.$telephone.$email.$address.$ull;
			}
			exit($html);
		}
		else
		{		
		exit($html);
		}
	}
}
//�߼�ְλ
elseif($act == 'hunterjobs_contact')
{
	$id=intval($_GET['id']);
	if ($id>0)
	{
		$show=false;
		if($_CFG['showhunterjobcontact']=='0')
		{
		$show=true;
		}
		elseif($_CFG['showhunterjobcontact']=='1')
		{
			if ($_SESSION['uid'] && $_SESSION['username'] && $_SESSION['utype']=='2')
			{
			$show=true;
			}
			else
			{
			$show=false;
			$html="<div class=\"c\">�� <a href=\"".$_CFG['site_dir']."user/login.php\" class=\"ajax_user_login\"> ��¼ </a>��<a href=\"".$_CFG['site_dir']."user/user_reg.php\"> ע�� </a> ���˻�Ա���鿴����ͷ��ϵ��ʽ��</div>";
			}
		}
		//�Ƿ񴴽��߼������˼���
		elseif($_CFG['showhunterjobcontact']=='2')
		{
			if ($_SESSION['uid'] && $_SESSION['username'] && $_SESSION['utype']=='2')
			{
				$val=$db->getone("select uid from ".table('manager_resume')." where uid='{$_SESSION['uid']}' and complete=1 and audit=1 LIMIT 1");
			 	if (!empty($val))
				{
				$show=true;
				}
				else
				{
				$show=false;
				$html="<p>��û�з��������˼������߼�����Ч�����������˼�����ſ��Բ鿴��ϵ��ʽ��<a href=\"".get_member_url($_SESSION['utype'],true)."personal_managerresume.php?act=resume_list\">[�鿴�ҵľ����˼���]</a></p>";
				}
			}
			else
			{
			$show=false;
			$html="<div class=\"c\">�� <a href=\"".$_CFG['site_dir']."user/login.php\" class=\"ajax_user_login\"> ��¼ </a>��<a href=\"".$_CFG['site_dir']."user/user_reg.php\"> ע�� </a> ���˻�Ա���鿴����ͷ��ϵ��ʽ��</div>";
			}
		}
		
		if ($show)
		{
		$sql = "select telephone,telephone_show,email,email_show,address,address_show,contact,contact_show,qq,qq_show from ".table('hunter_jobs')." where id='{$id}' LIMIT 1";
		$val=$db->getone($sql);
			if ($_CFG['contact_img_hunterjob']=='2')
			{
			$token=md5($val['contact'].$id.$val['telephone']);
			$contact=$val['contact_show']=='1'?"<div class=\"c\">�� ϵ �ˣ�<img src=\"{$_CFG['site_dir']}plus/contact_img.php?act=hunterjobs_contact&type=1&id={$id}&token={$token}\"  border=\"0\" align=\"absmiddle\"/></div>":"<div class=\"c\">�� ϵ �ˣ���ҵ���ò����⹫��</div>";
			$telephone=$val['telephone_show']=='1'?"<div class=\"c\">��ϵ�绰��<img src=\"{$_CFG['site_dir']}plus/contact_img.php?act=hunterjobs_contact&type=2&id={$id}&token={$token}\"  border=\"0\" align=\"absmiddle\"/></div>":"<div class=\"c\">��ϵ�绰����ҵ���ò����⹫��</div>";
			$email=$val['email_show']=='1'?"<div class=\"c\">��ϵ���䣺<img src=\"{$_CFG['site_dir']}plus/contact_img.php?act=hunterjobs_contact&type=3&id={$id}&token={$token}\"  border=\"0\" align=\"absmiddle\"/></div>":"<div class=\"c\">��ϵ���䣺��ҵ���ò����⹫��</div>";
			$address=$val['address_show']=='1'?"<div class=\"c\">��ϵ��ַ��<img src=\"{$_CFG['site_dir']}plus/contact_img.php?act=hunterjobs_contact&type=4&id={$id}&token={$token}\"  border=\"0\" align=\"absmiddle\"/></div>":"<div class=\"c\">��ϵ��ַ����ҵ���ò����⹫��</div>";
			$html=$contact.$telephone.$email.$address;
			}
			else
			{
			$contact=$val['contact_show']=='1'?"<div class=\"c\">�� ϵ �ˣ�{$val['contact']}</div>":"<div class=\"c\">�� ϵ �ˣ���ҵ���ò����⹫��</div>";
			$telephone=$val['telephone_show']=='1'?"<div class=\"c\">��ϵ�绰��{$val['telephone']}</div>":"<div class=\"c\">��ϵ�绰����ҵ���ò����⹫��</div>";
			$email=$val['email_show']=='1'?"<div class=\"c\">��ϵ���䣺{$val['email']}</div>":"<div class=\"c\">��ϵ���䣺��ҵ���ò����⹫��</div>";
			$address=$val['address_show']=='1'?"<div class=\"c\">��ϵ��ַ��{$val['address']}</div>":"<div class=\"c\">��ϵ��ַ����ҵ���ò����⹫��</div>";
			$html=$contact.$telephone.$email.$address;
			}
		exit($html);
		}
		else
		{		
		exit($html);
		}
	}
		
}
elseif($act == 'hunter_contact')
{
	$id=intval($_GET['id']);
	if ($id>0)
	{
		$show=false;
		if($_CFG['showhunterjobcontact']=='0')
		{
		$show=true;
		}
		elseif($_CFG['showhunterjobcontact']=='1')
		{
			if ($_SESSION['uid'] && $_SESSION['username'] && $_SESSION['utype']=='2')
			{
			$show=true;
			}
			else
			{
			$show=false;
			$html="<div class=\"c\">�� <a href=\"".$_CFG['site_dir']."user/login.php\" class=\"ajax_user_login\"> ��¼ </a>��<a href=\"".$_CFG['site_dir']."user/user_reg.php\"> ע�� </a> ���˻�Ա���鿴����ͷ��ϵ��ʽ��</div>";
			}
		}
		//�Ƿ񴴽��߼�����
		elseif($_CFG['showhunterjobcontact']=='2')
		{
			if ($_SESSION['uid'] && $_SESSION['username'] && $_SESSION['utype']=='2')
			{
				$val=$db->getone("select uid from ".table('resume')." where uid='{$_SESSION['uid']}' and complete=1 and audit=1 and talent=2 LIMIT 1");
			 	if (!empty($val))
				{
				$show=true;
				}
				else
				{
				$show=false;
				$html="<p>��û�з����߼��������߼�����Ч�������߼�������ſ��Բ鿴��ϵ��ʽ��<a href=\"".get_member_url($_SESSION['utype'],true)."personal_managerresume.php?act=resume_list\">[�鿴�ҵľ����˼���]</a></p>";
				}
			}
			else
			{
			$show=false;
			$html="<div class=\"c\">�� <a href=\"javascript:;\" class=\"ajax_user_login\"> ��¼ </a>��<a href=\"".$_CFG['site_dir']."user/user_reg.php\"> ע�� </a> ���˻�Ա���鿴����ͷ��ϵ��ʽ��</div>";
			}
		}
		
		if ($show)
		{
		$sql = "select telephone,telephone_show,email,email_show,address,address_show,contact,contact_show,qq,qq_show from ".table('hunter_jobs')." where id='{$id}' LIMIT 1";
		$val=$db->getone($sql);
			if ($_CFG['contact_img_hunterjob']=='2')
			{
			$token=md5($val['contact'].$id.$val['telephone']);
			$contact=$val['contact_show']=='1'?"<div class=\"c\">�� ϵ �ˣ�<img src=\"{$_CFG['site_dir']}plus/contact_img.php?act=hunterjobs_contact&type=1&id={$id}&token={$token}\"  border=\"0\" align=\"absmiddle\"/></div>":"<div class=\"c\">�� ϵ �ˣ���ҵ���ò����⹫��</div>";
			$telephone=$val['telephone_show']=='1'?"<div class=\"c\">��ϵ�绰��<img src=\"{$_CFG['site_dir']}plus/contact_img.php?act=hunterjobs_contact&type=2&id={$id}&token={$token}\"  border=\"0\" align=\"absmiddle\"/></div>":"<div class=\"c\">��ϵ�绰����ҵ���ò����⹫��</div>";
			$email=$val['email_show']=='1'?"<div class=\"c\">��ϵ���䣺<img src=\"{$_CFG['site_dir']}plus/contact_img.php?act=hunterjobs_contact&type=3&id={$id}&token={$token}\"  border=\"0\" align=\"absmiddle\"/></div>":"<div class=\"c\">��ϵ���䣺��ҵ���ò����⹫��</div>";
			$address=$val['address_show']=='1'?"<div class=\"c\">��ϵ��ַ��<img src=\"{$_CFG['site_dir']}plus/contact_img.php?act=hunterjobs_contact&type=4&id={$id}&token={$token}\"  border=\"0\" align=\"absmiddle\"/></div>":"<div class=\"c\">��ϵ��ַ����ҵ���ò����⹫��</div>";
			$qq=$val['qq_show']=='1'?"<div class=\"c\">��ϵQ Q�� <img src=\"{$_CFG['site_dir']}plus/contact_img.php?act=hunterjobs_contact&type=5&id={$id}&token={$token}\"  border=\"0\" align=\"absmiddle\"/></div>":"<div class=\"c\">��ϵQ Q����ҵ���ò����⹫�� </div>";
			$html=$contact.$telephone.$email.$address.$qq;
			}
			else
			{
			$contact=$val['contact_show']=='1'?"<div class=\"c\">�� ϵ �ˣ�{$val['contact']}</div>":"<div class=\"c\">�� ϵ �ˣ���ҵ���ò����⹫��</div>";
			$telephone=$val['telephone_show']=='1'?"<div class=\"c\">��ϵ�绰��{$val['telephone']}</div>":"<div class=\"c\">��ϵ�绰����ҵ���ò����⹫��</div>";
			$email=$val['email_show']=='1'?"<div class=\"c\">��ϵ���䣺{$val['email']}</div>":"<div class=\"c\">��ϵ���䣺��ҵ���ò����⹫��</div>";
			$address=$val['address_show']=='1'?"<div class=\"c\">��ϵ��ַ��{$val['address']}</div>":"<div class=\"c\">��ϵ��ַ����ҵ���ò����⹫��</div>";
			$qq=$val['qq_show']=='1'?"<div class=\"c\">��ϵQ Q��{$val['qq']}</div>":"<div class=\"c\">��ϵQ Q����ҵ���ò����⹫��</div>";
			$html=$contact.$telephone.$email.$address.$qq;
			}
		exit($html);
		}
		else
		{		
		exit($html);
		}
	}
}
?>