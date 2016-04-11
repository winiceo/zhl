<?php
/**
 * Created by PhpStorm.
 * User: leven
 * Date: 16/1/15
 * Time: ����9:54
 */
require_once(QISHI_ROOT_PATH . '/genv/lib.php');

//�ж������ƹ㷽�����������޸ģ��رգ�ɾ��
function check_job_promotion($jid)
{
    global $db;
    $rs = $db->getone("select * from " . table('promotion') . "   where  cp_jobid={$jid} LIMIT 1");

    return $rs;
}

function get_user_balance($uid)
{
    global $db;
    $uid = intval($uid);
    $points = $db->getone("select balance from " . table('members_points') . " where uid ='{$uid}' LIMIT 1");
    return $points['balance'];
}

//��ȡ�û��������
function get_user_can_balance($uid)
{
    global $db;
    $uid = intval($uid);
    $points = $db->getone("select balance-block_balance as can_balance from " . table('members_points') . " where uid ='{$uid}' LIMIT 1");
    return $points['can_balance'];
}

//�鿴ְλ��û���Ѿ����ƹ�
function get_job_reward_one($jobid, $uid, $promotionid)
{
//    global $db;
//    $jobid=intval($jobid);
//    $sql = "select * from ".table('jobs_reward')." where cp_jobid='{$jobid}' AND cp_uid='{$uid}' AND cp_promotionid='{$promotionid}'  LIMIT 1";
//    return $db->getone($sql);
}

//�鿴ְλ��û���Ѿ����ƹ�
function get_promotion_info($jobid, $promotionid)
{
    global $db;
    $jobid = intval($jobid);
    $sql = "select * from " . table('promotion') . " where cp_jobid='{$jobid}'   AND cp_promotionid='{$promotionid}'  LIMIT 1";
    $rs = $db->getone($sql);

//
//    $sql = "select * from ".table('promotion_category')." where cat_id='{$promotionid}'  LIMIT 1";
//
//    $pcinfo= $db->getone($sql);
//    $json=json_array($pcinfo["cp_json"]);
//
//    $rs["amount_per"]=$json["amount_per"];
//    $rs["jober_success_per"]=$json["jober_success_per"];
//    $rs["amount_success_per"]=$json["amount_success_per"];

    return $rs;


}

function set_job_reward($jobid, $type, $val = '')
{
    global $db;
    $jobid = intval($jobid);
    $db->query("UPDATE " . table('jobs') . " SET reward=1 WHERE id='{$jobid}' LIMIT 1");
    $db->query("UPDATE " . table('jobs_tmp') . " SET reward=1 WHERE id='{$jobid}' LIMIT 1");
    $db->query("UPDATE " . table('jobs_search_rtime') . " SET reward=1 WHERE id='{$jobid}' LIMIT 1");

    return true;

}

//�����ʽ�

function block_balance_reward($uid, $balance = 0)
{
    global $db, $timestamp;
    $balance = intval($balance);
    $uid = intval($uid);
    $balance_can = get_user_can_balance($uid);
    if ($balance_can >= $balance) {
        $sql = "UPDATE " . table('members_points') . " SET block_balance= block_balance+{$balance} WHERE uid='{$uid}' LIMIT 1";
        if (!$db->query($sql)) return false;
        return true;
    }
    return false;

}


//��ҵ�ʽ��춯��1Ϊ���ӣ�2Ϊ���٣�
function balance_deal($uid, $type = 1, $money = 0)
{
    global $db, $timestamp;
    $money = intval($money);
    $uid = intval($uid);
    $balance = get_user_can_balance($uid);
    if ($type == 1) {
        $sql = "UPDATE " . table('members_points') . " SET balance= balance+{$money} WHERE uid='{$uid}' LIMIT 1";
        if (!$db->query($sql)) return false;
    }
    if ($type == 2 && $balance >= $money) {
        $sql = "UPDATE " . table('members_points') . " SET balance= balance-{$money}  WHERE uid='{$uid}' LIMIT 1";
        if (!$db->query($sql)) return false;

        $sql = "UPDATE " . table('members_points') . " SET block_balance=block_balance-{$money} WHERE uid='{$uid}' LIMIT 1";
        $db->query($sql);
    }

    return true;
}

