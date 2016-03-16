<?php
 /*
 * 74cms ajax
*/
define('IN_QISHI', true);
require_once(dirname(__FILE__).'/../include/common.inc.php');
require_once(dirname(__FILE__).'/../include/mysql.class.php');
require_once(dirname(__FILE__).'/../include/fun_company.php');
$db = new mysql($dbhost,$dbuser,$dbpass,$dbname);
$act = !empty($_GET['act']) ? trim($_GET['act']) : '';
$promotionid=intval($_GET['promotionid']);
$promotion=get_promotion_category_one($promotionid);
$jobsid=intval($_GET['jobsid']);
if ($_CFG['operation_mode']=='2')
{
	$setmeal=get_user_setmeal($_SESSION['uid']);//��ȡ��Ա�ײ�
	if($setmeal['endtime']<time() && $setmeal['endtime']<>'0'){
		$end=1;
	}
	$data=get_setmeal_promotion($_SESSION['uid'],$promotionid);//��ȡ��Աĳ���ƹ��ʣ�����������������ƣ�������
	
	$operation_mode = 2;
}
elseif($_CFG['operation_mode']=='1')
{
	$user_points = get_user_points($_SESSION['uid']);
	$operation_mode = 1;
}
elseif($_CFG['operation_mode']=='3')
{
	$setmeal=get_user_setmeal($_SESSION['uid']);//��ȡ��Ա�ײ�
	if($setmeal['endtime']<time() && $setmeal['endtime']<>'0'){
		$end=1;
	}
	$data=get_setmeal_promotion($_SESSION['uid'],$promotionid);//��ȡ��Աĳ���ƹ��ʣ�����������������ƣ�������
	
	if($data['num']<1){
		$operation_mode = 1;
		$user_points = get_user_points($_SESSION['uid']);
	}else{
		$operation_mode = 2;
	}
}

