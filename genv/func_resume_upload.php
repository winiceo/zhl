<?php
/**
 * Created by PhpStorm.
 * User: leven
 * Date: 16/1/15
 * Time: 上午9:54
 */
require_once(QISHI_ROOT_PATH . '/genv/lib.php');
function get_upload($offset,$perpage,$get_sql= '')
{
    global $db;
    $limit=" LIMIT {$offset},{$perpage}";
    $result = $db->query("SELECT  * FROM ".table('resume_temp')." {$get_sql}  ORDER BY addtime desc  {$limit}");
    while($row = $db->fetch_array($result))
    {
        $row["addtime"]= date('Y-m-d',$row['addtime']);

        $row_arr[] = $row;
    }
    return $row_arr;
}
//简历临时表
function get_resume_temp($offset,$perpage,$get_sql= '')
{
    global $db;
    $limit=" LIMIT {$offset},{$perpage}";

    $result = $db->query("SELECT  r.*,l.uid as check_uid  FROM ".table('resume_temp')." as r left join (select * from qs_resume_check_log where uid=".$_SESSION["uid"]." and result!='') as l on r.id=l.rid {$get_sql}  ORDER BY check_uid asc  {$limit}");
    while($row = $db->fetch_array($result))
    {
        ///$row["addtime"]= date('Y-m-d',$row['addtime']);

        $row_arr[] = $row;
    }

    return $row_arr;
}

function get_resume_temp_list($offset,$perpage,$get_sql= '')
{
    global $db;

    $limit=" LIMIT ".$offset.','.$perpage;
    $result = $db->query($get_sql.$limit);

    while($row = $db->fetch_array($result))
    {

         $row["log"]=get_resume_check_log($row["id"]);
         $row_arr[] = $row;

    }

    return $row_arr;
}

//正式通过的记录
function get_resume_pass_list($offset,$perpage,$get_sql= '')
{
    global $db;

    $limit=" LIMIT ".$offset.','.$perpage;
    $result = $db->query($get_sql.$limit);

    while($row = $db->fetch_array($result))
    {

        $row["log"]=get_resume_check_log($row["id"]);
        $row_arr[] = $row;

    }

    return $row_arr;
}