//�����ʽ��춯��1Ϊ���ӣ�2Ϊ���٣�
function balance_deal_person($uid, $type = 1, $money = 0)
{
    global $db, $timestamp;
    $money = intval($money);
    $uid = intval($uid);
    $balance = get_user_balance($uid);

    if ($type == 1) {
        $sql = "select * from " . table('members_points') . " where uid='{$uid}'    LIMIT 1";
        if ($db->getone($sql)) {
            $sql = "UPDATE " . table('members_points') . " SET balance= balance+{$money} WHERE uid='{$uid}' LIMIT 1";
            if (!$db->query($sql)) return false;
        } else {
            $sqlarr['uid'] = $uid;
            $sqlarr['balance'] = $money;

            $db->inserttable(table('members_points'), $sqlarr);
        }
    }
    if ($type == 2 && $balance >= $money) {
        $sql = "UPDATE " . table('members_points') . " SET balance= balance-{$money} WHERE uid='{$uid}' LIMIT 1";
        if (!$db->query($sql)) return false;
    }

    return true;
}


function report_deal_reward($uid, $i_type = 1, $balance = 0)
{
    global $db, $timestamp;
    $balance = intval($balance);
    $uid = intval($uid);
    $points_val = get_user_points($uid);
    if ($i_type == 1) {
        $points_val = $points_val + $points;
    }
    if ($i_type == 2) {
        $points_val = $points_val - $points;
        $points_val = $points_val < 0 ? 0 : $points_val;
    }
    $sql = "UPDATE " . table('members_points') . " SET balance= '{$points_val}' WHERE uid='{$uid}' LIMIT 1";
    if (!$db->query($sql)) return false;
    return true;
}

//�鿴ְλ��û���Ѿ����ƹ�
function get_company_profile($uid)
{
    global $db;
    $uid = intval($uid);
    $sql = "select * from " . table('company_profile') . " where uid='{$uid}'     LIMIT 1";
    return $db->getone($sql);
}

//��ȡ��«�Ҽƻ�
function get_points_plan($order = "id asc ")
{
    global $db;
    $row_arr = array();

    $result = $db->query("SELECT * FROM " . table('company_points') . " order by " . $order . " ");
    while ($row = $db->fetch_array($result)) {

        $row_arr[] = $row;
    }
    return $row_arr;
}


function get_member_check_list($offset, $perpage, $get_sql = '')
{
    global $db;
    $row_arr = array();
    $limit = " LIMIT " . $offset . ',' . $perpage;


    $result = $db->query("SELECT m.*,c.status as check_status,c.id as cid,c.addtime,c.reason,p.companyname FROM " . table('resume_check_apply') . " as c " . $get_sql . $limit);


    while ($row = $db->fetch_array($result)) {

        $company = get_company_profile($row["uid"]);
        $row['company_url'] = url_rewrite('QS_companyshow', array('id' => $company['id']));
        $row['company_name'] = $company['companyname'];

        $row['company_id'] = $row['uid'];
        $address = $db->getone("select log_address,log_id,log_uid from " . table("members_log") . " where log_type = '1000' and log_uid = " . $row['uid'] . " order by log_id asc limit 1");
        $row['ipAddress'] = $address['log_address'];
        $row_arr[] = $row;
    }
    return $row_arr;
}

function get_clue_check_list($offset, $perpage, $get_sql = '')
{
    global $db;
    $row_arr = array();
    $limit = " LIMIT " . $offset . ',' . $perpage;
    $result = $db->query("SELECT * FROM " . table('jobs_reward_clue') . " as m " . $get_sql . $limit);
    while ($row = $db->fetch_array($result)) {
        $row['company_url'] = url_rewrite('QS_companyshow', array('id' => $row['company_id']));
        $row['jobs_url'] = url_rewrite('QS_jobsshow', array('id' => $row['job_id']));

        $row_arr[] = $row;
    }
    return $row_arr;
}

//������Ϣ
function get_clue_one($id)
{
    global $db;
    $id = intval($id);
    $sql = "select * from " . table('jobs_reward_clue') . " where    id='{$id}'  LIMIT 1";
    return $db->getone($sql);
}

function get_member_info($memberuid)
{
    global $db;
    $sql = "select * from " . table('members') . " where uid=" . intval($memberuid) . " LIMIT 1";
    $val = $db->getone($sql);
    return $val;
}