if ($act=="get_promotion_one")
{
	
	if($operation_mode==1){
		$html = '<tr>
      <td width="20" height="10">&nbsp;</td>
      <td height="10" ><font color="#FF3300" id="re">�ƹ��������٣�'.$promotion["cat_minday"].'��</font></td>
    </tr>
    <tr>
      <td width="20" height="10">&nbsp;</td>
      <td height="10"><font color="#FF3300">�ƹ���ÿ�����Ļ��֣� '.$promotion["cat_points"].'�����</font></td>
    </tr>
    <tr>
      <td width="20" height="10">&nbsp;</td>
      <td height="10"><font color="#FF3300">�ƹ�������ࣺ '.$promotion["cat_maxday"].'��</font></td>
    </tr><tr>
      <td width="20" height="10">&nbsp;</td>
      <td height="10"><font color="#FF3300">�ƹ㷽����'.$promotion["cat_name"].'<font></td>
    </tr>';
    if($promotion["cat_id"]==4){
		$color = get_color();
	    $html .='<tr>
					<td align="right"></td>
					<td>
					ѡ����ɫ��
					</td>
				  </tr><tr>
					<td align="right"></td>
					<td>
						<input id="val" name="val" type="text" value="" class="input_text_100"/><div style="width:220px;" class="color_picker">';
		foreach ($color as $key => $value) {
			$html .= '<div class="icolor" value="'.$value["value"].'" style="cursor:pointer;float:left; background-color: '.$value["value"].';height:20px; width:30px;font-size:0px;margin:1px;"></div>';
		}
		$html .='</div>
					</td>
				  </tr>';
    }
    $html .='<tr>
      <td width="20" height="30">&nbsp;</td>
      <td height="30">�ƹ����ޣ�<input name="pdays" type="text" class="input_text_100" id="pdays" value="" maxlength="4"   />
			��<span><font class="notice" color="red"></font></span></td>
    </tr><tr>
      <td width="20" height="30">&nbsp;</td>
      <td height="30">�ƹ�ְλ��www</td>
    </tr>
    <input name="jobsid" id="jobsid" type="hidden" value="'.$jobsid.'" />
    <input name="promotionid" id="promotionid" type="hidden" value="'.$promotionid.'" />
	<input name="pro_name" id="pro_name" type="hidden" value="" />
    <tr>
      <td height="25">&nbsp;</td>
      <td>
	  <input type="button" name="set_promotion" value="ȷ��" class="user_submit set_promotion"/>
 </td>
    </tr>';
	}elseif($operation_mode==2){
		if($end==1){
			$html = '<tr>
      <td width="10" height="20">&nbsp;</td>
      <td height="10" >��ܰ���ѣ����ĵķ����Ѿ����ڣ�����������<a href="company_service.php?act=setmeal_list"> [�������ײ�] </a></td>
    </tr>';
		}else{
			$html = '<tr>
      <td width="10" height="20">&nbsp;</td>
      <td height="10" ><font color="#FF3300" id="re">�����ײͣ�'.$setmeal["setmeal_name"].'</font></td>
    </tr>
    <tr>
      <td width="10" height="20">&nbsp;</td>
      <td height="10"><font color="#FF3300">�ײ���'.$promotion["cat_name"].'��������
			  '.$data["total_num"].'��</font></td>
    </tr>
    <tr>
      <td width="10" height="20">&nbsp;</td>
      <td height="10"><font color="#FF3300">�ײ���'.$promotion["cat_name"].'ʣ�ࣺ
			   '.$data["num"].'��</font></td>
    </tr><tr>
      <td width="20" height="10">&nbsp;</td>
      <td height="10"><font color="#FF3300">�ƹ㷽����'.$promotion["cat_name"].'<font></td>
    </tr>';
	if($promotion["cat_id"]==4){
		$color = get_color();
	    $html .='<tr>
					<td align="right"></td>
					<td>
					ѡ����ɫ��
					</td>
				  </tr><tr>
					<td align="right"></td>
					<td>
						<input id="val" name="val" type="text" value="" class="input_text_100"/><div style="width:220px;" class="color_picker">';
		foreach ($color as $key => $value) {
			$html .= '<div class="icolor" value="'.trim($value["value"],"#").'" style="cursor:pointer;float:left; background-color: '.$value["value"].';height:20px; width:30px;font-size:0px;margin:1px;"></div>';
		}
		$html .='</div>
					</td>
				  </tr>';
    }
    $html .='<tr>
      <td width="20" height="20">&nbsp;</td>
      <td height="20">�ƹ����ޣ�'.$data["days"].'��</td>
    </tr>
    <tr>
      <td width="20" height="20">&nbsp;</td>
      <td height="20">�ƹ�ְλ��www</td>
    </tr>
    <input name="jobsid" id="jobsid" type="hidden" value="'.$jobsid.'" />
    <input name="promotionid" id="promotionid" type="hidden" value="'.$promotion["cat_id"].'" />
	<input name="pdays" id="pdays" type="hidden" value="'.$data["days"].'" />
	<input name="pro_name" id="pro_name" type="hidden" value="'.$data["name"].'" />
     <tr>
      <td height="25">&nbsp;</td>
      <td>
	  <input type="button" name="set_promotion" value="ȷ��" class="user_submit set_promotion"/>
 </td>
    </tr>';
		}
	}
	exit($html);
	
}
elseif($act == "promotion_save"){
	$jobsid=intval($_GET['jobsid'])==0?exit("0"):intval($_GET['jobsid']);
	$jobs=get_jobs_one($jobsid,$_SESSION['uid']);
	$jobs = array_map("addslashes",$jobs);
	if($jobs['deadline']<time()){
		exit("-1");
		// showmsg("��ְλ�ѵ��ڣ��������ڣ�",1);
	}
	$days=intval($_GET['pdays']);
	$_GET['val']="#".trim($_GET['val']);
	if($operation_mode==1){
		if($promotion["cat_minday"]>0 && $days<$promotion["cat_minday"]){
			exit("-5");//С����������
		}elseif ($promotion["cat_maxday"]>0 && $days>$promotion["cat_maxday"]) {
			exit("-6");//�����������
		}
	}
	if ($jobsid>0 && $days>0)
	{
		$pro_cat=get_promotion_category_one(intval($_GET['promotionid']));
		if($_CFG['operation_mode']=='3'){
			$setmeal=get_setmeal_promotion($_SESSION['uid'],intval($_GET['promotionid']));//��ȡ��Ա�ײ�
			$num=$setmeal['num'];
			if(($setmeal['endtime']<time() && $setmeal['endtime']<>'0') || $num<=0){
				if($_CFG['setmeal_to_points']==1){
					if ($pro_cat['cat_points']>0)
					{
						$points=$pro_cat['cat_points']*$days;
						$user_points=get_user_points($_SESSION['uid']);
						if ($points>$user_points)
						{
							exit("-2");
						// showmsg("���".$_CFG['points_byname']."�������д˴β��������ȳ�ֵ��",1,$link);
						}else{
							$_CFG['operation_mode']=1;
						}
					}else{
						$_CFG['operation_mode']=2;
					}
				}else{
					exit("-3");
					// showmsg("����ײ��ѵ��ڻ��ײ���ʣ��{$pro_cat['cat_name']}�������뾡�쿪ͨ���ײ�",1,$link);
				}
			}else{
				$_CFG['operation_mode']=2;
			}
		}elseif($_CFG['operation_mode']=='1'){
			if ($pro_cat['cat_points']>0)
			{
				$points=$pro_cat['cat_points']*$days;
				$user_points=get_user_points($_SESSION['uid']);
				if ($points>$user_points)
				{
				exit("-2");
				}
			}
		}elseif($_CFG['operation_mode']=='2'){
			$setmeal=get_setmeal_promotion($_SESSION['uid'],intval($_GET['promotionid']));//��ȡ��Ա�ײ�
			$num=$setmeal['num'];
			if(($setmeal['endtime']<time() && $setmeal['endtime']<>'0') || $num<=0){
				exit("-3");
			}
		}
		$info=get_promotion_one($jobsid,$_SESSION['uid'],$_GET['promotionid']);
		if (!empty($info))
		{
			exit("-4");
		// showmsg("��ְλ�����ƹ��У���ѡ������ְλ����������",1);
		}
		$setsqlarr['cp_available']=1;
		$setsqlarr['cp_promotionid']=intval($_GET['promotionid']);
		$setsqlarr['cp_uid']=$_SESSION['uid'];
		$setsqlarr['cp_jobid']=$jobsid;
		$setsqlarr['cp_days']=$days;
		$setsqlarr['cp_starttime']=time();
		$setsqlarr['cp_endtime']=strtotime("{$days} day");
		$setsqlarr['cp_val']=$_GET['val'];
		$setsqlarr['cp_hour_cn']=trim($_GET['hour']);
		$setsqlarr['cp_hour']=intval($_GET['hour']);
		if ($setsqlarr['cp_promotionid']=="4" && empty($setsqlarr['cp_val']))
		{
		showmsg("��ѡ����ɫ��",1);
		}
			if ($db->inserttable(table('promotion'),$setsqlarr))
			{
				set_job_promotion($jobsid,$setsqlarr['cp_promotionid'],$_GET['val']);
				if ($_CFG['operation_mode']=='1' && $pro_cat['cat_points']>0)
				{
					report_deal($_SESSION['uid'],2,$points);
					$user_points=get_user_points($_SESSION['uid']);
					write_memberslog($_SESSION['uid'],1,9001,$_SESSION['username'],"{$pro_cat['cat_name']}��<strong>{$jobs['jobs_name']}</strong>���ƹ� {$days} �죬(-{$points})��(ʣ��:{$user_points})");
				}elseif($_CFG['operation_mode']=='2'){
					$user_pname=trim($_GET['pro_name']);
					action_user_setmeal($_SESSION['uid'],$user_pname); //�����ײ�����Ӧ�ƹ㷽ʽ������
					$setmeal=get_user_setmeal($_SESSION['uid']);//��ȡ��Ա�ײ�
					write_memberslog($_SESSION['uid'],1,9002,$_SESSION['username'],"{$pro_cat['cat_name']}��<strong>{$jobs['jobs_name']}</strong>���ƹ� {$days} �죬�ײ���ʣ��{$pro_cat['cat_name']}������{$setmeal[$user_pname]}����");//9002���ײͲ���
				}
				write_memberslog($_SESSION['uid'],1,3004,$_SESSION['username'],"{$pro_cat['cat_name']}��<strong>{$jobs['jobs_name']}</strong>���ƹ� {$days} �졣");
				if ($_GET['golist'])
				{
					exit("1");
				// showmsg("�ƹ�ɹ���",2,$link);
				}
				else
				{
					exit("1");
				}
			}
	}
	else
	{
	exit("0");
	//showmsg("��������",0);
	}
}

?>