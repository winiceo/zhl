<?php
/*
 * 74cms 企业会员中心
*/
define('IN_QISHI', true);

require_once(dirname(__FILE__) . '/company_common.php');
$smarty->assign('leftmenu', "recruitment");
//error_reporting(-1);
if ($act == 'upload') {

    if ($company_profile["audit"]!=1){

        $link[0]['text'] = "请上传营业执照,才有资格进行简历置换";
        $link[0]['href'] = 'company_info.php?act=company_auth';
        showmsg("请上传营业执照,才有资格进行简历置换",1,$link);

    }
    require_once(QISHI_ROOT_PATH . 'include/page.class.php');

    $wheresql .= "where uid=" . $_SESSION['uid'];

    $perpage = 10;
    $total_sql = "SELECT COUNT(*) AS num FROM " . table('resume_upload') . "  {$wheresql} ";
    $total = $db->get_total($total_sql);
    $page = new page(array('total' => $total, 'perpage' => $perpage));
    $offset = ($page->nowindex - 1) * $perpage;
    $smarty->assign('act', $act);
    $smarty->assign('title', '批量上传简历 - 企业会员中心 - ' . $_CFG['site_name']);


    $smarty->display('member_company/company_upload.htm');
} elseif ($act == 'upload_save') {
    //error_reporting(-1);
    require_once(QISHI_ROOT_PATH . 'include/upload.php');
    require_once(QISHI_ROOT_PATH . 'genv/lib.php');
    require_once(QISHI_ROOT_PATH . 'genv/func_resume_upload.php');


    !$_FILES['logo']['name'] ? showmsg('请上传文件！', 1) : "";
    $uplogo_dir = "../../data/xls/" . date("Y/m/d/");
    make_dir($uplogo_dir);
    $setsqlarr['path'] = _asUpFiles($uplogo_dir, "logo", 1024 * 5, 'xls', true);

    if ($setsqlarr['path']) {
        $setsqlarr['path'] = date("Y/m/d/") . $setsqlarr['path'];

        $setsqlarr['name'] =& $_FILES["logo"]["name"];
        $setsqlarr['uid'] = $_SESSION['uid'];
        $setsqlarr['addtime'] = time();

        if ($db->inserttable(table('resume_upload'), $setsqlarr, 1)) {
            $link[0]['text'] = "上传了简历";
            $link[0]['href'] = '?act=upload_list';
            write_memberslog($_SESSION['uid'], 1, 8003, $_SESSION['username'], "上传了简历");

            $path =  "data/xls/" . $setsqlarr['path'];

            $excel = excel_upload(QISHI_ROOT_PATH.$path);

            $data = $excel["data"];
            foreach ($data as $key => $value) {
                $data[$key]["color"] = "#ffffff";
                if (get_telephone($value["telephone"])) {
                    $data[$key]["color"] = "#E8AFC5";
                }
                if (!reg_email($value["email"])||!reg_mobile($value["telephone"])||!reg_name($value["fullname"])) {
                    $data[$key]["color"] = "gray";
                }

            }


            $smarty->assign("colsinfo", $excel["cols"]);
            $smarty->assign("uploads", $data);
            $smarty->assign("file", $path);
            $smarty->display('member_company/company_upload_save.htm');

            //showmsg('上传成功！',2,$link);
        } else {
            showmsg('保存失败！', 1);
        }
    } else {
        showmsg('保存失败！', 1);
    }
} elseif ($act == 'upload_finish') {

    global $_CFG;

    require_once(QISHI_ROOT_PATH . 'include/upload.php');
    require_once(QISHI_ROOT_PATH . 'genv/lib.php');
    require_once(QISHI_ROOT_PATH . 'genv/func_resume_upload.php');

    $path = !empty($_REQUEST['file']) ? $_REQUEST['file'] : showmsg("上传文件错误，请重新上传", 1);


    $data = resume_upload_insert($path);
    $link = array(array("title" => "查看上传列表", "href" => "/user/company/company_upload.php?act=upload_list"));
    showmsg("成功上传简历" . count($data) . "条", 1, $link);


} elseif ($act == 'upload_list') {
    require_once(QISHI_ROOT_PATH . 'include/page.class.php');
    require_once(QISHI_ROOT_PATH . 'genv/func_resume_upload.php');

    $wheresql .= "where upload_uid=" . $_SESSION['uid'];

    $perpage = 10;
    $total_sql = "SELECT COUNT(*) AS num FROM " . table('resume_temp') . "  {$wheresql} ";
    $total = $db->get_total($total_sql);
    $page = new page(array('total' => $total, 'perpage' => $perpage));
    $offset = ($page->nowindex - 1) * $perpage;



    $smarty->assign('act', $act);
    $smarty->assign('title', '批量上传简历 - 企业会员中心 - ' . $_CFG['site_name']);
    $data=get_upload($offset, $perpage, $wheresql);
    foreach($data as $k=>$v){
        $data[$k]["upload_time"]=date('Y-m-d H:i:s', $v["upload_time"]);
    }


    $smarty->assign('uploads', $data);
    if ($total > $perpage) {
        $smarty->assign('page', $page->show(3));
    }
    $rs=$db->getone("SELECT *  FROM " . table('members') . " where uid=".$_SESSION['uid']." limit 1");


    $smarty->assign('userinfo', $rs);
    $smarty->assign('total', $total);

    $smarty->display('member_company/company_upload_list.htm');
} elseif ($act == 'cheking_resume') {
    //error_reporting(-1);
    if (!$cominfo_flge){

            $link[0]['text'] = "完善企业资料";
            $link[0]['href'] = 'company_info.php?act=company_profile';
            showmsg("为了达到更好的招聘效果，请先完善您的企业资料！",1,$link);

    }
     $passed = $db->getone("select * from "  . table('members') ." where uid=".$_SESSION["uid"]." and resume_passed>=1000");


    if (!$passed){

        $link[0]['text'] = "您的简历审核通过数少于1000,无法审请";
         showmsg("您的简历审核通过数少于1000,无法审请",1);

    }
    //待审核简历
    require_once(QISHI_ROOT_PATH . 'include/page.class.php');
    require_once(QISHI_ROOT_PATH . 'genv/func_resume_upload.php');

    $wheresql .= "where r.status=0 and r.upload_uid!=" . $_SESSION["uid"] . " ";

    $perpage = 10;
    $total_sql = "SELECT COUNT(*) AS num FROM " . table('resume_temp') . " as r  {$wheresql} ";
    $total = $db->get_total($total_sql);
    $page = new page(array('total' => $total, 'perpage' => $perpage));
    $offset = ($page->nowindex - 1) * $perpage;
    $smarty->assign('act', $act);
    $smarty->assign('title', '批量上传简历 - 企业会员中心 - ' . $_CFG['site_name']);
    $smarty->assign('resumes', get_resume_temp($offset, $perpage, $wheresql));
    if ($total > $perpage) {
        $smarty->assign('page', $page->show(3));
    }
    $rs = $db->getone("select * from " . table("resume_check_apply") . " where uid=" . $_SESSION["uid"]);

    if ($rs) {
        global $db;
        $sql = "SELECT *  FROM " . table('resume_check_apply_log') . " where cid=".$rs["id"]." order by id desc";

        $result=$db->query($sql);
        $row_arr=array();
        while ($row = $db->fetch_array($result)) {

            $row_arr[] = $row;
        }



        $smarty->assign("loglist",$row_arr);
    } else {
        $smarty->assign("is_check", 0);

    }


    $smarty->assign('apply_rs', $rs);

    $smarty->display('member_company/company_checking.htm');

} elseif ($act == 'my_checked_resume') {
    require_once(QISHI_ROOT_PATH . 'include/page.class.php');

    $wheresql .= "where uid=" . $_SESSION['uid'];

    $perpage = 10;
    $total_sql = "SELECT COUNT(*) AS num FROM " . table('resume_upload') . "  {$wheresql} ";
    $total = $db->get_total($total_sql);
    $page = new page(array('total' => $total, 'perpage' => $perpage));
    $offset = ($page->nowindex - 1) * $perpage;
    $smarty->assign('act', $act);
    $smarty->assign('title', '批量上传简历 - 企业会员中心 - ' . $_CFG['site_name']);
    $smarty->assign('uploads', get_upload($offset, $perpage, $wheresql));
    if ($total > $perpage) {
        $smarty->assign('page', $page->show(3));
    }

    $smarty->display('member_company/company_upload.htm');
} elseif ($act == 'apply_check') {
    if (!$cominfo_flge){

        $link[0]['text'] = "完善企业资料";
        $link[0]['href'] = 'company_info.php?act=company_profile';
        showmsg("为了达到更好的招聘效果，请先完善您的企业资料！",1,$link);

    }
    $data = array();
    $data["uid"] = $_SESSION["uid"];
    $data["addtime"] = time();
    $rs = $db->getfirst("select * from " . table("resume_check_apply") . " where uid=" . $data["uid"]);
    if ($rs) {
        $data["status"]=0;
        $wheresql=" uid='".intval( $data["uid"] )."' ";

        $db->updatetable(table("resume_check_apply"),$data,$wheresql);

        showmsg("您的审请已经提交,请等待管理员审核", 1);

    } else {
        $db->inserttable(table("resume_check_apply"), $data);
        showmsg("您的审请已经提交,请等待管理员审核", 1);
    }
} elseif ($act == 'check_resume_detail') {
    require_once(QISHI_ROOT_PATH . 'genv/func_resume_upload.php');
    if (!check_resume_check_apply()) {
        showmsg("你还不是审核员，无权操作！", 1);
    }
    //check_permissions($_SESSION['admin_purview'],"resume_show");
    $id = !empty($_REQUEST['id']) ? intval($_REQUEST['id']) : showmsg("你没有选择简历！", 1);
    $uid = intval($_REQUEST['uid']);
    //判断有无没审核的记录
    $rs = resume_log_not_check();

    if ($rs && $rs["rid"] != $id) {
        $link[0]['text'] = "前往简历";
        $link[0]['href'] = '?act=check_resume_detail&id=' . $rs["rid"];
        showmsg('有一份简历你没有提供审结果', 1, $link);
    }
    $resume = get_resume_temp_basic($id);
    //resume_check_log_add($id);

    if (empty($resume)) {

        $link[0]['text'] = "返回简历列表";
        $link[0]['href'] = '?act=cheking_resume';
        showmsg('简历不存在或已经被删除！', 1, $link);
    }
    $smarty->assign('random', mt_rand());
    $smarty->assign('time', time());
    $smarty->assign('url', $_SERVER["HTTP_REFERER"]);
    $smarty->assign('resume', $resume);

    $smarty->assign("check_result", get_category("Genv_check_false"));
    $smarty->assign("check_result_pass", get_category("Genv_check_pass"));


    $smarty->display('member_company/resume-show.htm');

} elseif ($act == 'check_result') {
    require_once(QISHI_ROOT_PATH . 'genv/func_resume_upload.php');
    if (!check_resume_check_apply()) {
        showmsg("你还不是审核员，无权操作！", 1);
    }
    $id = !empty($_REQUEST['id']) ? intval($_REQUEST['id']) : showmsg("你没有选择简历！", 1);
    $result = $_REQUEST['result'] ? trim($_REQUEST['result']) : showmsg("你没有选择审核结果！", 1);;
    resume_check_log_add($id, $result);
    resume_check_log_save($id, $result);
    $link[0]['text'] = "返回简历列表";
    $link[0]['href'] = '?act=cheking_resume';
    showmsg('简历结果已提交！', 1, $link);


}elseif ($act == 'reward_check_list') {
    //人才线索审核列表;

    require_once(QISHI_ROOT_PATH . 'include/page.class.php');
    $cate=$_GET["cate"];

    $wheresql = " WHERE 1=1 ";
    $oederbysql = " order BY addtime DESC ";





     $wheresql .= " AND uid = ".$_SESSION["uid"];




    $perpage = 10;
    $total_sql = "SELECT COUNT(*) AS num FROM " . table('jobs_reward_clue') . "   {$wheresql} ";
    $total = $db->get_total($total_sql);
    $page = new page(array('total' => $total, 'perpage' => $perpage));
    $offset = ($page->nowindex - 1) * $perpage;
    $smarty->assign('act', $act);
    $smarty->assign('title', '批量上传简历 - 企业会员中心 - ' . $_CFG['site_name']);

    if ($total > $perpage) {
        $smarty->assign('page', $page->show(3));
    }

    $member=get_clue_check_list($offset, $perpage, $wheresql);


    $smarty->assign('member',$member );
    $smarty->assign('page', $page->show(3));
    $smarty->assign("cate",$cate);



    $smarty->display('member_company/company_reward_list.htm');
}
unset($smarty);



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
?>