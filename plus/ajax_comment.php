<?php
 /*
 * 74cms ajax ��������
*/
define('IN_QISHI', true);
require_once(dirname(dirname(__FILE__)).'/include/plus.common.inc.php');
if ($_CFG['open_comment']=='0')
{
exit();
}
$act = !empty($_POST['act']) ? trim($_POST['act']) :trim($_GET['act']);
if ($act=='')
{
	$id=intval($_GET['companyid']);
	$company_profile=$db->getone("select * from ".table('company_profile')." where id='{$id}' LIMIT 1");
	$captcha=get_cache('captcha');
	$html='';
	$html.='<div class="comment-top clearfix">
							<span class="f-left">�����������Է���</span>
							<span class="f-right">'.$company_profile['comment'].'������</span>
						</div>';
	if ($_SESSION['uid']=='' || $_SESSION['username']=='' || intval($_SESSION['uid'])===0 || intval($_SESSION['utype'])!=2)
	{
	$html.='<div class="comment-block">
							<div class="comment-textarea" style="text-align: center;line-height: 87px;">����û�е�¼�����˻�Ա�� <a href="javascript:void(0);" class="ajax_user_login">��¼</a> �� <a href="'.$_CFG['site_dir'].'user/user_reg.php">ע��</a> ��������ۣ�</div>
							<div class="textarea-control clearfix">
								<div class="text-nologin f-left"></div>
								<div class="comment-submit f-right"><input type="button" value="��������" /></div>
							</div>
						</div><div class="clear"></div>';
	}
	else
	{
		if ($captcha['verify_comment']=='1')
		{
			$verify_comment='<input type="text" name="postcaptcha" id="postcaptcha" value="�����ȡ��֤��" style="width: 100px;	height: 28px;border-width:1px;padding-left:5px;"/><div id="imgdiv"></div>
			<script>
				$(document).ready(function(){
	function imgcaptcha(inputID,imgdiv)
	{
		$(inputID).focus(function(){
			if ($(inputID).val()=="�����ȡ��֤��")
			{
				$(inputID).val("");
				$(inputID).css("color","#333333");
			}
			$(inputID).parent("div").css("position","relative");
			$(imgdiv).css({ position: "absolute", left:  $(inputID).width(), "bottom": "0px" , "z-index": "10", "background-color": "#FFFFFF", "border": "1px #A3C8DC solid","display": "none","margin-left": "15px"});
			$(imgdiv).show();
			if ($(imgdiv).html()=="")
			{
				var tsTimeStamp= new Date().getTime();
				$(imgdiv).append("<img width=\'100\' src=\''.$_CFG["site_dir"].'include/imagecaptcha.php?t=\'+tsTimeStamp+\' id=\'getcode\' align=\'absmiddle\'  style=\'cursor:pointer; margin:1px;\' title=\'��������֤�룿�������һ��\' border=\'0\'/>");
			}
			$(imgdiv+" img").click(function(){
				$(imgdiv+" img").attr("src",$(imgdiv+" img").attr("src")+"1");
			});
			$(document).unbind().click(function(event){
				var clickid=$(event.target).attr("id");
				if (clickid!="getcode" &&  clickid!="postcaptcha")
				{
					$(imgdiv).hide();
					$(inputID).parent("div").css("position","");
					$(document).unbind();
				}
			});
		});
	}
	imgcaptcha("#postcaptcha","#imgdiv");
});
			</script>';
		}
		$html.='<div class="comment-block">
							<div class="comment-textarea"><textarea id="content" cols="30" rows="3" style="font-size: 12px;">�������������Է���</textarea></div>
							<div class="textarea-control clearfix">
								<div class="text-nologin f-left">'.$verify_comment.'</div>
								<div class="comment-submit f-right"><input type="button" value="��������" id="submitcomment"/></div>
								<div class="f-right" id="posterr" style="color: red;margin-right: 10px;height: 28px;line-height: 28px;"></div>
							</div>
						</div>';
	}

	$commentarray=$db->getall("select  c.*,m.username,m.avatars from ".table('comment')." as c LEFT JOIN  ".table('members')." AS m ON c.uid=m.uid where company_id = '{$id}' and c.audit=1 ORDER BY c.id DESC LIMIT 3");
	//��������  ѭ�����
	$html.='<div class="comment-list" id="comment_list_box">';
	if (!empty($commentarray))
	{
		foreach($commentarray as $li)
		{
			++$num;
			$li['username'] = cut_str($li['username'],1,0,'****');
			if($li['avatars'])
			{
				$avatars='<div class="logoimg"><img src="'.$_CFG['site_dir'].'data/avatar/48/'.$li['avatars'].'"></div>';
			}
			else
			{
				$avatars='<div class="logoimg"><img src="'.$_CFG['site_dir'].'data/avatar/no_photo.gif"></div>';
			}
			if($li['reply'])
			{
				$hf_a='<a href="javascript:;" class="control-item f-left more-talk">��ҵ�ظ�</a>';
			}
			else
			{
				if($_SESSION['uid']==$company_profile['uid'])
				{
					$hf_a='<a href="javascript:;" class="control-item f-left talk">�ظ�</a>';
				}else
				{
					$hf_a='';
				}
			}
			$html.='<div class="comment-item" id="li-0">
								'.$avatars.'
								<div class="comment-title"><a>'.$li['username'].'</a><span>'.$num.'¥</span><span>'.date('Y-m-d H:i',$li['addtime']).'</span></div>
								<div class="comment-content">'.$li['content'].'</div>
								<div class="comment-control clearfix">
									'.$hf_a.'
								</div>';
			//�ظ� ����					
			if($li['reply'])
			{
				$html.='<div class="talk-again" style="display:block">
									<i class="ta-arrow" style="left:37px"></i>
									<div class="talk-again-item f-left">
										<div class="talk-man clearfix">
											<div class="talk-id f-left"><a>'.$company_profile['companyname'].'</a> �ظ��� <a>'.$li['username'].'</a></div>
											<div class="talk-time f-right">'.date('Y-m-d H:i',$li['reply_time']).'</div>
										</div>
										<div class="talk-content">'.$li['reply'].'</div>
									</div><div class="clear"></div>
								</div>';
			}
			else
			{
				// ��ҵ�Լ�
				if($_SESSION['uid']==$company_profile['uid'])
				{
					$html.='<div class="comment-block talk-block">
									<div class="comment-textarea"><textarea  class="reply" cols="30" rows="3"></textarea></div>
									<div class="textarea-control clearfix">
										<div class="comment-submit f-right"><input type="button" value="�ظ�����" class="reply_submit" id='.$li['id'].' company_id="'.$li['company_id'].'"/></div>
									</div>
								</div>';
				}
			}
			$html.='</div>';
		}
		$html.='</div>';
		if($company_profile['comment']>3)
		{
			$html.="<div class=\"comment_more\"><span>���ظ���...</span></div>";
		}
	}
	else
	{
		$html.='<div style="text-align: center;padding: 10px 0;">Ŀǰ��û���˷������ۣ�</div>';
		$html.="<div class=\"comment_more\" style=\"display:none\"><span>���ظ���...</span></div>";
	}
	$html.="<script type=\"text/javascript\">";
	$html.="$(document).ready(function()";
	$html.="{";
	$html.="$(\"#content\").focus(function(){";
	$html.="if ($(\"#content\").val()==\"�������������Է���\")";
	$html.="{";
	$html.="$(\"#content\").val('');";
	$html.="} "; 
	$html.="});";
	$html.="$(\"#submitcomment\").click(function(){";
	$html.="$(\"#posterr\").html('');";
	$html.="var content=$(\"#content\").val();";
	$html.="var postcaptcha=$(\"#postcaptcha\").val();";
	$html.="if (content=='' || content=='�������������Է���')";
	$html.="{";
	$html.="$(\"#posterr\").html('����д����');";
	$html.="$(\"#content\").focus();";
	$html.="}";
	$html.="else if (content.length<5 || content.length>300)";
	$html.="{";
	$html.="$(\"#posterr\").html('���ݳ���Ϊ����Ϊ5-300���ַ���');";
	$html.="}";
	if ($captcha['verify_comment']=='1')
	{
		$html.="else if (postcaptcha=='' || postcaptcha=='�����ȡ��֤��')";
		$html.="{";
		$html.="$(\"#posterr\").html('����д��֤��');";
		$html.="}";
	}
	$html.="else";
	$html.="{";
	$html.="$(\"#submitcomment\").val('�ύ��').attr(\"disabled\",\"disabled\");";
	$html.="$.post(\"{$_CFG['site_dir']}plus/ajax_comment.php\", {\"content\": $(\"#content\").val(),\"postcaptcha\": $(\"#postcaptcha\").val(),\"company_id\": \"{$company_profile['id']}\",\"time\": new Date().getTime(),\"act\":\"comment_save\"},";
	$html.="function (data,textStatus)";
	$html.="{";
	$html.="if (data==\"err\" || data==\"errcaptcha\" || data==\"err1\")";
	$html.="{";		
	$html.="$(\"#submitcomment\").val('�ύ').attr(\"disabled\",\"\");";
	$html.="$(\"#posterr\").html('');";
	$html.="if (data==\"err\")";
	$html.="{";
	$html.="errinfo=\"����ʧ�ܣ�\";";
	$html.="}";
	$html.="else if(data==\"errcaptcha\")";
	$html.="{";
	$html.="$(\"#imgdiv img\").attr(\"src\",$(\"#imgdiv img\").attr(\"src\")+\"1\");";
	$html.="errinfo=\"��֤�����\";";
	$html.="}";
	$html.="if (data==\"err1\")";
	$html.="{";
	$html.="errinfo=\"�����췢������۹��࣬�����������԰ɣ�\";";
	$html.="}";
	$html.="$(\"#posterr\").html(errinfo);";
	$html.="}";
	$html.="else";
	$html.="{";
	$html.="$(\"#submitcomment\").val('�ύ').attr(\"disabled\",\"\");";
	$html.="$(\"#countcomment\").html(data);";
	$html.="$(\"#content\").val('');";
	$html.="$(\"#postcaptcha\").val('�����ȡ��֤��');";
	$html.="$(\"#imgdiv img\").attr(\"src\",$(\"#imgdiv img\").attr(\"src\")+\"1\");";
	$html.="var tsTimeStamp= new Date().getTime();";
	$html.="$.get(\"{$_CFG['site_dir']}plus/ajax_comment.php\", { \"id\": \"{$company_profile['id']}\",\"time\":tsTimeStamp,\"act\":\"show_list\",\"offset\":\"0\",\"rows\":\"3\"},";
	$html.="function (data,textStatus)";
	$html.="{";
	$html.="$(\"#comment_list_box\").html(data);";
	$html.="$(\".comment_more\").show();";
	$html.="$(\".noinfo\").remove();";
	$html.="$(\".comment_more span\").html('���ظ���...');";
	$html.="}";
	$html.=");";

	if($_CFG['open_commentaudit']=='1'){
		$html.="alert('����ɹ�,���������');";
	}else{
		$html.="alert('����ɹ�');";
	}

	$html.="}";
	$html.="})	";
	$html.="}";
	$html.="});";
	//���ظ���
	$html.="$(\".comment_more\").bind('click',function(){";
	$html.="$(\".comment_more\").show();";
	$html.="$(\".comment_more span\").html('���ڼ��أ����Ժ�...');";
	$html.="var offset=$(\"#comment_list_box div[class='comment-item']:last-child\").attr('id');";
	$html.="offset=parseInt(offset.substr(3));";
	$html.="var tsTimeStamp= new Date().getTime();";
	$html.="$.get(\"{$_CFG['site_dir']}plus/ajax_comment.php\", { \"id\": \"{$company_profile['id']}\",\"time\":tsTimeStamp,\"act\":\"show_list\",\"offset\":offset+3,\"rows\":\"3\"},";
	$html.="function (data,textStatus)";
	$html.="{";
	$html.="if (data=='empty!')";
	$html.="{";
	$html.="$(\".comment_more span\").html('�Ѽ����������ݣ�');";
	$html.="}";
	$html.="else";
	$html.="{";
	$html.="$(\".comment_more span\").html('���ظ���...');";
	$html.="$(\"#comment_list_box\").append(data);";
	// if($_SESSION['uid']==$company_profile['uid'])
	// {
	// $html.="$(\".comment-control\").append('<a href=\"javascript:;\" class=\"control-item f-left talk\">�ظ�</a>');";
	// }
	$html.="}";
	$html.="}";
	$html.=");";
	$html.="});";
	$html.="});";
	$html.="</script>";
	exit($html);
}
elseif ($act=='comment_save')
{
	if ($_SESSION['uid']=='' || $_SESSION['username']=='' || intval($_SESSION['uid'])===0)
	{
	exit('err');
	}
	$today=mktime(0,0,0,date("m"),date("d"),date("Y"));
	$n=$db->get_total("SELECT COUNT(*) AS num FROM ".table('comment')." WHERE uid='".intval($_SESSION['uid'])."' AND addtime>{$today}");
	if($n>=30)
	{
	 exit('err1');
	}
	$captcha=get_cache('captcha');
	if ($captcha['verify_comment']=="1")
	{
			$postcaptcha=$_POST['postcaptcha'];
			if ($captcha['captcha_lang']=="cn" && strcasecmp(QISHI_DBCHARSET,"utf8")!=0)
			{
			$postcaptcha=iconv("utf-8",QISHI_DBCHARSET,$postcaptcha);
			}
			if (empty($postcaptcha) || empty($_SESSION['imageCaptcha_content']) || strcasecmp($_SESSION['imageCaptcha_content'],$postcaptcha)!=0)
			{
			unset($_SESSION['imageCaptcha_content']);
			exit("errcaptcha");
			}
			unset($_SESSION['imageCaptcha_content']);
	}
	$setsqlarr['content']=trim($_POST['content']);
	if (strcasecmp(QISHI_DBCHARSET,"utf8")!=0)
	{
	$setsqlarr['content']=utf8_to_gbk($setsqlarr['content']);
	}
	$setsqlarr['uid']=intval($_SESSION['uid']);
	$setsqlarr['company_id']=intval($_POST['company_id']);
	$setsqlarr['addtime']=time();

	if($_CFG['open_commentaudit']=='1'){
		$setsqlarr['audit']=3;//�����
	}else{
		$setsqlarr['audit']=1;//û�п���ְλ������ˣ�ֱ�����ͨ��
	}

	$db->inserttable(table('comment'),$setsqlarr);
	$sql="update ".table('company_profile')." set comment=comment+1 WHERE id='{$setsqlarr['company_id']}'  LIMIT 1";
	$db->query($sql);
	$company_profile=$db->getone("select comment from ".table('company_profile')." where id='{$setsqlarr['company_id']}' LIMIT 1");
	exit($company_profile['comment']);
}
elseif ($act=='show_list')
{
	$commenthtml="";
	$rows=intval($_GET['rows']);
	$offset=intval($_GET['offset']); 
	$id=intval($_GET['id']); 
	$company_profile=$db->getone("select * from ".table('company_profile')." where id='{$id}' LIMIT 1");
	$commentarray=$db->getall("select  c.*,m.username,m.avatars from ".table('comment')." as c LEFT JOIN  ".table('members')." AS m ON c.uid=m.uid where company_id = '{$id}' and c.audit=1 ORDER BY c.id DESC LIMIT {$offset},{$rows}");
	if (!empty($commentarray) && $offset<=100)
	{
		foreach($commentarray as $li)
		{
			++$num;
			$li['username'] = cut_str($li['username'],1,0,'****');
			if($li['avatars'])
			{
				$avatars='<div class="logoimg"><img src="'.$_CFG['site_dir'].'data/avatar/48/'.$li['avatars'].'"></div>';
			}
			else
			{
				$avatars='<div class="logoimg"><img src="'.$_CFG['site_dir'].'data/avatar/no_photo.gif"></div>';
			}
			if($li['reply'])
			{
				$hf_a='<a href="javascript:;" class="control-item f-left more-talk">��ҵ�ظ�</a>';
			}
			else
			{
				if($_SESSION['uid']==$company_profile['uid'])
				{
					$hf_a='<a href="javascript:;" class="control-item f-left talk">�ظ�</a>';
				}else
				{
					$hf_a='';
				}
			}
			$commenthtml.='<div class="comment-item" id="li-'.$offset.'">
								'.$avatars.'
								<div class="comment-title"><a>'.$li['username'].'</a><span>'.$num.'¥</span><span>'.date('Y-m-d H:i',$li['addtime']).'</span></div>
								<div class="comment-content">'.$li['content'].'</div>
								<div class="comment-control clearfix">
									'.$hf_a.'
								</div>';
			//�ظ� ����					
			if($li['reply'])
			{
				$commenthtml.='<div class="talk-again" style="display:block">
									<i class="ta-arrow" style="left:37px"></i>
									<div class="talk-again-item f-left">
										<div class="talk-man clearfix">
											<div class="talk-id f-left"><a>'.$company_profile['companyname'].'</a> �ظ��� <a>'.$li['username'].'</a></div>
											<div class="talk-time f-right">'.date('Y-m-d H:i',$li['reply_time']).'</div>
										</div>
										<div class="talk-content">'.$li['reply'].'</div>
									</div><div class="clear"></div>
								</div>';
			}
			else
			{
				// ��ҵ�Լ�
				if($_SESSION['uid']==$company_profile['uid'])
				{
					$commenthtml.='<div class="comment-block talk-block">
									<div class="comment-textarea"><textarea  class="reply" cols="30" rows="3"></textarea></div>
									<div class="textarea-control clearfix">
										<div class="comment-submit f-right"><input type="button" value="�ظ�����" class="reply_submit" id='.$li['id'].' company_id="'.$li['company_id'].'" /></div>
									</div>
								</div>';
				}
			}
			$commenthtml.='</div>';
		}
		exit($commenthtml);
	}
	else
	{
		exit('empty!');
	}
}
// ��ҵ�ظ�
elseif($act == "company_reply")
{
	$comment_id=$_POST['comment_id']?intval($_POST['comment_id']):exit("����ID��ʧ��");
	$company_id=$_POST['company_id']?intval($_POST['company_id']):exit("��ҵID��ʧ��");
	$reply=$_POST['reply']?trim($_POST['reply']):exit("�ظ����ݲ���Ϊ�գ�");
	$setarr['reply']=iconv("utf-8", "GBK", $reply);
	$setarr['reply_time']=time();
	$db->updatetable(table("comment"),$setarr,array("company_id"=>$company_id,"id"=>$comment_id))?exit("ok"):exit('�ظ�����');
}
?>