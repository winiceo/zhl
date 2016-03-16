<?php
 /*
 * 74cms �������Ĺ��ú���
*/
 if(!defined('IN_QISHI'))
 {
 	die('Access Denied!');
 }
function admin_addslashes_deep($value)
{
    if (empty($value))
    {
        return $value;
    }
    else
    {
        return is_array($value) ? array_map('admin_addslashes_deep', $value) : addslashes($value);
    }
}
function deep_stripslashes($str)
 {
 	if(is_array($str)){
 		foreach($str as $key => $val){
 			$str[$key] = deep_stripslashes($val);
 		}
 	} else {
 		$str = stripslashes($str);
 	}
 	return $str;
 }
function adminmsg($msg_detail, $msg_type = 0, $links = array(), $auto_redirect = true,$seconds=3)
{
	global $smarty;
    if (count($links) == 0)
    {
        $links[0]['text'] = '������һҳ';
        $links[0]['href'] = 'javascript:history.go(-1)';
    }
   $smarty->assign('ur_here',     'ϵͳ��ʾ');
   $smarty->assign('msg_detail',  $msg_detail);
   $smarty->assign('msg_type',    $msg_type);
   $smarty->assign('links',       $links);
   $smarty->assign('default_url', $links[0]['href']);
   $smarty->assign('auto_redirect', $auto_redirect);
   $smarty->assign('seconds', $seconds);
   $smarty->display('sys/admin_showmsg.htm');
	exit();
}
function get_token()
{
	global $_CFG;
	if ($_CFG["open_csrf"]=="1")
	{
		global $smarty;
		if (!empty($_SESSION['token']))
		{
		unset($_SESSION['token']);
		}
		$hash = md5(uniqid(rand(), true));
		$n = mt_rand(1, 24);
		$token = substr($hash, $n, 8);
		$page=!empty($_SERVER['PHP_SELF'])?md5($_SERVER['PHP_SELF']):'token';
		$_SESSION['token'][$page] = $token;
		$smarty->assign('inputtoken', "<input type=\"hidden\"  name=\"hiddentoken\" value=\"{$_SESSION['token'][$page]}\" />");
		$smarty->assign('urltoken', "hiddentoken={$_SESSION['token'][$page]}");
	}
}
function check_token()
{
	global $_CFG;
	if ($_CFG["open_csrf"]=="1")
	{
		if (empty($_SESSION['token']))
		{
		unset($_SESSION['token']);
		global $smarty;
		$smarty->display('sys/admin_csrf.htm');
		exit();
		}
		else
		{
			$page=!empty($_SERVER['PHP_SELF'])?md5($_SERVER['PHP_SELF']):'token';
			$hiddentoken=!empty($_POST['hiddentoken'])?$_POST['hiddentoken']:$_GET['hiddentoken'];
			if ($_SESSION['token'][$page]!=$hiddentoken)
			{
			unset($_SESSION['token'],$hiddentoken);
			global $smarty;
			$smarty->display('sys/admin_csrf.htm');
			exit();
			}
		}
	unset($_SESSION['token'],$hiddentoken);
	}
}
function refresh_cache($cachename)
{
	global $db;
	$config_arr = array();
	$cache_file_path =QISHI_ROOT_PATH. "data/cache_".$cachename.".php";
	$sql = "SELECT * FROM ".table($cachename);
	$arr = $db->getall($sql);
		foreach($arr as $key=> $val)
		{
		$config_arr[$val['name']] = $val['value'];
		}
	write_static_cache($cache_file_path,$config_arr);
}
function refresh_page_cache()
{
	global $db;
	$cache_file_path =QISHI_ROOT_PATH. "data/cache_page.php";
		$sql = "SELECT * FROM ".table('page');
		$arr = $db->getall($sql);
			foreach($arr as $key=> $val)
			{
			$config_arr[$val['alias']] =array("file"=>$val['file'],"tpl"=>$val['tpl'],"rewrite"=>$val['rewrite'],"url"=>$val['url'],"caching"=>intval($val['caching'])*60,"tag"=>$val['tag'],"alias"=>$val['alias'],"pagetpye"=>$val['pagetpye']);
			}
		write_static_cache($cache_file_path,$config_arr);
}
function refresh_points_rule_cache()
{
	global $db;
	$cache_file_path =QISHI_ROOT_PATH. "data/cache_points_rule.php";
		$sql = "SELECT * FROM ".table('members_points_rule');
		$arr = $db->getall($sql);
			foreach($arr as $key=> $val)
			{
			$config_arr[$val['name']] =array("type"=>$val['operation'],"value"=>$val['value']);
			}
		write_static_cache($cache_file_path,$config_arr);
}
function refresh_category_cache()
{
	global $db;
	$cache_file_path =QISHI_ROOT_PATH. "data/cache_category.php";
	$sql = "SELECT * FROM ".table('category')."  ORDER BY c_order DESC,c_id ASC";
	$result = $db->query($sql);
		while($row = $db->fetch_array($result))
		{
			if ($row['c_alias']=="QS_officebuilding" || $row['c_alias']=="QS_street")
			{
			continue;
			}
			$catarr[$row['c_alias']][$row['c_id']] =array("id"=>$row['c_id'],"parentid"=>$row['c_parentid'],"categoryname"=>$row['c_name'],"stat_jobs"=>$row['stat_jobs'],"stat_resume "=>$row['stat_resume ']);
		}
		write_static_cache($cache_file_path,$catarr);
}
function refresh_nav_cache()
{
	global $db;
	$cache_file_path =QISHI_ROOT_PATH. "data/cache_nav.php";
		$sql = "SELECT * FROM ".table('navigation')." WHERE display=1   ORDER BY navigationorder DESC";
		$result = $db->query($sql);
			while($row = $db->fetch_array($result))
			{
				$row['color']?$row['title']="<span style=\"color:".$row['color']."\">".$row['title']."</span>":'';
				if ($row['urltype']=="0")
				{
				$row['url']=nav_url_rewrite($row['pagealias'],!empty($row['list_id'])?array('id'=>$row['list_id']):'');
				}
			$catarr[$row['alias']][] =array("title"=>$row['title'],"urltype"=>$row['urltype'],"url"=>$row['url'],"target"=>$row['target'],"tag"=>$row['tag'],"pagealias"=>$row['pagealias']);
			}
		write_static_cache($cache_file_path,$catarr);
}
function refresh_plug_cache()
{
	global $db;
	$cache_file_path =QISHI_ROOT_PATH. "data/cache_plug.php";
	$sql = "SELECT * FROM ".table('plug');
	$result = $db->query($sql);
		while($row = $db->fetch_array($result))
		{
			$catarr[$row['typename']] =array("plug_name"=>$row['plug_name'],"p_install"=>$row['p_install']);
		}
		write_static_cache($cache_file_path,$catarr);
}
function nav_url_rewrite($alias=NULL,$get=NULL,$rewrite=true)
{
	global $_CFG,$_PAGE;
	$url ='';
	if ($_PAGE[$alias]['url']=='0' || $rewrite==false)//ԭʼ����
	{
			if (!empty($get))
			{
				foreach($get as $k=>$v)
				{
				$url .="{$k}={$v}&";
				}
			}
			$url=!empty($url)?"?".rtrim($url,'&'):'';
			return $_CFG['site_dir'].$_PAGE[$alias]['file'].$url;
	}
	else 
	{
			$url =$_CFG['site_dir'].$_PAGE[$alias]['rewrite'];
			if ($_PAGE[$alias]['pagetpye']=='2' && empty($get['page']))
			{
			$get['page']=1;
			}
			foreach($get as $k=>$v)
			{
			$url=str_replace('($'.$k.')',$v,$url);
			}
			return preg_replace('/\(\$(.+?)\)/','',$url);
	}
}
function write_static_cache($cache_file_path, $config_arr)
{
	$content = "<?php\r\n";
	$content .= "\$data = " . var_export($config_arr, true) . ";\r\n";
	$content .= "?>";
	if (!file_put_contents($cache_file_path, $content, LOCK_EX))
	{
		$fp = @fopen($cache_file_path, 'wb+');
		if (!$fp)
		{
			exit('���ɻ����ļ�ʧ��');
		}
		if (!@fwrite($fp, trim($content)))
		{
			exit('���ɻ����ļ�ʧ��');
		}
		@fclose($fp);
	}
}
function write_log($str, $user,$log_type=1)
{
 	global $db, $timestamp,$online_ip;
 	$sql = "INSERT INTO ".table('admin_log')." (log_id, admin_name, add_time, log_value,log_ip,log_type) VALUES ('', '$user', '$timestamp', '$str','$online_ip','".intval($log_type)."')"; 
	return $db->query($sql);
}
function check_admin($name,$pwd)
{
 	global $db,$QS_pwdhash;
	$admin=get_admin_one($name);
	$md5_pwd=md5($pwd.$admin['pwd_hash'].$QS_pwdhash);
 	$row = $db->getone("SELECT COUNT(*) AS num FROM ".table('admin')." WHERE admin_name='$name' and pwd ='".$md5_pwd."' ");
 	if($row['num'] > 0){
 		return true;
 	}else{
 		return false;
 	}
}
function update_admin_info($admin_name,$refresh = true)
{
 	global $timestamp, $online_ip, $db;
	$admin = $db->getone("SELECT admin_id, admin_name, purview FROM ".table('admin')." WHERE admin_name = '$admin_name'");
 	$_SESSION['admin_id'] = $admin['admin_id'];
 	$_SESSION['admin_name'] = $admin['admin_name'];
 	$_SESSION['admin_purview'] = $admin['purview'];
	if ($refresh == true)
	{
		$last_login_time = $timestamp;
		$last_login_ip = $online_ip;
		$sql = "UPDATE ".table('admin')." SET last_login_time = '$last_login_time', last_login_ip = '$last_login_ip' WHERE admin_id='$_SESSION[admin_id]'";
		$db->query($sql);
		del_log($admin['admin_name'],90);
	}
 }
