<?php
/*
 * 74cms ��ҵ��Ա����ajax������
*/
define('IN_QISHI', true);
require_once(dirname(__FILE__) . '/company_common.php');
require_once(QISHI_ROOT_PATH . 'genv/func_company.php');

if ($act == "company_profile_save_succeed") {
    $tpl = '../../templates/' . $_CFG['template_dir'] . "member_company/ajax_companyprofile_save_succeed_box.htm";
    $contents = file_get_contents($tpl);
    if ($company_profile['map_open'] == '1') {
        $save_msg = '���������Ϳ��Է���ְλ���� <br />';
        $opt_button = '<div class="but130cheng " onclick="javascript:location.href=\'company_jobs.php?act=addjobs\'">����ְλ</div>';
    } else {
        $save_msg = 'Ϊ������ְ�߸�ֱ�۵��˽⹫˾����λ�ã�����ƻ� <br />���Գ���·�ߣ�98%����ҵ�ѿ�ͨ�˵��ӵ�ͼ��';
        $opt_button = '<div class="but130cheng" onclick="javascript:location.href=\'company_info.php?act=company_map_open\'">������ͨ</div>
		<div class="but130hui but_right" onclick="javascript:location.href=\'company_jobs.php?act=addjobs\'">����ְλ</div>';
	}
	$contents=str_replace('{#$save_msg#}',$save_msg,$contents);
	$contents=str_replace('{#$opt_button#}',$opt_button,$contents);
	exit($contents);
}
elseif($act=="user_email"){
	$tpl='../../templates/'.$_CFG['template_dir']."plus/ajax_authenticate_email_box.htm";
	$contents=file_get_contents($tpl);
	$_SESSION['send_email_key']=mt_rand(100000, 999999);
	$contents=str_replace('{#$email#}',$user["email"],$contents);
	$contents=str_replace('{#$site_name#}',$_CFG['site_name'],$contents);
	$contents=str_replace('{#$send_email_key#}',$_SESSION['send_email_key'],$contents);
	$contents=str_replace('{#$notice#}','����ְλ�����ʼ�',$contents);
	$contents=str_replace('{#$site_template#}',$_CFG['site_template'],$contents);
	exit($contents);
}	
elseif($act=="user_mobile"){
	$tpl='../../templates/'.$_CFG['template_dir']."plus/ajax_authenticate_mobile_box.htm";
	$contents=file_get_contents($tpl);
	$_SESSION['send_mobile_key']=mt_rand(100000, 999999);
	$contents=str_replace('{#$mobile#}',$user["mobile"],$contents);
	$contents=str_replace('{#$site_name#}',$_CFG['site_name'],$contents);
	$contents=str_replace('{#$send_mobile_key#}',$_SESSION['send_mobile_key'],$contents);
	$contents=str_replace('{#$notice#}','����ְλ����֪ͨ',$contents);
	$contents=str_replace('{#$site_template#}',$_CFG['site_template'],$contents);
	exit($contents);
}
elseif($act=="old_mobile"){
	$tpl='../../templates/'.$_CFG['template_dir']."plus/ajax_authenticate_old_mobile_box.htm";
	$contents=file_get_contents($tpl);
	$_SESSION['send_mobile_key']=mt_rand(100000, 999999);
	$user["hid_mobile"] = substr($user["mobile"],0,3)."*****".substr($user["mobile"],7,4);
	$contents=str_replace('{#$mobile#}',$user["mobile"],$contents);
	$contents=str_replace('{#$hid_mobile#}',$user["hid_mobile"],$contents);
	$contents=str_replace('{#$send_mobile_key#}',$_SESSION['send_mobile_key'],$contents);
	$contents=str_replace('{#$site_template#}',$_CFG['site_template'],$contents);
	exit($contents);
}
elseif($act=="edit_mobile"){
	$tpl='../../templates/'.$_CFG['template_dir']."plus/ajax_authenticate_edit_mobile_box.htm";
	$contents=file_get_contents($tpl);
	$_SESSION['send_mobile_key']=mt_rand(100000, 999999);
	$contents=str_replace('{#$send_mobile_key#}',$_SESSION['send_mobile_key'],$contents);
	$contents=str_replace('{#$notice#}','����ְλ����֪ͨ',$contents);
	$contents=str_replace('{#$site_template#}',$_CFG['site_template'],$contents);
	exit($contents);
}
elseif($act=="set_promotion"){
	$catid = intval($_GET['catid'])?intval($_GET['catid']):exit("��������");
	$jobid = intval($_GET['jobid'])?intval($_GET['jobid']):exit("��������");
	$uid = intval($_SESSION['uid'])?intval($_SESSION['uid']):exit("��������");
	$jobinfo = get_jobs_one($jobid);
	$promotion = get_promotion_category_one($catid);
	if ($_CFG['operation_mode']=='2')
	{
		$setmeal=get_user_setmeal($uid);//��ȡ��Ա�ײ�
		if($setmeal['endtime']<time() && $setmeal['endtime']<>'0'){
			$end=1;//�ж��ײ��Ƿ���
			$tpl='../../templates/'.$_CFG['template_dir']."member_company/ajax_set_promotion_end.htm";
		}else{
			$data=get_setmeal_promotion($uid,$catid);//��ȡ��Աĳ���ƹ��ʣ�����������������ƣ�������
			$operation_mode=2;
			$tpl='../../templates/'.$_CFG['template_dir']."member_company/ajax_set_setmeal_promotion.htm";
		}
	}
	elseif($_CFG['operation_mode']=='1')
	{
		$points = get_user_points($uid);
		$operation_mode=1;
		$tpl='../../templates/'.$_CFG['template_dir']."member_company/ajax_set_points_promotion.htm";
	}
	elseif($_CFG['operation_mode']=='3')
	{
		$setmeal=get_user_setmeal($_SESSION['uid']);//��ȡ��Ա�ײ�
		if($setmeal['endtime']<time() && $setmeal['endtime']<>'0'){
			if($_CFG['setmeal_to_points']!=1){
				$end=1;//�ж��ײ��Ƿ���
				$tpl='../../templates/'.$_CFG['template_dir']."member_company/ajax_set_promotion_end.htm";
			}else{
				$operation_mode=1;
				$points = get_user_points($uid);
				$tpl='../../templates/'.$_CFG['template_dir']."member_company/ajax_set_points_promotion.htm";
			}
		}else{
			$data=get_setmeal_promotion($uid,$catid);//��ȡ��Աĳ���ƹ��ʣ�����������������ƣ�������
			if($data['num']<1){
				if($_CFG['setmeal_to_points']==1){
					$operation_mode=1;
					$points = get_user_points($uid);
					$tpl='../../templates/'.$_CFG['template_dir']."member_company/ajax_set_points_promotion.htm";
				}else{
					$operation_mode=2;
					$tpl='../../templates/'.$_CFG['template_dir']."member_company/ajax_set_setmeal_promotion.htm";
				}
			}else{
				$operation_mode=2;
				$tpl='../../templates/'.$_CFG['template_dir']."member_company/ajax_set_setmeal_promotion.htm";
			}
		}
	}
    if ($catid == '5') {
        $balance = get_user_can_balance($uid);

        $operation_mode = 1;

        $tpl = '../../templates/' . $_CFG['template_dir'] . "member_company/ajax_set_balance_promotion.htm";
    }
	$contents=file_get_contents($tpl);
	if($end!=1){
		if($catid=="4"){
			$color = get_color();
			$color_list = '<tr>
				<td height="50">ѡ����ɫ��</td>
				<td>
					<div style="position:relateve;">
	             	 	<div id="val_menu" class="input_text_70_bg">��ѡ��</div>	
	             	 	<div class="menu" id="menu1">
		              		<ul style="width:88px;">';
			foreach ($color as $key => $value) {
				$color_list.='<li id="'.$value["id"].'" title="'.$value["value"].'" style="background-color:'.$value["value"].'">&nbsp;</li>';
			}
			$color_list.='</ul>
		              	</div>
		            </div>
		            <input type="hidden" name="val" value="" id="val">
				</td>
				<td></td>
			</tr>';
			$contents=str_replace('{#$color_list#}',$color_list,$contents);
		}else{
			$contents=str_replace('{#$color_list#}',"",$contents);
		}
		$contents=str_replace('{#$jobid#}',$jobid,$contents);
		$contents=str_replace('{#$catid#}',$catid,$contents);
		$contents=str_replace('{#$jobs_name#}',$jobinfo['jobs_name'],$contents);
		$contents=str_replace('{#$promotion_name#}',$promotion['cat_name'],$contents);
		$contents=str_replace('{#$site_template#}',$_CFG['site_template'],$contents);
		if($operation_mode==1){
			if($promotion['cat_minday']=="0"){
				$promotion['cat_minday'] = "������";
			}
			if($promotion['cat_maxday']=="0"){
				$promotion['cat_maxday'] = "������";
			}
			if($promotion['cat_points']=="0"){
				$promotion['cat_points'] = "���";
			}
			$contents=str_replace('{#$user_points#}',$points,$contents);
			$contents=str_replace('{#$points_perday#}',$promotion['cat_points'],$contents);
			$contents=str_replace('{#$points_quantifier#}',$_CFG['points_quantifier'],$contents);
			$contents=str_replace('{#$points_byname#}',$_CFG['points_byname'],$contents);
			$contents=str_replace('{#$cat_minday#}',$promotion['cat_minday'],$contents);
			$contents=str_replace('{#$cat_maxday#}',$promotion['cat_maxday'],$contents);
            if ($catid == 5) {


                $json=json_array($promotion["cp_json"]);

                foreach($json as $key=>$v){

                    $contents = str_replace('{#$'.$key.'#}', $v, $contents);

                }
                $contents = str_replace('{#$cat_notes#}', htmlspecialchars_decode($promotion['cat_notes']), $contents);
                $contents = str_replace('{#$balance#}', $balance, $contents);

            }
		}elseif($operation_mode==2){
			$contents=str_replace('{#$days#}',$data['days'],$contents);
			$contents=str_replace('{#$setmeal_name#}',$setmeal['setmeal_name'],$contents);
			$contents=str_replace('{#$num#}',$data['num'],$contents);
			$contents=str_replace('{#$pro_name#}',$data['name'],$contents);
			$contents=str_replace('{#$cat_minday#}',$promotion['cat_minday'],$contents);
			$contents=str_replace('{#$cat_maxday#}',$promotion['cat_maxday'],$contents);
		}
	}
	exit($contents);
}elseif ($act == "set_date") {
    $catid = intval($_GET['catid']) ? intval($_GET['catid']) : exit("��������");
    $jobid = intval($_GET['jobid']) ? intval($_GET['jobid']) : exit("��������");
    $uid = intval($_SESSION['uid']) ? intval($_SESSION['uid']) : exit("��������");
    $jobinfo = get_jobs_one($jobid);
    $promotion_category = get_promotion_category_one($catid);
    $promotion = get_promotion_one($jobid,$uid,$catid);

    $tpl = '../../templates/' . $_CFG['template_dir'] . "member_company/ajax_set_date_promotion.htm";
    $contents = file_get_contents($tpl);
    if($catid==5){
         $json=json_array($promotion["cp_json"]);
        $block_balance=$json['block_balance'];

        $detail[]="����������".$json["num"]."<br>";
        $detail[]="���Գɹ���".$json["amount"]."<br>";
        $detail[]="��Ƹ������".$json["success_num"]."<br>";
        $detail[]="��Ƹ�ɹ���".$json["success_amount"]."<br>";
        $detail=join(" ",$detail);
        $other='<tr>
            <td height="50">�����</td>
            <td>'.$block_balance.'</td>

        </tr><tr>
            <td height="50">��ϸ��</td>

            <td>'.$detail.'</td>
        </tr>';
    }else{
        $other='<tr>
            <td height="50">�ƹ����ޣ�</td>
            <td>�� '.date("Y-m-d", $promotion["cp_starttime"]).' �� '.date("Y-m-d", $promotion["cp_endtime"]).'</td>

        </tr>';
    }
    $contents = str_replace('{#$cp_val#}', $promotion["cp_val"], $contents);
    $contents = str_replace('{#$other#}', $other, $contents);
    $contents = str_replace('{#$jobs_name#}', $jobinfo['jobs_name'], $contents);
    $contents = str_replace('{#$promotion_name#}', $promotion_category['cat_name'], $contents);



    exit($contents);
} 
elseif ($act == "promotion_add_save") {
    $catid = intval($_POST['catid']) ? intval($_POST['catid']) : exit("��ѡ���ƹ����ͣ�");
    $jobid = intval($_POST['jobid']) ? intval($_POST['jobid']) : exit("ְλid��ʧ��");
    $days = intval($_POST['days']) ? intval($_POST['days']) : exit("����д�ƹ�������");
    $uid = intval($_SESSION['uid']) ? intval($_SESSION['uid']) : exit("UID��ʧ��");

	if($catid==4){
		$val = intval($_POST['val'])?intval($_POST['val']):exit("��ѡ����ɫ��");
		$color = get_color_one($val);
		$val_code = $color['value'];
	}else{
		$val_code = "";
	}
	$jobs=get_jobs_one($jobid,$uid);
	$jobs = array_map("addslashes", $jobs);
	if($jobs['deadline']<time()){
		exit("��ְλ�ѵ��ڣ��������ڣ�");
	}
	if ($jobid>0 && $days>0)
	{
		$pro_cat=get_promotion_category_one($catid);
		if($_CFG['operation_mode']=='3'){
			$setmeal=get_setmeal_promotion($uid,$catid);//��ȡ��Ա�ײ�
			$num=$setmeal['num'];
			if(($setmeal['endtime']<time() && $setmeal['endtime']<>'0') || $num<=0){
				if($_CFG['setmeal_to_points']==1){
					if ($pro_cat['cat_points']>0)
					{
						$points=$pro_cat['cat_points']*$days;
						$user_points=get_user_points($uid);
						if ($points>$user_points)
						{
							exit("���".$_CFG['points_byname']."�������д˴β��������ȳ�ֵ��");
						}else{
							$_CFG['operation_mode']=1;
						}
					}else{
						$_CFG['operation_mode']=1;
					}
				}else{
					exit("����ײ��ѵ��ڻ��ײ���ʣ��{$pro_cat['cat_name']}�������뾡�쿪ͨ���ײ�");
				}
			}else{
				$_CFG['operation_mode']=2;
			}
		}elseif($_CFG['operation_mode']=='1'){
			if ($pro_cat['cat_points']>0)
			{
				$points=$pro_cat['cat_points']*$days;
				$user_points=get_user_points($uid);
				if ($points>$user_points)
				{
				exit("���".$_CFG['points_byname']."�������д˴β��������ȳ�ֵ��");
				}
			}
		}elseif($_CFG['operation_mode']=='2'){
			$setmeal=get_setmeal_promotion($uid,$catid);//��ȡ��Ա�ײ�
			$num=$setmeal['num'];
			if(($setmeal['endtime']<time() && $setmeal['endtime']<>'0') || $num<=0){
				exit("����ײ��ѵ��ڻ��ײ���ʣ��{$pro_cat['cat_name']}�������뾡�쿪ͨ���ײ�");
			}
		}
		$info=get_promotion_one($jobid,$uid,$catid);
		if (!empty($info))
		{
		exit("��ְλ�����ƹ��У���ѡ������ְλ����������");
		}
		$setsqlarr['cp_available']=1;
		$setsqlarr['cp_promotionid']=$catid;
		$setsqlarr['cp_uid']=$uid;
		$setsqlarr['cp_jobid']=$jobid;
		$setsqlarr['cp_days']=$days;
		$setsqlarr['cp_starttime']=time();
		$setsqlarr['cp_endtime']=strtotime("{$days} day");
		$setsqlarr['cp_val']=$val_code;
		if ($setsqlarr['cp_promotionid']=="4" && empty($setsqlarr['cp_val']))
		{
		exit("��ѡ����ɫ��");
		}
			if ($db->inserttable(table('promotion'),$setsqlarr))
			{
				set_job_promotion($jobid,$setsqlarr['cp_promotionid'],$val_code);
				if ($_CFG['operation_mode']=='1' && $pro_cat['cat_points']>0)
				{
					report_deal($uid,2,$points);
					$user_points=get_user_points($uid);
					write_memberslog($uid,1,9001,$_SESSION['username'],"{$pro_cat['cat_name']}��<strong>{$jobs['jobs_name']}</strong>���ƹ� {$days} �죬(-{$points})��(ʣ��:{$user_points})",1,1018,"{$pro_cat['cat_name']}","-{$points}","{$user_points}");
				}elseif($_CFG['operation_mode']=='2'){
					$user_pname=trim($_POST['pro_name']);
					action_user_setmeal($uid,$user_pname); //�����ײ�����Ӧ�ƹ㷽ʽ������
					$setmeal=get_user_setmeal($uid);//��ȡ��Ա�ײ�
					write_memberslog($uid,1,9002,$_SESSION['username'],"{$pro_cat['cat_name']}��<strong>{$jobs['jobs_name']}</strong>���ƹ� {$days} �죬�ײ���ʣ��{$pro_cat['cat_name']}������{$setmeal[$user_pname]}����",2,1018,"{$pro_cat['cat_name']}","-{$days}","{$setmeal[$user_pname]}");//9002���ײͲ���
				}
				write_memberslog($uid,1,3004,$_SESSION['username'],"{$pro_cat['cat_name']}��<strong>{$jobs['jobs_name']}</strong>���ƹ� {$days} �졣");
				exit('�ƹ�ɹ���');
			}
	}
	else
	{
	exit("�ƹ�ʧ�ܣ�");
	}
}

