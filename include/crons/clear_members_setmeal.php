<?php
 /*
 * 74cms �ƻ����� ÿ�������Ա������־
*/
if(!defined('IN_QISHI'))
{
die('Access Denied!');
}
	global $_CFG,$db;
	$time=time();
	$result = $db->query("select * from ".table('members_setmeal')." where endtime<>0 and endtime<$time ");
	while($row = $db->fetch_array($result))
	{
		//���������ײ�  (1->��  2->��)   ������Ԥ���ײ���
		if($_CFG['is_superposition']==1)
		{
			// Ԥ���ײ�����
			$setmeal_reserved=$db->getone("select * from ".table("members_setmeal_reserved")." where uid=".intval($row['uid']));
			if($_CFG['superposition_time']>0 && empty($setmeal_reserved))
			{
				$setmeal_reserved_arr['uid']=$row['uid'];
				$setmeal_reserved_arr['download_resume_ordinary']=$row['download_resume_ordinary'];
				$setmeal_reserved_arr['download_resume_senior']=$row['download_resume_senior'];
				$setmeal_reserved_arr['interview_ordinary']=$row['interview_ordinary'];
				$setmeal_reserved_arr['interview_senior']=$row['interview_senior'];
				$setmeal_reserved_arr['talent_pool']=$row['talent_pool'];
				$setmeal_reserved_arr['recommend_num']=$row['recommend_num'];
				$setmeal_reserved_arr['recommend_days']=$row['recommend_days'];
				$setmeal_reserved_arr['stick_num']=$row['stick_num'];
				$setmeal_reserved_arr['stick_days']=$row['stick_days'];
				$setmeal_reserved_arr['emergency_num']=$row['emergency_num'];
				$setmeal_reserved_arr['emergency_days']=$row['emergency_days'];
				$setmeal_reserved_arr['highlight_num']=$row['highlight_num'];
				$setmeal_reserved_arr['highlight_days']=$row['highlight_days'];
				$setmeal_reserved_arr['jobsfair_num']=$row['jobsfair_num'];
				$setmeal_reserved_arr['change_templates']=$row['change_templates'];
				$setmeal_reserved_arr['map_open']=$row['map_open'];
				$setmeal_reserved_arr['added']=$row['added'];
				$setmeal_reserved_arr['refresh_jobs_space']=$row['refresh_jobs_space'];
				$setmeal_reserved_arr['refresh_jobs_time']=$row['refresh_jobs_time'];
				$setmeal_reserved_arr['reserved_time']=$time+$_CFG['superposition_time']*24*3600;
				$db->inserttable(table("members_setmeal_reserved"),$setmeal_reserved_arr)	;
			}
		}
		//���������ײ�
		$setmeal=$db->getone("select * from ".table('setmeal')." WHERE id = ".intval($_CFG['reset_service'])." AND display=1 LIMIT 1");
		$setsqlarr['effective']=1;
		$setsqlarr['setmeal_id']=$setmeal['id'];
		$setsqlarr['setmeal_name']=$setmeal['setmeal_name'];
		$setsqlarr['days']=$setmeal['days'];
		$setsqlarr['starttime']=$timestamp;
		if ($setmeal['days']>0)
		{
			$setsqlarr['endtime']=strtotime("".$setmeal['days']." days");
		}
		else
		{
			$setsqlarr['endtime']="0";	
		}
		$setsqlarr['expense']=$setmeal['expense'];
		$setsqlarr['jobs_ordinary']=$setmeal['jobs_ordinary'];
		$setsqlarr['download_resume_ordinary']=$setmeal['download_resume_ordinary'];
		$setsqlarr['download_resume_senior']=$setmeal['download_resume_senior'];
		$setsqlarr['interview_ordinary']=$setmeal['interview_ordinary'];
		$setsqlarr['interview_senior']=$setmeal['interview_senior'];
		$setsqlarr['talent_pool']=$setmeal['talent_pool'];
		$setsqlarr['recommend_num']=$setmeal['recommend_num'];
		$setsqlarr['recommend_days']=$setmeal['recommend_days'];
		$setsqlarr['stick_num']=$setmeal['stick_num'];
		$setsqlarr['stick_days']=$setmeal['stick_days'];
		$setsqlarr['emergency_num']=$setmeal['emergency_num'];
		$setsqlarr['emergency_days']=$setmeal['emergency_days'];
		$setsqlarr['highlight_num']=$setmeal['highlight_num'];
		$setsqlarr['highlight_days']=$setmeal['highlight_days'];
		$setsqlarr['change_templates']=$setmeal['change_templates'];
		$setsqlarr['jobsfair_num']=$setmeal['jobsfair_num'];
		$setsqlarr['map_open']=$setmeal['map_open'];

		$setsqlarr['added']=$setmeal['added'];
		$setsqlarr['refresh_jobs_space']=$setmeal['refresh_jobs_space'];
		$setsqlarr['refresh_jobs_time']=$setmeal['refresh_jobs_time'];
		$setsqlarr['set_sms']=$setmeal['set_sms'];
		$db->updatetable(table('members_setmeal'),$setsqlarr," uid=".intval($row['uid'])."");
		//�޸�ְλ�ײ���Ϣ
		$setmeal_jobs['setmeal_deadline']=$setsqlarr['endtime'];
		$setmeal_jobs['setmeal_id']=$setsqlarr['setmeal_id'];
		$setmeal_jobs['setmeal_name']=$setsqlarr['setmeal_name'];
		$db->updatetable(table('jobs'),$setmeal_jobs," uid=".intval($row['uid'])." AND add_mode='2' ");
		$db->updatetable(table('jobs_tmp'),$setmeal_jobs," uid=".intval($row['uid'])." AND add_mode='2' ");
		//�޸���Ӧ�Ĳ���ְλ ����Ϊ�ײͷ����ı䣬������Ҳ��֮�ı䣩
		$jobs_num= $db->get_total("select count(*) num from ".table("jobs")." where uid=$row[uid] and audit=1 and display=1 ");
		if ($jobs_num > $setmeal['jobs_ordinary'])
		{
			//��֮ǰ������ʾ��ְλ��  ���� ��ǰ�ײ�ְλ������  ����ˢ��ʱ������ �����ְλ�Զ����ùر�
			$idarr = $db->getall("select id from ".table('jobs')." where  uid=$row[uid] and audit=1 and display=1 order by refreshtime desc limit  ".$setmeal['jobs_ordinary'].",".$row['jobs_ordinary']);
			foreach ($idarr as $key=>$value) 
			{
				$idarr[$key]=$value['id'];
			}
			$sqlin=implode(",",$idarr);
			if (!preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin)) return false;
			$sql = "UPDATE ".table('jobs')." SET display = 2 where id IN ({$sqlin}) ";
			$db->query($sql);
		}
		distribution_jobs_uid(intval($row['uid']));
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
	$setsqlarr_crons['nextrun']=$nextrun;
	$setsqlarr_crons['lastrun']=time();
	$db->updatetable(table('crons'), $setsqlarr_crons," cronid ='".intval($crons['cronid'])."'");
	function distribution_jobs($id,$uid)
	{
		global $db,$_CFG;
		$uid=intval($uid);
		$uidsql=" AND uid='{$uid}' ";
		if (!is_array($id))$id=array($id);
		$time=time();
		foreach($id as $v)
		{
			$v=intval($v);
			$t1=$db->getone("select * from ".table('jobs')." where id='{$v}' {$uidsql} LIMIT 1");
			$t2=$db->getone("select * from ".table('jobs_tmp')." where id='{$v}' {$uidsql} LIMIT 1");
			if ((empty($t1) && empty($t2)) || (!empty($t1) && !empty($t2)))
			{
			continue;
			}
			else
			{
					$j=!empty($t1)?$t1:$t2;
					if (!empty($t1) &&  $j['audit']=="1" && $j['display']=="1" && $j['user_status']=="1")
					{
						if ($_CFG['outdated_jobs']=="1")
						{
							if ($j['deadline']>$time && ($j['setmeal_deadline']=="0" || $j['setmeal_deadline']>$time))
							{
							continue;
							}
						}
						else
						{
						continue;
						}
					}
					elseif (!empty($t2))
					{
							if ($j['audit']!="1" || $j['display']!="1" || $j['user_status']!="1")
							{
							continue;
							}
							else
							{
									if ($_CFG['outdated_jobs']=="1" && ($j['deadline']<$time || ($j['setmeal_deadline']<$time && $j['setmeal_deadline']!="0")))
									{
										continue;
									}
							}
					}
					//������
					$j=array_map('addslashes',$j);
					if (!empty($t1))
					{
						$db->query("Delete from ".table('jobs_tmp')." WHERE id='{$v}' {$uidsql}  LIMIT 1");
						$db->query("Delete from ".table('jobs')." WHERE id='{$v}' {$uidsql}  LIMIT 1");
						if ($db->inserttable(table('jobs_tmp'),$j))
						{
							$db->query("Delete from ".table('jobs_search_hot')." WHERE id='{$v}' {$uidsql} LIMIT 1");
							$db->query("Delete from ".table('jobs_search_key')." WHERE id='{$v}' {$uidsql} LIMIT 1");
							$db->query("Delete from ".table('jobs_search_rtime')." WHERE id='{$v}' {$uidsql} LIMIT 1");
							$db->query("Delete from ".table('jobs_search_scale')." WHERE id='{$v}' {$uidsql} LIMIT 1");
							$db->query("Delete from ".table('jobs_search_stickrtime')." WHERE id='{$v}' {$uidsql} LIMIT 1");
							$db->query("Delete from ".table('jobs_search_wage')." WHERE id='{$v}' {$uidsql} LIMIT 1");
						}
					}
					else
					{
						$db->query("Delete from ".table('jobs')." WHERE id='{$v}' {$uidsql} LIMIT 1");
						$db->query("Delete from ".table('jobs_tmp')." WHERE id='{$v}' {$uidsql} LIMIT 1");
						if ($db->inserttable(table('jobs'),$j))
						{
							$searchtab['id']=$j['id'];
							$searchtab['uid']=$j['uid'];
							$searchtab['recommend']=$j['recommend'];
							$searchtab['emergency']=$j['emergency'];
							$searchtab['nature']=$j['nature'];
							$searchtab['sex']=$j['sex'];
							$searchtab['topclass']=$j['topclass'];
							$searchtab['category']=$j['category'];
							$searchtab['subclass']=$j['subclass'];
							$searchtab['trade']=$j['trade'];
							$searchtab['district']=$j['district'];
							$searchtab['sdistrict']=$j['sdistrict'];
							$searchtab['street']=$j['street'];
							$searchtab['education']=$j['education'];
							$searchtab['experience']=$j['experience'];
							$searchtab['wage']=$j['wage'];
							$searchtab['refreshtime']=$j['refreshtime'];
							$searchtab['scale']=$j['scale'];
							//--
							$db->inserttable(table('jobs_search_wage'),$searchtab);
							$db->inserttable(table('jobs_search_scale'),$searchtab);
							//--
							$searchtab['map_x']=$j['map_x'];
							$searchtab['map_y']=$j['map_y'];
							$db->inserttable(table('jobs_search_rtime'),$searchtab);
							unset($searchtab['map_x'],$searchtab['map_y']);
							//--
							$searchtab['stick']=$j['stick'];
							$db->inserttable(table('jobs_search_stickrtime'),$searchtab);
							unset($searchtab['stick']);
							//--
							$searchtab['click']=$j['click'];
							$db->inserttable(table('jobs_search_hot'),$searchtab);
							unset($searchtab['click']);
							//--
							$searchtab['key']=$j['key'];
							$searchtab['map_x']=$j['map_x'];
							$searchtab['map_y']=$j['map_y'];
							$searchtab['likekey']=$j['jobs_name'].','.$j['companyname'];
							$db->inserttable(table('jobs_search_key'),$searchtab);
							unset($searchtab);
						}
					}		
			}
		}
	}
	function distribution_jobs_uid($uid)
	{
		global $db;
		$uid=intval($uid);
		$result = $db->query("select id from ".table('jobs')." where uid={$uid} UNION ALL select id from ".table('jobs_tmp')." where uid={$uid} ");
		while($row = $db->fetch_array($result))
		{
		$id[] = $row['id'];
		}
		if (!empty($id))
		{
		return distribution_jobs($id,$uid);
		}
	}
?>