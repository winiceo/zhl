<?php
/*
 * 74cms ��ҵ��Ա����
*/
define('IN_QISHI', true);
require_once(dirname(__FILE__) . '/company_common.php');
$smarty->assign('leftmenu', "recruitment");
$smarty->assign('act', $act);

//����
if ($act=='fortune')
{


    $smarty->assign('title','�����Ը���� - ���˻�Ա���� - '.$_CFG['site_name']);

    $smarty->display('member_company/personal_fortune.htm');
}
elseif ($act=='fotrune_save')
{
    require_once(QISHI_ROOT_PATH.'genv/lib.php');

    require_once QISHI_ROOT_PATH.'genv/Requests/library/Requests.php';
    Requests::register_autoloader();

    $name=isChineseName(trim($_POST['name']))?($_POST['name']):showmsg('��������ȷ������',1);

    $points_rule=get_cache('points_rule');
    $user_points=get_user_points($_SESSION["uid"]);

    if($user_points<$points_rule['fotrune_points']['value']){
        showmsg("���ֲ���,���ֵ",2);
    }
    $_POST["id"]=$_POST["category"];;

    $uri='http://192.168.1.102/mpfxsys_ajax.asp';
    $post=$_POST;
    if($_POST["category"]==7){
        $post["showming"]=1;
    }else{
        $post["showming"]=0;
    }

    $html = Requests::post($uri, array(), $post);

    $html=($html->body);

    $html = preg_replace('~<(tr|table)\s+?.*?>~i','<$1>',$html);
    $html = preg_replace('~<(td).*?(colspan=["\']?\d+["\']?|rowspan=["\']?\d+["\']?).*?>~i','<td $2>',$html);
    $html = str_replace('#DEE2FA','#f5f5f5',$html);

    global $db;
    $data=array();
    $data["name"]=$name;
    $data["category"]=intval($_POST["category"]);
    $category_cn="";
    switch(intval($_POST["category"])){
        case 1:
            $category_cn="�Ը����";
            break;
        case 2:
            $category_cn="��ô�ദ��������";
            break;
        case 3:
            $category_cn="���׷����ϲ������";
            break;
        case 4:
            $category_cn="��Ϊ���޻���ô��";
            break;
        case 5:
            $category_cn="��Ҫ�л��ҵ�ͬ��";
            break;
        case 6:
            $category_cn="Ҫ���̳�սʤ�ͻ�";
            break;
        case 7:
            $category_cn="����";
            break;
    }
    $data["category_cn"]=$category_cn ;
    $data["sex"]=$_POST["X2"] ;
    $data["uid"]=$_SESSION["uid"];
    $data["addtime"]=time();
    $data["result"]=(trim($html));

    //echo json_encode($testJSON);

    foreach ( $_POST as $key => $value ) {
        $_POST[$key] = urlencode ( $value );
    }


    $data["json"]=urldecode ( json_encode ( $_POST ) );

    $new=ORM::for_table(table("fotrune"))->create($data);
    $rs=$new->save();
    fortune($_SESSION['uid'],$_SESSION['username'],$new->id,2);

    if($rs)
    {
        header("Location: ?act=fotrune_show&id=".$new->id);
    }
    else
    {
        showmsg("����ʧ��",2);
    }

}elseif ($act=='fotrune_show')
{
    require_once(QISHI_ROOT_PATH.'genv/lib.php');

    global $db;

    $id=$_GET["id"];
    $sql="select * from ".table("fotrune"). " where id=".$id;
    $rs=ORM::for_table(table("fotrune"))->find_one($id);
    if(!$rs){
        showmsg("û�ҵ������¼",2);
    }
    $rs=$rs->as_array();

    $smarty->assign('title','�����Ը���� - ���˻�Ա���� - '.$_CFG['site_name']);
    $smarty->assign('result',$rs	);
    $smarty->display('member_company/personal_fortune_result.htm');

}elseif ($act=='fotrune_list')
{
    require_once(QISHI_ROOT_PATH.'genv/lib.php');

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

    $smarty->display('member_company/personal_fortune_list.htm');
}




//����������
function fortune($uid,$username,$pid,$type="2")
{
    $uid = intval($_SESSION['uid']);
    $username = trim($_SESSION['username']);
    $type = intval($type);
    $points_rule=get_cache('points_rule');
    $user_points=get_user_points($uid);
    if ($points_rule['fotrune_company_points']['value']>0)
    {
        report_deal($uid,$type,$points_rule['fotrune_company_points']['value']);
        $user_points=get_user_points($uid);
        $operator=$type=="1"?"+":"-";
        write_memberslog($uid,2,9001,$username,"�������� ({$operator}{$points_rule['perfect_resume']['value']})��(ʣ��:{$user_points})",2,1105,"��������","{$operator}{$points_rule['perfect_resume']['value']}","{$user_points}");
    }
    else
    {
        write_memberslog($uid,2,1105,$username,"��������");
    }

}
unset($smarty);
?>