elseif ($act == "reward_add_save") {
    //������Ƹ���
    $_POST = $_GET;
    $catid = intval($_POST['catid']) ? intval($_POST['catid']) : exit("��ѡ���ƹ����ͣ�");
    $jobid = intval($_POST['jobid']) ? intval($_POST['jobid']) : exit("ְλid��ʧ��");
    $interview_num = intval($_POST['interview_num']) ? intval($_POST['interview_num']) : exit("����д����������");
    $interview_money = intval($_POST['interview_money']) ? intval($_POST['interview_money']) : exit("����д���Խ�");
    $interview_success_num = intval($_POST['interview_success_num']) ? intval($_POST['interview_success_num']) : exit("����Ƹ������");
    $interview_success_money = intval($_POST['interview_success_money']) ? intval($_POST['interview_success_money']) : exit("����д���Գɹ���");


    $uid = intval($_SESSION['uid']) ? intval($_SESSION['uid']) : exit("UID��ʧ��");
    $val_code = "";
    //���ý��
    $can_balance = get_user_can_balance($uid);

    $jobs = get_jobs_one($jobid, $uid);
    $jobs = array_map("addslashes", $jobs);

    if ($jobs['deadline'] < time()) {
        exit("��ְλ�ѵ��ڣ��������ڣ�");
    }
    if ($jobid > 0) {
        $pro_cat = get_promotion_category_one($catid);

        $json=json_array($pro_cat["cp_json"]);

        if ($json["num"] > $interview_num) {
            exit("������������");
        }
        if ($json["amount"] > $interview_money) {
            exit("���Խ������");
        }
        if ($json["success_num"]> $interview_success_num) {
            exit("��Ƹ��������");
        }
        if ($json["success_amount"]> $interview_success_money) {
            exit("��Ƹ�ɹ��������");
        }
        $block_balance = $interview_num * $interview_money + $interview_success_num*$interview_success_money;


        if ($block_balance > $can_balance) {
            exit("���㣡");
        }

        $info = get_promotion_one($jobid, $uid, $catid);
        if (!empty($info)) {
            exit("��ְλ�����ƹ��У���ѡ������ְλ����������");
        }
        $setsqlarr['cp_available'] = 1;
        $setsqlarr['cp_promotionid'] = $catid;
        $setsqlarr['cp_uid'] = $uid;
        $setsqlarr['cp_jobid'] = $jobid;


        $json=array();
        $json["num"]=$interview_num;
        $json["amount"]=$interview_money;
        $json["success_num"]=$interview_success_num;
        $json["success_amount"]=$interview_success_money;
        $json["block_balance"]=$block_balance;

        $setsqlarr['cp_json'] = json_encode($json);
        $db->inserttable(table('promotion'), $setsqlarr);
//        $setsqlarr['addtime'] = time();
//        $setsqlarr['interview_num'] = $interview_num;
//        $setsqlarr['interview_money'] = $interview_money;
//        $setsqlarr['interview_success_money'] = $interview_success_money;
//
//        if ($db->inserttable(table('jobs_reward'), $setsqlarr)) {
            //��ע�����ƹ�
            set_job_reward($jobid, $setsqlarr['cp_promotionid'], $val_code);
            //�������
            block_balance_reward($uid, $block_balance);
            $can_balance = get_user_can_balance($uid);
            write_memberslog($uid, 1, 9200, $_SESSION['username'], "{$pro_cat['cat_name']}��<strong>{$jobs['jobs_name']}</strong>�����ͼ������� {$block_balance} ��(����:{$can_balance})", 1, 1018, "{$pro_cat['cat_name']}", "-{$block_balance}", "{$can_balance}");
            exit('�ƹ�ɹ���');

    } else {
        exit("�ƹ�ʧ�ܣ�");
    }
}


