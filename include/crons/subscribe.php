<?php
 /*
 * 74cms �ƻ����� ÿ������ͳ��
*/
if(!defined('IN_QISHI'))
{
die('Access Denied!');
}
ignore_user_abort(true);
set_time_limit(180);
	global $_CFG;
	$_CFG = get_cache('config');
	$_CFG['web_logo']=$_CFG['web_logo']?$_CFG['web_logo']:'logo.gif';
	$_CFG['upfiles_dir']=$_CFG['site_dir']."data/".$_CFG['updir_images']."/";
	$mailconfig=get_cache('mailconfig');
	$today = strtotime(date('Y-m-d'));
	$list = $db->getall("select * from ".table('jobs_subscribe'));
	if($list){
		foreach ($list as $key => $value) {
			$value = array_map("addslashes", $value);
			$addtime = strtotime(date('Y-m-d',$value['addtime']));
				 if(((($today-$addtime)/24/3600)%$value['days'])==0){
					$search_name = trim($value['search_name']);
					$district = trim($value['district']);
					$district_arr = explode(",", $district);
					foreach ($district_arr as $key => $n) {
						$v = explode(".", $n);
						if(intval($v[1])==0){
							if(intval($v[0])==0){
								continue;
							}else{
								$district_str_arr[] = intval($v[0]);
							}
					    }else{
					        $sdistrict_str_arr[] = intval($v[1]);
					    }
					}
					$district_str = implode(",", $district_str_arr);
				    $sdistrict_str = implode(",", $sdistrict_str_arr);
				    if($district_str){
				        $wheresql = " where district in (".$district_str.")";
				    }
					if($sdistrict_str){
				        $wheresql = $wheresql==''?" where sdistrict in (".$sdistrict_str.")":$wheresql." OR sdistrict in (".$sdistrict_str.")";
				    }

					if($search_name){
				        $wheresql = $wheresql==''?" WHERE likekey LIKE '%{$search_name}%' ":$wheresql." AND likekey LIKE '%{$search_name}%' ";
				    }
					$jobsearchkey = $db->getall("select id from ".table('jobs_search_key').$wheresql);
					foreach ($jobsearchkey as $n) {
						$idarr[] = $n['id'];
					}
					$id_str = empty($idarr)?"0":implode(",", $idarr);
					$html = '<table width="700" cellspacing="0" cellpadding="0" border="0" style="margin:0 auto;color:#555; font:16px/26px \'΢���ź�\',\'����\',Arail; ">
	    	<tbody><tr>
	        	<td style="height:62px; background-color:#FCFCFC; padding:10px 0 0 10px;">
	            	<a target="_blank" href="'.$_CFG['site_domain'].$_CFG['site_dir'].'"><img src="'.$_CFG['site_domain'].$_CFG['upfiles_dir'].$_CFG['web_logo'].'" width="200" height="45" style="border:none;"/></a>
	            </td>
	        </tr>
	        <tr style="background-color:#fff;">
	        	<td style="padding:30px 38px;">
	            	<div>�װ���<span style="color:#017FCF;"><a href="mailto:'.$setsqlarr['email'].'" target="_blank">'.$setsqlarr['email'].'</a></span>�����!</div>';
	    if($id_str=="0"){
	    	$html .='<div style="text-indent:2em;">����<a style="color:#017FCF;" href="'.$_CFG['site_domain'].$_CFG['site_dir'].'" target="_blank">'.$_CFG['site_name'].'</a>�϶�����"<span style="color:#017FCF;">'.$search_name.'</span>"���ְλ��Ϣ�������Ǿ��ĵ���ѡ����û���ҵ��������������ְλ��������<a href="'.$_CFG['site_domain'].$_CFG['site_dir'].'">����ѡ��</a>��ףְҵ����һ��¥��</div>';
	    }else{
	    	$html .='<div style="text-indent:2em;">����<a style="color:#017FCF;" href="'.$_CFG['site_domain'].$_CFG['site_dir'].'" target="_blank">'.$_CFG['site_name'].'</a>�϶�����"<span style="color:#017FCF;">'.$search_name.'</span>"���ְλ��Ϣ�������Ǿ��ĵ���ѡ���ֽ�ɸѡ������͸��㣬ϣ�����ǵ��ʼ��ܹ���������������ףְҵ����һ��¥��</div>
	                <div style="text-indent:2em;"></div>
	            	<div style="border-bottom:1px solid #e6e6e6; font-weight:bold; margin:20px 0 0 0; padding-bottom:5px;">�������㶩�ĵ�ְλ��</div>
	            	<ul style="list-style:none; margin:0; padding:0;">';
	    $jobslist = $db->getall("select id,uid,jobs_name,companyname,company_id,district_cn from ".table('jobs')." where id in (".$id_str.")");
	   	if($jobslist){
			foreach ($jobslist as $k=>$v) {
				$jobs_url = url_rewrite('QS_jobsshow',array('id'=>$v['id']));
				$company_url = url_rewrite('QS_companyshow',array('id'=>$v['company_id']));
				$logo = $db->getone("select logo,district_cn from ".table('company_profile')." where id=".$v['company_id']);
				if($logo['logo']==""){
					$company_logo = $_CFG['site_domain'].$_CFG['site_dir']."data/logo/no_logo.gif";
				}else{
					$company_logo = $_CFG['site_domain'].$_CFG['site_dir']."data/logo/".$logo['logo'];
				}
				$html .='<li style="list-style:none;padding:15px 10px 15px 0;border-bottom:1px solid #e6e6e6; overflow:hidden;">
	    <a target="_blank" href="'.$company_url.'">
	    <img width="80" height="80" style="border:none; float:left; margin-right:15px;" src="'.$company_logo.'">
	    </a>
	    <div>
	    <a target="_blank" style="float:left; color:#017FCF; text-decoration:underline;" href="'.$jobs_url.'">
	    '.$v['jobs_name'].'
	    </a>
	    <a target="_blank" style="float:right; color:#017FCF; text-decoration:underline;" href="'.$jobs_url.'">
	    �鿴����
	    </a><br>
	    <div style="font-weight:700;">'.$v['companyname'].'</div>
	    <div>����������'.$v["district_cn"].'</div>
	    </div>
	    </li>';
			}
		}
	    
	    $html .='</ul>
	                <a target="_blank" style="float:right; text-decoration:underline; font-weight:700; margin:15px 0;color:#017FCF;" href="'.$_CFG["site_domain"].$_CFG["site_dir"].'jobs/jobs-list.php">�鿴����ְλ</a>
	            </td>
	        </tr>';
	    }
	                
	    $html .='
	        <tr>
	        	<td style="text-align:center; color:#c9cbce; font-size:14px; padding:5px 0;">��˾��ַ��<a style="color:#017FCF;" target="_blank" href="'.$_CFG["site_domain"].$_CFG["site_dir"].'">'.$_CFG["site_domain"].$_CFG["site_dir"].'</a>   </td>
	        </tr>
	        <tr>
	            <td style="line-height:30px;text-align:right;font-size:14px;"> Ϊ��֤�����������գ��뽫<a href="mailto:'.$mailconfig['smtpfrom'].'" target="_blank">'.$mailconfig['smtpfrom'].'</a>��ӽ����ͨѶ¼</td>
	        </tr>
	    </tbody></table>';
					 
					smtp_mail($value['email'],$_CFG['site_name']."���������������ĵ�ְλ��Ϣ",$html,$mailconfig['smtpfrom'],$_CFG['site_name']);
						
				 }
			
		}
	}
	//��������ʱ���
	if ($crons['weekday']>=0)
	{
	$weekday=array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');
	$nextrun=strtotime("Next ".$weekday[$crons['weekday']]);
	}
	elseif ($crons['day']>0)
	{
	$nextrun=strtotime('+1 months'); 
	$nextrun=mktime(0,0,0,date("m",$nextrun),$crons['day'],date("Y",$nextrun));
	}
	else
	{
	$nextrun=time();
	}
	if ($crons['hour']>=0)
	{
	$nextrun=strtotime('+1 days',$nextrun); 
	$nextrun=mktime($crons['hour'],0,0,date("m",$nextrun),date("d",$nextrun),date("Y",$nextrun));
	}
	if (intval($crons['minute'])>0)
	{
	$nextrun=strtotime('+1 hours',$nextrun); 
	$nextrun=mktime(date("H",$nextrun),$crons['minute'],0,date("m",$nextrun),date("d",$nextrun),date("Y",$nextrun));
	}
	$setsqlarr['nextrun']=$nextrun;
	$setsqlarr['lastrun']=time();
	$db->updatetable(table('crons'), $setsqlarr," cronid ='".intval($crons['cronid'])."'");
?>