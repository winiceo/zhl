<?php
 /*
 * 74cms ajax Ԥ����Ƹ��
*/
define('IN_QISHI', true);
require_once(dirname(dirname(__FILE__)).'/include/plus.common.inc.php');
require_once(dirname(dirname(__FILE__)).'/include/fun_company.php');
$act = !empty($_GET['act']) ? trim($_GET['act']) : '';
if ($act=='booth')
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
}
?>