//��ע ���ͼƬ
elseif($act=='img_title')
{
	global $_CFG;
	$id = intval($_GET['id']);
	$uid = intval($_SESSION['uid']);
	$img =  $db->getone("SELECT * FROM ".table('company_img')." WHERE uid ='{$uid}' AND id='{$id}' LIMIT 1");
	$tpl='../../templates/'.$_CFG['template_dir']."member_company/ajax_set_img_title.htm";
	$contents=file_get_contents($tpl);
	$contents=str_replace('{#$id#}',$id,$contents);
	$contents=str_replace('{#$title#}',$img['title'],$contents);
	$contents=str_replace('{#$addtime#}',date('Y-m-d',$img['addtime']),$contents);
	$contents=str_replace('{#$site_template#}',$_CFG['site_template'],$contents);
	exit($contents);
}
//���� ��ע ���ͼƬ
elseif($act=='img_title_save')
{
	$id = intval($_POST['id'])?intval($_POST['id']):exit("-1");
	$title = trim($_POST['title'])?utf8_to_gbk($_POST['title']):exit("-1");
	if($db->query("update  ".table('company_img')." SET title='{$title}'  WHERE id='{$id}'  LIMIT 1"))
	{
		exit('1');
	} 
	else
	{
		exit('2');
	}
}
//��������
elseif($act=='order_detail')
{
	$uid = intval($_SESSION['uid']);
	$order_id = intval($_GET['order_id'])?intval($_GET['order_id']):exit("������Ŷ�ʧ��");
	$order =  $db->getone("SELECT * FROM ".table('order')." WHERE uid ='{$uid}' AND id='{$order_id}' LIMIT 1");
	$tpl='../../templates/'.$_CFG['template_dir']."member_company/ajax_order_detail.htm";
	$contents=file_get_contents($tpl);
	$contents=str_replace('{#$order_oid#}',$order['oid'],$contents);
	$contents=str_replace('{#$order_addtime#}',date('Y-m-d',$order['addtime']),$contents);
	if($order['is_paid']=='1')
	{
		$contents=str_replace('{#$order_is_paid#}','δ���',$contents);
		$button = '<a href="?act=payment&order_id={#$order_id#}"><input type="button" value="֧��" class="btn-65-30blue btn-big-font" /></a>';
		$contents=str_replace('{#$button#}',$button,$contents);
	}
	else
	{
		$contents=str_replace('{#$order_is_paid#}','��֧��',$contents);
		$button = '<input type="button" value="��֧��" class="btn-65-30blue btn-big-font" />';
		$contents=str_replace('{#$button#}',$button,$contents);
	}
	$contents=str_replace('{#$order_des#}',$order['description'],$contents);
	if($order['payment_name']!='points')
	{
		$contents=str_replace('{#$order_amount#}','��'.$order['amount'],$contents);
	}
	else
	{
		$contents=str_replace('{#$order_amount#}','�һ�'.$order['amount'].'����',$contents);
	}
	$contents=str_replace('{#$order_payname#}',get_payment_info($order['payment_name'],ture),$contents);
	if($order['notes'])
	{
		$contents=str_replace('{#$order_note#}',$order['notes'],$contents);
	}
	else
	{
		$contents=str_replace('{#$order_note#}',"��",$contents);
	}
	$contents=str_replace('{#$order_id#}',$order['id'],$contents);
	exit($contents);
}
//  �������͵�����
elseif($act == "sendtoemail")
{
	global $_CFG;
	$uid=intval($_GET['uid']);
	$resume_id=intval($_GET['resume_id']);
	$email=trim($_GET['email']);
	$resume_basic=get_resume_basic($resume_id);
	if($resume_basic['tag_cn'])
	{
		$resume_tag=explode(',',$resume_basic['tag_cn']);
		$tag_str='<p>';
		foreach ($resume_tag as $value)
		{
			$tag_str.='<span style="color: #656565;display:inline-block;background-color: #f2f4f7; border: 1px solid #d6d6d7;text-align: center;height:30px;line-height: 30px;margin-right:10px;padding:0 10px">'.$value.'</span>';
		}
		$tag_str.='</p>';
	}
	$resume_work=get_resume_work($uid,$resume_id);
	$show_contact = false;
	if($_CFG['showapplycontact']=='1' || $_CFG['showresumecontact']=='0')
	{
		$show_contact = '<p>�ֻ����룺'.$resume_basic["telephone"].' �������䣺'.$resume_basic["email"].'</p>';
	}
	else
	{
		$show_contact = '<p>��ϵ��ʽ��<a href='.url_rewrite('QS_resumeshow',array('id'=>$resume_id)).'>����鿴</a></p>';
	}	
	$htm='<div style="width: 900px;margin: 0 auto;font-size: 14px;">
		<div style="margin-bottom:10px">
			<div style="float: left;"><a href="'.$_CFG['site_dir'].'"><img src="'.$_CFG['site_domain'].$_CFG['upfiles_dir'].$_CFG['web_logo'].'" alt="'.$_CFG['site_name'].'" border="0" align="absmiddle" width=180 height=50 /></div>
			<div style="float: right;padding-top:10px;">'.$templates.'����ʱ�䣺'.date("Y-m-d",$resume_basic["refreshtime"]).'</div>
			<div style="clear:both"></div>
		</div>
		<div style="padding-bottom: 10px;">
			<span style="font-size: 18px;font-weight: 700;">'.$resume_basic["fullname"].'</span><span>��'.$resume_basic["sex_cn"].'��'.$resume_basic["age"].'��</span>
			<p>ѧ����'.$resume_basic["education_cn"].' | רҵ��'.$resume_basic["major_cn"].' | �������飺'.$resume_basic["experience_cn"].'�� | �־�ס�أ�'.$resume_basic["residence"].'</p>

			'.$show_contact.$tag_str.'

		</div>
		<div style="padding-bottom: 10px;">
			<p style="font-size: 16px;font-weight: 700;">��ְ����</p>
			<p>����ְλ��'.$resume_basic["intention_jobs"].'</p>
			<p>����н�ʣ�'.$resume_basic["wage_cn"].'</p>
			<p>����������'.$resume_basic["district_cn"].'</p>
		</div>
		<div style="padding-bottom: 10px;">
			<p style="font-size: 16px;font-weight: 700;">��������</p>';
				if(!empty($resume_work))
				{
					foreach ($resume_work as $value)
					{
						$htm.='<div>
								<p style="font-size: 14px;font-weight: 700;">'.$value["companyname"].'</p>
								<p>'.$value["startyear"].'��'.$value["startmonth"].'��-'.$value["endyear"].'��'.$value["endmonth"].'�� '.$value["jobs"].'</p>
								<div style="float: left;width: 100px;">�������ݣ�</div>
								<div style="float: right;width: 800px;">'.$value["achievements"].'</div>
								<div style="clear:both"></div>
							</div>'	;
					}
				}
				else
				{
					$htm.='<div>
								û����д��������
							</div>'	;
				}
				
		$htm.='</div>';
		if($resume_basic["specialty"])
		{
			$htm.='<div style="padding-bottom: 10px;">
				<p style="font-size: 16px;font-weight: 700;">��������</p>
				<p>'.$resume_basic["specialty"].'</p>
			</div>';
		}
		$htm.='<div style="text-align: center;margin-top:20px">
				�ü�������<a href="'.$_CFG["site_domain"].$_CFG["site_dir"].'">'.$_CFG["site_name"].'</a>
			</div>
		</div>';
		$rst=smtp_mail($_GET['email'],"{$resume_basic['fullname']}�ļ���",$htm);
		exit($rst);
}
// ԤԼ����
elseif($act == "auto_refresh")
{
	global $db;
	$id=$_GET['id']?intval($_GET['id']):exit('ID��ʧ��');
	$user_points=get_user_points($_SESSION['uid']);
	$row=$db->getone("select * from ".table("jobs")." where id=$id and uid=$_SESSION[uid] limit 1 ");
	/*  ԤԼˢ��  */
	if($row['auto_refresh']==1)
	{
		$auto_refresh=$db->getone('select appointment_time,appointment_time_available,points,execute_time from '.table("jobs_appointment_refresh")." where jobs_id=$row[id] limit 1");
		$row['auto_refresh_num_all']=$auto_refresh['appointment_time'];
		$row['auto_refresh_num']=$auto_refresh['appointment_time_available'];
		$row['points']=$auto_refresh['points'];
		$row['points_day']=$auto_refresh['points']/=$auto_refresh['appointment_time'];
		$taday=date('Y-m-d');
		$is_taday=date("Y-m-d",$auto_refresh['execute_time']);
		if($taday==$is_taday)
		{
			$row['auto_refresh_day']="�����Ѿ�Ϊ���Զ�ˢ��ְλ";
		}
		else
		{
			$row['auto_refresh_day']="���컹û��Ϊ���Զ�ˢ��ְλ";
		}
	}

	$htm='<div class="yuyue-one-dialog">
				<div class="yo-block">
					<div class="short-text-tip" style="margin-left:0">�����˻�ʣ�� <span>'.$user_points.' ����,</span> '.$row['auto_refresh_day'].',������Ϊ���Զ�ˢ��'.$row['auto_refresh_num'].'�Ρ�</div>
					<div class="yue-one-item">
						<span class="yo-type">ԤԼְλ��</span><font>'.$row['jobs_name'].'</font>
					</div>
					<div class="yue-one-item">
						<span class="yo-type">ˢ�´�����</span>1��/��
					</div>
					<div class="yue-one-item">
						<span class="yo-type">ԤԼ������</span>'.$row['auto_refresh_num_all'].' ��
					</div>
					<div class="yue-one-item">
						<span class="yo-type">���Ļ��֣�</span> '.$row['points'].'��<span class="use-fen">��ÿ������'.$row['points_day'].'���֣�</span>
					</div>
				</div>
			</div>';
	exit($htm);
}
// ������������
elseif($act == "interview_detail")
{
	global $db;
	$did=$_GET['did']?intval($_GET['did']):exit('ID��ʧ��');
	$interview_info = $db->getone("SELECT * FROM ".table('company_interview')." WHERE did=".$did." LIMIT 1 ");
	if($interview_info)
	{
		if(empty($interview_info['notes']))
		{
			$interview_info['notes'] = '��֪ͨ���ݣ�';
		}
		$htm = '<div class="interview-dialog dialog-block">
					<div class="dialog-item clearfix">
						<div class="d-type f-left">���������</div>
						<div class="d-content f-left">'.$interview_info['resume_name'].'</div>
					</div>
					<div class="dialog-item clearfix">
						<div class="d-type f-left">����ְλ��</div>
						<div class="data-filter d-content f-left">
							<div class="dropdown">'.$interview_info['jobs_name'].'</div>
				            <input type="hidden" name="jobsid" value="" id="jobsid">
						</div>
					</div>
					<div class="dialog-item clearfix">
						<div class="d-type f-left">�������ڣ�</div>
						<div class="d-content f-left">'.date('Y-m-d',$interview_info['interview_addtime']).'</div>
					</div>
					<div class="dialog-item clearfix">
						<div class="d-type f-left">֪ͨ���ݣ�</div>
						<div class="d-content f-left">'.$interview_info['notes'].'</div>
					</div>
				</div>';
		exit($htm);
	}
	else
	{
		exit('�޴����ݣ�');
	}
	
}
elseif($act == 'check_weixinpay_notify'){
	if(file_exists(QISHI_ROOT_PATH.'data/wxpay/'.$_SESSION['wxpay_no'].'.tmp')){
		exit('1');
	}else{
		@unlink(QISHI_ROOT_PATH.'data/wxpay/'.$_SESSION['wxpay_no'].'.tmp');
		unset($_SESSION['wxpay_no']);
		exit($_CFG['site_dir'].'user/company/company_service.php?act=order_list');
	}
}
// ְλˢ��ajax
elseif($act == "jobs_refresh_ajax")
{
	global $db,$_CFG;
	$jobsid=$_GET['jobsid']?intval($_GET['jobsid']):exit('ְλID��ʧ��');
	$jobs_info = get_jobs_one($jobsid,$_SESSION['uid']);
	if($jobs_info['deadline']<time())
	{
		exit('<div class="del-dialog"><div class="tip-block"><span class="del-tips-text">��ְλ�ѵ��ڣ�</span></div></div>');
	}
	if($_CFG['operation_mode']=='1')
	{
		$mode = 1;
	}
	elseif($_CFG['operation_mode']=='2')
	{
		$mode = 2;
	}
	elseif($_CFG['operation_mode']=='3') 
	{
		$setmeal=get_user_setmeal($_SESSION['uid']);
		//�û�Ա�ײ͹��� (�ײ͹��ں���û�����ˢ)
		if($setmeal['endtime']<time() && $setmeal['endtime']<>"0")
		{
			//��̨��ͨ  ������ʱ���û�������
			if($_CFG['setmeal_to_points']=='1')
			{
				$mode = 1;
			}
			//��̨û�п�ͨ  ������ʱ���û�������
			else
			{
				exit('<div class="del-dialog"><div class="tip-block"><span class="del-tips-text">���ķ����Ѿ����ڣ������¿�ͨ</span></div></div>');
			}
		}
		//�û�Ա�ײ�δ���� 
		else
		{
			$points_rule=get_cache('points_rule');
			$user_points=get_user_points($_SESSION['uid']);
			//��ȡ����ˢ�µ�ְλ��(���ײ�ģʽ��ˢ�µ�)
			$refresh_time = get_today_refresh_times($_SESSION['uid'],"1001",2);
			//����ʣ��ˢ��ְλ��(���ײ�ģʽ��ˢ�µ�)
			$surplus_time =  $setmeal['refresh_jobs_time'] - $refresh_time['count(*)'];
			//ˢ��ְλ�� ���� ʣ��ˢ��ְλ�� (����)
			if($surplus_time <= 0)
			{
				//��̨��ͨ  ������ʱ���û�������
				if($_CFG['setmeal_to_points']=='1')
				{
					$mode = 1;
				}
				else
				{
					exit('<div class="del-dialog"><div class="tip-block"><span class="del-tips-text">���ķ����Ѿ����ڣ������¿�ͨ</span></div></div>');
				}
			}
			else
			{
				$mode = 2;
			}
		}
	}
	//����ģʽ
	if($mode=='1')
	{
		//����ˢ��ʱ��
		//���һ�ε�ˢ��ʱ��
		$refrestime=get_last_refresh_date($_SESSION['uid'],"1001",1);
		$duringtime=time()-$refrestime['max(addtime)'];
		$space = $_CFG['com_pointsmode_refresh_space']*60;
		$refresh_time = get_today_refresh_times($_SESSION['uid'],"1001",1);
		if($_CFG['com_pointsmode_refresh_time']!=0&&($refresh_time['count(*)']>=$_CFG['com_pointsmode_refresh_time']))
		{
			exit('<div class="del-dialog"><div class="tip-block"><span class="del-tips-text" style="line-height:30px;">��Ǹ������ˢ�´����Ѵ����ޣ���Ҫ���˿죿 <a href="company_jobs.php?act=jobs" class="underline">ְλ�ƹ�</a> Ч������5����</span></div></div>');	
		}
		elseif($duringtime<=$space)
		{
			exit('<div class="del-dialog"><div class="tip-block"><span class="del-tips-text">'.$_CFG['com_pointsmode_refresh_space'].'�����ڲ����ظ�ˢ��ְλ��</span></div></div>');
		}
		else 
		{
			$points_rule=get_cache('points_rule');
			if($points_rule['jobs_refresh']['value']>0)
			{
				$user_points=get_user_points($_SESSION['uid']);
				$total_point=1*$points_rule['jobs_refresh']['value'];
				if ($total_point>$user_points && $points_rule['jobs_refresh']['type']=="2")
				{
					exit('<div class="del-dialog"><div class="tip-block"><span class="del-tips-text" style="line-height:30px;">��Ǹ������ʣ��'.$_CFG['points_byname'].'���㣬�޷����д˲����������� <a href="company_service.php?act=order_add" class="underline">��ֵ</a>��</span></div></div>');
				}
				else
				{
					// ���޴���
					if($_CFG['com_pointsmode_refresh_time']==0)
					{
						exit('<div class="del-dialog"><div class="tip-block"><span class="del-tips-text" style="line-height:30px;">����ˢ����Ҫ����<span style="color:#ff9900;">'.$points_rule['jobs_refresh']['value'].'</span>'.$_CFG['points_byname'].'��ȷ��Ҫˢ����</span></div></div><div class="center-btn-wrap"><input type="button" value="ȷ��" class="btn-65-30blue btn-big-font DialogSubmit" /><input type="button" value="ȡ��" class="btn-65-30grey btn-big-font DialogClose" /></div>');
					}
					else
					{
						//ʣ�����
						$surplus = intval($_CFG['com_pointsmode_refresh_time']) - intval($refresh_time['count(*)']);
						exit('<div class="del-dialog"><div class="tip-block"><span class="del-tips-text" style="line-height:30px;">���컹���û���ˢ��<span style="color:#ff9900;">'.$surplus.'</span>�Σ�����ˢ����Ҫ����<span style="color:#ff9900;">'.$points_rule['jobs_refresh']['value'].'</span>'.$_CFG['points_byname'].'��ȷ��Ҫˢ����</span></div></div><div class="center-btn-wrap"><input type="button" value="ȷ��" class="btn-65-30blue btn-big-font DialogSubmit" /><input type="button" value="ȡ��" class="btn-65-30grey btn-big-font DialogClose" /></div>');
					}
				}
			}
			else
			{
				// ���޴���
				if($_CFG['com_pointsmode_refresh_time']==0)
				{

					exit('<div class="del-dialog"><div class="tip-block"><span class="del-tips-text">ȷ��Ҫˢ����</span></div></div><div class="center-btn-wrap"><input type="button" value="ȷ��" class="btn-65-30blue btn-big-font DialogSubmit" /><input type="button" value="ȡ��" class="btn-65-30grey btn-big-font DialogClose" /></div>');
				}
				else
				{
					//ʣ�����
					$surplus = intval($_CFG['com_pointsmode_refresh_time']) - intval($refresh_time['count(*)']);
					exit('<div class="del-dialog"><div class="tip-block"><span class="del-tips-text" style="line-height:30px;">���컹���û���ˢ��<span style="color:#ff9900;">'.$surplus.'</span>�Σ�ȷ��Ҫˢ����</span></div></div><div class="center-btn-wrap"><input type="button" value="ȷ��" class="btn-65-30blue btn-big-font DialogSubmit" /><input type="button" value="ȡ��" class="btn-65-30grey btn-big-font DialogClose" /></div>');
				}
			}
		}
	}
	//�ײ�ģʽ
	elseif($mode=='2') 
	{
		
		//����ˢ��ʱ��
		$setmeal=get_user_setmeal($_SESSION['uid']);
		if (empty($setmeal))
		{			
			exit('<div class="del-dialog"><div class="tip-block"><span class="del-tips-text">����û�п�ͨ�����뿪ͨ����</span></div></div>');
		}
		elseif ($setmeal['endtime']<time() && $setmeal['endtime']<>"0")
		{			
			exit('<div class="del-dialog"><div class="tip-block"><span class="del-tips-text">���ķ����Ѿ����ڣ������¿�ͨ</span></div></div>');
		}
		else
		{
			//���һ�ε�ˢ��ʱ��
			$refrestime=get_last_refresh_date($_SESSION['uid'],"1001",2);
			$duringtime=time()-$refrestime['max(addtime)'];
			$space = $setmeal['refresh_jobs_space']*60;
			$refresh_time = get_today_refresh_times($_SESSION['uid'],"1001",2);
			if($setmeal['refresh_jobs_time']!=0&&($refresh_time['count(*)']>=$setmeal['refresh_jobs_time']))
			{
				exit('<div class="del-dialog"><div class="tip-block"><span class="del-tips-text" style="line-height:30px;">��Ǹ������ˢ�´����Ѵ����ޣ���Ҫ���˿죿 <a href="company_jobs.php?act=jobs" class="underline">ְλ�ƹ�</a> Ч������5����</span></div></div>');
			}
			elseif($duringtime<=$space)
			{
				exit('<div class="del-dialog"><div class="tip-block"><span class="del-tips-text">'.$setmeal['refresh_jobs_space'].'�����ڲ����ظ�ˢ��ְλ��</span></div></div>');
			}
			else
			{
				//ʣ�����
				$surplus = intval($setmeal['refresh_jobs_time']) - intval($refresh_time['count(*)']);
				exit('<div class="del-dialog"><div class="tip-block"><span class="del-tips-text" style="line-height:30px;">�������ˢ�´���ʣ��<span style="color:#ff9900;">'.$surplus.'</span>�Σ�����ˢ��<span style="color:#ff9900;">���</span>��ȷ��Ҫˢ����</span></div></div><div class="center-btn-wrap"><input type="button" value="ȷ��" class="btn-65-30blue btn-big-font DialogSubmit" /><input type="button" value="ȡ��" class="btn-65-30grey btn-big-font DialogClose" /></div>');
			}
		}
	}
}
// ����ְλˢ��ajax
elseif($act == "jobs_all_refresh_ajax")
{
	global $db,$_CFG;
	$length=$_GET['length']?intval($_GET['length']):exit('<div class="del-dialog"><div class="tip-block"><span class="del-tips-text">��ѡ��ְλ��</span></div></div>');
	if($_CFG['operation_mode']=='1')
	{
		$mode = 1;
	}
	elseif($_CFG['operation_mode']=='2')
	{
		$mode = 2;
	}
	elseif($_CFG['operation_mode']=='3') 
	{
		$setmeal=get_user_setmeal($_SESSION['uid']);
		//�û�Ա�ײ͹��� (�ײ͹��ں���û�����ˢ)
		if($setmeal['endtime']<time() && $setmeal['endtime']<>"0")
		{
			exit('<div class="del-dialog"><div class="tip-block"><span class="del-tips-text">���ķ����Ѿ����ڣ������¿�ͨ</span></div></div>');
		}
		//�û�Ա�ײ�δ���� 
		else
		{
			$points_rule=get_cache('points_rule');
			$user_points=get_user_points($_SESSION['uid']);
			//��ȡ����ˢ�µ�ְλ��(���ײ�ģʽ��ˢ�µ�)
			$refresh_time = get_today_refresh_times($_SESSION['uid'],"1001",2);
			//����ʣ��ˢ��ְλ��(���ײ�ģʽ��ˢ�µ�)
			$surplus_time =  $setmeal['refresh_jobs_time'] - $refresh_time['count(*)'];
			//ˢ��ְλ�� ���� ʣ��ˢ��ְλ�� (����)
			if($setmeal['refresh_jobs_time']!=0&&($length>$surplus_time))
			{
				exit('<div class="del-dialog"><div class="tip-block"><span class="del-tips-text" style="line-height:30px;">������ˢ��ְλ���������ײ�ʣ�������뵥��ְλˢ�²�����</span></div></div>');
			}
			else
			{
				$mode = 2;
			}
		}
	}
	//����ģʽ
	if($mode=='1')
	{
		//����ˢ��ʱ��
		//���һ�ε�ˢ��ʱ��
		$refrestime=get_last_refresh_date($_SESSION['uid'],"1001",1);
		$duringtime=time()-$refrestime['max(addtime)'];
		$space = $_CFG['com_pointsmode_refresh_space']*60;
		$refresh_time = get_today_refresh_times($_SESSION['uid'],"1001",1);
		$surplus_time =  $_CFG['com_pointsmode_refresh_time'] - $refresh_time['count(*)'];
		if($_CFG['com_pointsmode_refresh_time']!=0&&($length>$surplus_time))
		{
			exit('<div class="del-dialog"><div class="tip-block"><span class="del-tips-text" style="line-height:30px;">��Ǹ��������ˢ��ְλ�������˻���ˢ��ʣ�������뵥��ְλˢ�²�����</span></div></div>');	
		}
		elseif($duringtime<=$space)
		{
			exit('<div class="del-dialog"><div class="tip-block"><span class="del-tips-text">'.$_CFG['com_pointsmode_refresh_space'].'�����ڲ����ظ�ˢ��ְλ��</span></div></div>');
		}
		else 
		{
			$points_rule=get_cache('points_rule');
			if($points_rule['jobs_refresh']['value']>0)
			{
				$user_points=get_user_points($_SESSION['uid']);
				$total_point=$length*$points_rule['jobs_refresh']['value'];
				if ($total_point>$user_points && $points_rule['jobs_refresh']['type']=="2")
				{
					exit('<div class="del-dialog"><div class="tip-block"><span class="del-tips-text" style="line-height:30px;">��Ǹ������ʣ��'.$_CFG['points_byname'].'���㣬�޷����д˲����������� <a href="company_service.php?act=order_add" class="underline">��ֵ</a>��</span></div></div>');
				}
				else
				{
					// ���޴���
					if($_CFG['com_pointsmode_refresh_time']==0)
					{
						exit('<div class="del-dialog"><div class="tip-block"><span class="del-tips-text" style="line-height:30px;">����ˢ����Ҫ����<span style="color:#ff9900;">'.$length*$points_rule['jobs_refresh']['value'].'</span>'.$_CFG['points_byname'].'��ȷ��Ҫˢ����</span></div></div><div class="center-btn-wrap"><input type="button" value="ȷ��" class="btn-65-30blue btn-big-font DialogSubmit" /><input type="button" value="ȡ��" class="btn-65-30grey btn-big-font DialogClose" /></div>');
					}
					else
					{
						//ʣ�����
						$surplus = intval($_CFG['com_pointsmode_refresh_time']) - intval($refresh_time['count(*)']);
						exit('<div class="del-dialog"><div class="tip-block"><span class="del-tips-text" style="line-height:30px;">���컹���û���ˢ��<span style="color:#ff9900;">'.$surplus.'</span>�Σ�����ˢ����Ҫ����<span style="color:#ff9900;">'.$length*$points_rule['jobs_refresh']['value'].'</span>'.$_CFG['points_byname'].'��ȷ��Ҫˢ����</span></div></div><div class="center-btn-wrap"><input type="button" value="ȷ��" class="btn-65-30blue btn-big-font DialogSubmit" /><input type="button" value="ȡ��" class="btn-65-30grey btn-big-font DialogClose" /></div>');
					}
				}
			}
			else
			{
				// ���޴���
				if($_CFG['com_pointsmode_refresh_time']==0)
				{

					exit('<div class="del-dialog"><div class="tip-block"><span class="del-tips-text">ȷ��Ҫˢ����</span></div></div><div class="center-btn-wrap"><input type="button" value="ȷ��" class="btn-65-30blue btn-big-font DialogSubmit" /><input type="button" value="ȡ��" class="btn-65-30grey btn-big-font DialogClose" /></div>');
				}
				else
				{
					//ʣ�����
					$surplus = intval($_CFG['com_pointsmode_refresh_time']) - intval($refresh_time['count(*)']);
					exit('<div class="del-dialog"><div class="tip-block"><span class="del-tips-text" style="line-height:30px;">���컹���û���ˢ��<span style="color:#ff9900;">'.$surplus.'</span>�Σ�ȷ��Ҫˢ����</span></div></div><div class="center-btn-wrap"><input type="button" value="ȷ��" class="btn-65-30blue btn-big-font DialogSubmit" /><input type="button" value="ȡ��" class="btn-65-30grey btn-big-font DialogClose" /></div>');
				}
			}
		}
	}
	//�ײ�ģʽ
	elseif($mode=='2') 
	{
		//����ˢ��ʱ��
		$setmeal=get_user_setmeal($_SESSION['uid']);
		if (empty($setmeal))
		{			
			exit('<div class="del-dialog"><div class="tip-block"><span class="del-tips-text">����û�п�ͨ�����뿪ͨ����</span></div></div>');
		}
		elseif ($setmeal['endtime']<time() && $setmeal['endtime']<>"0")
		{			
			exit('<div class="del-dialog"><div class="tip-block"><span class="del-tips-text">���ķ����Ѿ����ڣ������¿�ͨ</span></div></div>');
		}
		else
		{
			//���һ�ε�ˢ��ʱ��
			$refrestime=get_last_refresh_date($_SESSION['uid'],"1001",2);
			$duringtime=time()-$refrestime['max(addtime)'];
			$space = $setmeal['refresh_jobs_space']*60;
			$refresh_time = get_today_refresh_times($_SESSION['uid'],"1001",2);
			$surplus_time =  $setmeal['refresh_jobs_time'] - $refresh_time['count(*)'];
			if($setmeal['refresh_jobs_time']!=0&&($length>$surplus_time))
			{
				exit('<div class="del-dialog"><div class="tip-block"><span class="del-tips-text" style="line-height:30px;">��Ǹ��������ˢ��ְλ���������ײ�ˢ��ʣ�������뵥��ְλˢ�²�����</span></div></div>');	
			}
			elseif($duringtime<=$space)
			{
				exit('<div class="del-dialog"><div class="tip-block"><span class="del-tips-text">'.$setmeal['refresh_jobs_space'].'�����ڲ����ظ�ˢ��ְλ��</span></div></div>');
			}
			else
			{
				//ʣ�����
				$surplus = intval($setmeal['refresh_jobs_time']) - intval($refresh_time['count(*)']);
				
				if($setmeal['refresh_jobs_time']==0){
					exit('<div class="del-dialog"><div class="tip-block">����ˢ��<span style="color:#ff9900;">���</span>��ȷ��Ҫˢ����</span></div></div><div class="center-btn-wrap"><input type="button" value="ȷ��" class="btn-65-30blue btn-big-font DialogSubmit" /><input type="button" value="ȡ��" class="btn-65-30grey btn-big-font DialogClose" /></div>');
				}else{
					exit('<div class="del-dialog"><div class="tip-block"><span class="del-tips-text" style="line-height:30px;">�������ˢ�´���ʣ��<span style="color:#ff9900;">'.$surplus.'</span>�Σ�����ˢ��<span style="color:#ff9900;">���</span>��ȷ��Ҫˢ����</span></div></div><div class="center-btn-wrap"><input type="button" value="ȷ��" class="btn-65-30blue btn-big-font DialogSubmit" /><input type="button" value="ȡ��" class="btn-65-30grey btn-big-font DialogClose" /></div>');
				}
			}
		}
	}
} elseif($act=="get_telephone"){
	require_once(QISHI_ROOT_PATH . 'genv/func_resume_upload.php');

	$id=$_REQUEST["id"];
	$rs = resume_log_not_check();
	$data=array();
	$data["err"]=0;

	if ($rs && $rs->rid != $id) {
		$data["err"]=2;
		$data["data"]=$rs->rid;

	}else{
		$resume = get_resume_temp_basic($id);
		resume_check_log_add($id);
		if($resume){
			$data["data"]=urlencode($resume["telephone"]);
		}else{
			$data["err"]=1;
		}
	}

	echo  urldecode(json_encode($data));
	exit;
}

unset($smarty);
?>