function check_cookie($user_name, $pwd)
 {
 	global $db,$QS_pwdhash;
 	$sql = "SELECT * FROM ".table('admin')." WHERE admin_name='".$user_name."' ";
 	$user = $db->getone($sql);
 	if(md5($user['admin_name'].$user['pwd'].$user['pwd_hash'].$QS_pwdhash) == $pwd)
	{
	return true;
	}
	return false;
 }
function get_admin_one($username){
	global $db;
	$sql = "select * from ".table('admin')." where admin_name = '".$username."' LIMIT 1";
	return $db->getone($sql);
}

function del_log($admin_name,$settr=30)
{
global $db;
$settr_val=strtotime("-".$settr." day");
if (!$db->query("Delete from ".table('admin_log')." WHERE admin_name='".$admin_name."' AND  add_time<".$settr_val." ")) return false;
return true;
}
function check_permissions($purview,$str)
{
	 if ($purview=="all")
	 {
	 return true;
	 }
	 else
	 {
	 $purview_arr=explode(',',$purview);
	 }
	 	if (in_array($str,$purview_arr))
		{
		return true;
		}
		else
		{
		permissions_insufficient();
		}
}


//�Ƿ����ĳȨ��
function get_permissions($purview,$str)
{
	if ($purview=="all")
	{
		return true;
	}
	else
	{
		$purview_arr=explode(',',$purview);
	}
	if (in_array($str,$purview_arr))
	{
		return true;
	}
	else
	{
		return false;
	}
}
function permissions_insufficient()
{
	global $smarty;
    $smarty->display('sys/admin_sotp.htm');
	exit();
}
function html2text($str){
	$str = preg_replace("/<sty(.*)\\/style>|<scr(.*)\\/script>|<!--(.*)-->/isU","",$str);
	$alltext = "";
	$start = 1;
	for($i=0;$i<strlen($str);$i++)
	{
		if($start==0 && $str[$i]==">")
		{
			$start = 1;
		}
		else if($start==1)
		{
			if($str[$i]=="<")
			{
				$start = 0;
				$alltext .= " ";
			}
			else if(ord($str[$i])>31)
			{
				$alltext .= $str[$i];
			}
		}
	}
	$alltext = str_replace("��"," ",$alltext);
	$alltext = preg_replace("/&([^;&]*)(;|&)/","",$alltext);
	$alltext = preg_replace("/[ ]+/s"," ",$alltext);
	return $alltext;
}
function getsubdirs($dir) {
        $subdirs = array();
        if(!$dh = opendir($dir)) return $subdirs;
        while ($f = readdir($dh)) {
                if($f =='.' || $f =='..') continue;
                $path = $dir.'/'.$f;  //���ֻҪ��Ŀ¼��, path = $f;
				$subdir=$f;
                if(is_dir($path)) {
                        $subdirs[] = $subdir;
                }
        }
		closedir($dh);
        return $subdirs;
}
function makejs_classify()
{
	global $db;
	$content = "//JavaScript Document ����ʱ�䣺".date("Y-m-d  H:i:s")."\n\n";
	$sql = "select * from ".table('subsite')." where s_effective=1 order BY s_order desc,s_id asc";
	$list=$db->getall($sql);
	foreach($list as $parent)
	{
	$parentarr[]="\"".$parent['s_id']."-".$parent['s_district'].",".$parent['s_districtname']."\"";
	}
	$content .= "var QS_subsite=new Array(".implode(',',$parentarr).");\n";	
	unset($parentarr);
	$sql = "select * from ".table('category_district')." where parentid=0 order BY category_order desc,id asc";
	$list=$db->getall($sql);
	foreach($list as $parent)
	{
	$parentarr[]="\"".$parent['id'].",".$parent['categoryname']."\"";
	}
	$content .= "var QS_city_parent=new Array(".implode(',',$parentarr).");\n";	
	unset($parentarr);
	$content .= "var QS_city=new Array();\n";
	$third_content = "";
	foreach($list as $val)
	{
		$sql1 = "select * from ".table('category_district')." where parentid=".$val['id']."  order BY category_order desc,id asc";
		$list1=$db->getall($sql1);
		if (is_array($list1))
		{	
			foreach($list1 as $val1)
			{
				$sarr[]=$val1['id'].",".$val1['categoryname'];
				$sql2 = "select * from ".table('category_district')." where parentid=".$val1['id']."  order BY category_order desc,id asc";
				$list2=$db->getall($sql2);
				if (is_array($list2))
				{	
					foreach($list2 as $val2)
					{
					$third_arr[]=$val2['id'].",".$val2['categoryname'];
					}
				$content_third .= "QS_city[".$val1['id']."]=\"".implode('|',$third_arr)."\"; \n";
				unset($third_arr);
				}
			}
		$content .= "QS_city[".$val['id']."]=\"".implode('|',$sarr)."\"; \n";	
		unset($sarr);
		}
	}
	// $content .= "var QS_city_third=new Array(); \n";
	$content .= $content_third;	
	// unset($third_arr);
	$sql = "select * from ".table('category_jobs')." where parentid=0 order BY category_order desc,id asc";
	$list=$db->getall($sql);
	foreach($list as $parent)
	{
	$parentarr[]="\"".$parent['id'].",".$parent['categoryname']."\"";
	}
	$content .= "var QS_jobs_parent=new Array(".implode(',',$parentarr).");\n";	
	unset($parentarr);
	$content .= "var QS_jobs=new Array(); \n";
	$content_third = "";
	foreach($list as $val)
	{
		$sql1 = "select * from ".table('category_jobs')." where parentid=".$val['id']."  order BY category_order desc,id asc";
		$list1=$db->getall($sql1);
		if (is_array($list1))
		{	
			foreach($list1 as $val1)
			{
				$sarr[]=$val1['id'].",".$val1['categoryname'];
				$sql2 = "select * from ".table('category_jobs')." where parentid=".$val1['id']."  order BY category_order desc,id asc";
				$list2=$db->getall($sql2);
				if (is_array($list2))
				{	
					foreach($list2 as $val2)
					{
					$third_arr[]=$val2['id'].",".$val2['categoryname'];
					}
				$content_third .= "QS_jobs[".$val1['id']."]=\"".implode('|',$third_arr)."\"; \n";
				unset($third_arr);
				}
			}
		$content .= "QS_jobs[".$val['id']."]=\"".implode('|',$sarr)."\"; \n";	
		unset($sarr);
		}
	}
	// $content .= "var QS_jobs_third=new Array(); \n";
	$content .= $content_third;	
	// unset($third_arr);
	
	//�߼�ְλ����
	$sql = "select * from ".table('category_hunterjobs')." where parentid=0 ";
	$list=$db->getall($sql);
	foreach($list as $parent)
	{
	$hunterjobs_arr[]="\"".$parent['id'].",".$parent['categoryname']."\"";
	}
	$content .= "var QS_hunter_jobs_parent=new Array(".implode(',',$hunterjobs_arr).");\n";	
	$content .= "var QS_hunter_jobs=new Array(); \n";
	foreach($list as $val)
	{
		$sql1 = "select * from ".table('category_hunterjobs')." where parentid=".$val['id']."  order BY category_order desc,id asc";
		$list1=$db->getall($sql1);
		if (is_array($list1))
		{	
			foreach($list1 as $val1)
			{
			$sarr[]=$val1['id'].",".$val1['categoryname'];
			}
		$content .= "QS_hunter_jobs[".$val['id']."]=\"".implode('|',$sarr)."\"; \n";	
		unset($sarr);
		}
	}
	
	//
	$sql = "select * from ".table('category')." ORDER BY c_order DESC,c_id ASC";
	$list=$db->getall($sql);
	foreach($list as $li)
	{
		if ($li['c_alias']=="QS_trade")
		{
		$trade[]="\"".$li['c_id'].",".$li['c_name']."\"";
		}
		elseif ($li['c_alias']=="QS_company_type")
		{
		$companytype[]="\"".$li['c_id'].",".$li['c_name']."\"";
		}
		elseif ($li['c_alias']=="QS_wage")
		{
		$wage[]="\"".$li['c_id'].",".$li['c_name']."\"";
		}
		elseif ($li['c_alias']=="QS_jobs_nature")
		{
		$jobsnature[]="\"".$li['c_id'].",".$li['c_name']."\"";
		}
		elseif ($li['c_alias']=="QS_education")
		{
		$education[]="\"".$li['c_id'].",".$li['c_name']."\"";
		}
		elseif ($li['c_alias']=="QS_experience")
		{
		$experience[]="\"".$li['c_id'].",".$li['c_name']."\"";
		}
		elseif ($li['c_alias']=="QS_scale")
		{
		$scale[]="\"".$li['c_id'].",".$li['c_name']."\"";
		}
		elseif ($li['c_alias']=="QS_jobtag")
		{
		$jobtag[]="\"".$li['c_id'].",".$li['c_name']."\"";
		}
		elseif ($li['c_alias']=="QS_resumetag")
		{
		$resumetag[]="\"".$li['c_id'].",".$li['c_name']."\"";
		}
		elseif ($li['c_alias']=="QS_hunter_wage")
		{
		$hunterwage[]="\"".$li['c_id'].",".$li['c_name']."\"";
		}
		elseif ($li['c_alias']=="QS_hunter_age")
		{
		$hunterage[]="\"".$li['c_id'].",".$li['c_name']."\"";
		}
		elseif ($li['c_alias']=="QS_hunter_wage_structure")
		{
		$wagestructure[]="\"".$li['c_id'].",".$li['c_name']."\"";
		}
		elseif ($li['c_alias']=="QS_hunter_socialbenefits")
		{
		$socialbenefits[]="\"".$li['c_id'].",".$li['c_name']."\"";
		}
		elseif ($li['c_alias']=="QS_hunter_livebenefits")
		{
		$livebenefits[]="\"".$li['c_id'].",".$li['c_name']."\"";
		}
		elseif ($li['c_alias']=="QS_hunter_annualleave")
		{
		$annualleave[]="\"".$li['c_id'].",".$li['c_name']."\"";
		}		
		elseif ($li['c_alias']=="QS_hunter_rank")
		{
		$rank[]="\"".$li['c_id'].",".$li['c_name']."\"";
		}		
		elseif ($li['c_alias']=="QS_language")
		{
		$language[]="\"".$li['c_id'].",".$li['c_name']."\"";
		}
		elseif ($li['c_alias']=="QS_current")
		{
		$current[]="\"".$li['c_id'].",".$li['c_name']."\"";
		}
	
	}
	$content .= "var QS_trade=new Array(".implode(',',$trade).");\n";
	$content .= "var QS_companytype=new Array(".implode(',',$companytype).");\n";
	$content .= "var QS_wage=new Array(".implode(',',$wage).");\n";
	$content .= "var QS_jobsnature=new Array(".implode(',',$jobsnature).");\n";
	$content .= "var QS_education=new Array(".implode(',',$education).");\n";
	$content .= "var QS_experience=new Array(".implode(',',$experience).");\n";
	$content .= "var QS_scale=new Array(".implode(',',$scale).");\n";
	$content .= "var QS_jobtag=new Array(".implode(',',$jobtag).");\n";
	$content .= "var QS_resumetag=new Array(".implode(',',$resumetag).");\n";
	$content .= "var QS_hunter_wage=new Array(".implode(',',$hunterwage).");\n";
	$content .= "var QS_hunter_age=new Array(".implode(',',$hunterage).");\n";
	$content .= "var QS_hunter_wage_structure=new Array(".implode(',',$wagestructure).");\n";
	$content .= "var QS_hunter_socialbenefits=new Array(".implode(',',$socialbenefits).");\n";
	$content .= "var QS_hunter_livebenefits =new Array(".implode(',',$livebenefits).");\n";
	$content .= "var QS_hunter_annualleave=new Array(".implode(',',$annualleave).");\n";
	$content .= "var QS_hunter_rank=new Array(".implode(',',$rank).");\n";
	$content .= "var QS_language=new Array(".implode(',',$language).");\n";
	$content .= "var QS_current=new Array(".implode(',',$current).");\n";
	/*
		������Ʒ����js
	*/
	$sql = "select * from ".table('shop_category')." where parentid=0 order BY category_order desc,id asc";
	$list=$db->getall($sql);
	foreach($list as $parent)
	{
	$parentarr[]="\"".$parent['id'].",".$parent['categoryname']."\"";
	}
	$content .= "var QS_shop_parent=new Array(".implode(',',$parentarr).");\n";	
	unset($parentarr);
	$content .= "var QS_shop=new Array();\n";
	$third_content = "";
	foreach($list as $val)
	{
		$sql1 = "select * from ".table('shop_category')." where parentid=".$val['id']."  order BY category_order desc,id asc";
		$list1=$db->getall($sql1);
		if (is_array($list1))
		{	
			foreach($list1 as $val1)
			{
				$sarr[]=$val1['id'].",".$val1['categoryname'];
				$sql2 = "select * from ".table('shop_category')." where parentid=".$val1['id']."  order BY category_order desc,id asc";
				$list2=$db->getall($sql2);
				if (is_array($list2))
				{	
					foreach($list2 as $val2)
					{
					$third_arr[]=$val2['id'].",".$val2['categoryname'];
					}
				$content_third .= "QS_shop[".$val1['id']."]=\"".implode('|',$third_arr)."\"; \n";
				unset($third_arr);
				}
			}
		$content .= "QS_shop[".$val['id']."]=\"".implode('|',$sarr)."\"; \n";	
		unset($sarr);
		}
	}
	/*
		����רҵ����js
	*/
	$sql = "select * from ".table('category_major')." where parentid=0 order BY category_order desc,id asc";
	$list=$db->getall($sql);
	foreach($list as $parent)
	{
	$parentarr[]="\"".$parent['id'].",".$parent['categoryname']."\"";
	}
	$content .= "var QS_major_parent=new Array(".implode(',',$parentarr).");\n";	
	unset($parentarr);
	$content .= "var QS_major=new Array();\n";
	$third_content = "";
	foreach($list as $val)
	{
		$sql1 = "select * from ".table('category_major')." where parentid=".$val['id']."  order BY category_order desc,id asc";
		$list1=$db->getall($sql1);
		if (is_array($list1))
		{	
			foreach($list1 as $val1)
			{
				$sarr[]=$val1['id'].",".$val1['categoryname'];
				$sql2 = "select * from ".table('category_major')." where parentid=".$val1['id']."  order BY category_order desc,id asc";
				$list2=$db->getall($sql2);
				if (is_array($list2))
				{	
					foreach($list2 as $val2)
					{
					$third_arr[]=$val2['id'].",".$val2['categoryname'];
					}
				$content_third .= "QS_major[".$val1['id']."]=\"".implode('|',$third_arr)."\"; \n";
				unset($third_arr);
				}
			}
		$content .= "QS_major[".$val['id']."]=\"".implode('|',$sarr)."\"; \n";	
		unset($sarr);
		}
	}
	$fp = @fopen(QISHI_ROOT_PATH . 'data/cache_classify.js', 'wb+');
	if (!$fp){
			exit('����JS�ļ�ʧ��');
		}
	if (strcasecmp(QISHI_DBCHARSET,"utf8")!=0)
	{
	$content=iconv(QISHI_DBCHARSET,"utf-8//IGNORE",$content);
	}
	 if (!@fwrite($fp, trim($content))){
			exit('д��JS�ļ�ʧ��');
		}
	@fclose($fp);
}
function makejs_train_classify()
{
	global $db;
	$content = "//JavaScript Train Document ����ʱ�䣺".date("Y-m-d  H:i:s")."\n\n";
	$sql = "select * from ".table('category_district')." where parentid=0  ORDER BY category_order desc,id asc";
	$list=$db->getall($sql);
	foreach($list as $parent)
	{
	$parentarr[]="\"".$parent['id'].",".$parent['categoryname']."\"";
	}
	$content .= "var QS_city_parent=new Array(".implode(',',$parentarr).");\n";	
	unset($parentarr);
	$content .= "var QS_city=new Array();\n";
	foreach($list as $val)
	{
		$sql1 = "select * from ".table('category_district')." where parentid=".$val['id']."  order BY category_order desc,id asc";
		$list1=$db->getall($sql1);
		if (is_array($list1))
		{	
			foreach($list1 as $val1)
			{
				$sarr[]=$val1['id'].",".$val1['categoryname'];
				$sql2 = "select * from ".table('category_district')." where parentid=".$val1['id']."  order BY category_order desc,id asc";
				$list2=$db->getall($sql2);
				if (is_array($list2))
				{	
					foreach($list2 as $val2)
					{
					$third_arr[]=$val2['id'].",".$val2['categoryname'];
					}
				$content_third .= "QS_city[".$val1['id']."]=\"".implode('|',$third_arr)."\"; \n";
				unset($third_arr);
				}
			}
		$content .= "QS_city[".$val['id']."]=\"".implode('|',$sarr)."\"; \n";	
		unset($sarr);
		}
	}
	// $content .= "var QS_city_third=new Array(); \n";
	$content .= $content_third;	
	//
	$sql = "select * from ".table('category')." ORDER BY c_order DESC,c_id ASC";
	$list=$db->getall($sql);
	foreach($list as $li)
	{
		if ($li['c_alias']=="QS_train_category")
		{
		$train_category[]="\"".$li['c_id'].",".$li['c_name']."\"";
		}elseif ($li['c_alias']=="QS_train_type")
		{
		$train_type[]="\"".$li['c_id'].",".$li['c_name']."\"";
		}elseif ($li['c_alias']=="QS_train_classtype")
		{
		$train_classtype[]="\"".$li['c_id'].",".$li['c_name']."\"";
		}elseif ($li['c_alias']=="QS_education")
		{
		$education[]="\"".$li['c_id'].",".$li['c_name']."\"";
		}

	}
	$content .= "var QS_train_category=new Array(".implode(',',$train_category).");\n";
	$content .= "var QS_train_type=new Array(".implode(',',$train_type).");\n";
	$content .= "var QS_train_classtype=new Array(".implode(',',$train_classtype).");\n";
	$content .= "var QS_education=new Array(".implode(',',$education).");\n";
	$fp = @fopen(QISHI_ROOT_PATH . 'data/cache_train_classify.js', 'wb+');
	if (!$fp){
			exit('����JS�ļ�ʧ��');
		}
	if (strcasecmp(QISHI_DBCHARSET,"utf8")!=0)
	{
	$content=iconv(QISHI_DBCHARSET,"utf-8//IGNORE",$content);
	}
	 if (!@fwrite($fp, trim($content))){
			exit('д��JS�ļ�ʧ��');
		}
	@fclose($fp);
}
function traverse($path) {
    $current_dir = opendir("../".$path);    //opendir()����һ��Ŀ¼���,ʧ�ܷ���false
 
    while(($file = readdir($current_dir)) !== false) {    //readdir()���ش�Ŀ¼����е�һ����Ŀ
        $sub_dir = $path . DIRECTORY_SEPARATOR . $file;    //������Ŀ¼·��
        if($file == '.' || $file == '..') {
            continue;
        } else {    //������ļ�,ֱ�����
            // echo 'File in Directory ' . $path . ': ' . $file . '<br>';
            if(strpos(strtolower($file),"php")){
				$f[] = $file;
            }
        }
    }
    return $f;
}
function make_city_file($dirname,$cityid){
	if(!$dirname||!$cityid){
		return ;
	}
	// write_city_file($dirname,"",$cityid);
	write_city_file($dirname,"company",$cityid);
	write_city_file($dirname,"explain",$cityid);
	write_city_file($dirname,"help",$cityid);
	write_city_file($dirname,"hrtools",$cityid);
	write_city_file($dirname,"hunter",$cityid);
	write_city_file($dirname,"jobfair",$cityid);
	write_city_file($dirname,"jobs",$cityid);
	write_city_file($dirname,"news",$cityid);
	write_city_file($dirname,"notice",$cityid);
	write_city_file($dirname,"resume",$cityid);
	write_city_file($dirname,"simple",$cityid);
	write_city_file($dirname,"train",$cityid);
	write_city_file($dirname,"plus",$cityid);
	write_city_file($dirname,"user",$cityid);
	// if(file_exists(QISHI_ROOT_PATH."httpd.ini")){

	// 	copy(QISHI_ROOT_PATH."httpd.ini",QISHI_ROOT_PATH.$dirname."/httpd.ini");
	// }
	// if(file_exists(QISHI_ROOT_PATH.".htaccess")){
	// 	copy(QISHI_ROOT_PATH.".htaccess",QISHI_ROOT_PATH.$dirname."/.htaccess");
	// }
}
function create_excel($top_str,$data){
	header("Content-Type: application/vnd.ms-execl");
	header("Content-Disposition: attachment; filename=myExcel.xls");
	header("Pragma: no-cache");
	header("Expires: 0");
	echo $top_str;
	foreach ($data as $k => $v) {
		foreach ($v as $k1 => $v1) {
			echo $v1;
			echo ($k1+1)<count($v)?"\t":"";
		}
		echo "\t\n";
	}
}
/*
����΢�Ų˵�ʱ��ȡ�˵�json����
 */