//��UID��ȡ���м���
function get_resume_uid($uid)
{
    global $db;
    $uid = intval($uid);
    $result = $db->query("select * FROM " . table('resume') . " where uid='{$uid}'");
    while ($row = $db->fetch_array($result)) {
        $row['resume_url'] = url_rewrite('QS_resumeshow', array('id' => $row['id']));
        $row_arr[] = $row;
    }
    return $row_arr;
}

//����������־
function get_clue_log_list($id)
{
    global $db;
    $id = intval($id);
    $sql = "select * from " . table('jobs_reward_clue_log') . " where    cid='{$id}' order by id desc  ";
    $result = $db->query($sql);
    while ($row = $db->fetch_array($result)) {
        $row["addtime"] = date("Y-m-d H:i", $row["addtime"]);
        $row_arr[] = $row;
    }
    return $row_arr;
}


function json_array($json)
{
    $json = str_replace('&quot;', '"', trim($json));
    $json = (array)json_decode($json);
    return $json;

}

//���Ӷ���
function admin_add_order($uid, $pay_type, $oid, $amount, $payment_name, $description, $addtime, $points = '', $setmeal = '', $utype = '1')
{
    global $db;
    $setsqlarr['uid'] = intval($uid);
    $setsqlarr['pay_type'] = $pay_type;
    $setsqlarr['oid'] = $oid;
    $setsqlarr['is_paid'] = 2;

    $setsqlarr['amount'] = $amount;
    $setsqlarr['payment_name'] = $payment_name;
    $setsqlarr['description'] = $description;
    $setsqlarr['addtime'] = $addtime;
    $setsqlarr['points'] = $points;
    $setsqlarr['setmeal'] = $setmeal;
    $setsqlarr['utype'] = $utype;
    write_memberslog($uid, 1, 3001, $_SESSION['username'], "��Ӷ��������{$oid}�����{$amount}Ԫ");
//    $userinfo=get_user_info($uid);
//    //sendemail
//    $mailconfig=get_cache('mailconfig');
//    if ($mailconfig['set_order']=="1" && $userinfo['email_audit']=="1" && $amount>0)
//    {
//        global $_CFG;
//        $paymenttpye=get_payment_info($payment_name);
//        dfopen("{$_CFG['site_domain']}{$_CFG['site_dir']}plus/asyn_mail.php?uid={$uid}&key=".asyn_userkey($uid)."&act=set_order&oid={$oid}&amount={$amount}&paymenttpye={$paymenttpye['byname']}");
//    }
//    //sendemail
//    //sms
//    $sms=get_cache('sms_config');
//    if ($sms['open']=="1" && $sms['set_order']=="1"  && $userinfo['mobile_audit']=="1" && $amount>0)
//    {
//        global $_CFG;
//        $paymenttpye=get_payment_info($payment_name);
//        dfopen("{$_CFG['site_domain']}{$_CFG['site_dir']}plus/asyn_sms.php?uid={$uid}&key=".asyn_userkey($uid)."&act=set_order&oid={$oid}&amount={$amount}&paymenttpye={$paymenttpye['byname']}");
//    }
    //sms
    return $db->inserttable(table('order'), $setsqlarr, true);
}

function get_fortune($offset, $perpage, $get_sql = '')
{
    global $db;
    $limit = " LIMIT {$offset},{$perpage}";
    $result = $db->query("SELECT  * FROM " . table('fotrune') . " {$get_sql}  ORDER BY addtime desc  {$limit}");
    $row_arr = array();
    while ($row = $db->fetch_array($result)) {
        $row["addtime"] = date('Y-m-d', $row['addtime']);

        $row_arr[] = $row;
    }
    return $row_arr;
}


