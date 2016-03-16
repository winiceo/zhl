<?php
/*
* 74cms �ƻ�����
*/
define('IN_QISHI', true);
require_once(dirname(__FILE__) . '/../data/config.php');
require_once(dirname(__FILE__) . '/include/admin_common.inc.php');
require_once(ADMIN_ROOT_PATH . 'include/admin_replace_fun.php');
$act = !empty($_GET['act']) ? trim($_GET['act']) : 'list';
check_permissions($_SESSION['admin_purview'], "upload_replace");
$smarty->assign('pageheader', "�����ֶ��滻");
if ($act == 'list') {
    require_once(QISHI_ROOT_PATH . 'include/page.class.php');
    $wheresql=" WHERE  1=1 ";
    $oederbysql=" order BY id DESC ";
    $name=isset($_GET['name'])?trim($_GET['name']):"";
    if ($name )
    {
        $wheresql.=" AND `name` like '{$name}%'";

        $oederbysql="";
    }
    else
    {

        if (!empty($_GET['type'])) {
            if ($_GET['type'] == "1") {
                $wheresql .= " AND `type` = 1";
            } elseif ($_GET['type'] == "2") {
                $wheresql .= " AND type = 2";
            }

        }
        if (!empty($_GET['source'])) {
            $source=$_GET["source"];

             $wheresql .= " AND `source` like '%".$source."%'";


        }
        if (!empty($_GET['title'])) {

            $wheresql .= " AND `parent_id` = '".$_GET["title"]."' ";


        }

    }



    $total_sql = "SELECT COUNT(*) AS num FROM " . table('relation').$wheresql;
    $total_val = $db->get_total($total_sql);
    $page = new page(array('total' => $total_val, 'perpage' => $perpage, 'getarray' => $_GET));
    $currenpage = $page->nowindex;
    $offset = ($currenpage - 1) * $perpage;
    $list = get_list($offset, $perpage, $wheresql . $oederbysql);
    $smarty->assign('list', $list);
    $smarty->assign('page', $page->show(3));
    $smarty->assign('navlabel', "list");

    $smarty->assign('source_list', get_source_list());
    $smarty->assign('title_list', get_title_list());

    $smarty->assign('source', $_GET['source']);
    $smarty->assign('title', $_GET['title']);
    $smarty->assign('type', $_GET['type']);

    get_token();
    $smarty->display('replace/admin_replace.htm');
} elseif ($act == 'add') {
    get_token();
    $smarty->assign('navlabel', "add");
    $smarty->display('replace/admin_replace_add.htm');
}elseif ($act == 'add_title') {
    get_token();
    $smarty->assign('navlabel', "add_title");
    $smarty->display('replace/admin_replace_add_title.htm');
} elseif ($act == 'add_save') {
    check_token();
    $setsqlarr['name'] = !empty($_POST['name']) ? trim($_POST['name']) : adminmsg('ԭ���ݲ���Ϊ��', 1);
    $setsqlarr['value'] = !empty($_POST['value']) ? trim($_POST['value']) : adminmsg('�滻�������ݲ���Ϊ��', 1);
     $setsqlarr['source'] = !empty($_POST['source']) ? trim($_POST['source']) : "";
    $setsqlarr['parent_name'] = !empty($_POST['parent_name']) ? trim($_POST['parent_name']) : "";
    $setsqlarr['parent_id'] = !empty($_POST['parent_id']) ? trim($_POST['parent_id']) : "";
    $setsqlarr['type'] = !empty($_POST['type']) ? trim($_POST['type']) : "";

    if( $setsqlarr['type']==1){

        $setsqlarr['parent_id'] = $setsqlarr["value"];
    }elseif($setsqlarr['type']==2){
            $rs=get_replace_one( $setsqlarr['parent_id'] );

            $setsqlarr['parent_id'] = $rs["value"];
            $setsqlarr['source'] = $rs["source"];

    }

    if ($db->inserttable(table('relation'), $setsqlarr)) {
        $link[0]['text'] = "�����б�";
        $link[0]['href'] = "?act=";
        write_log("��ӣ�" . $setsqlarr['name'], $_SESSION['admin_name'], 3);
        adminmsg("��ӳɹ���", 2, $link);
    } else {
        adminmsg("���ʧ�ܣ�", 0);
    }
} elseif ($act == 'edit') {
    get_token();
    $show=get_replace_one(intval($_GET['id']));
    $smarty->assign('show',$show );
    if($show["type"]==1){
        $smarty->display('replace/admin_replace_edit_title.htm');

    }else{
        $smarty->display('replace/admin_replace_edit.htm');

    }
} elseif ($act == 'edit_save') {
    check_token();
    $link[0]['text'] = "�����б�";
    $link[0]['href'] = "?act=";
    $setsqlarr['name'] = !empty($_POST['name']) ? trim($_POST['name']) : adminmsg('ԭ���ݲ���Ϊ��', 1);
    $setsqlarr['value'] = !empty($_POST['value']) ? trim($_POST['value']) : adminmsg('�滻�������ݲ���Ϊ��', 1);
    $setsqlarr['source'] = !empty($_POST['source']) ? trim($_POST['source']) : "";

    $setsqlarr['parent_name'] = !empty($_POST['parent_name']) ? trim($_POST['parent_name']) : "";
    $setsqlarr['parent_id'] = !empty($_POST['parent_id']) ? trim($_POST['parent_id']) : "";
    $rs=get_replace_one( $_POST['id']);
    if( $rs['type']==1){

        $setsqlarr['parent_id'] = $setsqlarr["value"];
    }elseif($rs['type']==2){


        $setsqlarr['parent_id'] = $rs["value"];
        $setsqlarr['source'] = $rs["source"];

    }
    $wheresql = " id=" . intval($_POST['id']);
    !$db->updatetable(table('relation'), $setsqlarr, $wheresql) ? adminmsg("�޸�ʧ�ܣ�", 0) : adminmsg("�޸ĳɹ���", 2, $link);
} elseif ($act == 'del') {
    get_token();
    $id = $_REQUEST['id'];
    if (empty($id)) adminmsg("��ѡ����Ŀ��", 0);
    if ($num = del_replace($id)) {
        write_log("ɾ��,��ɾ��" . $num . "��", $_SESSION['admin_name'], 3);
        adminmsg("ɾ���ɹ�����ɾ��" . $num . "��", 2);
    } else {
        adminmsg("ɾ��ʧ�ܣ�" . $num, 1);
    }
} elseif ($act == 'execution') {
    check_token();
    $id = intval($_GET['id']);
    $crons = $db->getone("select * from " . table('crons') . " WHERE  cronid='{$id}' LIMIT 1 ");
    if (!empty($crons)) {
        if (!file_exists(QISHI_ROOT_PATH . "include/crons/" . $crons['filename'])) {
            adminmsg("�����ļ� {$crons['filename']} �����ڣ�", 0);
        }
        require_once(QISHI_ROOT_PATH . "include/crons/" . $crons['filename']);
        adminmsg("ִ�гɹ���", 2);
    }
}
elseif ($act == 'title_field') {
    //���ݿ��ֶι���
    get_token();

    $list = get_title_field();
    $smarty->assign('pageheader', "�ֶα���");
    $smarty->assign('list', $list);


    $smarty->display('replace/admin_title_field.htm');
}elseif ($act == 'title_field_save') {
    //����б����
    get_token();

    $setsqlarr['key'] = !empty($_POST['key']) ? trim($_POST['key']):adminmsg('�ֶ�������Ϊ�գ�',1);
    $setsqlarr['value'] = !empty($_POST['value']) ? trim($_POST['value']):adminmsg('����������Ϊ�գ�',1);

    if(get_title_key($setsqlarr["key"])){
        adminmsg('�Ѵ��ڣ�',1);
    }
    $insert_id=$db->inserttable(table('resume_field'),$setsqlarr,true);
    write_log("����滻�����ֶ�".$setsqlarr['name'], $_SESSION['admin_name'],3);
    $link[0]['text'] = "�����б�";
    $link[0]['href'] = "?act=title_field";

    adminmsg('��ӳɹ���',2,$link);

    $smarty->display('company/admin_company_points.htm');
}elseif ($act == 'title_field_del') {
    //����б����
    get_token();
     $id = !empty($_REQUEST['id']) ? trim($_REQUEST['id']):adminmsg('id����Ϊ�գ�',1);

    if (!$db->query("Delete from ".table('resume_field')." WHERE id IN (".$id.")")){
        adminmsg("ɾ��ʧ�ܣ�",0);
    } else{
        adminmsg("ɾ���ɹ���",0);

    }


}
?>