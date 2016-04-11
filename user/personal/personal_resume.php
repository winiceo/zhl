<?php
/*
 * 74cms ���˻�Ա����
*/
define('IN_QISHI', true);
require_once(dirname(__FILE__) . '/personal_common.php');
$smarty->assign('leftmenu', "resume");
$smarty->assign('act', $act);

//�����б�
if ($act == 'resume_list') {
    $wheresql = " WHERE uid='" . $_SESSION['uid'] . "' ";
    $sql = "SELECT * FROM " . table('resume') . $wheresql;
    $smarty->assign('title', '�ҵļ��� - ���˻�Ա���� - ' . $_CFG['site_name']);
    $smarty->assign('act', $act);
    $total = $db->get_total("SELECT COUNT(*) AS num FROM " . table('resume') . " WHERE uid='{$_SESSION['uid']}'");
    $smarty->assign('total', $total);
    $smarty->assign('resume_list', get_resume_list($sql, 12, true, true, true));
    $smarty->display('member_personal/personal_resume_list.htm');
} elseif ($act == 'refresh') {
    $resumeid = intval($_GET['id']) ? intval($_GET['id']) : showmsg("��û��ѡ�������");
    $refrestime = get_last_refresh_date($_SESSION['uid'], "2001");
    $duringtime = time() - $refrestime['max(addtime)'];
    $space = $_CFG['per_refresh_resume_space'] * 60;
    $refresh_time = get_today_refresh_times($_SESSION['uid'], "2001");
    if ($_CFG['per_refresh_resume_time'] != 0 && ($refresh_time['count(*)'] >= $_CFG['per_refresh_resume_time'])) {
        showmsg("ÿ�����ֻ��ˢ��" . $_CFG['per_refresh_resume_time'] . "��,�������ѳ������ˢ�´������ƣ�", 2);
    } elseif ($duringtime <= $space) {
        showmsg($_CFG['per_refresh_resume_space'] . "�����ڲ����ظ�ˢ�¼�����", 2);
    } else {
        refresh_resume($resumeid, $_SESSION['uid']) ? showmsg('�����ɹ���', 2) : showmsg('����ʧ�ܣ�', 0);
    }
} //ɾ������
elseif ($act == 'del_resume') {
    if (intval($_GET['id']) == 0) {
        exit('��û��ѡ�������');
    } else {
        del_resume($_SESSION['uid'], intval($_GET['id'])) ? exit('success') : exit('fail');
    }
} //��������-������Ϣ
elseif ($act == 'make1') {
    //error_reporting(-1);
    $uid = intval($_SESSION['uid']);
    $pid = intval($_REQUEST['pid']);
    /**
     * 3.6�Ż�start
     * @var [type]
     */
    $total = $db->get_total("SELECT COUNT(*) AS num FROM " . table('resume') . " WHERE uid='{$uid}'");
    if ($total >= intval($_CFG['resume_max'])) {
        showmsg("�������Դ���{$_CFG['resume_max']} �ݼ���,�Ѿ�������������ƣ�", 1);
    }
    /**
     * 3.6�Ż�end
     * @var [type]
     */
    
    $_SESSION['send_mobile_key'] = mt_rand(100000, 999999);
    $smarty->assign('send_key', $_SESSION['send_mobile_key']);
    $smarty->assign('resume_basic', get_resume_basic($uid, $pid));
    $smarty->assign('resume_education', get_resume_education($uid, $pid));
    $smarty->assign('resume_work', get_resume_work($uid, $pid));
    $smarty->assign('resume_training', get_resume_training($uid, $pid));
    $smarty->assign('subsite', get_all_subsite());
    $smarty->assign('act', $act);
    $smarty->assign('pid', $pid);
    $smarty->assign('user', $user);
    $smarty->assign('userprofile', get_userprofile($_SESSION['uid']));
    $smarty->assign('title', '�ҵļ��� - ���˻�Ա���� - ' . $_CFG['site_name']);
    $captcha = get_cache('captcha');
    $smarty->assign('verify_resume', $captcha['verify_resume']);
    $smarty->assign('go_resume_show', $_GET['go_resume_show']);
    $smarty->display('member_personal/personal_make_resume_step1.htm');
} //�������� -���������Ϣ����ְ����
elseif ($act == 'make1_save') {


    $captcha = get_cache('captcha');
    $postcaptcha = trim($_POST['postcaptcha']);
    if ($captcha['verify_resume'] == '1' && empty($postcaptcha) && intval($_REQUEST['pid']) === 0) {
        showmsg("����дϵͳ��֤��", 1);
    }
    if ($captcha['verify_resume'] == '1' && intval($_REQUEST['pid']) === 0 && strcasecmp($_SESSION['imageCaptcha_content'], $postcaptcha) != 0) {
        showmsg("ϵͳ��֤�����", 1);
    }
    $setsqlarr['uid'] = intval($_SESSION['uid']);
    $setsqlarr['telephone'] = trim($_POST['mobile']) ? trim($_POST['mobile']) : showmsg('����д�ֻ��ţ�', 1);
    //����������ֻ���,��֤Ψһ��


    if ($user["mobile"] == "") {
        $sql = "select * from " . table('members') . " where mobile = '{$setsqlarr['telephone']}' LIMIT 1";
        $mob = $db->getone($sql);
        if ($mob) {
            showmsg('�ֻ����Ѵ��ڣ�', 1);
        }
    }else{
        $setsqlarr['telephone'] =$user["mobile"];
    }

    $members['mobile'] = $telephone;
    $members_info['phone'] = $telephone;
    $resume['telephone'] = $telephone;
    $db->updatetable(table("members"), $members, array("uid" => intval($_SESSION['uid'])));
    $db->updatetable(table("members_info"), $members_info, array("uid" => intval($_SESSION['uid'])));
    $db->updatetable(table("resume"), $resume, array("uid" => intval($_SESSION['uid'])));
    unset($members['mobile'], $members_info['phone'], $resume['telephone']);

    $setsqlarr['title'] = trim($_POST['title']) ? trim($_POST['title']) : "δ��������";
    check_word($_CFG['filter'], $_POST['title']) ? showmsg($_CFG['filter_tips'], 0) : '';
    $setsqlarr['fullname'] = trim($_POST['fullname']) ? trim($_POST['fullname']) : showmsg('����д������', 1);
    check_word($_CFG['filter'], $_POST['fullname']) ? showmsg($_CFG['filter_tips'], 0) : '';
    $setsqlarr['display_name'] = intval($_POST['display_name']);
    $setsqlarr['sex'] = trim($_POST['sex']) ? intval($_POST['sex']) : showmsg('��ѡ���Ա�', 1);
    $setsqlarr['sex_cn'] = trim($_POST['sex_cn']);
    $setsqlarr['birthdate'] = intval($_POST['birthdate']) > 1945 ? intval($_POST['birthdate']) : showmsg('����ȷ��д�������', 1);
    $setsqlarr['residence'] = trim($_POST['residence']) ? trim($_POST['residence']) : showmsg('����д�־�ס�أ�', 1);
    $setsqlarr['education'] = intval($_POST['education']) ? intval($_POST['education']) : showmsg('��ѡ��ѧ��', 1);
    $setsqlarr['education_cn'] = trim($_POST['education_cn']);
    $setsqlarr['major'] = intval($_POST['major']);
    $setsqlarr['major_cn'] = trim($_POST['major_cn']);
    $setsqlarr['experience'] = $_POST['experience'] ? $_POST['experience'] : showmsg('��ѡ��������', 1);
    $setsqlarr['experience_cn'] = trim($_POST['experience_cn']);
    $setsqlarr['email'] = trim($_POST['email']) ? trim($_POST['email']) : showmsg('����д���䣡', 1);
    if ($user['email_audit'] != "1") {
        $members['email'] = $setsqlarr['email'];
        $members_info['email'] = $setsqlarr['email'];
        $resume['email'] = $setsqlarr['email'];
        $db->updatetable(table("members"), $members, array("uid" => intval($_SESSION['uid'])));
        $db->updatetable(table("members_info"), $members_info, array("uid" => intval($_SESSION['uid'])));
        $db->updatetable(table("resume"), $resume, array("uid" => intval($_SESSION['uid'])));
        unset($members['email'], $members_info['email'], $resume['email']);
    }
    check_word($_CFG['filter'], $_POST['email']) ? showmsg($_CFG['filter_tips'], 0) : '';
    $setsqlarr['email_notify'] = $_POST['email_notify'] == "1" ? 1 : 0;
    $setsqlarr['height'] = intval($_POST['height']);
    $setsqlarr['householdaddress'] = trim($_POST['householdaddress']);
    $setsqlarr['marriage'] = intval($_POST['marriage']);
    $setsqlarr['marriage_cn'] = trim($_POST['marriage_cn']);;
    $setsqlarr['intention_jobs'] = trim($_POST['intention_jobs']) ? trim($_POST['intention_jobs']) : showmsg('��ѡ������ְλ��', 1);
    $setsqlarr['trade'] = $_POST['trade'] ? trim($_POST['trade']) : showmsg('��ѡ��������ҵ��', 1);
    $setsqlarr['trade_cn'] = trim($_POST['trade_cn']);
    $setsqlarr['subsite_id'] = !empty($_POST['subsite_id']) ? intval($_POST['subsite_id']) : showmsg('��ѡ����������������', 1);
    $setsqlarr['district'] = intval($_POST['district']);
    $setsqlarr['sdistrict'] = intval($_POST['sdistrict']);
    $setsqlarr['district_cn'] = empty($_POST['district_cn']) ? trim($_POST['subsite_name']) : (trim($_POST['subsite_name']) . '/' . trim($_POST['district_cn']));
    $setsqlarr['nature'] = intval($_POST['nature']) ? intval($_POST['nature']) : showmsg('��ѡ��������λ���ʣ�', 1);
    $setsqlarr['nature_cn'] = trim($_POST['nature_cn']);
    //Ŀǰ״̬
    $setsqlarr['current'] = intval($_POST['current']) ? intval($_POST['current']) : showmsg('��ѡ��Ŀǰ״̬��', 1);
    $setsqlarr['current_cn'] = trim($_POST['current_cn']);
    $setsqlarr['wage'] = intval($_POST['wage']) ? intval($_POST['wage']) : showmsg('��ѡ������н�ʣ�', 1);
    $setsqlarr['wage_cn'] = trim($_POST['wage_cn']);
    $setsqlarr['entrust'] = intval($_POST['entrust']);
    $setsqlarr['refreshtime'] = $timestamp;
    $setsqlarr['audit'] = intval($_CFG['audit_resume']);
    $setsqlarr['resume_from_pc'] = 1;
    $total = $db->get_total("SELECT COUNT(*) AS num FROM " . table('resume') . " WHERE uid='{$_SESSION['uid']}'");
    if ($total >= intval($_CFG['resume_max'])) {
        showmsg("�������Դ���{$_CFG['resume_max']} �ݼ���,�Ѿ�������������ƣ�", 1);
    } else {
        $setsqlarr['addtime'] = $timestamp;
        $pid = $db->inserttable(table('resume'), $setsqlarr, 1);
        $searchtab['id'] = $pid;
        $searchtab['uid'] = $_SESSION['uid'];
        $db->inserttable(table('resume_search_key'), $searchtab);
        $db->inserttable(table('resume_search_rtime'), $searchtab);
        if (empty($pid)) showmsg("����ʧ�ܣ�", 0);
        add_resume_jobs($pid, $_SESSION['uid'], $_POST['intention_jobs_id']) ? "" : showmsg('����ʧ�ܣ�', 0);
        add_resume_trade($pid, $_SESSION['uid'], $_POST['trade']) ? "" : showmsg('����ʧ�ܣ�', 0);
        check_resume($_SESSION['uid'], $pid);
        if (intval($_POST['entrust'])) {
            set_resume_entrust($pid);
        }
        // �鿴������¼�� ͳ�ƴ���������«����������«��  �ж��Ƿ񳬹�����   ��û�������� �������Ӻ�«��
        $today = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
        $info = $db->getone("SELECT sum(points) as num FROM " . table('members_handsel') . " WHERE uid ='{$_SESSION['uid']}' AND htype='createresume' AND addtime>{$today} ");
        if (intval($info['num']) >= intval($_CFG['create_resume_points_max'])) {
            write_memberslog($_SESSION['uid'], 2, 1101, $_SESSION['username'], "�����˼���");
        } else {
            $points_rule = get_cache('points_rule');
            $user_points = get_user_points($_SESSION['uid']);
            if ($points_rule['create_resume']['value'] > 0) {
                $time = time();
                $members_handsel_arr['uid'] = $_SESSION['uid'];
                $members_handsel_arr['htype'] = "createresume";
                $members_handsel_arr['points'] = $points_rule['create_resume']['value'];
                $members_handsel_arr['addtime'] = $time;
                $db->inserttable(table("members_handsel"), $members_handsel_arr);
                report_deal($_SESSION['uid'], $points_rule['create_resume']['type'], $points_rule['create_resume']['value']);
                $user_points = get_user_points($_SESSION['uid']);
                $operator = $points_rule['create_resume']['type'] == "1" ? "+" : "-";
                write_memberslog($_SESSION['uid'], 2, 9001, $_SESSION['username'], "�����˼�����<strong>{$setsqlarr['title']}</strong>��({$operator}{$points_rule['create_resume']['value']})��(ʣ��:{$user_points})", 2, 1101, "�����˼���", "{$operator}{$points_rule['create_resume']['value']}", "{$user_points}");
            } else {
                write_memberslog($_SESSION['uid'], 2, 1101, $_SESSION['username'], "�����˼���");
            }
        }

        if (!get_userprofile($_SESSION['uid'])) {
            $infoarr['realname'] = $setsqlarr['fullname'];
            $infoarr['sex'] = $setsqlarr['sex'];
            $infoarr['sex_cn'] = $setsqlarr['sex_cn'];
            $infoarr['birthday'] = $setsqlarr['birthdate'];
            $infoarr['residence'] = $setsqlarr['residence'];
            $infoarr['education'] = $setsqlarr['education'];
            $infoarr['education_cn'] = $setsqlarr['education_cn'];
            $infoarr['experience'] = $setsqlarr['experience'];
            $infoarr['experience_cn'] = $setsqlarr['experience_cn'];
            $infoarr['height'] = $setsqlarr['height'];
            $infoarr['householdaddress'] = $setsqlarr['householdaddress'];
            $infoarr['marriage'] = $setsqlarr['marriage'];
            $infoarr['marriage_cn'] = $setsqlarr['marriage_cn'];
            $infoarr['phone'] = $setsqlarr['telephone'];
            $infoarr['email'] = $setsqlarr['email'];
            $infoarr['uid'] = intval($_SESSION['uid']);
            $db->inserttable(table('members_info'), $infoarr);
        }
        header("Location: ?act=edit_resume&pid=" . $pid . "&make=1");
    }
} elseif ($act == 'make1_succeed') {
    $pid = intval($_GET['pid']);
    $smarty->assign('pid', $pid);
    $smarty->assign('resume_basic', get_resume_basic($_SESSION['uid'], $pid));
    $smarty->assign('title', '�ҵļ��� - ���˻�Ա���� - ' . $_CFG['site_name']);
    $smarty->display('member_personal/personal_make_resume_step1_succeed.htm');
} elseif ($act == 'ajax_get_interest_jobs') {
    global $_CFG;
    $uid = intval($_SESSION['uid']);
    $pid = intval($_GET['pid']);
    $html = "";
    $interest_id = get_interest_jobs_id_by_resume($uid, $pid);
    $jobs_list = get_interest_jobs_list($interest_id);
    if (!empty($jobs_list)) {
        foreach ($jobs_list as $k => $v) {
            $jobs_url = url_rewrite("QS_jobsshow", array("id" => $v['id']));
            $company_url = url_rewrite("QS_companyshow", array("id" => $v['company_id']));
            $html = '<tr>
					<td class="frist" width="117"><div class="index-line1"><a href="' . $jobs_url . '" class="underline job-link">' . $v["jobs_name"] . '</a></div></td>
					<td width="228"><div class="index-line2"><a href="' . $company_url . '" class="underline com-link">' . $v["companyname"] . '</a></div></td>
					<td width="139"><div class="index-line3">' . $v["district_cn"] . '</div></td>
					<td width="195"><span>' . $v["wage_cn"] . '</span></td>
					<td width="75">2015-06-01</td>
				</tr>';
        }
    }
    exit($html);
} elseif ($act == 'ajax_save_basic_info') {
    $telephone = trim($_POST['mobile']) ? trim($_POST['mobile']) : exit('����д�ֻ��ţ�');
    $resume_basic = get_resume_basic($_SESSION['uid'], $_REQUEST['pid']);
    $setsqlarr['telephone'] = $telephone;
    if ($user['mobile_audit'] != "1") {
        $members['mobile'] = $telephone;
        $members_info['phone'] = $telephone;
        $db->updatetable(table("members"), $members, array("uid" => intval($_SESSION['uid'])));
        $db->updatetable(table("members_info"), $members_info, array("uid" => intval($_SESSION['uid'])));
        unset($members['mobile'], $members_info['phone']);
    }
    $setsqlarr['title'] = utf8_to_gbk(trim($_POST['title']));
    $setsqlarr['fullname'] = trim($_POST['fullname']) ? utf8_to_gbk(trim($_POST['fullname'])) : exit('����д������');
    check_word($_CFG['filter'], $setsqlarr['fullname']) ? exit($_CFG['filter_tips']) : '';
    $setsqlarr['display_name'] = intval($_POST['display_name']);
    $setsqlarr['sex'] = trim($_POST['sex']) ? intval($_POST['sex']) : exit('��ѡ���Ա�');
    $setsqlarr['sex_cn'] = utf8_to_gbk(trim($_POST['sex_cn']));
    $setsqlarr['birthdate'] = intval($_POST['birthdate']) > 1945 ? intval($_POST['birthdate']) : exit('����ȷ��д�������');
    $setsqlarr['residence'] = trim($_POST['residence']) ? utf8_to_gbk(trim($_POST['residence'])) : exit('����д�־�ס�أ�');
    $setsqlarr['education'] = intval($_POST['education']) ? intval($_POST['education']) : exit('��ѡ��ѧ��');
    $setsqlarr['education_cn'] = utf8_to_gbk(trim($_POST['education_cn']));
    $setsqlarr['major'] = intval($_POST['major']);
    $setsqlarr['major_cn'] = utf8_to_gbk(trim($_POST['major_cn']));
    $setsqlarr['experience'] = $_POST['experience'] ? $_POST['experience'] : exit('��ѡ��������');
    $setsqlarr['experience_cn'] = utf8_to_gbk(trim($_POST['experience_cn']));
    $setsqlarr['email'] = trim($_POST['email']) ? utf8_to_gbk(trim($_POST['email'])) : exit('����д���䣡');
    if ($user['email_audit'] != "1") {
        $members['email'] = $setsqlarr['email'];
        $members_info['email'] = $setsqlarr['email'];
        $db->updatetable(table("members"), $members, array("uid" => intval($_SESSION['uid'])));
        $db->updatetable(table("members_info"), $members_info, array("uid" => intval($_SESSION['uid'])));
        unset($members['email'], $members_info['email']);


    }
    check_word($_CFG['filter'], $setsqlarr['email']) ? exit($_CFG['filter_tips']) : '';
    $setsqlarr['email_notify'] = $_POST['email_notify'] == "1" ? 1 : 0;
    $setsqlarr['height'] = intval($_POST['height']);
    $setsqlarr['householdaddress'] = utf8_to_gbk(trim($_POST['householdaddress']));
    $setsqlarr['marriage'] = intval($_POST['marriage']);
    $setsqlarr['marriage_cn'] = utf8_to_gbk(trim($_POST['marriage_cn']));
    $setsqlarr['refreshtime'] = $timestamp;
    $_CFG['audit_edit_resume'] != "-1" ? $setsqlarr['audit'] = intval($_CFG['audit_edit_resume']) : "";

    $db->updatetable(table('resume'), $setsqlarr, array("id" => intval($_REQUEST['pid']), "uid" => $_SESSION['uid']));

    check_resume($_SESSION['uid'], intval($_REQUEST['pid']));
    if ($_CFG['audit_edit_resume'] != "-1") {
        set_resume_entrust(intval($_REQUEST['pid']));
    }
    $title = utf8_to_gbk(trim($_POST['title']));
    write_memberslog($_SESSION['uid'], 2, 1105, $_SESSION['username'], "�޸��˼���({$title})");
    exit("success");
} elseif ($act == 'ajax_save_titl') {
    $setsqlarr['uid'] = intval($_SESSION['uid']);
    $setsqlarr['title'] = utf8_to_gbk(trim($_POST['title'])) ? utf8_to_gbk(trim($_POST['title'])) : exit('����д�������ƣ�');
    check_word($_CFG['filter'], $setsqlarr['title']) ? exit($_CFG['filter_tips']) : '';
    $db->updatetable(table('resume'), $setsqlarr, " id='" . intval($_POST['pid']) . "'  AND uid='{$setsqlarr['uid']}'");
    $title = $setsqlarr['title'];
    write_memberslog($_SESSION['uid'], 2, 1105, $_SESSION['username'], "�޸�({$title})");
    exit('success');
} elseif ($act == 'ajax_save_basic') {
    $setsqlarr['uid'] = intval($_SESSION['uid']);

    $setsqlarr['intention_jobs'] = utf8_to_gbk(trim($_POST['intention_jobs'])) ? utf8_to_gbk(trim($_POST['intention_jobs'])) : exit('��ѡ������ְλ��');
    $setsqlarr['trade'] = $_POST['trade'] ? trim($_POST['trade']) : exit('��ѡ��������ҵ��');
    $setsqlarr['trade_cn'] = utf8_to_gbk(trim($_POST['trade_cn']));
    $setsqlarr['subsite_id'] = !empty($_POST['subsite_id']) ? intval($_POST['subsite_id']) : exit('��ѡ����������������');
    $setsqlarr['district'] = intval($_POST['district']);
    $setsqlarr['sdistrict'] = intval($_POST['sdistrict']);
    $setsqlarr['district_cn'] = !utf8_to_gbk(trim($_POST['district_cn'])) ? utf8_to_gbk(trim($_POST['subsite_name'])) : (utf8_to_gbk(trim($_POST['subsite_name'])) . '/' . utf8_to_gbk(trim($_POST['district_cn'])));
    $setsqlarr['nature'] = intval($_POST['nature']) ? intval($_POST['nature']) : exit('��ѡ��������λ���ʣ�');
    $setsqlarr['nature_cn'] = utf8_to_gbk(trim($_POST['nature_cn']));
    //Ŀǰ״̬
    $setsqlarr['current'] = intval($_POST['current']) ? intval($_POST['current']) : exit('��ѡ��Ŀǰ״̬��');
    $setsqlarr['current_cn'] = utf8_to_gbk(trim($_POST['current_cn']));
    $setsqlarr['wage'] = intval($_POST['wage']) ? intval($_POST['wage']) : exit('��ѡ������н�ʣ�');
    $setsqlarr['wage_cn'] = utf8_to_gbk(trim($_POST['wage_cn']));
    $setsqlarr['refreshtime'] = $timestamp;
    $_CFG['audit_edit_resume'] != "-1" ? $setsqlarr['audit'] = intval($_CFG['audit_edit_resume']) : "";
    $db->updatetable(table('resume'), $setsqlarr, " id='" . intval($_REQUEST['pid']) . "'  AND uid='{$setsqlarr['uid']}'");
    add_resume_jobs(intval($_REQUEST['pid']), $_SESSION['uid'], $_POST['intention_jobs_id']) ? "" : showmsg('����ʧ�ܣ�', 0);
    add_resume_trade(intval($_REQUEST['pid']), $_SESSION['uid'], $_POST['trade']) ? "" : showmsg('����ʧ�ܣ�', 0);
    check_resume($_SESSION['uid'], intval($_REQUEST['pid']));
    if ($_CFG['audit_edit_resume'] != "-1") {
        set_resume_entrust(intval($_REQUEST['pid']));
    }
    $title = utf8_to_gbk(trim($_POST['title']));
    write_memberslog($_SESSION['uid'], 2, 1105, $_SESSION['username'], "�޸��˼���({$title})");
    exit("success");
} elseif ($act == 'resume_logo_save') {
    $resume_basic = get_resume_basic(intval($_SESSION['uid']), intval($_REQUEST['param1']));
    if ($resume_basic['photo_img']) {
        @unlink("../../data/" . $_CFG['_resume_photo_dir'] . "/" . $resume_basic['photo_img']);
        @unlink("../../data/" . $_CFG['_resume_photo_dir_thumb'] . "/" . $resume_basic['photo_img']);
    }
    $updir = date('Y/m/d');
    $savePath = "../../data/" . $_CFG['_resume_photo_dir'] . "/" . $updir;  //ͼƬ�洢·��
    $savePathThumb = "../../data/" . $_CFG['_resume_photo_dir_thumb'] . "/" . $updir;  //ͼƬ�洢·��
    make_dir($savePath);
    make_dir($savePathThumb);
    $savePicName = time();//ͼƬ�洢����
    $file_src = $savePath . '/' . $savePicName . "_src.jpg";
    $filename150 = $savePath . '/' . $savePicName . ".jpg";
    $filename50 = $savePathThumb . '/' . $savePicName . ".jpg";
    $src = base64_decode($_POST['pic']);
    $pic1 = base64_decode($_POST['pic1']);
    $pic2 = base64_decode($_POST['pic2']);
    if ($src) {
        file_put_contents($file_src, $src);
    }
    file_put_contents($filename150, $pic1);
    if ($pic2) file_put_contents($filename50, $pic2);
    $rs['status'] = 1;
    $rs['picUrl'] = $updir . '/' . $savePicName . ".jpg";
    $setarr['photo_img'] = $rs['picUrl'];
    $setarr['photo_audit'] = intval($_CFG['audit_resume_photo']);
    $setarr['photo'] = 1;
    $db->updatetable(table("resume"), $setarr, array("uid" => intval($_SESSION['uid']), "id" => $resume_basic['id']));
    check_resume($_SESSION['uid'], intval($resume_basic['id']));
    // ���Ƽ��� ��Ӧ�ĺ�«�Ҳ���
    if (empty($resume_basic['photo_img'])) {
        perfect_resume($_SESSION['uid'], $_SESSION['username'], $resume_basic['id']);
    }
    print json_encode($rs);

} //ί�м���
elseif ($act == "set_entrust") {
    $uid = intval($_SESSION['uid']);
    $pid = intval($_REQUEST['pid']);
    $setsqlarr['entrust'] = intval($_POST['entrust']);
    $ensetsqlarr['entrust'] = $setsqlarr['entrust'];
    $setsqlarr['entrust_start'] = intval(time());
    if ($setsqlarr['entrust'] == 1) {
        $setsqlarr['entrust_end'] = strtotime("+7 day");
    } elseif ($setsqlarr['entrust'] == 2) {
        $setsqlarr['entrust_end'] = strtotime("+14 day");
    } else {
        $setsqlarr['entrust_end'] = strtotime("+30 day");
    }
    $setsqlarr['isshield'] = intval($_POST['isshield']);
    //���ü���ί��
    if (set_resume_entrust($pid, $setsqlarr)) {
        $db->updatetable(table('resume'), $ensetsqlarr, " id='" . $pid . "' AND uid='" . $uid . "'");
        exit("ί�гɹ�");
    } else {
        exit("ί��ʧ��");
    }
} elseif ($act == "set_entrust_del") {
    if (intval($_GET['pid']) == 0) showmsg('��������', 1);
    $db->updatetable(table('resume'), array("entrust" => 0), " id='" . intval($_GET['pid']) . "' AND uid='" . intval($_SESSION['uid']) . "'");
    $db->query("delete from " . table("resume_entrust") . " where id=" . intval($_GET['pid']) . "");
    showmsg("ȡ��ί�гɹ���", 2);
} elseif ($act == 'save_education') {
    $id = intval($_POST['id']);
    $setsqlarr['uid'] = intval($_SESSION['uid']);
    $setsqlarr['pid'] = intval($_REQUEST['pid']);

    if ($setsqlarr['uid'] == 0 || $setsqlarr['pid'] == 0) exit('���������ڣ�');
    $resume_basic = get_resume_basic(intval($_SESSION['uid']), intval($_REQUEST['pid']));
    if (empty($resume_basic)) exit("������д����������Ϣ��");
    $resume_education = get_resume_education($_SESSION['uid'], $_REQUEST['pid']);
    if (count($resume_education) >= 6) exit('�����������ܳ���6����');
    $school = utf8_to_gbk(trim($_POST['school']));
    $speciality = utf8_to_gbk(trim($_POST['speciality']));
    $education_cn = utf8_to_gbk(trim($_POST['education_cn']));
    $setsqlarr['school'] = $school ? $school : exit("����дѧУ���ƣ�");
    check_word($_CFG['filter'], $setsqlarr['school']) ? exit($_CFG['filter_tips']) : '';
    $setsqlarr['speciality'] = $speciality ? $speciality : exit("����дרҵ���ƣ�");
    check_word($_CFG['filter'], $setsqlarr['speciality']) ? exit($_CFG['filter_tips']) : '';
    $setsqlarr['education'] = intval($_POST['education']) ? intval($_POST['education']) : exit("��ѡ����ѧ����");
    $setsqlarr['education_cn'] = $education_cn ? $education_cn : exit("��ѡ����ѧ����");
    // ѡ������Ͳ��жϽ���ʱ����
    if (intval($_POST['edu_todate']) == 1) {
        if (trim($_POST['edu_start_year']) == "" || trim($_POST['edu_start_month']) == "") {
            exit("��ѡ��Ͷ�ʱ�䣡");
        }
        if (intval(($_POST['edu_start_year'])) > intval(date('Y'))) {
            exit('�Ͷ���ʼʱ�䲻������ڱ�ҵʱ��');
        }
        if (intval($_POST['edu_start_year']) == intval(date('Y')) && intval(($_POST['edu_start_month'])) >= intval(date('m'))) {
            exit('�Ͷ���ʼ�·ݲ�������ڱ�ҵʱ��');
        }
    } else {
        if (trim($_POST['edu_start_year']) == "" || trim($_POST['edu_start_month']) == "" || trim($_POST['edu_end_year']) == "" || trim($_POST['edu_end_month']) == "") {
            exit("��ѡ��Ͷ�ʱ�䣡");
        }
        if (intval(($_POST['edu_start_year'])) > intval($_POST['edu_end_year'])) {
            exit('�Ͷ���ʼʱ�䲻������ڱ�ҵʱ��');
        }
        if (intval($_POST['edu_start_year']) == intval($_POST['edu_end_year']) && intval(($_POST['edu_start_month'])) >= intval($_POST['edu_end_month'])) {
            exit('�Ͷ���ʼ�·ݲ�������ڱ�ҵʱ��');
        }
    }
    $setsqlarr['startyear'] = intval($_POST['edu_start_year']);
    $setsqlarr['startmonth'] = intval($_POST['edu_start_month']);
    $setsqlarr['endyear'] = intval($_POST['edu_end_year']);
    $setsqlarr['endmonth'] = intval($_POST['edu_end_month']);
    $setsqlarr['todate'] = intval($_POST['edu_todate']); // ����
    if ($id) {
        $db->updatetable(table("resume_education"), $setsqlarr, array("id" => $id));
        exit("success");
    } else {
        $insert_id = $db->inserttable(table("resume_education"), $setsqlarr, 1);
        if ($insert_id) {
            perfect_resume($_SESSION['uid'], $_SESSION['username'], intval($_REQUEST['pid']));
            check_resume($_SESSION['uid'], intval($_REQUEST['pid']));
            exit("success");
        } else {
            exit("err");
        }
    }

} elseif ($act == 'ajax_get_education_list') {
    $pid = intval($_GET['pid']);
    $uid = intval($_SESSION['uid']);
    $education_list = get_resume_education($uid, $pid);
    $html = "";
    if ($education_list) {
        foreach ($education_list as $key => $value) {
            // �жϽ���ʱ���Ƿ�����
            $datehtm = '';
            if ($value["todate"] == 1) {
                $datehtm = '����';
            } else {
                $datehtm = $value["endyear"] . '��' . $value["endmonth"] . '��';
            }
            // ===========================
            $html .= '<div class="jl1">
				 	 <div class="l1">' . $value["startyear"] . '��' . $value["startmonth"] . '��-' . $datehtm . '</div>
					 <div class="l2">' . $value["school"] . '</div>
					 <div class="l3">' . $value["speciality"] . '</div>
					 <div class="l4">' . $value["education_cn"] . '</div>
					 <div class="l5">
					 <a class="edit_education" todate="' . $value["todate"] . '" href="javascript:void(0);" url="?act=edit_education&id=' . $value["id"] . '&pid=' . $pid . '"></a>
					 <a class="del_education d" href="javascript:void(0);" pid="' . $pid . '" edu_id="' . $value["id"] . '" ></a><div class="clear"></div>
					 </div>
					 <div class="clear"></div>
				</div>';
        }
    } else {
        $js = '<script type="text/javascript">$("#add_education").hide();$(function(){$(".but130lan_add").hover(function(){$(this).addClass("hover")},function(){$(this).removeClass("hover");})})</script>';
        $html .= '<div class="noinfo" id="education_empty_box">
		 	 <div class="txt">��������������������ѧ����רҵ���������������������ҵ��HR�����ɣ�</div>
			 <div class="addbut">
			 	<input type="button" name="" id="empty_add_education" value="��Ӿ���"  class="but130lan_add"/>
			 </div>
		</div>';
        $html .= $js;
    }

    exit($html);
} //��������-�޸Ľ�������
elseif ($act == 'edit_education') {
    $uid = intval($_SESSION['uid']);
    $pid = intval($_REQUEST['pid']);
    if ($uid == 0 || $pid == 0) exit('���������ڣ�');
    $resume_basic = get_resume_basic(intval($_SESSION['uid']), intval($_REQUEST['pid']));
    if (empty($resume_basic)) exit("������д����������Ϣ��");
    $id = intval($_GET['id']) ? intval($_GET['id']) : exit('��������');
    $education_edit = get_resume_education_one($_SESSION['uid'], $pid, $id);
    foreach ($education_edit as $key => $value) {
        $education_edit[$key] = gbk_to_utf8($value);
    }
    $json_encode = json_encode($education_edit);
    exit($json_encode);
} //��������-ɾ����������
elseif ($act == 'del_education') {
    $id = intval($_GET['id']);
    $sql = "Delete from " . table('resume_education') . " WHERE id='{$id}'  AND uid='" . intval($_SESSION['uid']) . "' AND pid='" . intval($_REQUEST['pid']) . "' LIMIT 1 ";
    if ($db->query($sql)) {
        check_resume($_SESSION['uid'], intval($_REQUEST['pid']));//���¼������״̬
        perfect_resume($_SESSION['uid'], $_SESSION['username'], $id, 2);
        exit('ɾ���ɹ���');
    } else {
        exit('ɾ��ʧ�ܣ�');
    }
} elseif ($act == 'save_work') {
    $id = intval($_POST['id']);
    $setsqlarr['uid'] = intval($_SESSION['uid']);
    $setsqlarr['pid'] = intval($_REQUEST['pid']);
    if ($setsqlarr['uid'] == 0 || $setsqlarr['pid'] == 0) exit('���������ڣ�');
    $resume_basic = get_resume_basic(intval($_SESSION['uid']), intval($_REQUEST['pid']));
    if (empty($resume_basic)) exit("������д����������Ϣ��");
    $resume_work = get_resume_work($_SESSION['uid'], $_REQUEST['pid']);
    if (count($resume_work) >= 6) exit('�����������ܳ���6����');

    $companyname = utf8_to_gbk(trim($_POST['companyname']));
    $jobs = utf8_to_gbk(trim($_POST['jobs']));
    $achievements = utf8_to_gbk(trim($_POST['achievements']));
    $setsqlarr['companyname'] = $companyname ? $companyname : exit("����д��˾���ƣ�");
    check_word($_CFG['filter'], $setsqlarr['companyname']) ? exit($_CFG['filter_tips']) : '';
    $setsqlarr['jobs'] = $jobs ? $jobs : exit("����дְλ���ƣ�");
    check_word($_CFG['filter'], $setsqlarr['jobs']) ? exit($_CFG['filter_tips']) : '';
    // ѡ������Ͳ��жϽ���ʱ����
    if (intval($_POST['work_todate']) == 1) {
        if (trim($_POST['work_start_year']) == "" || trim($_POST['work_start_month']) == "") {
            exit("��ѡ����ְʱ�䣡");
        }
        if (intval(($_POST['work_start_year'])) > intval(date('Y'))) {
            exit('������ʼʱ�䲻������ڹ�������ʱ��');
        }
        if (intval($_POST['work_start_year']) == intval(date('Y')) && intval(($_POST['work_start_month'])) >= intval(date('m'))) {
            exit('������ʼ�·ݲ�������ڹ�������ʱ��');
        }
    } else {
        if (trim($_POST['work_start_year']) == "" || trim($_POST['work_start_month']) == "" || trim($_POST['work_end_year']) == "" || trim($_POST['work_end_month']) == "") {
            exit("��ѡ����ְʱ�䣡");
        }
        if (intval(($_POST['work_start_year'])) > intval($_POST['work_end_year'])) {
            exit('������ʼʱ�䲻������ڹ�������ʱ��');
        }
        if (intval($_POST['work_start_year']) == intval($_POST['work_end_year']) && intval(($_POST['work_start_month'])) >= intval($_POST['work_end_month'])) {
            exit('������ʼ�·ݲ�������ڹ�������ʱ��');
        }
    }
    $setsqlarr['startyear'] = intval($_POST['work_start_year']);
    $setsqlarr['startmonth'] = intval($_POST['work_start_month']);
    $setsqlarr['endyear'] = intval($_POST['work_end_year']);
    $setsqlarr['endmonth'] = intval($_POST['work_end_month']);
    $setsqlarr['achievements'] = $achievements ? $achievements : exit("����д����ְ��");
    $setsqlarr['todate'] = intval($_POST['work_todate']); // ����
    check_word($_CFG['filter'], $setsqlarr['achievements']) ? exit($_CFG['filter_tips']) : '';

    if ($id) {
        $db->updatetable(table("resume_work"), $setsqlarr, array("id" => $id));
        exit("success");
    } else {
        $insert_id = $db->inserttable(table("resume_work"), $setsqlarr, 1);
        if ($insert_id) {
            check_resume($_SESSION['uid'], intval($_REQUEST['pid']));
            perfect_resume($_SESSION['uid'], $_SESSION['username'], $_REQUEST['pid'], 1);
            exit("success");
        } else {
            exit("err");
        }
    }

} elseif ($act == 'ajax_get_work_list') {
    $pid = intval($_GET['pid']);
    $uid = intval($_SESSION['uid']);
    $work_list = get_resume_work($uid, $pid);
    $html = "";
    if ($work_list) {
        foreach ($work_list as $key => $value) {
            // �жϽ���ʱ���Ƿ�����
            $datehtm = '';
            if ($value["todate"] == 1) {
                $datehtm = '����';
            } else {
                $datehtm = $value["endyear"] . '��' . $value["endmonth"] . '��';
            }
            // ===========================
            $html .= '<div class="jl2">
					 	 <div class="l1">' . $value["startyear"] . '��' . $value["startmonth"] . '��-' . $datehtm . '</div>
						 <div class="l2">' . $value["companyname"] . '</div>
						 <div class="l3">' . $value["jobs"] . '</div>
						 <div class="l4">
						 <a class="edit_work" todate="' . $value["todate"] . '" href="javascript:void(0);" url="?act=edit_work&id=' . $value["id"] . '&pid=' . $pid . '"></a>
						 <a class="del_work d" href="javascript:void(0);" pid="' . $pid . '" work_id="' . $value["id"] . '" ></a><div class="clear"></div>
						 <div class="clear"></div>
						 </div>
						 <div class="l5">����ְ��</div>
						 <div class="l6">' . $value["achievements"] . '
						 </div>
						 <div class="clear"></div>
					</div>';
        }
    } else {
        $js = '<script type="text/javascript">$("#add_work").hide();$(function(){$(".but130lan_add").hover(function(){$(this).addClass("hover")},function(){$(this).removeClass("hover");})})</script>';
        $html .= '<div class="noinfo" id="work_empty_box">
			 	 <div class="txt">�������������������ḻ�������ͳ��ڵĹ�������������н�귭���ĳ���ŶHR�����ɣ�</div>
				 <div class="addbut">
				 	<input type="button" name="" id="empty_add_work" value="��Ӿ���"  class="but130lan_add"/>
				 </div>
			</div>';
        $html .= $js;
    }

    exit($html);
} //��������-�޸Ĺ�������
elseif ($act == 'edit_work') {
    $uid = intval($_SESSION['uid']);
    $pid = intval($_REQUEST['pid']);
    if ($uid == 0 || $pid == 0) exit('���������ڣ�');
    $resume_basic = get_resume_basic(intval($_SESSION['uid']), intval($_REQUEST['pid']));
    if (empty($resume_basic)) exit("������д����������Ϣ��");
    $id = intval($_GET['id']) ? intval($_GET['id']) : exit('��������');
    $work_edit = get_resume_work_one($_SESSION['uid'], $pid, $id);
    foreach ($work_edit as $key => $value) {
        $work_edit[$key] = gbk_to_utf8($value);
    }
    $json_encode = json_encode($work_edit);
    exit($json_encode);
} //��������-ɾ����������
elseif ($act == 'del_work') {
    $id = intval($_GET['id']);
    $sql = "Delete from " . table('resume_work') . " WHERE id='{$id}'  AND uid='" . intval($_SESSION['uid']) . "' AND pid='" . intval($_REQUEST['pid']) . "' LIMIT 1 ";
    if ($db->query($sql)) {
        check_resume($_SESSION['uid'], intval($_REQUEST['pid']));//���¼������״̬
        perfect_resume($_SESSION['uid'], $_SESSION['username'], $id, 2);
        exit('ɾ���ɹ���');
    } else {
        exit('ɾ��ʧ�ܣ�');
    }
} elseif ($act == 'save_training') {
    $id = intval($_POST['id']);
    $setsqlarr['uid'] = intval($_SESSION['uid']);
    $setsqlarr['pid'] = intval($_REQUEST['pid']);
    if ($setsqlarr['uid'] == 0 || $setsqlarr['pid'] == 0) exit('���������ڣ�');
    $resume_basic = get_resume_basic(intval($_SESSION['uid']), intval($_REQUEST['pid']));
    if (empty($resume_basic)) exit("������д����������Ϣ��");
    $resume_training = get_resume_training($_SESSION['uid'], $_REQUEST['pid']);
    if (count($resume_training) >= 6) exit('��ѵ�������ܳ���6����');

    $agency = utf8_to_gbk(trim($_POST['agency']));
    $course = utf8_to_gbk(trim($_POST['course']));
    $description = utf8_to_gbk(trim($_POST['description']));
    $setsqlarr['agency'] = $agency ? $agency : exit("����д��ѵ������");
    check_word($_CFG['filter'], $setsqlarr['agency']) ? exit($_CFG['filter_tips']) : '';
    $setsqlarr['course'] = $course ? $course : exit("����д��ѵ�γ̣�");
    check_word($_CFG['filter'], $setsqlarr['course']) ? exit($_CFG['filter_tips']) : '';
    // ѡ������Ͳ��жϽ���ʱ����
    if (intval($_POST['training_todate']) == 1) {
        if (trim($_POST['training_start_year']) == "" || trim($_POST['training_start_month']) == "") {
            exit("��ѡ����ѵʱ�䣡");
        }
        if (intval(($_POST['training_start_year'])) > intval(date('Y'))) {
            exit('��ѵ��ʼʱ�䲻���������ѵ����ʱ��');
        }
        if (intval($_POST['training_start_year']) == intval(date('Y')) && intval(($_POST['training_start_month'])) >= intval(date('m'))) {
            exit('��ѵ��ʼ�·ݲ����������ѵ����ʱ��');
        }
    } else {
        if (trim($_POST['training_start_year']) == "" || trim($_POST['training_start_month']) == "" || trim($_POST['training_end_year']) == "" || trim($_POST['training_end_month']) == "") {
            exit("��ѡ����ѵʱ�䣡");
        }
        if (intval(($_POST['training_start_year'])) > intval($_POST['training_end_year'])) {
            exit('��ѵ��ʼʱ�䲻���������ѵ����ʱ��');
        }
        if (intval($_POST['training_start_year']) == intval($_POST['training_end_year']) && intval(($_POST['training_start_month'])) >= intval($_POST['training_end_month'])) {
            exit('��ѵ��ʼ�·ݲ����������ѵ����ʱ��');
        }
    }
    $setsqlarr['startyear'] = intval($_POST['training_start_year']);
    $setsqlarr['startmonth'] = intval($_POST['training_start_month']);
    $setsqlarr['endyear'] = intval($_POST['training_end_year']);
    $setsqlarr['endmonth'] = intval($_POST['training_end_month']);
    $setsqlarr['description'] = $description ? $description : exit("����д��ѵ���ݣ�");
    $setsqlarr['todate'] = intval($_POST['training_todate']); // ����
    check_word($_CFG['filter'], $setsqlarr['description']) ? exit($_CFG['filter_tips']) : '';

    if ($id) {
        $db->updatetable(table("resume_training"), $setsqlarr, array("id" => $id));
        exit("success");
    } else {
        $insert_id = $db->inserttable(table("resume_training"), $setsqlarr, 1);
        if ($insert_id) {
            check_resume($_SESSION['uid'], intval($_REQUEST['pid']));
            perfect_resume($_SESSION['uid'], $_SESSION['username'], intval($_REQUEST['pid']));
            exit("success");
        } else {
            exit("err");
        }
    }
} elseif ($act == 'ajax_get_training_list') {
    $pid = intval($_GET['pid']);
    $uid = intval($_SESSION['uid']);
    $training_list = get_resume_training($uid, $pid);
    $html = "";
    if ($training_list) {
        foreach ($training_list as $key => $value) {
            // �жϽ���ʱ���Ƿ�����
            $datehtm = '';
            if ($value["todate"] == 1) {
                $datehtm = '����';
            } else {
                $datehtm = $value["endyear"] . '��' . $value["endmonth"] . '��';
            }
            // ===========================
            $html .= '<div class="jl2">
			 	 <div class="l1">' . $value["startyear"] . '��' . $value["startmonth"] . '��-' . $datehtm . '</div>
				 <div class="l2">' . $value["agency"] . '</div>
				 <div class="l3">' . $value["course"] . '</div>
				 <div class="l4">
				 <a class="edit_training" todate="' . $value["todate"] . '" href="javascript:void(0);" url="?act=edit_training&id=' . $value["id"] . '&pid=' . $pid . '"></a>
				 <a class="del_training d" href="javascript:void(0);" pid="' . $pid . '" training_id="' . $value["id"] . '" ></a><div class="clear"></div>
				 </div>
				 <div class="l5">��ѵ���ݣ�</div>
				 <div class="l6">' . $value["description"] . '</div>
				 <div class="clear"></div>
			</div>';
        }
    } else {
        $js = '<script type="text/javascript">$("#add_training").hide();$(function(){$(".but130lan_add").hover(function(){$(this).addClass("hover")},function(){$(this).removeClass("hover");})})</script>';
        $html .= '<div class="noinfo" id="training_empty_box">
		 	 <div class="txt">��ѵ�������������Ͻ�����õ����֣�����˵˵����������ѧϰ�����ɣ�</div>
			 <div class="addbut">
			 	<input type="button" name="" id="empty_add_training" value="��Ӿ���"  class="but130lan_add"/>
			 </div>
		</div>';
        $html .= $js;
    }
    exit($html);
} //��������-�޸���ѵ����
elseif ($act == 'edit_training') {
    $uid = intval($_SESSION['uid']);
    $pid = intval($_REQUEST['pid']);
    if ($uid == 0 || $pid == 0) exit('���������ڣ�');
    $resume_basic = get_resume_basic(intval($_SESSION['uid']), intval($_REQUEST['pid']));
    if (empty($resume_basic)) exit("������д����������Ϣ��");
    $id = intval($_GET['id']) ? intval($_GET['id']) : exit('��������');
    $training_edit = get_resume_training_one($_SESSION['uid'], $pid, $id);
    foreach ($training_edit as $key => $value) {
        $training_edit[$key] = gbk_to_utf8($value);
    }
    $json_encode = json_encode($training_edit);
    exit($json_encode);
} //��������-ɾ����ѵ����
elseif ($act == 'del_training') {
    $id = intval($_GET['id']);
    $sql = "Delete from " . table('resume_training') . " WHERE id='{$id}'  AND uid='" . intval($_SESSION['uid']) . "' AND pid='" . intval($_REQUEST['pid']) . "' LIMIT 1 ";
    if ($db->query($sql)) {
        check_resume($_SESSION['uid'], intval($_REQUEST['pid']));//���¼������״̬
        perfect_resume($_SESSION['uid'], $_SESSION['username'], intval($_REQUEST['pid']), 2);
        exit('ɾ���ɹ���');
    } else {
        exit('ɾ��ʧ�ܣ�');
    }
} //����
elseif ($act == 'ajax_get_language_list') {
    $pid = intval($_GET['pid']);
    $uid = intval($_SESSION['uid']);
    $language_list = get_resume_language($uid, $pid);
    $html = "";
    if ($language_list) {
        foreach ($language_list as $key => $value) {
            $html .= '<div class="jl2">
			 	 <div class="l1">' . $value["language_cn"] . '</div>
				 <div class="l2">' . $value["level_cn"] . '</div>
				  <div class="l3"> </div>
				 <div class="l4">
				 <a class="edit_language" href="javascript:void(0);" url="?act=edit_language&id=' . $value["id"] . '&pid=' . $pid . '"></a>
				 <a class="del_language d" href="javascript:void(0);" pid="' . $pid . '" language_id="' . $value["id"] . '" ></a><div class="clear"></div>
				 </div>
				 <div class="clear"></div>
			</div>';
        }
    } else {
        $js = '<script type="text/javascript">$("#add_language").hide();$(function(){$(".but130lan_add").hover(function(){$(this).addClass("hover")},function(){$(this).removeClass("hover");})})</script>';
        $html .= '<div class="noinfo" id="language_empty_box">
		 	 <div class="txt">�����������������Ͻ�����õ����֣�����˵˵�������������������ɣ�</div>
			 <div class="addbut">
			 	<input type="button" name="" id="empty_add_language" value="�������"  class="but130lan_add"/>
			 </div>
		</div>';
        $html .= $js;
    }
    exit($html);
} elseif ($act == 'save_language') {
    $id = intval($_POST['id']);
    $setsqlarr['uid'] = intval($_SESSION['uid']);
    $setsqlarr['pid'] = intval($_REQUEST['pid']);
    if ($setsqlarr['uid'] == 0 || $setsqlarr['pid'] == 0) exit('���������ڣ�');
    $resume_basic = get_resume_basic(intval($_SESSION['uid']), intval($_REQUEST['pid']));
    if (empty($resume_basic)) exit("������д����������Ϣ��");
    $resume_language = get_resume_language($_SESSION['uid'], $_REQUEST['pid']);
    if (count($resume_language) >= 6) exit('�����������ܳ���6����');

    $language_cn = utf8_to_gbk(trim($_POST['language_cn']));
    $language_level_cn = utf8_to_gbk(trim($_POST['language_level_cn']));

    $setsqlarr['language_cn'] = $language_cn ? $language_cn : exit("�����������ͣ�");
    $setsqlarr['level_cn'] = $language_level_cn ? $language_level_cn : exit("����д���Եȼ���");

    $setsqlarr['language'] = intval($_POST['language']);
    $setsqlarr['level'] = intval($_POST['language_level']);
    if ($id) {
        $db->updatetable(table("resume_language"), $setsqlarr, array("id" => $id));
        exit("success");
    } else {
        $insert_id = $db->inserttable(table("resume_language"), $setsqlarr, 1);
        if ($insert_id) {
            check_resume($_SESSION['uid'], intval($_REQUEST['pid']));
            perfect_resume($_SESSION['uid'], $_SESSION['username'], intval($_REQUEST['pid']));
            exit("success");
        } else {
            exit("err");
        }
    }
} //��������-�޸���ѵ����
elseif ($act == 'edit_language') {
    $uid = intval($_SESSION['uid']);
    $pid = intval($_REQUEST['pid']);
    if ($uid == 0 || $pid == 0) exit('���������ڣ�');
    $resume_basic = get_resume_basic(intval($_SESSION['uid']), intval($_REQUEST['pid']));
    if (empty($resume_basic)) exit("������д����������Ϣ��");
    $id = intval($_GET['id']) ? intval($_GET['id']) : exit('��������');
    $language_edit = get_resume_language_one($_SESSION['uid'], $pid, $id);
    foreach ($language_edit as $key => $value) {
        $language_edit[$key] = gbk_to_utf8($value);
    }
    $json_encode = json_encode($language_edit);
    exit($json_encode);
} //��������-ɾ����ѵ����
elseif ($act == 'del_language') {
    $id = intval($_GET['id']);
    $sql = "Delete from " . table('resume_language') . " WHERE id='{$id}'  AND uid='" . intval($_SESSION['uid']) . "' AND pid='" . intval($_REQUEST['pid']) . "' LIMIT 1 ";
    if ($db->query($sql)) {
        check_resume($_SESSION['uid'], intval($_REQUEST['pid']));//���¼������״̬
        perfect_resume($_SESSION['uid'], $_SESSION['username'], intval($_REQUEST['pid']), 2);
        exit('ɾ���ɹ���');
    } else {
        exit('ɾ��ʧ�ܣ�');
    }
} //֤��
elseif ($act == 'ajax_get_credent_list') {
    $pid = intval($_GET['pid']);
    $uid = intval($_SESSION['uid']);
    $credent_list = get_resume_credent($uid, $pid);
    $html = "";
    if ($credent_list) {
        foreach ($credent_list as $key => $value) {
            $html .= '<div class="jl2">
				 <div class="l1">' . $value["name"] . '</div>
			 	 <div class="l2">' . $value["year"] . '��' . $value["month"] . '��</div>
				 <div class="l3"></div>
				 <div class="l4">
				 <a class="edit_credent" href="javascript:void(0);" url="?act=edit_credent&id=' . $value["id"] . '&pid=' . $pid . '"></a>
				 <a class="del_credent d" href="javascript:void(0);" pid="' . $pid . '" credent_id="' . $value["id"] . '" ></a><div class="clear"></div>
				 </div>
			</div>';
        }
    } else {
        $js = '<script type="text/javascript">$("#add_credent").hide();$(function(){$(".but130lan_add").hover(function(){$(this).addClass("hover")},function(){$(this).removeClass("hover");})})</script>';
        $html .= '<div class="noinfo" id="credent_empty_box">
		 	 <div class="txt">֤�����������Ͻ�����õ����֣�����˵˵���������Ļ�õ�֤��ɣ�</div>
			 <div class="addbut">
			 	<input type="button" name="" id="empty_add_credent" value="���֤��"  class="but130lan_add"/>
			 </div>
		</div>';
        $html .= $js;
    }
    exit($html);
} elseif ($act == 'save_credent') {
    $id = intval($_POST['id']);
    $setsqlarr['uid'] = intval($_SESSION['uid']);
    $setsqlarr['pid'] = intval($_REQUEST['pid']);
    if ($setsqlarr['uid'] == 0 || $setsqlarr['pid'] == 0) exit('���������ڣ�');
    $resume_basic = get_resume_basic(intval($_SESSION['uid']), intval($_REQUEST['pid']));
    if (empty($resume_basic)) exit("������д����������Ϣ��");
    $resume_language = get_resume_language($_SESSION['uid'], $_REQUEST['pid']);
    if (count($resume_language) >= 6) exit('֤�鲻�ܳ���6����');

    $credent = utf8_to_gbk(trim($_POST['credent']));
    check_word($_CFG['filter'], $setsqlarr['credent']) ? exit($_CFG['filter_tips']) : '';
    $setsqlarr['name'] = $credent ? $credent : exit("����д֤�����ƣ�");
    $setsqlarr['year'] = intval($_POST['credent_year']) ? intval($_POST['credent_year']) : exit("��ѡ���꣡");
    $setsqlarr['month'] = intval($_POST['credent_month']) ? intval($_POST['credent_month']) : exit("��ѡ���£�");

    if ($id) {
        $db->updatetable(table("resume_credent"), $setsqlarr, array("id" => $id));
        exit("success");
    } else {
        $insert_id = $db->inserttable(table("resume_credent"), $setsqlarr, 1);
        if ($insert_id) {
            check_resume($_SESSION['uid'], intval($_REQUEST['pid']));
            perfect_resume($_SESSION['uid'], $_SESSION['username'], intval($_REQUEST['pid']));
            exit("success");
        } else {
            exit("err");
        }
    }
} elseif ($act == 'edit_credent') {
    $uid = intval($_SESSION['uid']);
    $pid = intval($_REQUEST['pid']);
    if ($uid == 0 || $pid == 0) exit('���������ڣ�');
    $resume_basic = get_resume_basic(intval($_SESSION['uid']), intval($_REQUEST['pid']));
    if (empty($resume_basic)) exit("������д����������Ϣ��");
    $id = intval($_GET['id']) ? intval($_GET['id']) : exit('��������');
    $credent_edit = get_resume_credent_one($_SESSION['uid'], $pid, $id);
    foreach ($credent_edit as $key => $value) {
        $credent_edit[$key] = gbk_to_utf8($value);
    }
    $json_encode = json_encode($credent_edit);
    exit($json_encode);
} //ɾ��֤��
elseif ($act == 'del_credent') {
    $id = intval($_GET['id']);
    $sql = "select * from " . table('resume_credent') . " where id='" . $id . "' AND uid='" . intval($_SESSION['uid']) . "'  LIMIT 1 ";
    $credent_one = $db->getone($sql);
    $images = $credent_one['images'];
    @unlink('../../data/credent_photo/' . $images);
    $sql = "Delete from " . table('resume_credent') . " WHERE id='{$id}'  AND uid='" . intval($_SESSION['uid']) . "' AND pid='" . intval($_REQUEST['pid']) . "' LIMIT 1 ";
    if ($db->query($sql)) {
        check_resume($_SESSION['uid'], intval($_REQUEST['pid']));//���¼������״̬
        perfect_resume($_SESSION['uid'], $_SESSION['username'], intval($_REQUEST['pid']), 2);
        exit('ɾ���ɹ���');
    } else {
        exit('ɾ��ʧ�ܣ�');
    }
}//�ϴ�֤��
elseif ($act == 'credent_photo') {
    !$_FILES['credent_photo']['name'] ? exit('���ϴ�ͼƬ��') : "";
    require_once(QISHI_ROOT_PATH . 'include/cut_upload.php');
    $up_res_original = "../../data/credent_photo/";
    $cdate = date("Y/m/d/");
    $mkdir = $up_res_original . $cdate;
    make_dir($mkdir);
    $images = _asUpFiles($up_res_original . $cdate, "credent_photo", $_CFG['resume_photo_max'], 'gif/jpg/bmp/png', true);
    $data['save_url'] = $cdate . $images;
    $images = $mkdir . $images;
    $data['url'] = $images;
    $json_encode = json_encode($data);
    exit($json_encode);
} ////�ϴ�WORD
elseif ($act == 'word_upload') {
    $pid = intval($_GET['pid']);
    $setsqlarr['uid'] = intval($_SESSION['uid']);
    !$_FILES['word_resume']['name'] ? exit('���ϴ��ļ���') : "";
    require_once(QISHI_ROOT_PATH . 'include/cut_upload.php');
    $up_res_original = "../../data/word/";
    $cdate = date("Y/m/d/");
    $mkdir = $up_res_original . $cdate;
    make_dir($mkdir);
    $word = get_resume_basic($setsqlarr['uid'], $pid);
    $word_resume = _asUpFiles($up_res_original . $cdate, "word_resume", 2048, 'doc', true);
    $setsqlarr['word_resume'] = $cdate . $word_resume;
    $db->updatetable(table("resume"), $setsqlarr, array("id" => $pid));
    perfect_resume($_SESSION['uid'], $_SESSION['username'], $pid);
    @unlink(QISHI_ROOT_PATH . "data/word/" . $word['word_resume']);
    $data['save_url'] = $setsqlarr['word_resume'];
    $json_encode = json_encode($data);
    exit($json_encode);
} // ɾ�� word
elseif ($act == "word_del") {
    $pid = $_POST['pid'] ? intval($_POST['pid']) : exit("����ID��ʧ");
    $uid = intval($_SESSION['uid']);
    $word = get_resume_basic($uid, $pid);
    @unlink(QISHI_ROOT_PATH . "data/word/" . $word['word_resume']);
    $setarr['word_resume'] = "";
    perfect_resume($_SESSION['uid'], $_SESSION['username'], $pid, 2);
    $db->updatetable(table("resume"), $setarr, array('uid' => $uid, "id" => $pid)) ? exit("ɾ���ɹ�") : exit("ɾ���ɹ�");
} elseif ($act == "ajax_save_specialty") {
    $uid = intval($_SESSION['uid']);
    $pid = $_POST['pid'] ? intval($_POST['pid']) : exit("����ID��ʧ");
    $specialty = $_POST['specialty'] ? iconv("utf-8", "gbk", trim($_POST['specialty'])) : exit("��������������");
    $setarr['specialty'] = $specialty;
    perfect_resume($_SESSION['uid'], $_SESSION['username'], $pid, 1);
    $db->updatetable(table('resume'), $setarr, array("id" => $pid, "uid" => $uid)) ? exit("ok") : exit("����ʧ��");
} // ajax ���� ����ͼƬ
elseif ($act == "ajax_resume_img_save") {
    $uid = intval($_SESSION['uid']);
    $pid = $_GET['pid'] ? intval($_GET['pid']) : exit("����ID��ʧ");
    $n = $db->get_total("SELECT COUNT(*) AS num FROM " . table('resume_img') . " WHERE uid=$uid and resume_id=$pid ");
    if ($n >= 4) {
        exit("-7");
    }
    require_once(QISHI_ROOT_PATH . 'include/upload.php');
    !$_FILES['resume_img']['name'] ? exit('���ϴ�ͼƬ��') : "";
    $datedir = date("Y/m/d/");
    $up_dir = "../../data/photo/" . $datedir;
    make_dir($up_dir);
    $setsqlarr['img'] = _asUpFiles($up_dir, "resume_img", 800, 'gif/jpg/bmp/png/jpeg', true);
    if ($setsqlarr['img']) {
        // ������ˮӡ
        if (extension_loaded('gd')) {
            include_once(QISHI_ROOT_PATH . 'include/watermark.php');
            $font_dir = QISHI_ROOT_PATH . "data/contactimgfont/cn.ttc";
            if (file_exists($font_dir)) {
                $tpl = new watermark;
                $tpl->img($up_dir . $setsqlarr['img'], gbk_to_utf8($_CFG['site_name']), $font_dir, 13, 0);
            }
        }
        $img_src = $up_dir . $setsqlarr['resume_img'];
        makethumb($img_src, $up_dir, 600, 600);
        $setsqlarr['uid'] = $uid;
        $setsqlarr['resume_id'] = $pid;
        $setsqlarr['addtime'] = time();
        $setsqlarr['img'] = $datedir . $setsqlarr['img'];
        $img_id = $db->inserttable(table('resume_img'), $setsqlarr, true);
        if ($img_id > 0) {
            perfect_resume($_SESSION['uid'], $_SESSION['username'], $pid, 1);
            $data['save_url'] = $setsqlarr['img'];
            $data['url'] = $setsqlarr['img'];
            $data['title'] = $setsqlarr['title'];
            $data['addtime'] = date('Y-m-d', $setsqlarr['addtime']);
            $data['id'] = $img_id;
            $json_encode = json_encode($data);
            exit($json_encode);
        } else {
            exit("-6");
        }
    } else {
        exit("-6");
    }
} // ���渽������
elseif ($act == "ajax_resume_img_title_save") {
    $uid = intval($_SESSION['uid']);
    $img_id = $_POST['id'] ? intval($_POST['id']) : exit("ID��ʧ");
    $setarr['title'] = $_POST['title'] ? iconv("utf-8", "gbk", trim($_POST['title'])) : exit("�����뱸ע��");
    $db->updatetable(table("resume_img"), $setarr, array("id" => $img_id, "uid" => $uid)) ? exit("��ӱ�ע�ɹ�") : exit("��ӱ�עʧ��");
} // ɾ������ ͼƬ
elseif ($act == "ajax_resume_img_del") {
    global $_CFG;
    $uid = intval($_SESSION['uid']);
    $img_id = $_POST['id'] ? intval($_POST['id']) : exit("ID��ʧ");
    $row = $db->getone("select img from " . table("resume_img") . " where id=$img_id and uid=$uid limit 1");
    @unlink("../../data/photo/" . $row['img']);
    perfect_resume($_SESSION['uid'], $_SESSION['username'], $pid, 2);
    $db->query("delete from " . table("resume_img") . " where id=$img_id and uid=$uid limit 1") ? exit("ɾ���ɹ�") : exit("ɾ��ʧ��");
} // ajax �����س���ǩ
elseif ($act == "ajax_save_tag") {
    $uid = intval($_SESSION['uid']);
    $pid = $_POST['pid'] ? intval($_POST['pid']) : exit("����ID��ʧ");
    $tag = $_POST['tag'] ? iconv("utf-8", "gbk", trim($_POST['tag'])) : "";
    $tag_cn = $_POST['tag_cn'] ? iconv("utf-8", "gbk", trim($_POST['tag_cn'])) : "";
    $setarr['tag'] = $tag;
    $setarr['tag_cn'] = $tag_cn;
    add_resume_tag($pid, $uid, $tag);
    if (empty($tag_cn)) {
        perfect_resume($_SESSION['uid'], $_SESSION['username'], $pid, 2);
    } else {
        perfect_resume($_SESSION['uid'], $_SESSION['username'], $pid, 1);
    }
    $db->updatetable(table('resume'), $setarr, array("id" => $pid, "uid" => $uid)) ? exit("ok") : exit("����ʧ��");
} // ����������ť
elseif ($act == "edit_resume_save") {
    $uid = intval($_SESSION['uid']);
    $pid = $_POST['pid'] ? intval($_POST['pid']) : showmsg("����ID��ʧ", 1);
    $resume_basic = get_resume_basic($uid, $pid);
    $auto_refresh = intval($_POST['auto_refresh']);
    // 3�����Զ�ˢ��
    if ($auto_refresh == 1) {
        $time = time();
        $auto_refresh_row = $db->getone("select * from  " . table("resume_auto_refresh") . " where  resume_id=" . $pid . " and  deadline_time>$time limit 1 ");

        $setarr['deadline_time'] = $time + 3600 * 24 * 3;
        if (empty($auto_refresh_row)) {
            $setarr['resume_id'] = $pid;
            $db->inserttable(table("resume_auto_refresh"), $setarr);
        } else {
            $db->updatetable(table("resume_auto_refresh"), $setarr, array("resume_id" => $pid));
        }
    }
    $auto_apply = intval($_POST['auto_apply']);
    // 3���� �Զ�Ͷ��
    if ($auto_apply == 1) {
        $time = time();
        $auto_apply_row = $db->getone("select * from " . table("resume_entrust") . " where id=$pid and uid=$uid and entrust_end>$time ");
        if (empty($auto_apply_row)) {
            $setarr_apply['id'] = $pid;
            $setarr_apply['uid'] = $uid;
            $setarr_apply['fullname'] = $resume_basic['fullname'];
            $setarr_apply['entrust'] = 1;
            $setarr_apply['entrust_start'] = time();
            $setarr_apply['entrust_end'] = $time + 3600 * 24 * 3;;
            $setarr_apply['isshield'] = 0;
            $setarr_apply['resume_addtime'] = $resume_basic['addtime'];
            $db->inserttable(table("resume_entrust"), $setarr_apply);
            $db->query("update " . table("resume") . " set entrust=1 where id=$pid and uid=$uid ");
        }
    }
    $make = intval($_POST['make']);
    check_resume($uid, $pid);
    if ($make == 1) {
        header("Location: ?act=make1_succeed&pid=" . $pid);
    } else {
        header("Location: ?act=resume_list");
    }

} elseif ($act == 'edit_resume') {
    $uid = intval($_SESSION['uid']);
    $pid = intval($_REQUEST['pid']);
    if ($_GET['make'] == 1) {
        $title = "��������";
    } else {
        $title = "�޸ļ���";
    }
    $smarty->assign('h_title', $title);
    $_SESSION['send_mobile_key'] = mt_rand(100000, 999999);
    $smarty->assign('send_key', $_SESSION['send_mobile_key']);
    $resume_basic = get_resume_basic($uid, $pid);
    $smarty->assign('resume_basic', $resume_basic);
    $smarty->assign('resume_education', get_resume_education($uid, $pid));
    $smarty->assign('resume_work', get_resume_work($uid, $pid));
    $smarty->assign('resume_training', get_resume_training($uid, $pid));
    $smarty->assign('resume_language', get_resume_language($uid, $pid));
    $smarty->assign('resume_credent', get_resume_credent($uid, $pid));
    $smarty->assign('resume_img', get_resume_img($uid, $pid));
    $smarty->assign('subsite', get_all_subsite());
    $subsite_cn = explode('/', $resume_basic['district_cn']);
    $smarty->assign('subsite_cn', $subsite_cn[0]);
    $smarty->assign('district_cn', $subsite_cn[1]);
    //��������
    $smarty->assign('district', get_subsite_district($resume_basic['district']));

    $resume_jobs = get_resume_jobs($pid);
    if ($resume_jobs) {
        foreach ($resume_jobs as $rjob) {
            $jobsid[] = $rjob['topclass'] . "." . $rjob['category'] . "." . $rjob['subclass'];
        }
        $resume_jobs_id = implode(",", $jobsid);
    }
    $smarty->assign('resume_jobs_id', $resume_jobs_id);
    $smarty->assign('act', $act);
    $smarty->assign('pid', $pid);
    $smarty->assign('user', $user);
    $smarty->assign('title', '�ҵļ��� - ���˻�Ա���� - ' . $_CFG['site_name']);
    $captcha = get_cache('captcha');
    $smarty->assign('verify_resume', $captcha['verify_resume']);
    $smarty->assign('go_resume_show', $_GET['go_resume_show']);
    $smarty->display('member_personal/personal_resume_edit.htm');
} elseif ($act == 'save_resume_privacy') {
    $uid = intval($_SESSION['uid']);
    $pid = intval($_REQUEST['pid']);
    $setsqlarr['display'] = intval($_POST['display']);
    $setsqlarr['display_name'] = intval($_POST['display_name']);
    $setsqlarr['photo_display'] = intval($_POST['photo_display']);
    $wheresql = " uid='" . $_SESSION['uid'] . "' ";
    !$db->updatetable(table('resume'), $setsqlarr, " uid='{$uid}' AND  id='{$pid}'");
    $setsqlarrdisplay['display'] = intval($_POST['display']);
    !$db->updatetable(table('resume_search_key'), $setsqlarrdisplay, " uid='{$uid}' AND  id='{$pid}'");
    !$db->updatetable(table('resume_search_rtime'), $setsqlarrdisplay, " uid='{$uid}' AND  id='{$pid}'");
    write_memberslog($_SESSION['uid'], 2, 1104, $_SESSION['username'], "���ü�����˽({$pid})");
} elseif ($act == 'talent_save') {
    $uid = intval($_SESSION['uid']);
    $pid = intval($_REQUEST['pid']);
    $resume = get_resume_basic($uid, $pid);
    if ($resume['complete_percent'] < $_CFG['elite_resume_complete_percent']) {
        showmsg("��������ָ��С��{$_CFG['elite_resume_complete_percent']}%����ֹ���룡", 0);
    }
    $setsqlarr['talent'] = 3;
    $wheresql = " uid='{$uid}' AND id='{$pid}' ";
    $db->updatetable(table('resume'), $setsqlarr, $wheresql);
    write_memberslog($uid, 2, 1107, $_SESSION['username'], "����߼��˲�");
    showmsg('����ɹ�����ȴ�����Ա��ˣ�', 2);
} //�����ⷢ
elseif ($act == 'resume_outward') {
    $captcha = get_cache('captcha');
    $smarty->assign('verify_resume_outward', $captcha['verify_resume_outward']);
    $smarty->assign('act', $act);
    $smarty->assign('resume_list', get_auditresume_list($_SESSION['uid']));
    $smarty->assign('title', '�ⷢ���� - ���˻�Ա���� - ' . $_CFG['site_name']);
    $smarty->display('member_personal/personal_resume_outward.htm');
} //�����ⷢ ����
elseif ($act == 'outward_save') {
    //�����ⷢ��¼
    $captcha = get_cache('captcha');
    $postcaptcha = trim($_POST['postcaptcha']);
    if ($captcha['verify_resume_outward'] == '1' && empty($postcaptcha)) {
        showmsg("����д��֤��", 1);
    }
    if ($captcha['verify_resume_outward'] == '1' && strcasecmp($_SESSION['imageCaptcha_content'], $postcaptcha) != 0) {
        showmsg("��֤�����", 1);
    }
    $setsqlarr['resume_id'] = intval($_POST['resume_id']) ? intval($_POST['resume_id']) : showmsg('��ѡ���ͼ�����', 1);
    $setsqlarr['resume_title'] = trim($_POST['resume_title']) ? trim($_POST['resume_title']) : showmsg('��ѡ���ͼ�����', 1);
    $setsqlarr['email'] = trim($_POST['email']) ? trim($_POST['email']) : showmsg('����д�����ַ��', 1);
    $setsqlarr['companyname'] = trim($_POST['companyname']) ? trim($_POST['companyname']) : showmsg('����д��ҵ���ƣ�', 1);
    check_word($_CFG['filter'], $_POST['companyname']) ? showmsg($_CFG['filter_tips'], 0) : '';
    $setsqlarr['jobs_name'] = trim($_POST['jobs_name']) ? trim($_POST['jobs_name']) : showmsg('����дְλ���ƣ�', 1);
    check_word($_CFG['filter'], $_POST['jobs_name']) ? showmsg($_CFG['filter_tips'], 0) : '';
    //��֤�����Ƿ���ȷ
    $resume_tpl = get_outward_resumes_tpl($_SESSION['uid'], $setsqlarr['resume_id']);
    $email = explode(';', $setsqlarr['email']);
    foreach ($email as $key => $value) {
        if (!preg_match("/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/", $value)) {
            showmsg('����ȷ��д�����ַ��', 1);
        }
        $rst = smtp_mail($value, $setsqlarr['resume_title'], $resume_tpl);
        if ($rst != '1') {
            showmsg('������:{$value}����ʧ�ܣ�', 0);
        }
    }
    $setsqlarr['uid'] = intval($_SESSION['uid']);
    $setsqlarr['addtime'] = time();
    //��Ӽ����ⷢ��¼
    $oid = $db->inserttable(table('resume_outward'), $setsqlarr, true);
    if (empty($oid)) {
        showmsg("���ʧ�ܣ�", 0);
    } else {
        showmsg("��ӳɹ���", 2);
    }
} elseif ($act == 'resume_import') {
    if ($_SESSION['cookie_name']) {
        @unlink(QISHI_ROOT_PATH . '/data/tmp/' . $_SESSION['cookie_name']);
        unset($_SESSION['cookie_name']);
    }
    $cookie_path = tempnam(QISHI_ROOT_PATH . '/data/tmp', 'cookie');
    $cookie_name = basename($cookie_path);
    $_SESSION['cookie_name'] = $cookie_name;
    $smarty->assign('act', $act);
    // $smarty->assign('resume_list',get_auditresume_list($_SESSION['uid']));
    $smarty->assign('title', '�������� - ���˻�Ա���� - ' . $_CFG['site_name']);
    $smarty->display('member_personal/personal_import_resume.htm');
} elseif ($act == 'get_import_resume_list') {
    $username = trim($_GET['username']);
    $password = trim($_GET['password']);
    $source = trim($_GET['source']);
    $cookie_path = QISHI_ROOT_PATH . '/data/tmp/' . $_SESSION['cookie_name'];
    require_once(QISHI_ROOT_PATH . 'include/CollectionZhilian.class.php');
    $collection = new CollectionZhilian($username, $password, $cookie_path);
    $cookie = file_get_contents($cookie_path);
    if (!$cookie) {
        $collection->login();
    }
    $resume_arr = $collection->get_resume_list();
    if (empty($resume_arr['resume_id_arr'])) {
        exit("-1");
    } else {
        foreach ($resume_arr['resume_id_arr'] as $key => $value) {
            $return_data[$key]['id'] = $value;
            $return_data[$key]['title'] = iconv('utf-8', 'gbk', $resume_arr['resume_title_arr'][$key]);
        }
        $smarty->assign('return_data', $return_data);
        $smarty->display('member_personal/ajax_import_resume_list.htm');
    }
} elseif ($act == 'do_import_resume') {
    $total = $db->get_total("SELECT COUNT(*) AS num FROM " . table('resume') . " WHERE uid='{$_SESSION['uid']}'");
    if ($total >= intval($_CFG['resume_max'])) {
        //�����������
        exit('-1');
    } else {
        $id = $_GET['id'];
        require_once(QISHI_ROOT_PATH . 'include/CollectionZhilian.class.php');
        $resume_info = CollectionZhilian::split_resume($id, QISHI_ROOT_PATH . '/data/tmp/' . $_SESSION['cookie_name']);
        $result = do_import_resume($resume_info, $_SESSION['uid']);
        if ($result) {
            exit('1');
        } else {
            exit('-2');
        }
    }
}//����
elseif ($act == 'fortune') {


    $smarty->assign('title', '�����Ը���� - ���˻�Ա���� - ' . $_CFG['site_name']);

    $smarty->display('member_personal/personal_fortune.htm');
} elseif ($act == 'fotrune_save') {
    require_once(QISHI_ROOT_PATH . 'genv/lib.php');

    require_once QISHI_ROOT_PATH . 'genv/Requests/library/Requests.php';
    Requests::register_autoloader();

    $name = isChineseName(trim($_POST['name'])) ? ($_POST['name']) : showmsg('��������ȷ������', 1);

    $points_rule = get_cache('points_rule');
    $user_points = get_user_points($_SESSION["uid"]);

    if ($user_points < $points_rule['fotrune_points']['value']) {
        showmsg("��«�Ҳ���,���ֵ", 2);
    }
    $_POST["id"] = $_POST["category"];;

    $uri = 'http://192.168.1.102/mpfxsys_ajax.asp';
    $post = $_POST;
    if ($_POST["category"] == 7) {
        $post["showming"] = 1;
    } else {
        $post["showming"] = 0;
    }

    $html = Requests::post($uri, array(), $post);

    $html = ($html->body);

    $html = preg_replace('~<(tr|table)\s+?.*?>~i', '<$1>', $html);
    $html = preg_replace('~<(td).*?(colspan=["\']?\d+["\']?|rowspan=["\']?\d+["\']?).*?>~i', '<td $2>', $html);
    $html = str_replace('#DEE2FA', '#f5f5f5', $html);

    global $db;
    $data = array();
    $data["name"] = $name;
    $data["category"] = intval($_POST["category"]);
    $category_cn = "";
    switch (intval($_POST["category"])) {
        case 1:
            $category_cn = "�Ը����";
            break;
        case 2:
            $category_cn = "��ô�ദ��������";
            break;
        case 3:
            $category_cn = "���׷����ϲ������";
            break;
        case 4:
            $category_cn = "��Ϊ���޻���ô��";
            break;
        case 5:
            $category_cn = "��Ҫ�л��ҵ�ͬ��";
            break;
        case 6:
            $category_cn = "Ҫ���̳�սʤ�ͻ�";
            break;
        case 7:
            $category_cn = "����";
            break;
    }
    $data["category_cn"] = $category_cn;
    $data["sex"] = $_POST["X2"];
    $data["uid"] = $_SESSION["uid"];
    $data["addtime"] = time();
    $data["result"] = (trim($html));

    //echo json_encode($testJSON);

    foreach ($_POST as $key => $value) {
        $_POST[$key] = urlencode($value);
    }


    $data["json"] = urldecode(json_encode($_POST));

    $new = ORM::for_table(table("fotrune"))->create($data);
    $rs = $new->save();
    fortune($_SESSION['uid'], $_SESSION['username'], $new->id, 2);

    if ($rs) {
        header("Location: ?act=fotrune_show&id=" . $new->id);
    } else {
        showmsg("����ʧ��", 2);
    }

} elseif ($act == 'fotrune_show') {
    require_once(QISHI_ROOT_PATH . 'genv/lib.php');

    global $db;

    $id = $_GET["id"];
    $sql = "select * from " . table("fotrune") . " where id=" . $id;
    $rs = ORM::for_table(table("fotrune"))->find_one($id);
    if (!$rs) {
        showmsg("û�ҵ������¼", 2);
    }
    $rs = $rs->as_array();

    $smarty->assign('title', '�����Ը���� - ���˻�Ա���� - ' . $_CFG['site_name']);
    $smarty->assign('result', $rs);
    $smarty->display('member_personal/personal_fortune_result.htm');

} elseif ($act == 'fotrune_list') {
    require_once(QISHI_ROOT_PATH . 'genv/lib.php');

    require_once(QISHI_ROOT_PATH . 'include/page.class.php');
    require_once(QISHI_ROOT_PATH . 'genv/func_company.php');

    $wheresql .= "where uid=" . $_SESSION['uid'];

    $perpage = 10;
    $total_sql = "SELECT COUNT(*) AS num FROM " . table('fotrune') . "  {$wheresql} ";
    $total = $db->get_total($total_sql);
    $page = new page(array('total' => $total, 'perpage' => $perpage));
    $offset = ($page->nowindex - 1) * $perpage;
    $smarty->assign('act', $act);
    $smarty->assign('title', '�����Ը���� - ���˻�Ա���� - ' . $_CFG['site_name']);
    $smarty->assign('fortune_list', get_fortune($offset, $perpage, $wheresql));
    if ($total > $perpage) {
        $smarty->assign('page', $page->show(3));
    }

    $smarty->display('member_personal/personal_fortune_list.htm');
}

unset($smarty);
?>