///�����˲Ž��״���
//�������;$idΪ��ְ��id
function get_job_resume($clue,$send=0){
    global $db;
    $id=$clue["member_id"];
    $job_id=$clue["job_id"];


    if($id==0||$id==""){
        return false;
    }

    $sql = "select * from " . table('resume') . " where uid=" . intval($id) . " LIMIT 1";
    $resume = $db->getone($sql);

    if(!$resume){
        return false;
    }else{
        if($send==1){
            job_applay($job_id,$resume["id"],$resume["uid"]);
        }
    }
    return true;

}
function job_applay($jobsid,$resumeid,$uid){
    global $db ,$_CFG;
    $userinfo=get_member_info($uid);
    $notes="";
    $pms_notice="";
    $jobsarr=app_get_jobs1($jobsid);
    if (empty($jobsarr))
    {
        exit("ְλ��ʧ");
    }
    $resume_basic=get_resume_basic3($uid,$resumeid);
    $resume_basic = array_map("addslashes", $resume_basic);
    if (empty($resume_basic))
    {
        exit("������ʧ");
    }
    $i=0;
    foreach($jobsarr as $jobs) {
        $jobs = array_map("addslashes", $jobs);

        $addarr['resume_id'] = $resumeid;
        $addarr['resume_name'] = $resume_basic['fullname'];
        $addarr['personal_uid'] = intval($uid);
        $addarr['jobs_id'] = $jobs['id'];
        $addarr['jobs_name'] = $jobs['jobs_name'];
        $addarr['company_id'] = $jobs['company_id'];
        $addarr['company_name'] = $jobs['companyname'];
        $addarr['company_uid'] = $jobs['uid'];
        $addarr['notes'] = $notes;
        if (strcasecmp(QISHI_DBCHARSET, "utf8") != 0) {
            $addarr['notes'] = utf8_to_gbk($addarr['notes']);
        }
        $addarr['apply_addtime'] = time();
        $addarr['personal_look'] = 1;
        $addarr['is_apply'] = 5;
        if ($db->inserttable(table('personal_jobs_apply'), $addarr)) {
            $mailconfig = get_cache('mailconfig');
            $weixinconfig = get_cache('weixin_config');
            $jobs['contact'] = $db->getone("select * from " . table('jobs_contact') . " where pid='{$jobs['id']}' LIMIT 1 ");
            $sms = get_cache('sms_config');
            $comuser = get_member_info($jobs['uid']);
            if ($mailconfig['set_applyjobs'] == "1" && $comuser['email_audit'] == "1" && $jobs['contact']['notify'] == "1") {
                dfopen($_CFG['site_domain'] . $_CFG['site_dir'] . "plus/asyn_mail.php?uid={$uid}&key=" . asyn_userkey($uid) . "&act=jobs_apply&jobs_id={$jobs['id']}&jobs_name={$jobs['jobs_name']}&personal_fullname={$resume_basic['fullname']}&email={$comuser['email']}&resume_id={$resumeid}");
            }
            //sms
            if ($sms['open'] == "1" && $sms['set_applyjobs'] == "1" && $comuser['mobile_audit'] == "1" && $jobs['contact']['notify_mobile'] == "1") {
                // �Ƿ���Ҫ����ҵ���ſ۳�
                if ($_CFG['company_sms'] == 1 && $comuser['sms_num'] > 0) {
                    $success = dfopen($_CFG['site_domain'] . $_CFG['site_dir'] . "plus/asyn_sms.php?uid={$uid}&key=" . asyn_userkey($uid) . "&act=jobs_apply&jobs_id=" . $jobs['id'] . "&jobs_name=" . $jobs['jobs_name'] . '&jobs_uid=' . $jobs['uid'] . "&personal_fullname=" . $resume_basic['fullname'] . "&mobile=" . $comuser['mobile']);
                    if ($success == "success") {
                        reduce_user_sms($jobs['uid']);
                    }
                } else {
                    dfopen($_CFG['site_domain'] . $_CFG['site_dir'] . "plus/asyn_sms.php?uid={$uid}&key=" . asyn_userkey($uid) . "&act=jobs_apply&jobs_id=" . $jobs['id'] . "&jobs_name=" . $jobs['jobs_name'] . '&jobs_uid=' . $jobs['uid'] . "&personal_fullname=" . $resume_basic['fullname'] . "&mobile=" . $comuser['mobile']);
                }

            }
            //վ����
            if ($pms_notice == '1') {
                $user = $db->getone("select username from " . table('members') . " where uid ={$jobs['uid']} limit 1");
                $jobs_url = url_rewrite('QS_jobsshow', array('id' => $jobs['id']));
                $resume_url = url_rewrite('QS_resumeshow', array('id' => $resumeid));
                $message = $resume_basic['fullname'] . '��������������ְλ��<a href="' . $jobs_url . '" target="_blank">' . $jobs['jobs_name'] . '</a>,<a href="' . $resume_url . '" target="_blank">����鿴</a>';
                write_pmsnotice($jobs['uid'], $user['username'], $message);
            }
            write_memberslog($uid, 2, 1301, $userinfo['username'], "Ͷ���˼�����ְλ:{$jobs['jobs_name']}");

        }
    }
}