function get_weixin_json_menu(){
	global $db;
	$arr = array();
	$menu_arr = $db->getall("select * from ".table('weixin_menu')." where parentid=0 and status=1 order by menu_order desc,id asc");
	foreach ($menu_arr as $key => $value) {
		$sub_menu = $db->getall("select * from ".table('weixin_menu')." where parentid=".$value['id']." and status=1 order by menu_order desc,id asc");
		if(!empty($sub_menu)){
			$arr[$key]['name'] = urlencode(iconv("gbk","utf-8",$value['title']));
			foreach ($sub_menu as $sub_key => $sub_value) {
				$arr[$key]['sub_button'][$sub_key]['type'] = $sub_value['type'];
				$arr[$key]['sub_button'][$sub_key]['name'] = urlencode(iconv("gbk","utf-8",$sub_value['title']));
				if($sub_value['type']=="click"){
					$arr[$key]['sub_button'][$sub_key]['key'] = $sub_value['key'];
				}else{
					$arr[$key]['sub_button'][$sub_key]['url'] = $sub_value['url'];
				}
			}
		}else{
			$arr[$key]['type'] = $value['type'];
			$arr[$key]['name'] = urlencode(iconv("gbk","utf-8",$value['title']));
			if($value['type']=="click"){
				$arr[$key]['key'] = $value['key'];
			}else{
				$arr[$key]['url'] = $value['url'];
			}
		}
	}
	$menu['button'] = $arr;
	return urldecode(json_encode($menu));
}
/**
 * uc���ϸ��»���
 * @return [type] [description]
 */
