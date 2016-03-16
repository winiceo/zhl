<?php
/*
 * 74cms ��ҵ��Ա����
*/
define('IN_QISHI', true);

require_once(dirname(__FILE__) . '/company_common.php');
$smarty->assign('leftmenu', "recruitment");
//error_reporting(-1);

if ($act == 'upload') {
    require_once(QISHI_ROOT_PATH . 'include/page.class.php');

    $wheresql .= "where uid=" . $_SESSION['uid'];

    $perpage = 10;
    $total_sql = "SELECT COUNT(*) AS num FROM " . table('resume_upload') . "  {$wheresql} ";
    $total = $db->get_total($total_sql);
    $page = new page(array('total' => $total, 'perpage' => $perpage));
    $offset = ($page->nowindex - 1) * $perpage;
    $smarty->assign('act', $act);
    $smarty->assign('title', '�����ϴ����� - ��ҵ��Ա���� - ' . $_CFG['site_name']);


    $smarty->display('member_company/company_upload.htm');
} elseif ($act == 'upload_save') {
    //error_reporting(-1);
    require_once(QISHI_ROOT_PATH . 'include/upload.php');
    require_once(QISHI_ROOT_PATH . 'genv/lib.php');
    require_once(QISHI_ROOT_PATH . 'genv/func_resume_upload.php');


    !$_FILES['logo']['name'] ? showmsg('���ϴ��ļ���', 1) : "";
    $uplogo_dir = "../../data/xls/" . date("Y/m/d/");
    make_dir($uplogo_dir);
    $setsqlarr['path'] = _asUpFiles($uplogo_dir, "logo", 1024 * 5, 'xls', true);

    if ($setsqlarr['path']) {
        $setsqlarr['path'] = date("Y/m/d/") . $setsqlarr['path'];

        $setsqlarr['name'] =& $_FILES["logo"]["name"];
        $setsqlarr['uid'] = $_SESSION['uid'];
        $setsqlarr['addtime'] = time();

        if ($db->inserttable(table('resume_upload'), $setsqlarr, 1)) {
            $link[0]['text'] = "�ϴ��˼���";
            $link[0]['href'] = '?act=upload_list';
            write_memberslog($_SESSION['uid'], 1, 8003, $_SESSION['username'], "�ϴ��˼���");

            $path = QISHI_ROOT_PATH . "data/xls/" . $setsqlarr['path'];

            $excel = excel_upload($path);


            $data = $excel["data"];
            foreach ($data as $key => $value) {
                $data[$key]["color"] = "#ffffff";
                if (get_telephone($value["telephone"])) {
                    $data[$key]["color"] = "#E8AFC5";
                }
            }

            $smarty->assign("colsinfo", $excel["cols"]);
            $smarty->assign("uploads", $data);
            $smarty->assign("file", $path);
            $smarty->display('member_company/company_upload.htm');

            //showmsg('�ϴ��ɹ���',2,$link);
        } else {
            showmsg('����ʧ�ܣ�', 1);
        }
    } else {
        showmsg('����ʧ�ܣ�', 1);
    }
} elseif ($act == 'upload_finish') {

    global $_CFG;

    require_once(QISHI_ROOT_PATH . 'include/upload.php');
    require_once(QISHI_ROOT_PATH . 'genv/lib.php');
    require_once(QISHI_ROOT_PATH . 'genv/func_resume_upload.php');

    $path = !empty($_REQUEST['file']) ? $_REQUEST['file'] : showmsg("�ϴ��ļ������������ϴ�", 1);


    $data = resume_upload_insert($path);
    $link = array(array("title" => "�鿴�ϴ��б�", "href" => "/user/company/company_upload.php?act=upload_list"));
    showmsg("�ɹ��ϴ�����" . count($data) . "��", 1, $link);


} elseif ($act == 'upload_list') {
    require_once(QISHI_ROOT_PATH . 'include/page.class.php');
    require_once(QISHI_ROOT_PATH . 'genv/func_resume_upload.php');

    $wheresql .= "where uid=" . $_SESSION['uid'];

    $perpage = 10;
    $total_sql = "SELECT COUNT(*) AS num FROM " . table('resume_upload') . "  {$wheresql} ";
    $total = $db->get_total($total_sql);
    $page = new page(array('total' => $total, 'perpage' => $perpage));
    $offset = ($page->nowindex - 1) * $perpage;
    $smarty->assign('act', $act);
    $smarty->assign('title', '�����ϴ����� - ��ҵ��Ա���� - ' . $_CFG['site_name']);
    $smarty->assign('uploads', get_upload($offset, $perpage, $wheresql));
    if ($total > $perpage) {
        $smarty->assign('page', $page->show(3));
    }

    $smarty->display('member_company/company_upload_list.htm');
} elseif ($act == 'cheking_resume') {
    //error_reporting(-1);
    if (!$cominfo_flge){

            $link[0]['text'] = "������ҵ����";
            $link[0]['href'] = 'company_info.php?act=company_profile';
            showmsg("Ϊ�˴ﵽ���õ���ƸЧ������������������ҵ���ϣ�",1,$link);

    }
    //����˼���
    require_once(QISHI_ROOT_PATH . 'include/page.class.php');
    require_once(QISHI_ROOT_PATH . 'genv/func_resume_upload.php');

    $wheresql .= "where r.status=0 and r.upload_uid!=" . $_SESSION["uid"] . " ";

    $perpage = 10;
    $total_sql = "SELECT COUNT(*) AS num FROM " . table('resume_temp') . " as r  {$wheresql} ";
    $total = $db->get_total($total_sql);
    $page = new page(array('total' => $total, 'perpage' => $perpage));
    $offset = ($page->nowindex - 1) * $perpage;
    $smarty->assign('act', $act);
    $smarty->assign('title', '�����ϴ����� - ��ҵ��Ա���� - ' . $_CFG['site_name']);
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
    $smarty->assign('title', '�����ϴ����� - ��ҵ��Ա���� - ' . $_CFG['site_name']);
    $smarty->assign('uploads', get_upload($offset, $perpage, $wheresql));
    if ($total > $perpage) {
        $smarty->assign('page', $page->show(3));
    }

    $smarty->display('member_company/company_upload.htm');
} elseif ($act == 'apply_check') {
    if (!$cominfo_flge){

        $link[0]['text'] = "������ҵ����";
        $link[0]['href'] = 'company_info.php?act=company_profile';
        showmsg("Ϊ�˴ﵽ���õ���ƸЧ������������������ҵ���ϣ�",1,$link);

    }
    $data = array();
    $data["uid"] = $_SESSION["uid"];
    $data["addtime"] = time();
    $rs = $db->getfirst("select * from " . table("resume_check_apply") . " where uid=" . $data["uid"]);
    if ($rs) {
        $data["status"]=0;
        $wheresql=" uid='".intval( $data["uid"] )."' ";

        $db->updatetable(table("resume_check_apply"),$data,$wheresql);

        showmsg("���������Ѿ��ύ,��ȴ�����Ա���", 1);

    } else {
        $db->inserttable(table("resume_check_apply"), $data);
        showmsg("���������Ѿ��ύ,��ȴ�����Ա���", 1);
    }
} elseif ($act == 'check_resume_detail') {
    require_once(QISHI_ROOT_PATH . 'genv/func_resume_upload.php');
    if (!check_resume_check_apply()) {
        showmsg("�㻹�������Ա����Ȩ������", 1);
    }
    //check_permissions($_SESSION['admin_purview'],"resume_show");
    $id = !empty($_REQUEST['id']) ? intval($_REQUEST['id']) : showmsg("��û��ѡ�������", 1);
    $uid = intval($_REQUEST['uid']);
    //�ж�����û��˵ļ�¼
    $rs = resume_log_not_check();

    if ($rs && $rs["rid"] != $id) {
        $link[0]['text'] = "ǰ������";
        $link[0]['href'] = '?act=check_resume_detail&id=' . $rs["rid"];
        showmsg('��һ�ݼ�����û���ṩ����', 1, $link);
    }
    $resume = get_resume_temp_basic($id);
    //resume_check_log_add($id);

    if (empty($resume)) {

        $link[0]['text'] = "���ؼ����б�";
        $link[0]['href'] = '?act=cheking_resume';
        showmsg('���������ڻ��Ѿ���ɾ����', 1, $link);
    }
    $smarty->assign('random', mt_rand());
    $smarty->assign('time', time());
    $smarty->assign('url', $_SERVER["HTTP_REFERER"]);
    $smarty->assign('resume', $resume);

    $smarty->assign("check_result", get_category("Genv_check_false"));

    $smarty->display('member_company/resume-show.htm');

} elseif ($act == 'check_result') {
    require_once(QISHI_ROOT_PATH . 'genv/func_resume_upload.php');
    if (!check_resume_check_apply()) {
        showmsg("�㻹�������Ա����Ȩ������", 1);
    }
    $id = !empty($_REQUEST['id']) ? intval($_REQUEST['id']) : showmsg("��û��ѡ�������", 1);
    $result = $_REQUEST['result'] ? trim($_REQUEST['result']) : showmsg("��û��ѡ����˽����", 1);;
    resume_check_log_add($id, $result);
    $link[0]['text'] = "���ؼ����б�";
    $link[0]['href'] = '?act=cheking_resume';
    showmsg('����������ύ��', 1, $link);


}elseif ($act == 'test') {
    require_once(QISHI_ROOT_PATH . 'include/page.class.php');
    require_once(QISHI_ROOT_PATH . 'genv/func_resume_upload.php');
echo 444;
    resume_intention_jobs(1,1,"����|�г�|�ͷ�|ó��");

}
unset($smarty);
?>��