function success_act_view($id, $type = 1){
    $clue = get_clue_one($id);
    if(!get_job_resume($clue,1 )){
        adminmsg("���ȴ�����ְ�߼���", 1);
    }
}
/*
 * $type=1;���Գɹ�;$type=2,��ְ�ɹ�
 */
function success_act($id, $type = 1)
{
    global $db;

    $clue = get_clue_one($id);

    $promotion = get_promotion_info($clue["job_id"], 5);

    if (!$promotion) {
        adminmsg("��Ƹ��Ϣ��ʧ", 1);
    }


    if(!get_job_resume($clue,0 )){
        adminmsg("���ȴ�����ְ�߼���", 1);
    }
    $json = json_array($promotion["cp_json"]);

    if($type==1){
        if($promotion["interview_success"]>=$json["num"]){
            adminmsg("���������Ѿ��ﵽĿ�꣡", 2);
        }

    }elseif($type==2){
        if($promotion["job_success"]>=$json["success_num"]){
            adminmsg("��ְ�����Ѿ��ﵽĿ�꣡", 2);
        }
    }

    qiye_deal($clue, $json, $promotion, $type);
    pro_deal($clue, $json, $promotion, $type);
    jober_deal($clue, $json, $promotion, $type);




}



//��ҵ�۷� type=1 ����;type=2��ְ
function qiye_deal($clue, $json, $promotion, $type)
{
    global $db;
    if ($type == 1) {
        if($clue["interview_success"]>=1){
           // adminmsg("�����ظ��ۿ�", 2);
        }

        $amount = $json["amount"]; //�ۿ���
        $uid = $promotion["cp_uid"];     //�û�id;

        $reason = "���Գɹ�";
        $notes = "���۷�{$amount},�۷�ԭ��" . $reason;
        $setsqlarr["cid"] = $clue["id"];
        $setsqlarr["notes"] = $notes;
        $setsqlarr['addtime'] = time();
        $setsqlarr['admin_name'] = $_SESSION["admin_name"];
        $setsqlarr['nexttime'] = "";

        if (!balance_deal($uid, 2, $amount)) {

            Ggven::log("qiye amount ".$amount);
            $setsqlarr['cate'] = $type;
            $setsqlarr['role'] = 1;

            $setsqlarr['is_success'] = 0;

        }

        //���ɶ���;
        $order['oid'] = "KK-" . date('ymd', time()) . "-" . date('His', time());//������
        $order_id = admin_add_order($_SESSION['uid'], 8, $order['oid'], $amount, "���۷�", $notes, time(), 0, '', 1);
        Ggven::log("qiye order create: ".$order_id);

        //������־;
        $db->inserttable(table('jobs_reward_clue_log'), $setsqlarr);

        $promotionarr["interview_success"] = $promotion['interview_success'] + 1;  //��¼�Ѿ���Ǯ
        $wheresql = " cp_id='" . intval($promotion['cp_id']) . "'";
        $db->updatetable(table('promotion'), $promotionarr, $wheresql);
        Ggven::log("update promotion");
        //��¼���������ѿ۷�
        $jobs_reward_cluearr["interview_success"] = 1;//��¼�Ѿ���Ǯ
        $wheresql = " id='" . intval($clue['id']) . "'";
        $db->updatetable(table('jobs_reward_clue'), $jobs_reward_cluearr, $wheresql);
        Ggven::log("update jobs_reward_clue");

    } else if ($type == 2) {
        if($clue["job_success"]>=1){
            adminmsg("�����ظ��ۿ�", 2);
        }
        $amount = $json["success_amount"]; //�ۿ���
        $uid = $promotion["cp_uid"];     //�û�id;

        $reason = "��ְ�ɹ�";
        $notes = "���۷�{$amount},�۷�ԭ��" . $reason;

        $setsqlarr["cid"] = $clue["id"];
        $setsqlarr["notes"] = $notes;
        $setsqlarr['addtime'] = time();
        $setsqlarr['admin_name'] = $_SESSION["admin_name"];
        $setsqlarr['nexttime'] = "";

        if (!balance_deal($uid, 2, $amount)) {
            Ggven::log("qiye amount ".$amount);
            $setsqlarr['cate'] = $type;
            $setsqlarr['role'] = 1;

            $setsqlarr['is_success'] = 0;
        }


        $order['oid'] = "KK-" . date('ymd', time()) . "-" . date('His', time());//������
        $order_id = admin_add_order($_SESSION['uid'], 8, $order['oid'], $amount, "���۷�", $notes, time(), 0, '', 1);
        Ggven::log("qiye order create: ".$order_id);

        $db->inserttable(table('jobs_reward_clue_log'), $setsqlarr);

        $promotionarr["job_success"] = $promotion['job_success'] + 1;  //��¼�Ѿ���Ǯ
        $wheresql = " cp_id='" . intval($promotion['cp_id']) . "'";
        $db->updatetable(table('promotion'), $promotionarr, $wheresql);

        //��¼���������ѿ۷�
        $jobs_reward_cluearr["job_success"] = 1;//��¼�Ѿ���Ǯ
        $wheresql = " id='" . intval($clue['id']) . "'";
        $db->updatetable(table('jobs_reward_clue'), $jobs_reward_cluearr, $wheresql);

    }
}

