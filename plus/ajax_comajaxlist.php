<?php
 /*
 * 74cms ajax ���ع�˾ְλ����
*/
define('IN_QISHI', true);
require_once(dirname(dirname(__FILE__)).'/include/plus.common.inc.php');
$act = !empty($_POST['act']) ? trim($_POST['act']) :trim($_GET['act']);
if ($act=='show_jobs_more')
{
	$comjobshtml="";
	$rows=intval($_GET['rows']);
	$offset=intval($_GET['offset']); 
	$companyid=intval($_GET['companyid']); 
	$comjobsarray=$db->getall("select * from ".table('jobs')." where company_id = '{$companyid}' ORDER BY stick DESC , refreshtime DESC LIMIT {$offset},{$rows}");
	if (!empty($comjobsarray) && $offset<=100)
	{
		foreach($comjobsarray as $li)
		{
			$jobs_url=url_rewrite("QS_jobsshow",array('id'=>$li['id']),1,$li['subsite_id']);
			$jobs_name=cut_str($li['jobs_name'],"10",0,"..");
			$comjobshtml.="<li class=\"listbox\" id=\"li-{$offset}\">
			<div class=\"j_name\"><a href=\"{$jobs_url}\" target=\"_blank\">{$jobs_name}</a></div>
			<span class=\"ji\"></span>
			<div class=\"clear\"></div>
			<p class=\"time_r\">".date('Y-m-d',$li['addtime'])."����</p>
			<p>
			<span>ѧ��Ҫ��{$li['education_cn']}</span>
			&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;
			<span>�������飺{$li['experience_cn']}</span>
			&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;
			<span>�����ص㣺{$li['district_cn']}</span>
			&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;
			<span>н�ʴ�����<em>{$li['wage_cn']}</em></span>
			</p>
			</li>";
		}
		exit($comjobshtml);
	}
	else
	{
		exit('empty!');
	}
}
elseif ($act=='show_news_more')
{
	$comnewshtml="";
	$rows=intval($_GET['rows']);
	$offset=intval($_GET['offset']); 
	$companyid=intval($_GET['companyid']); 
	$comnewsarray=$db->getall("select * from ".table('company_news')." where company_id = '{$companyid}' ORDER BY `order` DESC,id DESC LIMIT {$offset},{$rows}");
	if (!empty($comnewsarray) && $offset<=100)
	{
		foreach($comnewsarray as $li)
		{
			$title=cut_str($li['title'],"10",0,"..");
			$url = url_rewrite("QS_companynewsshow",array('id'=>$li['id']));
			$content=str_replace('&nbsp;','',$li['content']);
			$briefly=cut_str(strip_tags($content),"100",0,"..");
			$comnewshtml.="<li class=\"listbox\" id=\"li-{$offset}\">
			<div class=\"new_tit\"><a href=\"{$url}\" target=\"_blank\">{$title}</a></div>
			<p>����ʱ�䣺".date('Y-m-d',$li['addtime'])." &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span>���������".$li['click']."</span></p>
			<p>".$briefly."<a href=\"{$url}\" target=\"_blank\">[�鿴ȫ��]</a></p>
			</li>";
		}
		exit($comnewshtml);
	}
	else
	{
		exit('empty!');
	}
}
?>