function refresh_uc_cache($cachename){
	global $db;
	$data = "";
	$cache_file_path =QISHI_ROOT_PATH. "data/cache_".$cachename.".php";
	$sql = "SELECT * FROM ".table($cachename);
	$arr = $db->getall($sql);
		foreach($arr as $key=> $val)
		{
		$data .= 'define("'.strtoupper($val["name"]).'","'.$val["value"].'");';
		}
	$content = "<?php\r\n";
	$content .= $data . ";\r\n";
	$content .= "?>";
	if (!file_put_contents($cache_file_path, $content, LOCK_EX))
	{
		$fp = @fopen($cache_file_path, 'wb+');
		if (!$fp)
		{
			exit('���ɻ����ļ�ʧ��');
		}
		if (!@fwrite($fp, trim($content)))
		{
			exit('���ɻ����ļ�ʧ��');
		}
		@fclose($fp);
	}
}
//��ȡuc����
function get_uc_config(){
	global $db;
	$arr = array();
	$list = $db->getall("select * from ".table('uc_config'));
	foreach ($list as $key => $value) {
		$arr[$value['name']] = $value['value'];
	}
	return $arr;
}
// ��ȡ���ݾۺ�����
function get_content_key_link($offset, $perpage, $wheresql= '')
{
	global $db;
	$limit=" LIMIT ".$offset.','.$perpage;
	return $db->getall("SELECT * FROM ".table('content_key_link').$wheresql.$limit);
}
// ����Ƿ���д�����ݾۺϱ�ǩ
function check_content_key_link($content_key)
{
	global $db;
	$content_key=trim($content_key);
	return $db->getone("select * from ".table('content_key_link')." where name='{$content_key}' limit 1");
}
// ɾ�����ݾۺϹؼ���
function del_content_key_link($id)
{
	global $db;
	if(!is_array($id)) $id=array($id);
	$return=0;
	$sqlin=implode(",",$id);
	if (preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin))
	{
		if (!$db->query("Delete from ".table('content_key_link')." WHERE id IN (".$sqlin.") ")) return false;
		$return=$return+$db->affected_rows();
	}
	return $return;
}
// ��ȡ���� ���ݾۺϹؼ���
function get_content_key_link_one($id)
{
	global $db;
	return $db->getone("select * from ".table('content_key_link')." where id='{$id}' limit 1");
}
/**
 * ���ݾۺϹؼ��ֻ���
 * @return [type] [description]
 */