//�����ṩ�߸�Ǯ type=1 ����;type=2��ְ
function pro_deal($clue, $json, $promotion, $type)
{
    global $db;

    if ($type == 1) {
        $amount = intval($json["amount"] * $json["amount_per"] / 100); //�������
        $uid = $clue["uid"];     //�ṩ��id;

        $reason = "���Գɹ�";
        $notes = "�����ṩ���������{$amount},����ԭ��" . $reason;
        $setsqlarr["cid"] = $clue["id"];
        $setsqlarr["notes"] = $notes;
        $setsqlarr['addtime'] = time();
        $setsqlarr['admin_name'] = $_SESSION["admin_name"];
        $setsqlarr['nexttime'] = "";

        if (!balance_deal_person($uid, 1, $amount)) {
           // adminmsg("����ʧ��", 2);
            Ggven::log("pro amount ".$amount);
            $setsqlarr['cate'] = $type;
            $setsqlarr['role'] = 2;
            $setsqlarr['is_success'] = 0;
        }

        $order['oid'] = "KK-" . date('ymd', time()) . "-" . date('His', time());//������

        $order_id = admin_add_order($uid, 7, $order['oid'], $amount, "�������", $notes, time(), 0, '', 1);
        Ggven::log("pro order ".$order_id);

        $db->inserttable(table('jobs_reward_clue_log'), $setsqlarr);


    } else if ($type == 2) {

        $amount = intval($json["success_amount"] * $json["amount_success_per"] / 100); //�������
        $uid = $clue["uid"];     //�ṩ��id;

        $reason = "��ְ�ɹ�";
        $notes = "�����ṩ���������{$amount},����ԭ��" . $reason;
        $setsqlarr["cid"] = $clue["id"];
        $setsqlarr["notes"] = $notes;
        $setsqlarr['addtime'] = time();
        $setsqlarr['admin_name'] = $_SESSION["admin_name"];
        $setsqlarr['nexttime'] = "";

        if (!balance_deal_person($uid, 1, $amount)) {
            // adminmsg("����ʧ��", 2);
            Ggven::log("pro amount ".$amount);
            $setsqlarr['cate'] = $type;
            $setsqlarr['role'] = 2;
            $setsqlarr['is_success'] = 0;
        }

        $order['oid'] = "KK-" . date('ymd', time()) . "-" . date('His', time());//������

        $order_id = admin_add_order($uid, 7, $order['oid'], $amount, "�������", $notes, time(), 0, '', 1);
        Ggven::log("pro order ".$order_id);

        $db->inserttable(table('jobs_reward_clue_log'), $setsqlarr);

    }
}


