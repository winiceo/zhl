<?php
 /*
 * ajax����
*/
define('IN_QISHI', true);
define('REQUEST_MOBILE',true);
require_once(dirname(__FILE__).'/../../include/plus.common.inc.php');
require_once(dirname(__FILE__).'/../../include/fun_wap.php');
require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
require_once(QISHI_ROOT_PATH.'include/fun_company.php');
require_once(QISHI_ROOT_PATH.'include/fun_weixin.php');
$db = new mysql($dbhost,$dbuser,$dbpass,$dbname);
$act = !empty($_REQUEST['act']) ? trim($_REQUEST['act']) : '';
if($act == 'ajaxjobslist'){
	$jobslisthtml="";
	$district = intval($_GET['district'])==0?"":intval($_GET['district']);
	$sdistrict = intval($_GET['sdistrict'])==0?"":intval($_GET['sdistrict']);
	$trade = intval($_GET['trade'])==0?"":intval($_GET['trade']);
	$topclass = intval($_GET['topclass'])==0?"":intval($_GET['topclass']);
	$category = intval($_GET['category'])==0?"":intval($_GET['category']);
	$subclass = intval($_GET['subclass'])==0?"":intval($_GET['subclass']);
	$recommend = intval($_GET['recommend'])==0?"":intval($_GET['recommend']);
	$emergency = intval($_GET['emergency'])==0?"":intval($_GET['emergency']);
	$wage = intval($_GET['wage'])==0?"":intval($_GET['wage']);
	$key = empty($_GET['key'])?"":iconv("UTF-8","GBK",$_GET['key']);
	$settr = intval($_GET['settr'])==0?"":intval($_GET['settr']);
	$education = intval($_GET['education'])==0?"":intval($_GET['education']);
	$experience = intval($_GET['experience'])==0?"":intval($_GET['experience']);
	$nature = intval($_GET['nature'])==0?"":intval($_GET['nature']);
	$scale = intval($_GET['scale'])==0?"":intval($_GET['scale']);
	$jobstable=table('jobs_search_stickrtime');
	$orderbysql=" ORDER BY `stick` desc,`refreshtime` desc,`id` desc ";
	if ($district<>'')
	{
		$wheresql.=" AND `district` = ".$district;
		if ($sdistrict<>'')
		{
			$wheresql.=" AND `sdistrict` = ".$sdistrict;
		}
	}
	if ($trade<>'')
	{
		$wheresql.=" AND `trade` = ".$trade;
	}
	if ($topclass<>'')
	{
		$wheresql.=" AND `topclass` = ".$topclass;
		if ($category<>'')
		{
			$wheresql.=" AND `category` = ".$category;
			if ($subclass<>'')
			{
				$wheresql.=" AND `subclass` = ".$subclass;
			}
		}
	}
	if ($wage<>'')
	{
		$wheresql.=" AND `wage` = ".$wage;
	}
	if ($recommend<>'')
	{
		$wheresql.=" AND `recommend` = ".$recommend;
	}
	if ($emergency<>'')
	{
		$wheresql.=" AND `emergency` = ".$emergency;
	}
	if (!empty($key))
	{
		$key=trim($key);
		if ($_CFG['jobsearch_type']=='1')
		{
			$akey=explode(' ',$key);
			if (count($akey)>1)
			{
			$akey=array_filter($akey);
			$akey=array_slice($akey,0,2);
			$akey=array_map("fulltextpad",$akey);
			$key='+'.implode(' +',$akey);
			$mode=' IN BOOLEAN MODE';
			}
			else
			{
			$key=fulltextpad($key);
			$mode=' ';
			}
			$wheresql.=" AND  MATCH (`key`) AGAINST ('{$key}'{$mode}) ";
		}
		else
		{
			$wheresql.=" AND likekey LIKE '%{$key}%' ";
		}
		$orderbysql=" ORDER BY `refreshtime` desc,`id` desc ";
		$jobstable=table('jobs_search_key');
	}
	if($settr<>'')
	{
		$settr_val=strtotime("-{$settr} day");
		$wheresql.=" AND refreshtime>{$settr_val} ";
	}
	if($education<>'')
	{
		$wheresql.=" AND education={$education} ";
	}
	if($experience<>'')
	{
		$wheresql.=" AND experience={$experience} ";
	}
	if($nature<>'')
	{
		$wheresql.=" AND nature={$nature} ";
	}
	if($scale<>'')
	{
		$wheresql.=" AND scale={$scale} ";
	}
	if (!empty($wheresql))
	{
	$wheresql=" WHERE ".ltrim(ltrim($wheresql),'AND');
	}
	$rows=intval($_GET['rows']);
	$offset=intval($_GET['offset']); 


	$idresult = $db->query("SELECT id FROM {$jobstable} ".$wheresql.$orderbysql."  LIMIT {$offset},{$rows}");
	while($row = $db->fetch_array($idresult))
	{
	$id[]=$row['id'];
	}
	if (!empty($id))
	{
		$wheresql=" WHERE id IN (".implode(',',$id).") ";
		$jobs = $db->getall("SELECT * FROM ".table('jobs').$wheresql.$orderbysql);
		foreach ($jobs as $key => $value) {
			$jobs[$key]['url'] = wap_url_rewrite("jobs-show",array("id"=>$value['id']),1,$value['subsite_id']);
		}	
	}
	else
	{
		$jobs=array();
	}
	// $jobslistarray=$db->getall("select * from ".table("jobs").$wheresql." ORDER BY `refreshtime` DESC LIMIT {$offset},{$rows}");
	if (!empty($jobs) && $offset<=100)
	{
		foreach($jobs as $li)
		{
			$url = wap_url_rewrite("jobs-show",array("id"=>$li['id']),1,$li['subsite_id']);
			$jobslisthtml.='<section class="jobs-item thisurl box" url="'.$url.'">';
			$ding = '';
			if($li['stick']=="1")
			{
				$ding = '<span class="job-ding"></span><h3><a href="'.$url.'">'.$li["jobs_name"].'</a>';
			}
			else
			{
				$ding = '<h3><a href="'.$url.'">'.$li["jobs_name"].'</a>';
			}
			$emergency = '';
			if($li['emergency']=="1")
			{
				$emergency = '<i class="ji-icon">��</i>';
			}
			$recommend = '';
			if($li['recommend']=="1")
			{
				$recommend = '<i class="jian-icon">��</i>';
			}
			$jobslisthtml.=$ding.$emergency.$recommend;
			$jobslisthtml.='</h3>
						<div class="jobs-add">'.$li["district_cn"].' | '.$li["companyname"].'</div>
						<div class="pay-date clearfix">
							<div class="money f-left">'.$li["wage_cn"].'</div>
							<span class="f-right date">';
			$refreshtime = date('Y-m-d',$li["refreshtime"]);
			$jobslisthtml.=$refreshtime.'</span></div></section>';

		}
		exit($jobslisthtml);
	}
	else
	{
		exit('-1');
	}
}
elseif($act == 'ajaxnewslist'){
	$newslisthtml="";
	$rows=intval($_GET['rows']);
	$offset=intval($_GET['offset']); 
	$type = intval($_GET['type_id']);
	if($type > 0)
	{
		$wheresql=" WHERE type_id=".$type;
	}
	//�ؼ���
	$key = empty($_GET['key'])?"":iconv("UTF-8","GBK",$_GET['key']);
	if (!empty($key))
	{
		$key=trim($key);
		$wheresql=$wheresql==''?" where  title LIKE '%{$key}%' ":$wheresql." AND  title LIKE '%{$key}%' ";
	}
	$newslistarray=$db->getall("select * from ".table('article').$wheresql." ORDER BY `id` DESC LIMIT {$offset},{$rows}");
	if (!empty($newslistarray) && $offset<=100)
	{
		foreach($newslistarray as $li)
		{
			$li['content']  = htmlspecialchars_decode($li['content']);
			$li['content']=str_replace('&nbsp;','',$li['content']);
			$li['content']=strip_tags($li['content']);
			$url = "news-show.php?id=$li[id]";
			$newslisthtml.='<section class="news-item thisurl box" url="'.$url.'">
							<h2><a href="'.$url.'">'.$li["title"].'</a></h2>
							<div class="news-content clearfix">';
			$img = '';
			if($li['Small_img']!='')
			{
				$img = '<div class="around-img f-right">
									<img src="'.$li["bimg"].'" alt="" width="100" height="74">
								</div>';
			}
			$newslisthtml.=$img;
			$newslisthtml.='<p>'.cut_str($li["content"],100,0,"...").'</p>
							</div>
							<div class="bottom-view clearfix">
								<span class="f-left"><i class="w-icon w-icon-eye"></i>'.$li["click"].'</span>
								<em class="f-right">';
			$addtime = daterange(time(),$li['addtime'],'Y-m-d',"#FF3300").'</em>
							</div>
						</section>';
			$newslisthtml.=$addtime;
		}
		exit($newslisthtml);
	}
	else
	{
		exit('-1');
	}
}
elseif($act == 'ajaxjobfairlist'){
	$jobfairlisthtml="";
	$rows=intval($_GET['rows']);
	$offset=intval($_GET['offset']); 
	$jobfair=$db->getall("select * from ".table('jobfair')." ORDER BY `id` DESC LIMIT {$offset},{$rows}");
	$time=time();
	if (!empty($jobfair) && $offset<=100)
	{
		foreach($jobfair as $key => $li)
		{
			$li['url'] = wap_url_rewrite("jobfair-show",array("id"=>$li['id']),1,$li['subsite_id']);
			//״̬
			if($li['predetermined_status']=="1" && $li['predetermined_start']>$time)
			{
				$li['predetermined_ok'] = 1; // δ��ʼ
			}
			else if ($li['predetermined_status']=="1" && $li['holddate_start']>$time && ($li['predetermined_end']=="0" || $li['predetermined_end']>$time) && ($li['predetermined_web']=="1" || $li['predetermined_tel']=="1"))
			{
				$li['predetermined_ok'] = 2; // Ԥ����
			}
			else
			{
				$li['predetermined_ok'] = 0; // �ѽ���
			}
			$jobfairlisthtml.='<section class="jobfair-item thisurl box" url="'.$li['url'].'"><div class="jobfair-content"><h2><a href="'.$li['url'].'">'.$li['title'].'</a></h2><div class="jobfair-txt"><p>�ٰ�ʱ�䣺'.date("Y-m-d",$li['holddate_start']).' �� '.date("Y-m-d",$li['holddate_end']).'</p><p>��ѯ�绰��'.$li['phone'].'  '.$li['contact'].'</p><p>�ٰ��ַ��'.$li['address'].'<a href="javascript:;"><i class="w-icon w-icon-local-two"></i></a></p></div></div><div class="jobfair-state">';
			if($li['predetermined_ok']==0)
			{
				$jobfairlisthtml.='<i class="w-icon w-icon-booked"></i>';
			}
			elseif($li['predetermined_ok']==1)
			{
				$jobfairlisthtml.='<i class="w-icon w-icon-coming"></i>';
			}
			else
			{
				$jobfairlisthtml.='<i class="w-icon w-icon-booking"></i>';
			}
			$jobfairlisthtml.='</div></section>';
		}
		exit($jobfairlisthtml);
	}
	else
	{
		exit('-1');
	}
}
elseif($act == 'ajaxcomjobslist'){
	$jobslisthtml="";
	$rows=intval($_GET['rows']);
	$offset=intval($_GET['offset']); 
	$companyid=intval($_GET['companyid']); 
	$jobslistarray=$db->getall("select * from ".table('jobs')." WHERE `company_id`={$companyid} ORDER BY `refreshtime` DESC LIMIT {$offset},{$rows}");
	if (!empty($jobslistarray) && $offset<=100)
	{
		foreach($jobslistarray as $li)
		{
			$url = wap_url_rewrite("jobs-show",array("id"=>$li["id"]),1,$li['subsite_id']);
			$jobslisthtml.='<div class="list" url="'.$url.'" id="li-'.$offset.'">
	<h3><a href="'.$url.'">'.$li["jobs_name"].'</a></h3>
	<h5>'.$li["wage_cn"].' '.$li["district_cn"].' </h5>   
	</div>';
	}
		exit($jobslisthtml);
	}
	else
	{
		exit('-1');
	}
}
elseif($act == 'ajaxresumelist'){
	$resumelisthtml="";
	$district = intval($_GET['district'])==0?"":intval($_GET['district']);
	$sdistrict = intval($_GET['sdistrict'])==0?"":intval($_GET['sdistrict']);
	$experience = intval($_GET['experience'])==0?"":intval($_GET['experience']);
	$education = intval($_GET['education'])==0?"":intval($_GET['education']);
	$topclass = intval($_GET['topclass'])==0?"":intval($_GET['topclass']);
	$category = intval($_GET['category'])==0?"":intval($_GET['category']);
	$subclass = intval($_GET['subclass'])==0?"":intval($_GET['subclass']);
	$talent = intval($_GET['talent'])==0?"":intval($_GET['talent']);
	$sex = intval($_GET['sex'])==0?"":intval($_GET['sex']);
	$settr = intval($_GET['settr'])==0?"":intval($_GET['settr']);
	$photo = intval($_GET['photo'])==0?"":intval($_GET['photo']);
	$key = empty($_GET['key'])?"":iconv("UTF-8","GBK",$_GET['key']);
	$jobstable=table('resume_search_rtime');
	if ($talent<>'')
	{
		$wheresql.=" AND `talent`=".$talent." ";
	}
	if($settr<>'')
	{
		$settr_val=strtotime("-{$settr} day");
		$wheresql.=" AND refreshtime>{$settr_val} ";
	}
	if($sex<>'')
	{
		$wheresql.=" AND sex={$sex} ";
	}
	if($photo<>'')
	{
		$wheresql.=" AND photo!=0 ";
	}
	if ($district<>'')
	{
		$wheresql.=" AND `district`=".$district." ";
	}
	if ($sdistrict<>'')
	{
		$wheresql.=" AND `sdistrict`=".$sdistrict." ";
	}
	if ($experience<>'')
	{
		$wheresql.=" AND `experience`=".$experience." ";
	}
	if ($education<>'')
	{
		$wheresql.=" AND `education`=".$education." ";
	}
	if ($topclass<>'' || $category<>'' || $subclass<>'')
	{
		if ($topclass<>'')
		{
			$joinwheresql.=" AND  topclass=".$topclass;
		}
		if ($category<>'')
		{
			$joinwheresql.=" AND  category=".$category;
		}
		if ($subclass<>'')
		{
			$joinwheresql.=" AND  subclass=".$subclass;
		}
		if (!empty($joinwheresql))
		{
		$joinwheresql=" WHERE ".ltrim(ltrim($joinwheresql),'AND');
		}
		$joinsql="  INNER  JOIN  ( SELECT DISTINCT pid FROM ".table('resume_jobs')." {$joinwheresql} ) AS j ON  r.id=j.pid ";
	}
	$orderbysql=" ORDER BY `refreshtime` desc";
	if (!empty($key))
	{
		$key=trim($key);
		if ($_CFG['resumesearch_type']=='1')
		{
			$akey=explode(' ',$key);
			if (count($akey)>1)
			{
			$akey=array_filter($akey);
			$akey=array_slice($akey,0,2);
			$akey=array_map("fulltextpad",$akey);
			$key='+'.implode(' +',$akey);
			$mode=' IN BOOLEAN MODE';
			}
			else
			{
			$key=fulltextpad($key);
			$mode=' ';
			}
			$wheresql.=" AND  MATCH (r.`key`) AGAINST ('{$key}'{$mode}) ";
		}
		else
		{
			$wheresql.=" AND r.likekey LIKE '%{$key}%' ";
		}
		$jobstable=table('resume_search_key');
	}
	$wheresql.=" AND display=1 AND audit=1 ";
	if (!empty($wheresql))
	{
	$wheresql=" WHERE ".ltrim(ltrim($wheresql),'AND');
	}
	$rows=intval($_GET['rows']);
	$offset=intval($_GET['offset']); 
	$idresult = $db->query("SELECT id FROM {$jobstable} as r ".$joinsql.$wheresql.$orderbysql." limit $offset,$rows ");
	while($row = $db->fetch_array($idresult))
	{
	$id[]=$row['id'];
	}
	if (!empty($id))
	{
		$wheresql=" WHERE id IN (".implode(',',$id).") AND display=1 AND audit=1 ";
		$resume = $db->getall("SELECT * FROM ".table('resume').$wheresql.$orderbysql);	
		foreach ($resume as $key => $value) {
			if ($value['display_name']=="2")
			{
				$value['fullname']="N".str_pad($value['id'],7,"0",STR_PAD_LEFT);
				$value['fullname_']=$value['fullname'];		
			}
			elseif($value['display_name']=="3")
			{
				if($value['sex']==1)
				{
					$value['fullname']=cut_str($value['fullname'],1,0,"����");
				}
				elseif($value['sex']==2)
				{
					$value['fullname']=cut_str($value['fullname'],1,0,"Ůʿ");
				}
				$value['fullname_']=$value['fullname'];	
			}
			else
			{
				$value['fullname_']=$value['fullname'];
				$value['fullname']=$value['fullname'];
			}
			$resume[$key]['url'] = wap_url_rewrite("resume-show",array("id"=>$value["id"]),1,$value['subsite_id']);
			$resume[$key]['fullname_']=$value['fullname_'];
			$resume[$key]['fullname']=$value['fullname'];
		}
		
	}
	else
	{
		$resume=array();
	}
	if (!empty($resume) && $offset<=100)
	{
		foreach($resume as $li)
		{
			$url = wap_url_rewrite("resume-show",array("id"=>$li["id"]),1,$li['subsite_id']);
			$resumelisthtml.='<section class="resume-item thisurl box" url="'.$url.'"><div class="r-name-box clearfix"><h4 class="f-left"><a href="'.$url.'">'.$li["fullname"].'</a>';
			if($li['sex']=="1")
			{
				$resumelisthtml.='<i class="w-icon w-icon-male"></i>';
			}
			else
			{
				$resumelisthtml.='<i class="w-icon w-icon-female"></i>';
			}

			if($li['photo_img']!='')
			{
				$resumelisthtml.='<span class="icon-person"></span>';
			}
			if($li['mobile_audit']=='')
			{
				$resumelisthtml.='<span class="icon-phone"></span>';
			}
			$refreshtime_cn=daterange(time(),$li['refreshtime'],'Y-m-d',"#FF3300");
			$resumelisthtml.='</h4><span class="f-right">'.$refreshtime_cn.'</span></div>';

			$resumelisthtml.='<div class="person-info">'.$li['sex_cn'].' | '.$li['education_cn'].' | '.$li['experience_cn'].' | '.$li['major_cn'].'</div><div class="want-job">����ְλ��'.$li['intention_jobs'].'</div></section>';
		}
		exit($resumelisthtml);
	}
	else
	{
		exit('-1');
	}
}
elseif($act == 'ajaxsimplejoblist'){
	$simplelisthtml="";
	$rows=intval($_GET['rows']);
	$offset=intval($_GET['offset']); 
	//�ؼ���
	$key = empty($_GET['key'])?"":iconv("UTF-8","GBK",$_GET['key']);
	if (!empty($key))
	{
		$key=trim($key);
		$wheresql.=" AND  likekey LIKE '%{$key}%' ";
	}
	$simplelistarray=$db->getall("select * from ".table('simple')." ORDER BY `id` ".$wheresql." DESC LIMIT {$offset},{$rows}");
	if (!empty($simplelistarray) && $offset<=100)
	{
		foreach($simplelistarray as $li)
		{
			$url = "simple-job-show.php?id=$li[id]";
			$simplelisthtml .= '<section class="simple-data-item thisurl box" url="'.$url.'">
						<div class="data-main">
							<div class="data-txt">
								<div class="top-info clearfix"><h4 class="f-left"><a href="'.$url.'">'.$li['jobname'].'</a></h4><span class="f-right">';
			$refreshtime_cn=daterange(time(),$li['refreshtime'],'Y-m-d',"#FF3300");
			$simplelisthtml .=$refreshtime_cn.'</span></div>
								<p class="simple-data-detail">'.$li['detailed'].'</p>
							</div>
							<div class="data-contact layout-box box-center-v">
								<div class="box-col contact-local"><i class="w-icon w-icon-local"></i>';
			if($li['sdistrict_cn'])
			{
				$district_cn = $li['district_cn'].'-'.$li['sdistrict_cn'];
			}
			else
			{
				$district_cn = $li['district_cn'];
			}
			$simplelisthtml .=$district_cn.'</div>
								<div class="box-col center-name"><i class="w-icon w-icon-user-two"></i>'.$li['contact'].'</div>
								<div class="box-col contact-phone"><i class="w-icon w-icon-phone"></i>'.$li['tel'].'</div>
							</div>
						</div>
						<div class="bottom"></div>
					</section>';
		}
		exit($simplelisthtml);
	}
	else
	{
		exit('-1');
	}
}
elseif($act == 'ajaxsimpleresumelist'){
	$simplelisthtml="";
	$rows=intval($_GET['rows']);
	$offset=intval($_GET['offset']); 
	//�ؼ���
	$key = empty($_GET['key'])?"":iconv("UTF-8","GBK",$_GET['key']);
	if (!empty($key))
	{
		$key=trim($key);
		$wheresql.=" AND  likekey LIKE '%{$key}%' ";
	}
	$simplelistarray=$db->getall("select * from ".table('simple_resume')." ORDER BY `id` ".$wheresql." DESC LIMIT {$offset},{$rows}");
	if (!empty($simplelistarray) && $offset<=100)
	{
		foreach($simplelistarray as $li)
		{
			$url = "simple-resume-show.php?id=$li[id]";

			$simplelisthtml .= '<section class="simple-data-item thisurl box" url="'.$url.'">
						<div class="data-main">
							<div class="data-txt">
								<div class="top-info clearfix"><h4 class="f-left"><a href="'.$url.'">'.$li['uname'].'<i class="w-icon ';
			if($li['sex']==1)
			{
				$sex = 'w-icon-male';
			}
			else
			{
				$sex = 'w-icon-female';
			}
			$simplelisthtml .= $sex.'"></i></a></h4><span class="f-right">';
			$refreshtime_cn=daterange(time(),$li['refreshtime'],'Y-m-d',"#FF3300");
			$simplelisthtml .=$refreshtime_cn.'</span></div>
								<p class="simple-data-detail">'.$li['age'].'�� | '.$li['experience_cn'].'  | '.$li['category'].'</p>
								<p class="simple-data-detail">'.$li['detailed'].'</p>
							</div>
							<div class="data-contact layout-box box-center-v">
								<div class="box-col contact-local"><i class="w-icon w-icon-phone"></i>'.$li['tel'].'</div>
								<div class="box-col contact-phone s-resume"><i class="w-icon w-icon-local"></i>';
			if($li['sdistrict_cn'])
			{
				$district_cn = $li['district_cn'].'-'.$li['sdistrict_cn'];
			}
			else
			{
				$district_cn = $li['district_cn'];
			}
			$simplelisthtml .=$district_cn.'</div>
							</div>
						</div>
						<div class="bottom"></div>
					</section>';
		}
		exit($simplelisthtml);
	}
	else
	{
		exit('-1');
	}
}
elseif($act == 'jobs_contact')
{
	$id=intval($_GET['id']);
	if ($id>0)
	{
		$jobs_one=$db->getone("select * from ".table("jobs")." where id=$id ");
		$jobs_tmp=$db->getone("select * from ".table("jobs_tmp")." where id=$id ");
		$jobs = empty($jobs_one)?$jobs_tmp:$jobs_one;
		$show=false;
		if($_CFG['showjobcontact_wap']=='0')
		{
		$show=true;
		}
		elseif($_CFG['showjobcontact_wap']=='1')
		{
			if ($_SESSION['uid'] && $_SESSION['username'] && $_SESSION['utype']=='2')
			{
			$show=true;
			}
			else
			{
			$show=false;
			$html='<section class="company-content">
					<h2 class="company-title">��ϵ��ʽ</h2>
					<div class="contact-login">
						<div class="login-tips">���˻�Ա��¼��ſ��Բ鿴��ϵ��ʽ��</div>
						<div class="button-group layout-box">
							<div class="button-box box-col"><a href="login.php"><button class="button blue normal responsive">������¼</button></a></div>
							<div class="button-box box-col"><a href="user_reg.php"><button class="button orange normal responsive">ע���˺�</button></a></div>
						</div>
					</div>
				</section>';
			}
		}
		elseif($_CFG['showjobcontact_wap']=='2')
		{
			if ($_SESSION['uid'] && $_SESSION['username'] && $_SESSION['utype']=='2')
			{
				$val=$db->getone("select uid from ".table('resume')." where uid='{$_SESSION['uid']}' LIMIT 1");
			 	if (!empty($val))
				{
				$show=true;
				}
				else
				{
				$show=false;
				$html='<section class="company-content">
					<h2 class="company-title">��ϵ��ʽ</h2>
					<div class="contact-login">
						<div class="login-tips">��������Ч������ſ��Բ鿴��ϵ��ʽ��</div>
					</div>
				</section>';
				}
			}
			else
			{
			$show=false;
			$html='<section class="company-content">
					<h2 class="company-title">��ϵ��ʽ</h2>
					<div class="contact-login">
						<div class="login-tips">���˻�Ա��¼��ſ��Բ鿴��ϵ��ʽ��</div>
						<div class="button-group layout-box">
							<div class="button-box box-col"><a href="login.php"><button class="button blue normal responsive">������¼</button></a></div>
							<div class="button-box box-col"><a href="user_reg.php"><button class="button orange normal responsive">ע���˺�</button></a></div>
						</div>
					</div>
				</section>';
			}
		}
		if($_SESSION['uid'] && $_SESSION['username'] && $_SESSION['utype']=='1' && $show==false)
		{
			if($jobs['uid']==$_SESSION['uid'])
			{
				$show=true;
			}
			else
			{
				$show=false;
			}
		}
		if ($show)
		{
			$sql = "select * from ".table('jobs_contact')." where pid='{$id}' LIMIT 1";
			$val=$db->getone($sql);
			if ($_CFG['contact_img_job']=='2')
			{
				$user = $db->getone("select m.username uname from ".table("members")." as m left join ".table("jobs")." as j on m.uid=j.uid where j.id=$id ");
				$hashstr=substr(md5($user['uname']),8,16);
				$token=md5($val['contact'].$id.$val['telephone']);
				$html='<section class="company-content">
					<h2 class="company-title">��ϵ��ʽ</h2>
					<div class="company-contact">';
				$contact=$val['contact_show']=='1'?"<div class='contact-item clearfix'><i class='w-icon w-icon-user-two f-left'></i><p><img src=\"{$_CFG['site_domain']}{$_CFG['site_dir']}plus/contact_img.php?act=jobs_contact&type=1&id={$id}&token={$token}\"  border=\"0\" align=\"absmiddle\"/></p></div>":"<div class='contact-item clearfix'><i class='w-icon w-icon-user-two f-left'></i><p>��ҵ���ò����⹫��</p></div>";
				$telephone=$val['telephone_show']=='1'?"<div class='contact-item clearfix'><i class='w-icon w-icon-phone f-left'></i><p><img src=\"{$_CFG['site_domain']}{$_CFG['site_dir']}plus/contact_img.php?act=jobs_contact&type=2&id={$id}&token={$token}&hashstr={$hashstr}\"  border=\"0\" align=\"absmiddle\"/></p></div>":"<div class='contact-item clearfix'><i class='w-icon w-icon-phone f-left'></i><p>��ҵ���ò����⹫��</p></div>";
				if($val['landline_tel'] != '0-0-0')
				{
					$landline_tel="<div class='contact-item clearfix'><i class='w-icon w-icon-phone f-left'></i><p><img src=\"{$_CFG['site_domain']}{$_CFG['site_dir']}plus/contact_img.php?act=jobs_contact&type=6&id={$id}&token={$token}&hashstr={$hashstr}\"  border=\"0\" align=\"absmiddle\"/></p></div>";
				}
				$email=$val['email_show']=='1'?"<div class='contact-item clearfix'><i class='w-icon w-icon-mail f-left'></i><p><img src=\"{$_CFG['site_domain']}{$_CFG['site_dir']}plus/contact_img.php?act=jobs_contact&type=3&id={$id}&token={$token}\"  border=\"0\" align=\"absmiddle\"/></p></div>":"<div class='contact-item clearfix'><i class='w-icon w-icon-mail f-left'></i><p>��ҵ���ò����⹫��</p></div>";
				$address="<div class='contact-item clearfix'><i class='w-icon w-icon-local-two f-left'></i><p><img src=\"{$_CFG['site_domain']}{$_CFG['site_dir']}plus/contact_img.php?act=jobs_contact&type=4&id={$id}&token={$token}\"  border=\"0\" align=\"absmiddle\"/>&nbsp;</p></div>
					</div>
				</section>";
				$html.=$contact.$telephone.$landline_tel.$email.$address;
			}
			else
			{
				//���������зָ�
				$telarray = explode('-',$val['landline_tel']);
				if(intval($telarray[0]) > 0)
				{
					$landline_tel = $telarray[0];
				}
				if(intval($telarray[1]) > 0)
				{
					$landline_tel = empty($landline_tel)?$telarray[1]:$landline_tel."-".$telarray[1];
				}
				if(intval($telarray[2]) > 0)
				{
					$landline_tel = empty($landline_tel)?$telarray[2]:$landline_tel."-".$telarray[2];
				}
				$html='<section class="company-content">
					<h2 class="company-title">��ϵ��ʽ</h2>
					<div class="company-contact">';
				$contact=$val['contact_show']=='1'?"<div class='contact-item clearfix'><i class='w-icon w-icon-user-two f-left'></i><p>{$val['contact']}</p></div>":"<div class='contact-item clearfix'><i class='w-icon w-icon-user-two f-left'></i><p>��ҵ���ò����⹫��</p></div>";
				$telephone=$val['telephone_show']=='1'?"<div class='contact-item clearfix'><i class='w-icon w-icon-phone f-left'></i><p>{$val['telephone']}</p></div>":"<div class='contact-item clearfix'><i class='w-icon w-icon-phone f-left'></i><p>��ҵ���ò����⹫��</p></div>";
				if(!empty($landline_tel))
				{
					$landline_tel = "<div class='contact-item clearfix'><i class='w-icon w-icon-phone f-left'></i><p>".$landline_tel."</p></div>";
				}
				$email=$val['email_show']=='1'?"<div class='contact-item clearfix'><i class='w-icon w-icon-mail f-left'></i><p>{$val['email']}</p></div>":"<div class='contact-item clearfix'><i class='w-icon w-icon-mail f-left'></i><p>��ҵ���ò����⹫��</p></div>";
				$address="<div class='contact-item clearfix'><i class='w-icon w-icon-local-two f-left'></i><p>{$val['address']}&nbsp;</p></div>
					</div>
				</section>";
				$html.=$contact.$telephone.$landline_tel.$email.$address;
			}
			exit($html);
		}
		else
		{		
		exit($html);
		}
	}
}
elseif($act == 'company_contact')
{
	
	$id=intval($_GET['id']);
	$company_profile = $db->getone("SELECT uid FROM ".table('company_profile')." WHERE id=$id");
	if ($id>0)
	{
		$show=false;
		if($_CFG['showjobcontact_wap']=='0')
		{
		$show=true;
		}
		elseif($_CFG['showjobcontact_wap']=='1')
		{
			if ($_SESSION['uid'] && $_SESSION['username'] && $_SESSION['utype']=='2')
			{
			$show=true;
			}
			else
			{
			$show=false;
			$html='<section class="company-content">
						<h2 class="company-title">��ϵ��ʽ</h2>
						<div class="contact-login">
							<div class="login-tips">����û�е�¼����¼��ſ��Բ鿴��ϵ��ʽ��</div>
							<div class="button-group layout-box">
								<div class="button-box box-col"><a href="login.php"><button class="button blue normal responsive">������¼</button></a></div>
								<div class="button-box box-col"><a href="user_reg.php"><button class="button orange normal responsive">ע���˺�</button></a></div>
							</div>
						</div>
					</section>';
			}
		}
		elseif($_CFG['showjobcontact_wap']=='2')
		{
			if ($_SESSION['uid'] && $_SESSION['username'] && $_SESSION['utype']=='2')
			{
				$val=$db->getone("select uid from ".table('resume')." where uid='{$_SESSION['uid']}' LIMIT 1");
			 	if (!empty($val))
				{
				$show=true;
				}
				else
				{
				$show=false;

				$html='<section class="company-content">
						<h2 class="company-title">��ϵ��ʽ</h2>
						<div class="contact-login">
							<div class="login-tips">��������Ч������ſ��Բ鿴��ϵ��ʽ��</div>
						</div>
					</section>';
				}
			}
			else
			{
			$show=false;
			$html='<section class="company-content">
						<h2 class="company-title">��ϵ��ʽ</h2>
						<div class="contact-login">
							<div class="login-tips">����û�е�¼����¼��ſ��Բ鿴��ϵ��ʽ��</div>
							<div class="button-group layout-box">
								<div class="button-box box-col"><a href="login.php"><button class="button blue normal responsive">������¼</button></a></div>
								<div class="button-box box-col"><a href="user_reg.php"><button class="button orange normal responsive">ע���˺�</button></a></div>
							</div>
						</div>
					</section>';
			}
		}
		if($_SESSION['uid'] && $_SESSION['username'] && $_SESSION['utype']=='1' && $show==false)
		{
			if($company_profile['uid']==$_SESSION['uid'])
			{
				$show=true;
			}
			else
			{
				$show=false;
			}
		}
		if ($show)
		{
			$sql = "select contact,contact_show,telephone,landline_tel,telephone_show,email,email_show,address,address_show,website FROM ".table('company_profile')." where id='{$id}' LIMIT 1";
			$val=$db->getone($sql);
			if ($_CFG['contact_img_com']=='2')
			{
				$token=md5($val['contact'].$id.$val['telephone']);
				$html='<section class="company-content">
						<h2 class="company-title">��ϵ��ʽ</h2>
						<div class="company-contact">';
				$contact=$val['contact_show']=='1'?"<div class='contact-item clearfix'><i class='w-icon w-icon-user-two f-left'></i><p><img src=\"{$_CFG['site_domain']}{$_CFG['site_dir']}plus/contact_img.php?act=company_contact&type=1&id={$id}&token={$token}\"  border=\"0\" align=\"absmiddle\"/></p></div>":"<div class='contact-item clearfix'><i class='w-icon w-icon-user-two f-left'></i><p>��ҵ���ò����⹫��</p></div>";
				$telephone=$val['telephone_show']=='1'?"<div class='contact-item clearfix'><i class='w-icon w-icon-phone f-left'></i><p><img src=\"{$_CFG['site_domain']}{$_CFG['site_dir']}plus/contact_img.php?act=company_contact&type=2&id={$id}&token={$token}\"  border=\"0\" align=\"absmiddle\"/></p></div>":"<div class='contact-item clearfix'><i class='w-icon w-icon-phone f-left'></i><p>��ҵ���ò����⹫��</p></div>";
				if($val['landline_tel'] != '0-0-0')
				{
					$landline_tel="<div class='contact-item clearfix'><i class='w-icon w-icon-phone f-left'></i><img src=\"{$_CFG['site_domain']}{$_CFG['site_dir']}plus/contact_img.php?act=company_contact&type=5&id={$id}&token={$token}&hashstr={$hashstr}\"  border=\"0\" align=\"absmiddle\"/></p></div>";
				}
				$email=$val['email_show']=='1'?"<div class='contact-item clearfix'><i class='w-icon w-icon-mail f-left'></i><p><img src=\"{$_CFG['site_domain']}{$_CFG['site_dir']}plus/contact_img.php?act=company_contact&type=3&id={$id}&token={$token}\"  border=\"0\" align=\"absmiddle\"/></p></div>":"<div class='contact-item clearfix'><i class='w-icon w-icon-mail f-left'></i><p>��ҵ���ò����⹫��</p></div>";		
				$address="<div class='contact-item clearfix'><i class='w-icon w-icon-local-two f-left'></i><p><img src=\"{$_CFG['site_domain']}{$_CFG['site_dir']}plus/contact_img.php?act=company_contact&type=4&id={$id}&token={$token}\"  border=\"0\" align=\"absmiddle\"/>&nbsp;</p></div></div>
				</section>";
				$html.=$contact.$telephone.$landline_tel.$email.$address;
			}
			else
			{
				//���������зָ�
				$telarray = explode('-',$val['landline_tel']);
				if(intval($telarray[0]) > 0)
				{
					$landline_tel = $telarray[0];
				}
				if(intval($telarray[1]) > 0)
				{
					$landline_tel = empty($landline_tel)?$telarray[1]:$landline_tel."-".$telarray[1];
				}
				if(intval($telarray[2]) > 0)
				{
					$landline_tel = empty($landline_tel)?$telarray[2]:$landline_tel."-".$telarray[2];
				}
				$html='<section class="company-content">
					<h2 class="company-title">��ϵ��ʽ</h2>
					<div class="company-contact">';
				$contact=$val['contact_show']=='1'?"<div class='contact-item clearfix'><i class='w-icon w-icon-user-two f-left'></i><p>{$val['contact']}</p></div>":"<div class='contact-item clearfix'><i class='w-icon w-icon-user-two f-left'></i><p>��ҵ���ò����⹫��</p></div>";
				$telephone=$val['telephone_show']=='1'?"<div class='contact-item clearfix'><i class='w-icon w-icon-phone f-left'></i><p>{$val['telephone']}</p></div>":"<div class='contact-item clearfix'><i class='w-icon w-icon-phone f-left'></i><p>��ҵ���ò����⹫��</p></div>";
				if(!empty($landline_tel))
				{
					$landline_tel = "<div class='contact-item clearfix'><i class='w-icon w-icon-phone f-left'></i><p>".$landline_tel."</p></div>";
				}
				$email=$val['email_show']=='1'?"<div class='contact-item clearfix'><i class='w-icon w-icon-mail f-left'></i><p>{$val['email']}</p></div>":"<div class='contact-item clearfix'><i class='w-icon w-icon-mail f-left'></i><p>��ҵ���ò����⹫��</p></div>";		
				$address="<div class='contact-item clearfix'><i class='w-icon w-icon-local-two f-left'></i><p>{$val['address']}&nbsp;</p></div></div>
				</section>";
				$html.=$contact.$telephone.$landline_tel.$email.$address;
			}
			exit($html);
		}
		else
		{		
		exit($html);
		}
	}	
}
//������ϵ��ʽ
elseif($act == 'resume_contact')
{   
	$id=intval($_GET['id']);
	$show=false;
	$resume_sql="select * from ".table("resume")." where id=$id ";
	$resume_one=$db->getone($resume_sql);
	if($_SESSION["utype"]==2 && $_SESSION["uid"]==$resume_one["uid"]){
		$show=true;
	}
	if($_SESSION['uid'] && $_SESSION['username'] && $_SESSION['utype']=='1' && $_CFG['showapplycontact']=='1'){
		$has = $db->getone("select 1 from ".table('personal_jobs_apply')." where company_uid=".intval($_SESSION['uid'])." and resume_id=".$id);
		if($has){
			$show = true;
		}
	}
	if($show==false)
	{
		if($_CFG['showresumecontact_wap']=='0')
		{
			$show=true;
		}
		elseif($_CFG['showresumecontact_wap']=='1')
		{
			if ($_SESSION['uid'] && $_SESSION['username'] && $_SESSION['utype']=='1')
			{
				$show=true;
			}
			else
			{
				$show=false;
				$html='<article class="resumeconlist resumeconlistmb">
							<h2 class="title">��ϵ��ʽ</h2>
							<div class="con">
								<div class="login-tips">����û�е�¼����¼��ſ��Բ鿴��ϵ��ʽ��</div>
								<div class="button-group layout-box">
									<div class="button-box box-col"><a href="login.php"><button class="button blue normal responsive">������¼</button></a></div>
									<div class="button-box box-col"><a href="user_reg.php"><button class="button orange normal responsive">ע���˺�</button></a></div>
								</div>
							</div>
						</article>';
			}
		}
		elseif($_CFG['showresumecontact_wap']=='2')
		{
			if ($_SESSION['uid'] && $_SESSION['username'] && $_SESSION['utype']=='1')
			{
				$sql = "select did from ".table('company_down_resume')." WHERE company_uid = {$_SESSION['uid']} AND resume_id='{$id}' LIMIT 1";
				$info=$db->getone($sql);
			 	if (!empty($info))
				{
					$show=true;
				}
				else
				{
					$show=false;
					$html='<article class="resumeconlist resumeconlistmb">
								<h2 class="title">��ϵ��ʽ</h2>
								<div class="con">
									<div class="login-tips">��ҵ��Ա���ظü����ſ��Բ鿴��ϵ��ʽ��</div>
								</div>
							</article>
							<article class="invitecollectresume flex-box">
								<div class="left"><div class="invite" id="down_resume">���ؼ���</div></div>
								<div class="right flex2"><div class="collect"  id="collect_resume"></div></div>
							</article>';
				}
			}
			else
			{
				$show=false;
				$html='<article class="resumeconlist resumeconlistmb">
							<h2 class="title">��ϵ��ʽ</h2>
							<div class="con">
								<div class="login-tips">����û�е�¼����¼��ſ��Բ鿴��ϵ��ʽ��</div>
								<div class="button-group layout-box">
									<div class="button-box box-col"><a href="login.php"><button class="button blue normal responsive">������¼</button></a></div>
									<div class="button-box box-col"><a href="user_reg.php"><button class="button orange normal responsive">ע���˺�</button></a></div>
								</div>
							</div>
						</article>';
			}
		}
	}
	if ($show)
	{
		$tb1=$db->getone("select fullname,telephone,email,residence from ".table('resume')." WHERE  id='{$id}'  LIMIT 1");
		$val=$tb1;
		//���������״̬
		if($_SESSION['uid'] && $_SESSION['utype']==1)
		{
			$resume_state=$db->getone("select resume_state from ".table("company_label_resume")." where resume_id=$id and uid=$_SESSION[uid]");
			switch ($resume_state['resume_state']) {
				case 1:
					$state_htm="<article class=\"resumeconlist\"><div class=\"con\"><div class=\"list flex-box\"><div class=\"reason\">��δ��ͨ</div><div class=\"reason\">����</div><div class=\"reason no\">����</div><div class=\"reason\">������</div></div></div></article>";
					break;
				case 2:
					$state_htm="<article class=\"resumeconlist\"><div class=\"con\"><div class=\"list flex-box\"><div class=\"reason\">��δ��ͨ</div><div class=\"reason\">����</div><div class=\"reason\">����</div><div class=\"reason no\">������</div></div></div></article>";
					break;
				case 3:
					$state_htm="<article class=\"resumeconlist\"><div class=\"con\"><div class=\"list flex-box\"><div class=\"reason\">��δ��ͨ</div><div class=\"reason no\">����</div><div class=\"reason\">����</div><div class=\"reason\">������</div></div></div></article>";
					break;
				case 4:
					$state_htm="<article class=\"resumeconlist\"><div class=\"con\"><div class=\"list flex-box\"><div class=\"reason no\">��δ��ͨ</div><div class=\"reason\">����</div><div class=\"reason\">����</div><div class=\"reason\">������</div></div></div></article>";
					break;
			}
		}

		if ($_CFG['contact_img_resume']=='2')
		{
			$token=md5($val['fullname'].$id.$val['telephone']);
			$html = "<article class=\"resumeconlist\">
					<section class=\"title\">��ϵ��ʽ</section>
					<div class=\"con\">
						<div class=\"list lx p flex-box\"><img src=\"{$_CFG['site_domain']}{$_CFG['site_dir']}plus/contact_img.php?act=resume_contact&type=1&id={$id}&token={$token}\"  border=\"0\" align=\"absmiddle\"/></div>
						<div class=\"list lx m flex-box\"><img src=\"{$_CFG['site_domain']}{$_CFG['site_dir']}plus/contact_img.php?act=resume_contact&type=2&id={$id}&token={$token}\"  border=\"0\" align=\"absmiddle\"/><a href=\"tel:{$val['telephone']}\"><div class=\"call\"></div></a></div>
						<div class=\"list lx e flex-box\"><img src=\"{$_CFG['site_domain']}{$_CFG['site_dir']}plus/contact_img.php?act=resume_contact&type=3&id={$id}&token={$token}\"  border=\"0\" align=\"absmiddle\"/></div>
						<div class=\"list lx d flex-box\"><img src=\"{$_CFG['site_domain']}{$_CFG['site_dir']}plus/contact_img.php?act=resume_contact&type=4&id={$id}&token={$token}\"  border=\"0\" align=\"absmiddle\"/></div>
					</div>
				</article>
				<article class=\"invitecollectresume flex-box\">
					<div class=\"left\"><div class=\"invite\" id=\"invite\">��������</div></div>
					<div class=\"right flex2\"><div class=\"collect\" id=\"collect_resume\"></div></div>
				</article>";
			$html = $state_htm.$html;
		}
		else
		{
			$html='<article class="resumeconlist">
					<section class="title">��ϵ��ʽ</section>
					<div class="con">
						<div class="list lx p flex-box">'.$val['fullname'].'</div>
						<div class="list lx m flex-box">'.$val['telephone'].'<a href="tel:'.$val['telephone'].'" class="call"></a></div>
						<div class="list lx e flex-box">'.$val['email'].'</div>
						<div class="list lx d flex-box">'.$val['residence'].'</div>
					</div>
				</article>
				<article class="invitecollectresume flex-box">
					<div class="left"><div class="invite" id="invite">��������</div></div>
					<div class="right flex2"><div class="collect" id="collect_resume"></div></div>
				</article>';
			$html = $state_htm.$html;
		}
		exit($html);
	}
	else
	{		
		exit($html);
	}
}
// ajax ��ȡ��������
elseif($act=="ajax_interview_list")
{
	$interviewhtml="";
	//�õ�ҳ�洫��������ʾ����
	$rows=intval($_GET['rows']);
	//�õ�ҳ�洫������  ���һ����¼��didֵ
	$offset=intval($_GET['offset']); 
	$interviewarray=$db->getall("select * from ".table('company_interview')." where  resume_uid=$_SESSION[uid] order by interview_addtime desc  LIMIT {$offset},{$rows}");
	if (!empty($interviewarray) && $offset<=100)
	{
		foreach($interviewarray as $list)
		{
			$job_url = wap_url_rewrite("jobs-show",array("id"=>$list['jobs_id']));
			if ($list['personal_look'] != 1) {
				$interviewhtml.='<article class="classifylist box ed" url="../'.$job_url.'">';
			} else {
				$interviewhtml.='<article class="classifylist box">';
			}
			$interviewhtml.='<section class="title flex-box"><div class="name">'.$list['company_name'].'</div><div class="status flex2">';
			if ($list['personal_look'] == 1) {
				$interviewhtml.='<span>δ�鿴</span>';
			}
			$interviewhtml.='</div></section><section class="txt flex-box"><div class="left">ְλ��'.$list['jobs_name'].'</div><div class="right flex2">����ʱ�䣺'.date('Y-m-d',$list['addtime']).'</div></section><section class="content flex-box">���ݣ�';
			if ($list['notes'] != "") {
				$interviewhtml.= $list['notes'];
			} else {
				$interviewhtml.='������֪ͨ��';
			}
			$interviewhtml.='</section></article>';
		}
		exit($interviewhtml);
	}
	else
	{
		exit('-1');
	}
}
// ajax ��ȡ�ղ�ְλ
elseif($act=="ajax_favorites_list")
{
	$favoriteshtml="";
	$rows=intval($_GET['rows']);
	$offset=intval($_GET['offset']);
	$favoritesarry=$db->getall("select f.*,j.companyname,j.wage_cn from ".table("personal_favorites")." as f left join ".table("jobs")." as j on f.jobs_id=j.id where f.personal_uid=$_SESSION[uid] order by f.did desc limit $offset,$rows");
	if (!empty($favoritesarry) && $offset<=100)
	{
		foreach($favoritesarry as $list)
		{
			$job_url = wap_url_rewrite("jobs-show",array("id"=>$list['jobs_id']));
			$favoriteshtml.='<article class="classifylist nomb box thisurl" url="../'.$job_url.'"><section class="title flex-box"><div class="chk chkabaout"><input type="checkbox" id[]="'.$list['did'].'" /><label for="" class="check-box"></label></div><div class="name">'.$list['jobs_name'].'</div><div class="status flex2"><span class="money">��'.$list['wage_cn'].'</span></div></section><section class="txt flex-box"><div class="chk chkabaout"></div><div class="left">'.$list['companyname'].'</div><div class="right flex2">'.date('Y-m-d',$list['addtime']).'</div></section></article>';
		}
		exit($favoriteshtml);
	}
	else
	{
		exit('-1');
	}
}
// ajax ��ȡ ����ְλ
elseif($act=="ajax_apply_list")
{
	$favoriteshtml="";
	$rows=intval($_GET['rows']);
	$offset=intval($_GET['offset']);
	$favoritesarry=$db->getall("select a.*,j.wage_cn from ".table("personal_jobs_apply")." as a left join ".table("jobs")." as j on a.jobs_id=j.id where a.personal_uid=$_SESSION[uid]  order by a.apply_addtime desc limit $offset,$rows");
	if (!empty($favoritesarry) && $offset<=100)
	{
		foreach($favoritesarry as $list)
		{
			$job_url = wap_url_rewrite("jobs-show",array("id"=>$list['jobs_id']));
			//��״̬
			if($list['personal_look']=='1')
			{
				$list['reply_status'] = "��ҵδ�鿴";
			}
			else
			{
				if($list['is_reply']=='0')
				{
					$list['reply_status'] = "������";
				}
				elseif($list['is_reply']=='1')
				{
					$list['reply_status'] = "�ϸ�";
				}
				elseif($list['is_reply']=='2')
				{
					$list['reply_status'] = "���ϸ�";
				}
				elseif($list['is_reply']=='3')
				{
					$list['reply_status'] = "����";
				}
				elseif($list['is_reply']=='4')
				{
					$list['reply_status'] = "δ��ͨ";
				}
			}
			$favoriteshtml.='<article class="classifylist box thisurl" url="../'.$job_url.'"><section class="title flex-box"><div class="chk chkabaout"><input type="checkbox" id[]="'.$list['jobs_id'].'" /><label for="" class="check-box"></label></div><div class="name">'.$list['jobs_name'].'</div><div class="status flex2"><span>'.$list['reply_status'].'</span></div></section><section class="txt flex-box"><div class="chk chkabaout"></div><div class="left">'.$list['company_name'].'</div><div class="right flex2">'.date('Y-m-d',$list['apply_addtime']).'</div></section></article>';
		}
		exit($favoriteshtml);
	}
	else
	{
		exit('-1');
	}
}
// ��������
elseif ($act == 'invited_add')
{

	$smarty->cache = false;
	$resume=resume_one($_POST["resume_id"]);
	$jobs=jobs_one($_POST["jobs_id"]);
	if($_SESSION['utype']!=1){
		exit("��ҵ��Ա���¼����������");
	}

	if (check_interview($_POST["resume_id"],$_POST["jobs_id"],$_SESSION['uid']))
	{
	exit("repeat");
	}
	$addarr['resume_id']=$resume['id'];
	$addarr['resume_addtime']=$resume['addtime'];
	if ($resume['display_name']=="2")
	{
	$addarr['resume_name']="N".str_pad($resume['id'],7,"0",STR_PAD_LEFT);	
	}
	elseif ($resume['display_name']=="3")
	{
		if($resume['sex']==1)
		{
			$addarr['resume_name']=cut_str($resume['fullname'],1,0,"����");
		}
		elseif($resume['sex']==2)
		{
			$addarr['resume_name']=cut_str($resume['fullname'],1,0,"Ůʿ");
		}
	}
	else
	{
	$addarr['resume_name']=$resume['fullname'];
	}
	$addarr['resume_uid']=$resume['uid'];
	$addarr['company_id']=$jobs['company_id'];
	$addarr['company_addtime']=$jobs['company_addtime'];
	$addarr['company_name']=$jobs['companyname'];
	$addarr['company_uid']=$_SESSION['uid'];
	$addarr['jobs_id']=$jobs['id'];
	$addarr['jobs_name']=$jobs['jobs_name'];
	$addarr['jobs_addtime']=$jobs['addtime'];	

	$addarr['personal_look']= 1;
	$addarr['interview_addtime']=time();
	$user=get_user_info($resume['uid']);
	$resume_user=get_user_info($resume['uid']);
	//�ײ�ģʽ
	if ($_CFG['operation_mode']=="2")
	{
		$db->inserttable(table('company_interview'),$addarr);
		if ($resume['talent_']=='2')
		{
			action_user_setmeal($_SESSION['uid'],"interview_senior");
			$setmeal=get_user_setmeal($_SESSION['uid']);
			write_memberslog($_SESSION['uid'],1,9002,$_SESSION['username'],"������ {$resume_user['username']} ���ԣ�����������߼��˲� {$setmeal['interview_senior']} ��",2,1007,"����߼��˲�����","1","{$setmeal['interview_senior']}");
			write_memberslog($_SESSION['uid'],1,6001,$_SESSION['username'],"������ {$resume_user['username']} ����");
		}
		else
		{				 
			action_user_setmeal($_SESSION['uid'],"interview_ordinary");
			$setmeal=get_user_setmeal($_SESSION['uid']);
			write_memberslog($_SESSION['uid'],1,9002,$_SESSION['username'],"������ {$resume_user['username']} ���ԣ�������������ͨ�˲� {$setmeal['interview_ordinary']} ��",2,1006,"������ͨ�˲�����","1","{$setmeal['interview_ordinary']}");
			write_memberslog($_SESSION['uid'],1,6001,$_SESSION['username'],"������ {$resume_user['username']} ����");				
		}			
	}
	//����ģʽ	 
	elseif($_CFG['operation_mode']=="1")
	{
		$mypoints=get_user_points($_SESSION['uid']);
		$points_rule=get_cache('points_rule');
		$points=$resume['talent_']=='2'?$points_rule['interview_invite_advanced']['value']:$points_rule['interview_invite']['value'];
		$ptype=$resume['talent_']=='2'?$points_rule['interview_invite_advanced']['type']:$points_rule['interview_invite']['type'];
		if  ($mypoints<$points)
		{
			exit("���Ļ��ֲ���");
		}
		$db->inserttable(table('company_interview'),$addarr);
		if ($points>0)
		{
			report_deal($_SESSION['uid'],$ptype,$points);
			$user_points=get_user_points($_SESSION['uid']);
			$operator=$ptype=="1"?"+":"-";
			if($resume['talent_']=='2'){
				write_memberslog($_SESSION['uid'],1,9001,$_SESSION['username'],"���� {$resume_user['username']} ����({$operator}{$points}),(ʣ��:{$user_points})",1,1007,"����߼��˲�����","{$operator}{$points}","{$user_points}");
			}else{
				write_memberslog($_SESSION['uid'],1,9001,$_SESSION['username'],"���� {$resume_user['username']} ����({$operator}{$points}),(ʣ��:{$user_points})",1,1006,"������ͨ�˲�����","{$operator}{$points}","{$user_points}");
			}
			write_memberslog($_SESSION['uid'],1,6001,$_SESSION['username'],"���� {$resume_user['username']} ����");
		}		
	}
	//���ģʽ
	elseif($_CFG['operation_mode']=="3")
	{
		//�鿴���Ļ���
		$mypoints=get_user_points($_SESSION['uid']);
		$points_rule=get_cache('points_rule');
		//�ȿ��û�Ա�Ƿ��з���ͨ����˵ĵ�ְλ
		$user_jobs=get_auditjobs($_SESSION['uid']);
		if (count($user_jobs)==0)
		{
			exit("����ʧ�ܣ���û�з�����Ƹ��Ϣ������Ϣû�����ͨ��!");
		}
		//Ȼ�������ײ��Ƿ�����
		$setmeal = get_user_setmeal(intval($_SESSION['uid']));
		if (empty($setmeal) || ($setmeal['endtime']<time() && $setmeal['endtime']<>"0"))
		{
			exit("���ķ����ѵ��� !");
		}
		elseif($resume['talent_']=='2' && $setmeal['interview_senior']<=0)
		{
			//��̨���� ���û�������
			if ($_CFG['setmeal_to_points']=="1")
			{
				$points=$points_rule['interview_invite_advanced']['value'];
				$ptype=$points_rule['interview_invite_advanced']['type'];
				//������
				if(intval($ptype) == 2 && ($mypoints < $points))
				{
					exit("���������Դ����Ѿ��������� , ���һ��ֲ��� !");
				}
				$is_points = '1';
			}
			else
			{
				exit("������߼��˲����Դ����Ѿ����������ơ�");
			}
		}
		elseif ($resume['talent_']=='1' && $setmeal['interview_ordinary']<=0)
		{
			//��̨���� ���û�������
			if ($_CFG['setmeal_to_points']=="1")
			{
				$points=$points_rule['interview_invite']['value'];
				$ptype=$points_rule['interview_invite']['type'];
				//������
				if(intval($ptype) == 2 && ($mypoints < $points))
				{
					exit("���������Դ����Ѿ��������� , ���һ��ֲ��� !");
				}
				$is_points = '1';
			}
			else
			{
				exit("������߼��˲����Դ����Ѿ����������ơ�");
			}
		}
		//д��־�Լ������ݿ�
		$db->inserttable(table('company_interview'),$addarr);
		//$is_pointsΪ�� : ˵�����ײͲ�����   ��Ϊ�� :  ˵�����û��ֲ�����
		if(empty($is_points))
		{
			$resume_talent = $resume['talent_']=='1'?'interview_ordinary':'interview_senior';
			action_user_setmeal($_SESSION['uid'],$resume_talent);
			$setmeal=get_user_setmeal($_SESSION['uid']);
			$messgae = $resume['talent_']=='1'?"������ {$resume_user['username']} ���ԣ�������������ͨ�˲� {$setmeal['interview_ordinary']} ��":"������ {$resume_user['username']} ���ԣ�����������߼��˲� {$setmeal['interview_senior']} ��";
			$message_type = $resume['talent_']=='1'?"������ͨ�˲�����":"����߼��˲�����";
			write_memberslog($_SESSION['uid'],1,9002,$_SESSION['username'],$messgae,2,1007,$message_type,"1","{$setmeal['interview_senior']}");
			write_memberslog($_SESSION['uid'],1,6001,$_SESSION['username'],"������ {$resume_user['username']} ����");
		}
		else
		{
			if($points > 0)
			{
				report_deal($_SESSION['uid'],$ptype,$points);
				$user_points=get_user_points($_SESSION['uid']);
				$operator=$ptype=="1"?"+":"-";
				if($resume['talent_']=='2'){
					write_memberslog($_SESSION['uid'],1,9001,$_SESSION['username'],"���� {$resume_user['username']} ����({$operator}{$points}),(ʣ��:{$user_points})",1,1007,"����߼��˲�����","{$operator}{$points}","{$user_points}");
				}else{
					write_memberslog($_SESSION['uid'],1,9001,$_SESSION['username'],"���� {$resume_user['username']} ����({$operator}{$points}),(ʣ��:{$user_points})",1,1006,"������ͨ�˲�����","{$operator}{$points}","{$user_points}");
				}
				write_memberslog($_SESSION['uid'],1,6001,$_SESSION['username'],"���� {$resume_user['username']} ����");
			}
		}

	}
	$mailconfig=get_cache('mailconfig');
	$weixinconfig=get_cache('weixin_config');
	$sms=get_cache('sms_config');
	if ($mailconfig['set_invite']=="1" && $resume['email_notify']=='1' && $resume_user['email_audit']=="1")
	{
		dfopen($_CFG['site_domain'].$_CFG['site_dir']."plus/asyn_mail.php?uid={$_SESSION['uid']}&key=".asyn_userkey($_SESSION['uid'])."&act=set_invite&companyname={$jobs['companyname']}&email={$resume_user['email']}");				
	}

	
	//sms
	if ($sms['open']=="1"  && $sms['set_invite']=="1"  && $resume_user['mobile_audit']=="1")
	{
		dfopen($_CFG['site_domain'].$_CFG['site_dir']."plus/asyn_sms.php?uid={$_SESSION['uid']}&key=".asyn_userkey($_SESSION['uid'])."&act=set_invite&companyname={$jobs['companyname']}&mobile={$resume_user['mobile']}");		
	}
	//վ����
	if($pms_notice=='1'){
		$user=$db->getone("select username from ".table('members')." where uid ={$resume['uid']} limit 1");
		$jobs_url=url_rewrite('QS_jobsshow',array('id'=>$jobs['id']));
		$company_url=url_rewrite('QS_companyshow',array('id'=>$jobs['company_id']));
		$message=$jobs['companyname']."�������μӹ�˾���ԣ�����ְλ��<a href=\"{$jobs_url}\" target=\"_blank\"> {$jobs['jobs_name']} </a>��<a href=\"{$company_url}\" target=\"_blank\">����鿴��˾����</a>";
		write_pmsnotice($resume['uid'],$user['username'],$message);
	}
	
	//΢��
	set_invite($resume['uid'],$jobs['id'],$jobs['companyname'],$jobs['jobs_name'],$jobs['contact']['address'],$jobs['contact']['contact'],$jobs['contact']['telephone'],$notes);
	exit("ok");
	

}
//Ԥ����Ƹ��
elseif ($act=='booth')
{
	$id=intval($_GET['id']);
	if(intval($_SESSION['utype'])!=1){
		exit("ֻ����ҵ��Ա����Ԥ����");
	}
	if(empty($id))
	{
	exit("ERR");
	}
		$time=time();
		$sql = "select * from ".table('jobfair')." where id='{$id}' limit 1";
		$jobfair=$db->getone($sql);
		//����Ԥ�� �ٰ쿪ʼ���ڴ��ڵ�ǰ����  Ԥ������ʱ����ڵ�ǰʱ��  ��������Ԥ��
		if ($jobfair['predetermined_status']=="1" && $jobfair['holddate_start']>$time && ($jobfair['predetermined_end']=="0" || $jobfair['predetermined_end']>$time) && $jobfair['predetermined_web']=="1")
		{
			if($time<$jobfair['predetermined_start']){
				exit("����Ƹ�ỹδ��ʼԤ������ʼԤ��ʱ�䣺".date("Y-m-d",$jobfair['predetermined_start']));
			}
			if ($db->getone("select * from ".table('jobfair_exhibitors')." where jobfairid='{$id}' AND uid={$_SESSION['uid']} limit 1"))
			{
				exit("���Ѿ�Ԥ��������Ƹ���չλ�ˣ������ظ�Ԥ��");
			}

			if ($_CFG['operation_mode']=='1'){
					$user_points=get_user_points($_SESSION['uid']);
					if ($jobfair['predetermined_point']>$user_points)
					{
						exit("���".$_CFG['points_byname']."���㣬���ֵ����Ԥ����");
					}
			}elseif ($_CFG['operation_mode']=='2'){
				$setmeal=get_user_setmeal($_SESSION['uid']);
				if($setmeal['jobsfair_num']<=0){
					exit("���ۼƲμӵ���Ƹ���Ѿ�������������ƣ������������ײͣ�");
				}
			}elseif ($_CFG['operation_mode']=='3'){
				$_CFG['operation_mode']=2;
				$setmeal=get_user_setmeal($_SESSION['uid']);
				if($setmeal['jobsfair_num']<=0){
					if($_CFG['setmeal_to_points']==1){
						$user_points=get_user_points($_SESSION['uid']);
						if ($jobfair['predetermined_point']>$user_points)
						{
							exit("���".$_CFG['points_byname']."���㣬���ֵ����Ԥ����");
						}else{
							$_CFG['operation_mode']=1;
						}
					}else{
						exit("���ۼƲμӵ���Ƹ���Ѿ�������������ƣ������������ײͣ�");
					}
				}
			}
					$company_profile=get_company($_SESSION['uid']);
					$setsqlarr['jobfairid']=$id;
					$setsqlarr['uid']=intval($_SESSION['uid']);
					$setsqlarr['etypr']=1;
					$setsqlarr['eaddtime']=$timestamp;
					$setsqlarr['companyname']=$company_profile['companyname'];
					$setsqlarr['company_id']=$company_profile['id'];
					$setsqlarr['company_addtime']=$company_profile['addtime'];
					$setsqlarr['jobfair_title']=$jobfair['title'];
					$setsqlarr['jobfair_addtime']=$jobfair['addtime'];
					$setsqlarr['note']="{$_SESSION['username']} Ԥ������Ƹ�� ��{$jobfair['title']}�� ��չλ���ѳɹ��۳����� {$jobfair['predetermined_point']}";	
					if ($db->inserttable(table('jobfair_exhibitors'),$setsqlarr))
					{
					if ($jobfair['predetermined_point']>0 && $_CFG['operation_mode']=='1')
					{
						report_deal($_SESSION['uid'],2,$jobfair['predetermined_point']);
						$user_points=get_user_points($_SESSION['uid']);					
						write_memberslog($_SESSION['uid'],1,9001,$_SESSION['username'],"Ԥ������Ƹ�� ��{$jobfair['title']}�� ��չλ��(-{$jobfair['predetermined_point']})��(ʣ��:{$user_points})",1,1019,"Ԥ����Ƹ��չλ","-{$jobfair['predetermined_point']}","{$user_points}");
					}elseif($_CFG['operation_mode']=='2'){
						action_user_setmeal($_SESSION['uid'],'jobsfair_num');
						$jobsfair_num=$setmeal['jobsfair_num']-1;
						write_memberslog($_SESSION['uid'],1,9002,$_SESSION['username'],"Ԥ������Ƹ�� ��{$jobfair['title']}�� ��չλ��ʣ��μ���Ƹ��{$jobsfair_num}����",2,1019,"Ԥ����Ƹ��չλ","1","{$jobsfair_num}");
					}	
					write_memberslog($_SESSION['uid'],1,1401,$_SESSION['username'],"Ԥ������Ƹ�� ��{$jobfair['title']}�� ��չλ");
					exit("Ԥ���ɹ���");
					}
		}
		else
		{
			exit("����Ƹ���ѽ���Ԥ����");
		}
}
elseif($act=="salary_search")
{
	if (strcasecmp(QISHI_DBCHARSET,"utf8")!=0)
	{
	$_GET['category']=utf8_to_gbk($_GET['category']);
	$_GET['district']=utf8_to_gbk($_GET['district']);
	}
	unset($_GET['act']);
	$param=array_map("rawurlencode",$_GET);
	$url=$_CFG['wap_domain'].'/salary.php?category='.$param['category'].'&district='.$param['district'];
	unset($_GET,$param);
	exit($url);
}
//��ȡְλ���߼���������UID
function get_uid($aid,$type='jobs')
{
    global $db;
	if($type=='resume')
	{
	    $table=table('resume');
	}
	else
	{
	    $table=table('jobs');
	}
	$row=$db->getone("Select uid From {$table} Where id={$aid} LIMIT 1");
	return $row['uid'];
}

?>