function get_resume_temp_basic($id)
{
    global $db;
    $id=intval($id);

    $info=$db->getone("select * from ".table('resume_temp')." where id='{$id}'    LIMIT 1 ");

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
//查看是否有没审核的记录
function resume_log_not_check(){
    $rs=\ORM::for_table(table("resume_check_log"))->where("uid",$_SESSION["uid"])->where_null("result")->order_by_asc("addtime")->find_one();
    return $rs;
}
//是否存在查看历史
function resume_log_is_exist($id){
    $rs=\ORM::for_table(table("resume_check_log"))->where("uid",$_SESSION["uid"])->where("rid",$id)->find_one();
    return $rs;
}


//获取转换后文字对应的id;
function getId($value, $alias)
{
    $obj = \ORM::for_table(table('category'))->where("c_name", $value)->where("c_alias", $alias)->find_one();
    //->as_array();

    if ($obj) {
        return $obj->as_array();
    } else {
        return false;
    }
}

//获取替换文本
function get_reg($value)
{
    $obj = \ORM::for_table(table('relation'))->where("name", $value)->find_one();
    if ($obj) {
        return trim($obj->as_array()["value"]);
    }
    return false;
}


//有效审核次数；也就是有结果，没结果说明看了，没有审核；
function resume_log_num($id){
    $count=\ORM::for_table(table("resume_check_log"))->where("rid",$id)->where_not_equal("result","")->count('*');
    return $count;
}

//状态为0可用，1为进入后台管理员审核；
function update_resume_status($id,$status=1){
    $sql=vsprintf("update %s set status=%d where id=%d",array(table("resume_temp"),$status,$id));
    \ORM::raw_execute($sql);
}

//更新简历的阅读次数；
function update_resume_num($id){
    $count=resume_log_num($id);
    \ORM::raw_execute(vsprintf("update %s set num=%d where id=%d",array(table("resume_temp"),$count,$id)));

    if($count>=3){
        update_resume_status($id);

        //如果有三条都通过的记录，直接导入简历；
        $rs=\ORM::for_table(table("resume_check_log"))->where("rid",$id)->where("pass",1)->find_array();
        if($rs&&count($rs)>=3){
            import_resume_temp($id);
        }
    }

}

//添加浏览记录
function resume_check_log_add($id,$result=""){
   // import_resume_temp($id);

    $rs=resume_log_is_exist($id);
    if(!$rs){
        $rs=\ORM::for_table(table("resume_check_log"))->create();
        $rs->rid=$id;
        $rs->uid=$_SESSION["uid"];
    }
    $cate=\ORM::for_table(table("category"))->where("c_name",$result)->find_one();
    $pass=0;

    if($cate){
        if($cate->c_alias=="Genv_check_pass"){
            $pass=1;
        }
    }


    if($result!=""){
        $rs->pass=$pass;
        $rs->result=$result;
    }

    $rs->addtime=time();
    $rs->save();
    update_resume_num($id);

}

//审核提交
function resume_check_log_save($id,$result=""){
    // import_resume_temp($id);


    $cate=\ORM::for_table(table("category"))->where("c_name",$result)->find_one();
    $pass=0;

    if($cate){
        if($cate->c_alias=="Genv_check_pass"){
            $pass=1;
        }
    }

    if($pass==0){

        $resume=\ORM::for_table(table("resume_temp"))->where("id",$id)->find_one();
        $resume->is_refuse=1;
        $resume->save();

    }

}
//匹配简历意向行业
function resume_trade1($str=NULL)
{
    global $db,$locoyspider;
    $sql = "select c_id,c_name from ".table('category')." where  c_alias='QS_trade' AND  c_id=".intval($locoyspider['company_trade'])." LIMIT 1";
    $info=$db->getone($sql);
    $default=array("id"=>$info['c_id'],"cn"=>$info['c_name']);
    if (empty($str))
    {
        return $default;
    }
    else
    {
        $sql = "select c_id,c_name from ".table('category')." where  c_alias='QS_trade'";
        $info=$db->getall($sql);
        $return=resume_search_str($info,$str,"c_name");
        if ($return)
        {
            return array("id"=>$return['c_id'],"cn"=>$return['c_name']);
        }
        else
        {
            return $default;
        }
    }
}


//获取意向职位
function get_category_jobs_one($pid)
{
    global $db;
    $pid=intval($pid);
    $sql = "select * from ".table('category_jobs')." where id='{$pid}'  LIMIT 1" ;

    return $db->getone($sql);
}

//获取专业id
function getmajor_Id($value)
{
    global $db;

    $sql = "select * from ".table('category_major')." where categoryname='{$value}'  LIMIT 1" ;
    $rs=$db->getone($sql);

    if($rs){
        return $rs["id"];
    }
    return 0;
}
//增加意向职位
function add_resume_jobs1($pid,$uid,$str)
{
    global $db;
    $db->query("Delete from ".table('resume_jobs')." WHERE pid='".intval($pid)."'");
    $str=trim($str);
    $arr=explode(",",$str);
    if (is_array($arr) && !empty($arr))
    {
        foreach($arr as $a)
        {
            $a=explode(".",$a);
            $setsqlarr['uid']=intval($uid);
            $setsqlarr['pid']=intval($pid);
            $setsqlarr['topclass']=intval($a[0]);
            $setsqlarr['category']=intval($a[1]);
            $setsqlarr['subclass']=intval($a[2]);
            if (!$db->inserttable(table('resume_jobs'),$setsqlarr))return false;
        }
    }
    return true;
}

//匹配简历意向职位
function resume_intention_jobs($uid,$pid,$str=NULL)
{
    global $db,$locoyspider;

     $sql = "select * from ".table('category_jobs')." where  categoryname='".$str."'   LIMIT 1";
    // $info=ORM::for_table(table('category_jobs'))->where("categoryname",$str)->find_one();
   $info=$db->getone($sql);

    $tmp=array(0,0,0);
    if($info){

        if($info["parentid"]>0){
            $tmp[]=$info["id"];
            $info=get_category_jobs_one($info["parentid"]);

            if($info["parentid"]>0){
                $tmp[]=$info["id"];

                $info=get_category_jobs_one($info["parentid"]);

                if($info["parentid"]>0){
                    $tmp[]=$info["id"];
                }elseif($info["parentid"]==0){
                    $tmp[]=$info["id"];
                }
            }else{
                $tmp[]=$info["id"];
            }
        }else{
            $tmp[]=$info["id"];
        }
    }
    $array['uid']=$uid;
    $array['pid']=$pid;

    $array['topclass']=array_pop($tmp);
    $array['category']=array_pop($tmp);
    $array['subclass']=array_pop($tmp);

    $db->inserttable(table("resume_jobs"), $array, 1);

 }
//获取意向职位
function get_district_one($pid)
{
    global $db;
    $pid=intval($pid);
    $sql = "select * from ".table('category_district')." where id='{$pid}'  LIMIT 1" ;

    return $db->getone($sql);
}

//获取城市记录
function get_district_two($subsite_id)
{
    global $db;
    $tmp=array($subsite_id,0,"");
    $parentid=intval($subsite_id);
    $sql = "select * from ".table('category_district')." where id='{$subsite_id}' order by id  LIMIT 1" ;
    $rs=$db->getone($sql);

    if($rs){
        $tmp[2]=$rs["categoryname"];
    }
    $sql = "select * from ".table('category_district')." where parentid='{$parentid}' order by id  LIMIT 1" ;
    $rs=$db->getone($sql);
    if($rs){
       $tmp[1]=$rs["id"];
       $tmp[2]=$tmp[2]."/".$rs["categoryname"];
    }

    return $tmp;
 }


//行业数据处理
function get_trade($str){
    $pattern = '/\((.*)\)/isU';
    preg_match_all($pattern, $str, $matchess);
    if($matchess){
        $tmp=array();
        foreach($matchess[1] as $k=>$v){
            $tmp=array_merge($tmp,explode(",",$v));
        }

    }
    return  implode(",",$tmp);
}
//匹配地区
function resume_district_cn($uid,$pid,$str=NULL)
{
    global $db,$locoyspider;

    $sql = "select * from ".table('category_district')." where  categoryname='".$str."'   LIMIT 1";

    // $info=ORM::for_table(table('category_jobs'))->where("categoryname",$str)->find_one();
    $info=$db->getone($sql);


    $tmp=array(0,0);
    if($info){

        if($info["parentid"]>0){
            $tmp[]=$info["id"];
            $info=get_district_one($info["parentid"]);

            if($info["parentid"]>0){
                $tmp[]=$info["id"];
            }else{
                $tmp[]=$info["id"];
            }
        }else{
            $tmp[]=$info["id"];
        }
    }
    $array['uid']=$uid;
    $array['pid']=$pid;

    $array['district']=array_pop($tmp);
    $array['sdistrict']=array_pop($tmp);


   // $db->inserttable(table("resume_district"), $array, 1);


}
//特殊文字处理
function replace_word($value){
  $array=array(  '音乐,作曲',

    '戏剧学,表演' ,

    '导演,广播电视编导' ,

    '摄影,动画' ,

    '播音,主持,录音' ,

    '生物科学,技术',
     '检测,认证',
    '批发,零售',

    '教育,培训,院校');
    foreach($array as $key=>$val){
        if(strstr($value,$val)){
            $tmp=str_replace(',','/',$val);
            return str_replace($val,$tmp,$value);
        }
    }
    return $value;

}

//增加审核记录
function update_pass($uid){
    global $db ,$CFG;

    $db->query("update  ".table('members')."  SET resume_passed=resume_passed+1  WHERE uid='{$uid}'")  ;

}

function get_tagss(){
    $tagss=get_category("QS_resumetag");
    shuffle($tagss);
    $cc=array_slice($tagss,3,5);
    $tag_id=array();
    $tag_cn=array();
    foreach($cc as $k=>$v){
        $tag_id[]=$v["c_id"];
        $tag_cn[]=$v["c_name"];
    }
    return array(implode(",",$tag_id),implode(",",$tag_cn));

}

//导入简历入库
function import_resume_temp($id)
{
    global $db ,$CFG;
    $response=array();
    $response["msg"]="导入成功";
    $response["error"]=0;

    $rs = \ORM::for_table(table("resume_temp"))->where("id", $id)->find_one();
    update_pass($rs["upload_uid"]);
    if($rs){
        $rs=$rs->as_array();

        $exist_tel = \ORM::for_table(table("resume"))->where("telephone", $rs["telephone"])->find_one();
        if($exist_tel){
            $response["msg"]="已经存在相同手机号的用户，导入失败！";
            $response["error"]=1;
            Ggven::log($response);

            return $response;
        }

    }else{
        $response["msg"]="简历不存在！";
        $response["error"]=1;
        Ggven::log($response);
        return $response;
    }
    $upload_uid=$rs["upload_uid"];

    $username = uniqid() . time();
    $email = trim($rs["email"]);

    $mobile = str_replace("086-","",trim($rs["telephone"]));
    $subsite_id=$rs['subsite_id'];
    if($mobile){
        $username=$mobile;
    }
    if($email){
        $username=$email;
    }
    //注册会员
    $userid = import_user_register_upload($username, '123456', 2, $email, $mobile, false,$subsite_id);
    if ($userid > 0) {
        //个人信息表
        $member_info['uid'] = $userid;
        $member_info['realname'] = $rs["fullname"];
        $member_info['sex_cn'] = $rs["sex_cn"];
        if(trim($rs["birthdate"])==""||trim($rs["birthdate"])==0){
            $rs["birthdate"]=1990;
        }
        $member_info['birthday'] = $rs["birthdate"];
        switch ($member_info['sex_cn']) {
            case "男":
                $member_info['sex'] = 1;
                break;
            case "女":
                $member_info['sex'] = 2;
                break;
            default:
                $member_info['sex'] = 1;
                $member_info['sex_cn'] = "男";
                break;
        }
        $member_info['residence'] = $rs["residence"];
        $education_cn = get_reg($rs["education_cn"]);
        if ($education_cn) {
            $member_info['education_cn'] = $education_cn;
            $member_info['education'] = getId($education_cn, "QS_education")["c_id"];
        } else {
            $member_info['education_cn'] = "本科";
            $member_info['education'] = 69;
        }
        $major_cn = get_reg(replace_word($rs["major_cn"]));

        if ($major_cn) {
            $member_info['major_cn'] = $major_cn;
            $member_info['major'] = getmajor_Id($major_cn);
        } else {
            $member_info['major_cn'] = "";
            $member_info['major'] = 0;
        }

        $experience_cn = get_reg($rs["experience_cn"]);

        if ($experience_cn) {
            $member_info['experience_cn'] = $experience_cn;
            $member_info['experience'] = getId($experience_cn, "QS_experience")["c_id"];
        } else {
            $member_info['experience_cn'] = "1-3年";
            $member_info['experience'] = 77;
        }


        $member_info['email'] = $email;
        $member_info['phone'] = $mobile;
        if($rs["height"]==""){
            $rs["height"]=170;
        }
        $member_info['height'] = $rs["height"];

        $member_info['householdaddress'] = $rs["householdaddress"];
        $member_info['marriage_cn'] = $rs["marriage_cn"];
        switch ($member_info['marriage_cn']) {
            case "未婚":
                $member_info['marriage'] = 1;
                break;
            case "已婚":
                $member_info['marriage'] = 2;
                break;
            default:
                $member_info['marriage'] = 1;
                $member_info['marriage_cn'] = "未婚";
                break;
        }
        if (!$db->inserttable(table('members_info'), $member_info, true)) continue;
        //简历表
        $resume['uid'] = $userid;

        $resume['fullname'] = $rs["fullname"];
        $resume['birthdate'] = $member_info['birthday'];
        $resume['residence'] = $member_info['residence'];
        $resume['height'] = $member_info['height'];
        $resume['sex'] = $member_info['sex'];
        $resume['sex_cn'] = $member_info['sex_cn'];
        $resume['marriage'] = $member_info['marriage'];
        $resume['marriage_cn'] = $member_info['marriage_cn'];
        $resume['experience'] = $member_info['experience'];
        $resume['experience_cn'] = $member_info['experience_cn'];

        $resume['major_cn'] = $member_info['major_cn'];
        $resume['major'] = $member_info['major'];
        $resume['education'] = $member_info['education'];
        $resume['education_cn'] = $member_info['education_cn'];
        $resume['householdaddress'] = $member_info['householdaddress'];
        $resume['email'] = $member_info['email'];
        $resume['telephone'] = $member_info['phone'];
        $resume['nature_cn'] = $rs["nature_cn"];
        switch ($resume['nature_cn']) {
            case "全职":
                $resume['nature'] = 62;
                break;
            case "兼职":
                $resume['nature'] = 63;
                break;
            case "实习":
                $resume['nature'] = 64;
                break;
            default:
                $resume['nature'] = 62;
                $resume['nature_cn'] = "全职";
                break;
        }
        $resume["trade_cn"] = "其他行业";
        $resume["trade"] = 45;
        $trade = addslashes(replace_word($rs["trade_cn"]));
        $trade=get_trade($trade);
        $trade_array = explode(',', $trade);
        foreach ($trade_array as $key => $value) {
            $everyone = resume_trade1($value);
            $resume['trade'] .= empty($resume['trade']) ? $everyone['id'] : "," . $everyone['id'];
            $resume['trade_cn'] .= empty($resume['trade_cn']) ? $everyone['cn'] : "," . $everyone['cn'];
        }

        if($rs["intention_jobs"]==""){
            $rs["intention_jobs"]="其他";
        }
        $intention_jobs=$rs["intention_jobs"];


        $resume['intention_jobs'] =$intention_jobs;



        $wage_cn = get_reg($rs["wage_cn"]);
        if ($wage_cn) {
            $resume['wage_cn'] = $wage_cn;
            $resume['wage'] = getId($wage_cn, "QS_wage")["c_id"];
        } else {
            $resume['wage_cn'] = "面议";
            $resume['wage'] = 313;
        }

        $current_cn = get_reg($rs["current_cn"]);
        if ($current_cn) {
            $resume['current_cn'] = $current_cn;
            $resume['current'] = getId($current_cn, "QS_current")["c_id"];
        } else {
            $resume['current_cn'] = "我目前已离职，可快速到岗";
            $resume['current'] = 241;
        }


        $resume['addtime'] = time();
        $resume['refreshtime'] = time();
        $resume['specialty'] = addslashes($rs["specialty"]);
        $resume['complete_percent'] = addslashes($rs["complete_percent"]);
        $resume['display_name'] = 1;
        $resume['entrust'] = 0;
        $resume['resume_from_pc'] = 1;
        $resume['photo'] = 0;
        $resume['email_notify'] = 1;
        $resume['click'] =1;
        $resume['photo'] = 0;
        $resume['subsite_id'] = $subsite_id;
        $district_info=get_district_two($subsite_id);
        $resume['district'] = $subsite_id;
        $resume['sdistrict']=$district_info[1];

        $resume['district_cn'] =$district_info[2];

        $resume['tmp_id'] =$rs["id"];
        $tagss=get_tagss();
        $resume['tag'] =$tagss[0];
        $resume['tag_cn'] =$tagss[1];


        $resume['title'] = $resume["fullname"]."具有".$resume["experience_cn"]."的工作经验，学历".$resume["education_cn"]."寻找新工作";
//        require_once(QISHI_ROOT_PATH.'include/splitword.class.php');
//
//        $sp = new SPWord();
//        $resume['key']=addslashes($resume['title']).addslashes($resume['recentjobs']).addslashes($resume['specialty']);
//        $resume['key']=addslashes($resume['fullname']).$sp->extracttag($resume['key']);
//        $resume['key']=str_replace(","," ",addslashes($resume['intention_jobs']))." {$resume['key']} ".addslashes($resume['education_cn']);
//        $resume['key']=$sp->pad($resume['key']);
       // dump($resume);
        $pid = $db->inserttable(table("resume"), $resume, 1);
        $response["resume_id"]=$pid;
        change_level($userid,$pid);

        if ($pid) {
            //索引表
           // $searchtab['id'] = $pid;
            $searchtab['uid'] = $userid;
            $searchtab['sex'] = $resume['sex'];
            $searchtab['nature'] = $resume['nature'];
            $searchtab['marriage'] = $resume['marriage'];
            $searchtab['experience'] = $resume['experience'];
            $searchtab['wage'] = $resume['wage'];
            $searchtab['education'] = $resume['education'];
            $searchtab['refreshtime'] = $resume['refreshtime'];
            $searchtab['major'] = $resume['major'];
            $searchtab['audit'] = 1;
            $db->inserttable(table('resume_search_rtime'), $searchtab);
            $searchtab['likekey'] = $resume['trade_cn'] . ',' . $resume['fullname'];
            $db->inserttable(table('resume_search_key'), $searchtab);
            unset($searchtab);

          //  $db->inserttable(table('resume_district'), array("uid"=>$userid,"pid"=>$pid,"district"=>3,"sdistrict"=>0));

            $intention_jobs_array= explode(',', $intention_jobs);

            foreach ($intention_jobs_array as $key => $value) {
                resume_intention_jobs($userid,$pid,$value);

            }

            $district_array= explode(',', $resume["district_cn"]);

            foreach ($district_array as $key => $value) {
                resume_district_cn($userid,$pid,$value);

            }
            //教育经历
//            $resume_education = trim($resume['education_cn']);
//            $edu_array = explode(';', $resume_education);
//            foreach ($edu_array as $key => $value)
//            {
//                if(empty($value))
//                {
//                    continue;
//                }
//                $data_info = explode(',', $value);
//                $time_edu = explode('就读于', $data_info[0]);
//                $edu_time = explode('~', $time_edu[0]);
//                $edu_start_time = explode('-', $edu_time[0]);
//                $eduarrsql['startyear'] = $edu_start_time[0];
//                $eduarrsql['startmonth'] = $edu_start_time[1];
//                if(trim($edu_time[1]) == '至今')
//                {
//                    $eduarrsql['todate'] = 1;
//                    $eduarrsql['endyear'] = 0;
//                    $eduarrsql['endmonth'] = 7;
//                }
//                else
//                {
//                    $eduarrsql['todate'] = 0;
//                    $time_info = explode('-', $edu_time[1]);
//                    $eduarrsql['endyear'] = $time_info[0];
//                    $eduarrsql['endmonth'] = $time_info[1];
//                }
//
//                $eduarrsql['school'] = $time_edu[1];
//                $major_info = explode('：', $data_info[1]);
//                $eduarrsql['speciality'] = $major_info[1];
//                $education_info = explode('：', $data_info[2]);
//                $eduarrsql['education_cn'] = $education_info[1];
//                $work_edu = resume_work_education($eduarrsql['education_cn']);
//                $eduarrsql['education'] = $work_edu['id'];
//                $eduarrsql['pid'] =  $pid;
//                $eduarrsql['uid'] =  $userid;
//                $db->inserttable(table("resume_education"),$eduarrsql);
//            }
//            //工作经历
//            $resume_work = $data->sheets[0]['cells'][$i][19];
//            $work_array = explode(';', $resume_work);
//            foreach ($work_array as $key => $value)
//            {
//                if(empty($value))
//                {
//                    continue;
//                }
//                $data_info = explode(',', $value);
//                $time_work = explode('就职于', $data_info[0]);
//                $work_time = explode('~', $time_work[0]);
//                $edu_start_time = explode('-', $work_time[0]);
//                $workarrsql['startyear'] = $edu_start_time[0];
//                $workarrsql['startmonth'] = $edu_start_time[1];
//                if(trim($work_time[1]) == '至今')
//                {
//                    $workarrsql['todate'] = 1;
//                    $workarrsql['endyear'] = 0;
//                    $workarrsql['endmonth'] = 7;
//                }
//                else
//                {
//                    $workarrsql['todate'] = 0;
//                    $time_info = explode('-', $work_time[1]);
//                    $workarrsql['endyear'] = $time_info[0];
//                    $workarrsql['endmonth'] = $time_info[1];
//                }
//
//                $workarrsql['companyname'] = $time_work[1];
//                $job_info = explode('：', $data_info[1]);
//                $workarrsql['jobs'] = $job_info[1];
//                $workarrsql['pid'] =  $pid;
//                $workarrsql['uid'] =  $userid;
//                $db->inserttable(table("resume_work"),$workarrsql);
//            }
//            //培训经历
//            $resume_training = trim($data->sheets[0]['cells'][$i][20]);
//            $training_array = explode(';', $resume_training);
//            foreach ($training_array as $key => $value)
//            {
//                if(empty($value))
//                {
//                    continue;
//                }
//                $data_info = explode('在', $value);
//                $work_time = explode('~', $data_info[0]);
//                $edu_start_time = explode('-', $work_time[0]);
//                $trainingarrsql['startyear'] = $edu_start_time[0];
//                $trainingarrsql['startmonth'] = $edu_start_time[1];
//                if(trim($data_info[1]) == '至今')
//                {
//                    $trainingarrsql['todate'] = 1;
//                    $trainingarrsql['endyear'] = 0;
//                    $trainingarrsql['endmonth'] = 7;
//                }
//                else
//                {
//                    $trainingarrsql['todate'] = 0;
//                    $time_info = explode('-', $work_time[1]);
//                    $trainingarrsql['endyear'] = $time_info[0];
//                    $trainingarrsql['endmonth'] = $time_info[1];
//                }
//                $agency_info = explode('培训', $data_info[1]);
//                $trainingarrsql['agency'] = $agency_info[0];
//                $trainingarrsql['course'] = $agency_info[1];
//                $trainingarrsql['pid'] =  $pid;
//                $trainingarrsql['uid'] =  $userid;
//                $db->inserttable(table("resume_training"),$trainingarrsql);
//            }

        }
        \ORM::for_table(table("resume_temp"))->where("id",$id)->delete_many();
       // \ORM::for_table(table("resume_check_log"))->where("rid",$id)->delete_many();
        check_pass_add_point($upload_uid,$pid);

    }

    //给上传者增加葫芦币；

    return $response;

}

//获取简历基本信息
function get_resume_basic1($uid,$id)
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
//获取教育经历列表
function get_resume_education1($uid,$pid)
{
    global $db;
    if (intval($uid)!=$uid) return false;
    $sql = "SELECT * FROM ".table('resume_education')." WHERE  pid='".intval($pid)."' AND uid='".intval($uid)."' ";
    return $db->getall($sql);
}
//获取：工作经历
function get_resume_work1($uid,$pid)
{
    global $db;
    $sql = "select * from ".table('resume_work')." where pid='".$pid."' AND uid=".intval($uid)."" ;
    return $db->getall($sql);
}
//获取：培训经历列表
function get_resume_training1($uid,$pid)
{
    global $db;
    $sql = "select * from ".table('resume_training')." where pid='".intval($pid)."' AND  uid='".intval($uid)."' ";
    return $db->getall($sql);
}
//获取意向职位
function get_resume_jobs1($pid)
{
    global $db;
    $pid=intval($pid);
    $sql = "select * from ".table('resume_jobs')." where pid='{$pid}'  LIMIT 20" ;
    return $db->getall($sql);
}
//获取：语言能力列表
function get_resume_language1($uid,$pid)
{
    global $db;
    $sql = "select * from ".table('resume_language')." where pid='".intval($pid)."' AND  uid='".intval($uid)."' ";
    return $db->getall($sql);
}
// 获取简历附件图片
function get_resume_img1($uid,$pid)
{
    global $db;
    $sql = "SELECT * FROM ".table('resume_img')." WHERE  resume_id='".intval($pid)."' AND uid='".intval($uid)."' ";
    return $db->getall($sql);
}

//获取：证书列表
function get_resume_credent1($uid,$pid)
{
    global $db;
    $sql = "select * from ".table('resume_credent')." where pid='".intval($pid)."' AND  uid='".intval($uid)."' ";
    return $db->getall($sql);
}
function change_level($uid,$pid){
    global $db;
    $setsqlarr=array();

    $percent=0;


    $resume_basic=get_resume_basic1($uid,$pid);
    $resume_education=get_resume_education1($uid,$pid);
    $resume_work=get_resume_work1($uid,$pid);
    $resume_training=get_resume_training1($uid,$pid);
    $resume_tag=$resume_basic['tag'];
    $resume_specialty=$resume_basic['specialty'];
    $resume_photo=$resume_basic['photo_img'];
    $resume_language=get_resume_language1($uid,$pid);
    $resume_credent=get_resume_credent1($uid,$pid);
    $resume_img=get_resume_img1($uid,$pid);
    if (!empty($resume_basic))$percent=$percent+35;
    if (!empty($resume_education))$percent=$percent+15;
    if (!empty($resume_work))$percent=$percent+15;
    if (!empty($resume_training))$percent=$percent+5;
    if (!empty($resume_tag))$percent=$percent+5;
    if (!empty($resume_specialty))$percent=$percent+5;
    if (!empty($resume_photo))$percent=$percent+5;
    if (!empty($resume_language))$percent=$percent+5;//语言
    if (!empty($resume_credent))$percent=$percent+5;//证书
    if (!empty($resume_img))$percent=$percent+5;//附件

    $setsqlarr['complete_percent']=$percent;
    if($setsqlarr['complete_percent']<60){
        $setsqlarr['level'] = 1;
    }elseif($setsqlarr['complete_percent']>=60 && $setsqlarr['complete_percent']<80){
        $setsqlarr['level'] = 2;
    }elseif($setsqlarr['complete_percent']>=80){
        $setsqlarr['level'] = 3;
    }


    require_once(QISHI_ROOT_PATH.'include/splitword.class.php');
    $sp = new SPWord();
    $setsqlarr['key']=addslashes($resume_basic['intention_jobs']).addslashes($resume_basic['recentjobs']).addslashes($resume_basic['specialty']);
    $setsqlarr['key']=addslashes($resume_basic['fullname']).$sp->extracttag($setsqlarr['key']);
    $setsqlarr['key']=str_replace(","," ",addslashes($resume_basic['intention_jobs']))." {$setsqlarr['key']} ".addslashes($resume_basic['education_cn']);
    $setsqlarr['key']=$sp->pad($setsqlarr['key']);
    if (!empty($resume_education))
    {
        foreach($resume_education as $li)
        {
            $setsqlarr['key']=addslashes($li['school'])." {$setsqlarr['key']} ".addslashes($li['speciality']);
        }
    }
    if (!empty($resume_work))
    {
        foreach($resume_work as $li)
        {
            $setsqlarr['key']=addslashes($li['companyname'])." {$setsqlarr['key']} ".addslashes($li['speciality']);
        }
    }
    if (!empty($resume_training))
    {
        foreach($resume_training as $li)
        {
            $setsqlarr['key']=addslashes($li['agency'])." {$setsqlarr['key']} ".addslashes($li['speciality']);
        }
    }


      $db->updatetable(table("resume"), $setsqlarr, " id =".$pid);



}


function check_pass_add_point($uid,$pid){
    global $db;
    $sql = "select * from ".table('members')." where uid = '{$uid}' LIMIT 1";
    $user=$db->getone($sql);

    if($user){
        // 简历审核通过葫芦币处理
        $rule=get_cache('points_rule');

        if ($rule['resume_checked']['value']>0)
        {

            $time=time();
            report_deal($uid,$rule['resume_checked']['type'],$rule['resume_checked']['value']);
            $user_points=get_user_points($uid);
            $operator=$rule['resume_checked']['type']=="1"?"+":"-";
            $url="<a href=/resume/resume-show.php?id=".$pid." target=_blank>查看简历</a>";

            write_memberslog($uid,1,9001,$user["username"]," 简历通过审核{$url}，{$_CFG['points_byname']}({$operator}{$rule['company_logo_points']['value']})，(剩余:{$user_points})",1,1016,"简历审核通过","{$operator}{$rule['resume_checked']['value']}","{$user_points}");
        }
    }


}

//导入简历时的注册会员
function import_user_register_upload($username,$password,$member_type=0,$email,$mobile,$uc_reg=true,$subsite_id)
{
    global $db,$timestamp,$_CFG,$online_ip,$QS_pwdhash;
    $member_type=intval($member_type);
    $ck_username=get_user_inusername_import_upload($username);
    $ck_email=get_user_inemail_import_upload($email);
    $ck_mobile=get_user_inmobile_import_upload($mobile);
    if ($member_type==0)
    {
        return -1;
    }
    elseif (!empty($ck_username))
    {
        return $ck_username['uid'];
    }
    elseif ($email!=""&&!empty($ck_email))
    {
        return $ck_email['uid'];
    }
    elseif ($mobile!=""&&!empty($ck_mobile))
    {
        return $ck_mobile['uid'];
    }
    $pwd_hash=randstr_import1();
    $password_hash=md5(md5($password).$pwd_hash.$QS_pwdhash);
    $setsqlarr['username']=$username;
    $setsqlarr['password']=$password_hash;
    $setsqlarr['pwd_hash']=$pwd_hash;
    $setsqlarr['email']=$email;
    $setsqlarr['mobile']=$mobile;
    $setsqlarr['utype']=intval($member_type);
    $setsqlarr['reg_time']=$timestamp;
    $setsqlarr['reg_ip']=$online_ip;
    $setsqlarr['subsite_id']=$subsite_id;
    $setsqlarr['remind_email_time']=0;
    $insert_id=$db->inserttable(table('members'),$setsqlarr,true);

    insert_members_points($insert_id);//给注册用户加葫芦币;
    if(defined('UC_API') && $uc_reg)
    {
        include_once(QISHI_ROOT_PATH.'uc_client/client.php');
        $uc_reg_uid=uc_user_register($username,$password,$email);
    }
    return $insert_id;
}
function insert_members_points($uid){

    global $db;
    $points=get_cache('points_rule');
    $arr["uid"]=$uid;
    $arr["points"]=$points["reg_per_points"];

    $db->inserttable(table('members_points'),$arr,true);

}

function get_user_inemail_import_upload($email)
{
    global $db;
    return $db->getone("select * from ".table('members')." where email = '{$email}' LIMIT 1");
}
function get_user_inusername_import_upload($username)
{
    global $db;
    $sql = "select * from ".table('members')." where username = '{$username}' LIMIT 1";
    return $db->getone($sql);
}
//function get_user_inid_upload($uid)
//{
//    global $db;
//    $uid=intval($uid);
//    $sql = "select * from ".table('members')." where uid = '{$uid}' LIMIT 1";
//    return $db->getone($sql);
//}
function get_user_inmobile_import_upload($mobile)
{
    global $db;
    $sql = "select * from ".table('members')." where mobile = '{$mobile}' LIMIT 1";
    return $db->getone($sql);
}
//获取随机字符串
function randstr_import1($length=6)
{
    $hash='';
    $chars= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz@#!~?:-=';
    $max=strlen($chars)-1;
    mt_srand((double)microtime()*1000000);
    for($i=0;$i<$length;$i++)   {
        $hash.=$chars[mt_rand(0,$max)];
    }
    return $hash;
}
//获取字段对应关系
function get_relation($value,$type=2){

    $rs = \ORM::for_table(table('relation'))->where_equal("name", $value)->where_equal("type", $type)->find_one();

    if($type==2){
        if($rs){
            //gv($rs->as_array());
            return $rs->as_array()["value"];
        }else{
            return $value;
        }
    }elseif($type==1){
       return $rs?$rs->as_array():false;
    }

}//获取字段对应关系
function get_telephone($value ){

    $rs = \ORM::for_table(table('resume'))->where_equal("telephone", $value)->find_one();
    if($rs){return true;}
    $rs = \ORM::for_table(table('resume_temp'))->where_equal("telephone", $value)->find_one();
    if($rs){return true;}
}

function reg_email($email){
    if (!preg_match("/([\w\-]+\@[\w\-]+\.[\w\-]+)/",$email)) {
       return false;
    }
    return true;
}
function reg_mobile($mobile){
     $mobile=str_replace("086-","",$mobile);
     if (!preg_match("/^(13|15|14|17|18)\d{9}$/",$mobile)) {
       return false;
    }
    return true;
}
function reg_name($name){

     if (strlen($name)<2) {
       return false;
    }
    return true;
}


//获取选项信息
function get_category($value ){

    $rs = \ORM::for_table(table('category'))->where_equal("c_alias", $value)->find_array();
    if($rs){
        return $rs;
    }
    return array();


}


function get_array_name($cols,$name){

    foreach($cols as $key=>$value){

        if($value["name"]==$name){
            return $key;
        }
    }
    return false;

}
//处理上传简历
function excel_upload($file){
    global $db;
    ini_set('memory_limit','128M');
    require(QISHI_ROOT_PATH.'genv/spreadsheet-reader/php-excel-reader/excel_reader2.php');

    require(QISHI_ROOT_PATH.'genv/spreadsheet-reader/SpreadsheetReader.php');


    $reader = new SpreadsheetReader($file);
   // error_reporting(E_ALL ^ E_NOTICE);
    $data=array();
    $cols = array();//存储字段信息；

    foreach ($reader as $item)
    {
        $data[]=$item;
    }
    if(!is_array($data)||count($data)<2){
        return false;
    }


    foreach(array_shift($data) as $key=>$value){
        $colinfo = get_relation(trim($value),1) ;

         if ($colinfo&&$colinfo["value"]) {
             switch(trim($colinfo["value"])){
                 case "birthdate":
                     $func="birthdate";
                     break;
                 default:
                     $func="trim";
                     break;
             }
             $colinfo["func"]=$func;
             $cols[$key] = $colinfo;
        }
    }
    if(get_array_name($cols,"地址")&&get_array_name($cols,"目前居住地")){
        unset($cols[get_array_name($cols,"地址")]);
    }

    if(get_array_name($cols,"最近一个职位")&&get_array_name($cols,"应聘职位")){
        unset($cols[get_array_name($cols,"应聘职位")]);
    }

    $obj=array();
    foreach($data as $key=>$value){
        foreach($cols as $m=>$n){
            if($n["value"]){
                $temp_array= explode(',', $value[$m]);
                $cc=array();
                foreach($temp_array as $p=>$q){

                    $cc[]=get_relation($q);
                }

                $filter_value=join(",",$cc);



                $tv=call_user_func_array(array("Ggven", $n["func"]), array($filter_value));;

                $obj[$key][$n["value"]]=$tv;
            }
        }
    }

    foreach($obj as $key=> $item){
        if($item["fullname"]==""||$item["telephone"]==""){
            unset($obj[$key]);
        }
    }


    return array("cols"=>$cols,"data"=>$obj);


}
//判断是否有审核资格
function check_resume_check_apply(){
    $rs = \ORM::for_table(table('resume_check_apply'))->where_equal("uid", $_SESSION["uid"])->where("status",1)->find_one();
    if($rs){return true;}
    return false;

}

function get_upload_name($file_path){

    $path=str_replace("data/xls/","",$file_path);
    $rs=  \ORM::for_table(table('resume_upload'))->where("path",$path)->find_one();

    if($rs){
        return $rs->name;
    }else{
        return "";
    }


}

//插入临时简历库
function resume_upload_insert($file_path)
{
    global $_CFG;
    $excel=excel_upload(QISHI_ROOT_PATH.$file_path);
    $data=$excel["data"];


    $rs=array();
    foreach ($data as $key => $value) {
        
        if(!get_telephone($value["telephone"])&&reg_email($value["email"])&&reg_mobile($value["telephone"])&&reg_name($value["fullname"])){
            $value["status"]=0;
            $value["upload_uid"]=$_SESSION["uid"];
            $value["upload_time"]=time();
            $value["file"]=$file_path;
            $value["file_name"]=get_upload_name($file_path);
            $value["subsite_id"]=$_CFG['subsite_id'];
            $value["district_cn"]=$_CFG['subsite_districtname'];
            $value["telephone"] = str_replace("086-","",trim($value["telephone"]));


            $obj = \ORM::for_table(table('resume_temp'))->create($value);
            $rs[]=$obj->save();
        }
    }


    return $rs;
};
//删除临时简历
function del_resume_temp($id)
{
    global $db;
    if (!is_array($id)) $id=array($id);
    $sqlin=implode(",",$id);
    $return=0;
    if (preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin))
    {

        $rs=$db->getall("select * from ".table('resume_temp')." WHERE id IN ({$sqlin})");
        foreach($rs as $key=>$val){
            $db->query("update  ".table('members')."  SET resume_delete=resume_delete+1  WHERE uid='{$val['upload_uid']}'")  ;

        }


        if (!$db->query("Delete from ".table('resume_check_log')." WHERE rid IN ({$sqlin})")) return false;
        if (!$db->query("Delete from ".table('resume_temp')." WHERE id IN ({$sqlin})")) return false;

        $return=$return+$db->affected_rows();

       // if (!$db->query("Delete from ".table('resume_check_log')." WHERE rid IN ({$sqlin}) ")) return false;
//        if (!$db->query("Delete from ".table('resume_district')." WHERE pid IN ({$sqlin}) ")) return false;
//        if (!$db->query("Delete from ".table('resume_trade')." WHERE pid IN ({$sqlin}) ")) return false;
//        if (!$db->query("Delete from ".table('resume_tag')." WHERE pid IN ({$sqlin}) ")) return false;
//        if (!$db->query("Delete from ".table('resume_education')." WHERE pid IN ({$sqlin}) ")) return false;
//        if (!$db->query("Delete from ".table('resume_training')." WHERE pid IN ({$sqlin}) ")) return false;
//        if (!$db->query("Delete from ".table('resume_work')." WHERE pid IN ({$sqlin}) ")) return false;
//        if (!$db->query("Delete from ".table('resume_search_rtime')." WHERE id IN ({$sqlin})")) return false;
//        if (!$db->query("Delete from ".table('resume_search_key')." WHERE id IN ({$sqlin})")) return false;
//        if (!$db->query("Delete from ".table('view_resume')." WHERE resumeid IN ({$sqlin})")) return false;
//        $db->query("delete from ".table('resume_entrust')." where id IN (".$sqlin.")");
        //填写管理员日志
        write_log("删除简历id为".$id."的简历 , 共删除".$return."行", $_SESSION['admin_name'],3);
        return $return;
    }
    return $return;
}