function refresh_content_key_link_cache($cachename){
	global $db;
	$data = "";
	$cache_file_path =QISHI_ROOT_PATH. "data/cache_".$cachename.".php";
	$sql = "SELECT * FROM ".table($cachename);
	$arr = $db->getall($sql);
	write_static_cache($cache_file_path,$arr);
}
/*���ݾۺ� ��ǩ��� ����*/
//����������
function tag_sort($a, $b){
    if ( $a['name'] == $b['name'] ) return 0;
    return ( strlen($a['name']) > strlen($b['name']) ) ? -1 : 1;
}
//�ı��ǩ�ؼ���
function tag_link($content){
	 global $_CFG;
     $limit = $_CFG['content_key_link']; //һƪ������ͬһ���ؼ��������ֶ��ٴ����ı������鲻����2�Σ�
     $posttags =get_cache('content_key_link');
     if ($posttags) {
         usort($posttags, "tag_sort");
         foreach($posttags as $tag) {
             $link = $tag['url'];
             $keyword = $tag['name'];
             //���Ӵ���
             $cleankeyword = stripslashes($keyword);
             $ex_word = preg_quote($cleankeyword,'\'');
             $url = "<a href=\"$link\" title=\"".$cleankeyword."\"";
             $url .= ' target="_blank"';
             $url .= ">".addcslashes($cleankeyword, '$')."</a>";
             
             //�����ӵ� ����
             $content = preg_replace( '|(<a[^>]+>)(.*)('.$keyword.')(.*)(</a[^>]*>)|U', '$1$2%&&&&&%$4$5', $content);
             $content = preg_replace( '|(<img)(.*?)('.$keyword.')(.*?)(>)|U', '$1$2%&&&&&%$4$5', $content);
             $cleankeyword = preg_quote($cleankeyword,'\'');
             $regEx = '\'(?!((<.*?)|(<a.*?)))('. $cleankeyword . ')(?!(([^<>]*?)>)|([^>]*?</a>))\'s';
             $content = preg_replace($regEx,$url,$content,$limit);
             $content = str_replace( '%&&&&&%', stripslashes($ex_word), $content);
         }
     }
    return $content;
}
// ��ȡ�ʼ�ϵͳ��־
function get_mail_log($offset, $perpage, $wheresql= '')
{
	global $db;
	$row_arr = array();
	$limit=" LIMIT ".$offset.','.$perpage;
	$result = $db->query("SELECT * FROM ".table('sys_email_log').$wheresql.$limit);
	while($row = $db->fetch_array($result))
	{
	$row['subject']=$row['subject'];
	$row['subject_']=cut_str(strip_tags($row['subject']),18,0,"...");
	$row['body']=strip_tags($row['body']);
	$row['body_']=cut_str(strip_tags($row['body']),18,0,"...");
	$row_arr[] = $row;
	}
	return $row_arr;
}
?>