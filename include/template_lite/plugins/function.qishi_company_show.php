<?php
function tpl_function_qishi_company_show($params, &$smarty)
{ 
	global $db,$_CFG;
	$arr=explode(',',$params['set']);
	foreach($arr as $str)
	{
	$a=explode(':',$str);
		switch ($a[0])
		{
		case "��ҵID":
			$aset['id'] = $a[1];
			break;
		case "��ҵ���ܳ���":
			$aset['companynamelen'] = $a[1];
			break;
		case "�б���":
			$aset['listname'] = $a[1];
			break;
		}
	}
		$aset=array_map("get_smarty_request",$aset);
		$aset['id']=$aset['id']?intval($aset['id']):0;
		$aset['listname']=$aset['listname']?$aset['listname']:"list";
		$aset['companynamelen']=isset($aset['companynamelen'])?intval($aset['companynamelen']):50;
		$wheresql.=" AND  user_status=1 ";
		$sql = "select id,uid,contents,website,logo,companyname,audit,address,map_open,map_x,map_y,map_zoom,resume_processing,nature_cn,trade_cn,scale_cn,district_cn from ".table('company_profile')." WHERE  id='{$aset['id']}' {$wheresql} LIMIT  1";
		$profile=$db->getone($sql);
		if (empty($profile))
		{
			header("HTTP/1.1 404 Not Found"); 
			$smarty->display("404.htm");
			exit();
		}
		else
		{
			$profile['company_url']=url_rewrite('QS_companyshow',array('id'=>$profile['id']));
			$profile['company_profile']=htmlspecialchars_decode($profile['contents'],ENT_QUOTES);
			if(intval($_CFG['subsite_id'])>0){
				$count_wheresql = ' and subsite_id='.intval($_CFG['subsite_id']).' ';
			}else{
				$count_wheresql = '';
			}
			// ����ְλ
			$profile['jobs_num']=$db->get_total("SELECT COUNT(*) AS num FROM ".table('jobs')." WHERE uid='{$profile['uid']}' ".$count_wheresql);
			// ����Ȥ����
			$profile['resume_num']=$db->get_total("SELECT COUNT(*) AS num FROM ".table('company_favorites')." WHERE company_uid='{$profile['uid']}' ");

			/* ��ҵ ���ͼƬ start */
			$profile['company_img_num']=$db->get_total("SELECT COUNT(*) AS num FROM ".table('company_img')." WHERE uid='{$profile['uid']}' and audit=1 ");
			$company_img=$db->getall("select img from ".table("company_img"). " WHERE uid='{$profile['uid']}' and audit=1 order by addtime desc ");
			$profile['company_img']=$company_img[0]['img'];
			foreach ($company_img as $key => $value)
			{
				$company_img[$key]=$_CFG['site_domain'].$_CFG['site_dir']."data/companyimg/original/".$value['img'];
			}
			$profile['company_img_big']=$company_img;
			/* ��ҵ ���ͼƬ end */

			if($profile['website']){
				if(strstr($profile['website'],"http://")===false){
					$profile['website'] = "http://".$profile['website'];
				}
			}
			if ($profile['logo'])
			{
			$profile['logo']=$_CFG['site_dir']."data/logo/".$profile['logo'];
			}
			else
			{
			$profile['logo']=$_CFG['site_dir']."data/logo/no_logo.gif";
			} 
			$row = $db->getone("select * from ".table("members_setmeal")." where uid = ".$profile['uid']); 
			$profile['pay_user'] = $row['setmeal_id'];
			$profile['pay_setmeal_name'] = $row['setmeal_name'];
			require_once(QISHI_ROOT_PATH.'include/fun_user.php');
			$profile['loginlog']=get_loginlog_one($profile['uid'],'1001');
		} 
	$smarty->assign($aset['listname'],$profile);
}
 
?>