//读取简历的审核日志；
function get_resume_check_log($id){
    global $db;
    $rs=$db->getall(vsprintf("select result from %s where rid=%d",array(table("resume_check_log"),$id)));

    if($rs){
        $items=array();
        foreach($rs as $item){
            $items[]=$item["result"];
        }
        return   implode(";",$items);
    }
    return "";
}

function get_check_info($memberuid)
{
    global $db;
    $sql = "select m.*,c.companyname,c.contact from ".table('members')." as m left join ".table("company_profile")." as c on m.uid=c.uid where m.uid=".intval($memberuid)." LIMIT 1";
    $val=$db->getone($sql);

    return $val;
}


function get_check_admin_info($memberuid)
{
    global $db;
    $sql = "select * from ".table('admin')." where admin_id=".intval($memberuid)." LIMIT 1";
    $val=$db->getone($sql);
    return $val;
}

//读取简历的审核日志；
function get_resume_check_loglist($id){
    global $db;
   // echo vsprintf("select * from %s where rid=%d",array(table("resume_check_log"),$id));
    $rs=$db->getall(vsprintf("select * from %s where rid=%d",array(table("resume_check_log"),$id)));
     //gv($rs);
    if($rs){
        foreach($rs as $key=>$value){
            if($value["cate"]==1){
                $rs[$key]["admin"]=get_check_admin_info($value["uid"]);

            }elseif($value["cate"]==0){
                $rs[$key]["member"]=get_check_info($value["uid"]);

            }
        }
    }
   // gv($rs);
    return $rs;
}