//�����ṩ�߸�Ǯ type=1 ����;type=2��ְ
function jober_deal($clue, $json, $promotion, $type)
{
    global $db;

    if ($type == 1) {


        $amount = intval($json["amount"] * $json["amount_per"] / 100); //�������
        $uid = $clue["member_id"];     //��ְ��id;

        $reason = "���Գɹ�";
        $notes = "��ְ���������{$amount},����ԭ��" . $reason;
        $setsqlarr["cid"] = $clue["id"];
        $setsqlarr["notes"] = $notes;
        $setsqlarr['addtime'] = time();
        $setsqlarr['admin_name'] = $_SESSION["admin_name"];
        $setsqlarr['nexttime'] = "";
        if (!balance_deal_person($uid, 1, $amount)) {
            Ggven::log("jober amount ".$amount);
            $setsqlarr['cate'] = $type;
            $setsqlarr['role'] = 3;
            $setsqlarr['is_success'] = 0;
        }

        $order['oid'] = "KK-" . date('ymd', time()) . "-" . date('His', time());//������
        $order_id = admin_add_order($uid, 7, $order['oid'], $amount, "�������", $notes, time(), 0, '', 1);

        Ggven::log("pro order ".$order_id);

        $db->inserttable(table('jobs_reward_clue_log'), $setsqlarr);


    } else if ($type == 2) {

        $amount = intval($json["success_amount"] * $json["amount_success_per"] / 100); //�������
        $uid = $clue["uid"];     //�ṩ��id;

        $reason = "��ְ�ɹ�";
        $notes = "��ְ���������{$amount},����ԭ��" . $reason;
        $setsqlarr["cid"] = $clue["id"];
        $setsqlarr["notes"] = $notes;
        $setsqlarr['addtime'] = time();
        $setsqlarr['admin_name'] = $_SESSION["admin_name"];
        $setsqlarr['nexttime'] = "";
        if (!balance_deal_person($uid, 1, $amount)) {
            Ggven::log("jober amount ".$amount);
            $setsqlarr['cate'] = $type;
            $setsqlarr['role'] = 3;
            $setsqlarr['is_success'] = 0;
        }

        $order['oid'] = "KK-" . date('ymd', time()) . "-" . date('His', time());//������
        $order_id = admin_add_order($uid, 7, $order['oid'], $amount, "�������", $notes, time(), 0, '', 1);

        Ggven::log("pro order ".$order_id);

        $db->inserttable(table('jobs_reward_clue_log'), $setsqlarr);

    }
}

function app_get_jobs1($id)
{
    global $db;
    if (strpos($id,"-"))
    {
        $id=str_replace("-",",",$id);
        if (!preg_match("/^(\d{1,10},)*(\d{1,10})$/",$id)) return false;
    }
    else
    {
        $id=intval($id);
    }
    $sql = "select * from ".table('jobs')." WHERE id IN ({$id}) ";
    return $db->getall($sql);
}

//��ȡ����������Ϣ
function get_resume_basic3($uid,$id)
{
    global $db;
    $id=intval($id);
    $uid=intval($uid);
    $info=$db->getone("select * from ".table('resume')." where id='{$id}'  AND uid='{$uid}' LIMIT 1 ");

    if (empty($info))
    {
        return false;
    }
    else
    {
        $info['age']=date("Y")-$info['birthdate'];
        $info['number']="N".str_pad($info['id'],7,"0",STR_PAD_LEFT);
        $info['lastname']=$info['fullname'];
        return $info;
    }
}

//��ȡ��Ϣ;
function get_clue_detail($promotion){
     global $db;
     $info=$db->getone("select * from ".table('members_points')." where   uid='{$promotion["cp_uid"]}' LIMIT 1 ");
     $json=json_array($promotion["cp_json"]);

     $str=sprintf("�������ܶ������<span class='red'>%u</span>Ԫ���ƻ���Ƹ<span class='red'>%u</span>�ˣ�����<span class='red'>%u</span>�ˡ�Ŀǰ�Ѿ��е�<span class='red'>%u</span>�ˣ�����<span class='red'>%u</span>�ˡ�ʣ�ඳ�����<span class='red'>%u</span>Ԫ��",
          $json["block_balance"],$json["success_num"],$json["num"],$promotion["job_success"],$promotion["interview_success"],$info["block_balance"]);

    return $str;

}

//��������״̬;1.�ɹ�,2:ʧ��,0:������
function update_clue_status($id){
    global  $db;
    $success="�ѳɹ���ְ(�۳���ְӶ��)";
    $fail=array("���ʧ��(��ҵ��������)","���ʧ��(��ְ�߲�����)","�Դ�ְλ����Ȥ","����Ըʹ�ô˷���","�Դ���ְ������Ȥ");

    $str=$success;
    $rs=$db->getone("select * from ".table('jobs_reward_clue_log')." where   cid='{$id}' and result='{$str}' LIMIT 1 ");
    if($rs){
        $db->query("update ".table("jobs_reward_clue")." set status=1 where id=".$id);
        return ;
    }
    foreach($fail as $k=>$str){

        $rs=$db->getone("select * from ".table('jobs_reward_clue_log')." where   cid='{$id}' and result='{$str}' LIMIT 1 ");
        if($rs){
            $db->query("update ".table("jobs_reward_clue")." set status=2 where id=".$id);
            return ;
        }
    }
}