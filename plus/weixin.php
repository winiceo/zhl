<?php
define('IN_QISHI', true);
require_once(dirname(__FILE__).'/../include/plus.common.inc.php');
define("TOKEN", $_CFG['weixin_apptoken']);
define("APPID", $_CFG['weixin_appid']);
define("APPSECRET", $_CFG['weixin_appsecret']);
define("ROOT",$_CFG['site_domain']);
define("FIRST_PIC",$_CFG['weixin_first_pic']);
define("DEFAULT_PIC",$_CFG['weixin_default_pic']);
define("SITE_NAME",$_CFG['site_name']);
define("WAP_DOMAIN",rtrim($_CFG['wap_domain'],"/")."/");
define("APIOPEN", $_CFG['weixin_apiopen']);
define("PHP_VERSION",PHP_VERSION);
define("EncodingAESKey",$_CFG['weixin_encoding_aes_key']);

require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
require_once(QISHI_ROOT_PATH.'include/weixinencodingaes/wxBizMsgCrypt.php');

class wechatCallbackapiTest extends mysql
{
	public $timestamp;
	public $nonce;
	public $msg_signature;
	public $encrypt_type;
	public $content;

	//��֤ǩ��
	public function valid()
    {
        $echoStr = $_GET["echostr"];
        if($this->checkSignature())
		{
        	exit($echoStr);
        }
    }
    //��Ӧ��Ϣ
    public function responseMsg()
    {
		if(!$this->checkSignature())
		{
        	exit();
        };
        $this->timestamp  = $_GET['timestamp'];
		$this->nonce = $_GET["nonce"];
		$this->msg_signature  = $_GET['msg_signature'];
		$this->encrypt_type = (isset($_GET['encrypt_type']) && ($_GET['encrypt_type'] == 'aes')) ? "aes" : "raw";

		$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
		if (!empty($postStr)){
			//����
			if ($this->encrypt_type == 'aes'){
				$pc = new WXBizMsgCrypt(TOKEN, EncodingAESKey, APPID);                
				$decryptMsg = "";  //���ܺ������
				$errCode = $pc->DecryptMsg($this->msg_signature, $this->timestamp, $this->nonce, $postStr, $decryptMsg);
				$postStr = $decryptMsg;
			}
			if($this->check_php_version("5.2.11")){
				libxml_disable_entity_loader(true);
			}
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $rxType = trim($postObj->MsgType);
             
            //��Ϣ���ͷ���
            switch ($rxType)
            {
                case "event":
                    $result = $this->receiveEvent($postObj);
                    break;
                case "text":
                    $result = $this->receiveText($postObj);
                    break;
                default:
                    $result = "unknown msg type: ".$rxType;
                    break;
            }
            //����
			if ($this->encrypt_type == 'aes'){
				$encryptMsg = ''; //���ܺ������
				$errCode = $pc->encryptMsg($result, $this->timeStamp, $this->nonce, $encryptMsg);
				$result = $encryptMsg;
			}
            echo $result;
        }else {
            echo "";
            exit;
        }
	}	
	//�����¼���Ϣ
    private function receiveEvent($object)
    {
    	global $_CFG;
        switch ($object->Event)
        {
            case "subscribe":
                $this->content = "��ӭ��ע".$_CFG['site_name']."��
1.������".$_CFG['site_name']."�ʺţ���ְ��Ƹ���ӷ��㣬��ʵʱ��������֪ͨ��<a href='".WAP_DOMAIN."binding.php?from=".$object->FromUserName."'>���������</a>
2.�����Իظ���j����n����ȡ������������Ƹ��Ϣ�����߻ظ�ְλ����˾�ȹؼ����硾���ۡ���ȡ���ְλ��Ϣ��
3.ÿ��ǩ������ѻ�ȡ���֣��̳Ǻ���Ҳ�ͣ��";
                break;
            case "LOCATION":
                  $map=(array)$object;
            	$cache_file_path =QISHI_ROOT_PATH . 'data/weixin/location/'.$object->FromUserName.'.php';
            	$content = "<?php\r\n";
			$content .= " return \$data=".var_export($map, true).";\r\n";
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
	            break;
            case "SCAN":
                $this->actionScan($object);
                break;
            case "CLICK":
            	$this->check_weixin_open($object);
                switch ($object->EventKey)
                {
                    case "binding"://��
                    	$this->clickBinding($object);
						break;
					case "resume_refresh"://ˢ�¼���
						$this->clickResumeRefresh($object);
						break;
					case "nearby_jobs"://�ܱ�ְλ
						$this->clickNearbyJobs($object);
						break;
					case "jobs_refresh"://ˢ��ְλ
						$this->clickJobsRefresh($object);
						break;
					case "sign_day"://ÿ��ǩ��
						$this->clickSignDay($object);
						break;
                }
                break;
                case "unsubscribe":
	            	$fromUsername = addslashes($object->FromUserName);
	                $sql = "update ".table('members')." set weixin_openid='' where weixin_openid='".$fromUsername."'";
			  		$this->query($sql);
                break;
            default:
                $this->content = "�ظ�j���ؽ�����Ƹ���ظ�n����������Ƹ�������Գ�������ְλ�����硰��ơ���ϵͳ���᷵����Ҫ�ҵ���Ϣ������Ŭ�����������Ի��ķ���ƽ̨��лл��ע��";
                break;
        }
        if(is_array($this->content)){
            if (isset($this->content[0])){
                $result = $this->transmitNews($object, $this->content);
            }
        }else{
            $result = $this->transmitText($object, $this->content);
        }

        return $result;
    }
    //�����ı���Ϣ
    private function receiveText($object)
    {
    	$this->check_weixin_open($object);
        $keyword = trim($object->Content);
        $keyword = utf8_to_gbk($keyword);
		$keyword = addslashes($keyword);
       
        //�Զ��ظ�ģʽ
        $this->enterSearch($object,$keyword);
        if(is_array($this->content)){
            if (isset($this->content[0]['PicUrl'])){
                $result = $this->transmitNews($object, $this->content);
            }
        }else{
            $result = $this->transmitText($object, $this->content);
        }
        return $result;
    }

    //�ظ��ı���Ϣ
    private function transmitText($object, $content)
    {
        $xmlTpl = "<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[text]]></MsgType>
<Content><![CDATA[%s]]></Content>
</xml>";
		$content = gbk_to_utf8($content);
        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time(), $content);
        return $result;
    }

    //�ظ�ͼ����Ϣ
    private function transmitNews($object, $newsArray)
    {
        if(!is_array($newsArray)){
            return;
        }
        $itemTpl = "    <item>
        <Title><![CDATA[%s]]></Title>
        <Description><![CDATA[%s]]></Description>
        <PicUrl><![CDATA[%s]]></PicUrl>
        <Url><![CDATA[%s]]></Url>
    </item>
";
        $item_str = "";
        foreach ($newsArray as $item){
        	$item = array_map("gbk_to_utf8", $item);
            $item_str .= sprintf($itemTpl, $item['Title'], $item['Description'], $item['PicUrl'], $item['Url']);
        }
        $xmlTpl = "<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[news]]></MsgType>
<ArticleCount>%s</ArticleCount>
<Articles>
$item_str</Articles>
</xml>";

        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time(), count($newsArray));
        return $result;
    }
    //��֤ǩ��
	private function checkSignature()
	{
	    $signature = $_GET["signature"];
	    $timestamp = $_GET["timestamp"];
	    $nonce = $_GET["nonce"];        		
		$token = TOKEN;
		$tmpArr = array($token, $timestamp, $nonce);
		sort($tmpArr, SORT_STRING);
		$tmpStr = implode( $tmpArr );
		$tmpStr = sha1( $tmpStr );		
		if($tmpStr == $signature )
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	private function get_user_info($fromUsername){
		$usinfo = array();
		$fromUsername = addslashes($fromUsername);
		$usinfo_obj = $this->query("select * from ".table('members')." where weixin_openid='".$fromUsername."' limit 1");
		while($row = $this->fetch_array($usinfo_obj)){
			$usinfo = $row;
		}
		return $usinfo;
	}
	// ��ȡ��Ա����
	private function get_user_points($uid){
		$uid=intval($uid);
		$points = $this->getone("select points from ".table('members_points')." where uid='".$uid."' limit 1");
		return $points['points'];
	}
	// ��ȡ���ֹ���
	private function get_points_rule()
	{
		$cache_file_path =QISHI_ROOT_PATH. "data/cache_points_rule.php";
		@include($cache_file_path);
		return $data;
	}
	// ������Ա����
	private function report_deal($uid,$i_type=1,$points=0)
	{
		$points=intval($points);
		$uid=intval($uid);
		$points_val=$this->get_user_points($uid);
		if ($i_type==1)
		{
		$points_val=$points_val+$points;
		}
		if ($i_type==2)
		{
		$points_val=$points_val-$points;
		$points_val=$points_val<0?0:$points_val;
		}
		$sql = "UPDATE ".table('members_points')." SET points= '{$points_val}' WHERE uid='{$uid}' LIMIT 1";
		if (!$this->query($sql))return false;
		return true;
	}
	// ��ȡ�ײ�
	private function get_user_setmeal($uid)
	{
		$uid=intval($uid);
		$sql = "select * from ".table('members_setmeal')."  WHERE uid='{$uid}' AND  effective=1 LIMIT 1";
		return $this->getone($sql);
	}
	//�鿴��Ա����־
	private function get_last_refresh_date($uid,$type,$mode=0)
	{
		$sql = "select max(addtime) from ".table('refresh_log')." where uid=".intval($uid).' and ' . "`type`='".$type."' and mode = ".$mode;
		return $this->getone($sql);
	}
	//ͳ�ƽ���ˢ�´���
	private function get_today_refresh_times($uid,$type,$mode=0)
	{
		$today = strtotime(date('Y-m-d'));
		$tomorrow = $today+3600*24;
		$sql = "select count(*) from ".table('refresh_log')." where uid=".intval($uid).' and ' . "`type`='".$type."' and addtime>".$today." and addtime<".$tomorrow." and mode = ".$mode;
		return $this->getone($sql);
	}
	private function write_refresh_log($uid,$type,$mode=0)
	{
		$setsqlarr['uid'] = $uid;
		$setsqlarr['mode'] = $mode;
		$setsqlarr['type'] = $type;
		$setsqlarr['addtime'] = time();
		$this->inserttable(table('refresh_log'),$setsqlarr);
	}
	private function write_memberslog($uid,$utype,$type,$username,$str,$mode,$op_type,$op_type_cn,$op_used,$op_leave)
	{
	 	global $online_ip,$ip_address;
	 	$setarr["log_uid"]=$uid;
	 	$setarr["log_username"]=$username;
	 	$setarr["log_utype"]=$utype;
	 	$setarr["log_type"]=$type;
	 	$setarr["log_addtime"]=time();
	 	$setarr["log_ip"]=$online_ip;
	 	$setarr["log_address"]=$ip_address;
	 	$setarr["log_value"]=$str;
	 	$setarr["log_mode"]=$mode;
	 	$setarr["log_op_type"]=$op_type;
	 	$setarr["log_op_type_cn"]=$op_type_cn;
	 	$setarr["log_op_used"]=$op_used;
	 	$setarr["log_op_leave"]=$op_leave;
	 	return $this->inserttable(table("members_log"),$setarr);
	}
	private function refresh_jobs($uid)
	{
		global $_CFG;
		$uid=intval($uid);
		$time=time();
		$deadline=strtotime("".intval($_CFG['company_add_days'])." day");
		if (!$this->query("update  ".table('company_profile')."  SET refreshtime='{$time}' WHERE uid='{$uid}' LIMIT 1 ")) return false;
		if (!$this->query("update  ".table('jobs')."  SET refreshtime='{$time}',deadline='{$deadline}' WHERE  uid='{$uid}'")) return false;
		if (!$this->query("update  ".table('jobs_tmp')."  SET refreshtime='{$time}' WHERE  uid='{$uid}'")) return false;
		if (!$this->query("update  ".table('jobs_search_hot')."  SET refreshtime='{$time}' WHERE  uid='{$uid}'")) return false;
		if (!$this->query("update  ".table('jobs_search_key')."  SET refreshtime='{$time}' WHERE  uid='{$uid}'")) return false;
		if (!$this->query("update  ".table('jobs_search_rtime')."  SET refreshtime='{$time}' WHERE  uid='{$uid}'")) return false;
		if (!$this->query("update  ".table('jobs_search_scale')."  SET refreshtime='{$time}' WHERE  uid='{$uid}'")) return false;
		if (!$this->query("update  ".table('jobs_search_stickrtime')."  SET refreshtime='{$time}' WHERE  uid='{$uid}'")) return false;
		if (!$this->query("update  ".table('jobs_search_wage')."  SET refreshtime='{$time}' WHERE  uid='{$uid}'")) return false;
		return true;
	}
	private function check_php_version($version) {
		 $php_version = explode('-',phpversion());
		 // strnatcasecmp( $php_version[0], $version ) 0��ʾ���ڣ�1��ʾ���ڣ�-1��ʾС��
		 $is_pass = strnatcasecmp($php_version[0],$version)>=0?true:false;
		 return $is_pass;
	}
    //�����վ΢�Žӿ��Ƿ���
	private function check_weixin_open($object){
		if(APIOPEN=='0')
		{
			$this->content="��վ΢�Žӿ��Ѿ��ر�";
			$this->transmitText($object,$this->content);
		}
	}
    //���¼�
	private function clickBinding($object){
		global $_CFG;
		$usinfo = $this->get_user_info($object->FromUserName);
		if(!empty($usinfo)){

			$this->content="���Ѱ󶨹�".$_CFG['site_name']."�ʺš�".$usinfo['username']."��,������,��ظ�'���'";
		}else
		{
			$this->content="����δ��".$_CFG['site_name']."�ʺţ����ڿ�ʼ�󶨣�<a href='".WAP_DOMAIN."binding.php?from=".$object->FromUserName."'>�����ʼע��/���ʺ�</a>";
		}
	}
    //ˢ�¼���
	private function clickResumeRefresh($object)
	{
		global $_CFG;
		$usinfo = $this->get_user_info($object->FromUserName);
		if(!empty($usinfo)){
			if($usinfo['utype']!=2)
			{
				$this->content = "��������Ҫ�󶨸����ʺţ�";
			}
			else
			{	
				$uid = $usinfo['uid'];
				$refrestime=$this->get_last_refresh_date($uid,"2001");
				$duringtime=time()-$refrestime['max(addtime)'];
				$space = $_CFG['per_refresh_resume_space']*60;
				$refresh_time = $this->get_today_refresh_times($uid,"2001");
				if($_CFG['per_refresh_resume_time']!=0 && ($refresh_time['count(*)']>=$_CFG['per_refresh_resume_time']))
				{
					$this->content = "ÿ�����ֻ��ˢ��".$_CFG['per_refresh_resume_time']."��,�������ѳ������ˢ�´������ƣ�";
				}
				elseif($duringtime<=$space)
				{
					$this->content = $_CFG['per_refresh_resume_space']."�����ڲ����ظ�ˢ�¼�����";
				}
				else
				{
					$resume_num = $this->get_total("select count(*) as num from ".table('resume')." where uid=".$uid);
					$rule=$this->get_points_rule();
					$user_points=$this->get_user_points();
					// �жϻ����Ƿ��㹻
					if($rule['resume_refresh']['type']==2 && $user_points<$rule['resume_refresh']['value']*$resume_num)
					{
						$this->content = "���Ļ��ֲ��㲻��ˢ�¼�����";
					}
					else
					{
						$time = time();
						$this->query("update ".table('resume')." set refreshtime=".$time." where uid=".$uid);
						$this->query("update ".table('resume_search_key')." set refreshtime=".$time." where uid=".$uid);
						$this->query("update ".table('resume_search_rtime')." set refreshtime=".$time." where uid=".$uid);
						// ˢ����־
						for ($i=$resume_num; $i > 0 ; $i--) { 
							write_refresh_log($uid,2001);
						}
						/*���ֲ��� ----*/
						$this->report_deal($uid,$rule['resume_refresh']['type'],$rule['resume_refresh']['value']*$resume_num);
						$this->content = "ˢ�³ɹ�!";
					}
				}
			}
		}else{
			$this->content="����δ��".$_CFG['site_name']."�ʺţ����ڿ�ʼ�󶨣�<a href='".WAP_DOMAIN."binding.php?from=".$object->FromUserName."'>�����ʼע��/���ʺ�</a>";
		}
	}
	// �ܱ�ְλ
	private function clickNearbyJobs($object){
		global $_CFG;
		$usinfo = $this->get_user_info($object->FromUserName);
		if(!empty($usinfo)){
			if($usinfo['utype']!=2)
			{
				$this->content = "��������Ҫ�󶨸����ʺţ�";
			}
			else
			{
				require_once(QISHI_ROOT_PATH . 'data/weixin/location/'.$object->FromUserName.'.php');
				if(empty($data['Latitude']) || empty($data['Longitude']))
				{
					$this->content = "����ˣ��ѵ�HR�Ƕ�͵���ż�ȥ�ˣ���ʲôְλ��û���ţ�Ҫ���������ط��ٷ�λ�ø��ң���η��͵���λ�ã�";
				}
				$lat = $data['Latitude'];
				$lng = $data['Longitude'];
				$jobstable=table('jobs_search_key');
				$rows = 5;
				$offset = 0;
				// ��ȡ�ܱ�ְλ
				if(!empty($lng) && !empty($lat))
				{
					$idresult = $this->query("SELECT id , ROUND(6378.138*2*ASIN(SQRT(POW(SIN((".$lat."*PI()/180-map_y*PI()/180)/2),2)+COS(".$lat."*PI()/180)*COS(map_y*PI()/180)*POW(SIN((".$lng."*PI()/180-map_x*PI()/180)/2),2)))*1000) AS juli FROM {$jobstable}  WHERE map_x!='' AND map_y!='' ORDER BY juli ASC   LIMIT {$offset},{$rows}");
					while($row = $this->fetch_array($idresult))
					{
						$id[]=$row['id'];
					}
				}
				if (!empty($id))
				{
					$wheresql=" WHERE id IN (".implode(',',$id).") ";
					$sql = "SELECT *, ROUND(6378.138*2*ASIN(SQRT(POW(SIN((".$lat."*PI()/180-map_y*PI()/180)/2),2)+COS(".$lat."*PI()/180)*COS(map_y*PI()/180)*POW(SIN((".$lng."*PI()/180-map_x*PI()/180)/2),2)))*1000) AS juli FROM ".table('jobs').$wheresql."  ORDER BY juli ASC , stick DESC , refreshtime DESC limit 3";
					$jobs_list = $this->getall($sql);
				}
				if(empty($jobs_list))
				{
					$this->content="����ˣ��ѵ�HR�Ƕ�͵���ż�ȥ�ˣ���ʲôְλ��û���ţ�Ҫ���������ط��ٷ�λ�ø��ң���η��͵���λ�ã�";
				}
				else
				{
					$i=1;
					foreach ($jobs_list as $key => $value)
					{
						$jobs_name=$value['jobs_name'];
						$jobs_name=$value['jobs_name'];				
					    $companyname=$value['companyname'];
					    $title=$jobs_name."--".$companyname;
					    $url=WAP_DOMAIN."jobs-show.php?id=".$value['id']."&from=".$object->FromUserName;
					    if($i==1){
		                    $picurl=$first_pic;	
						}else{
							$picurl=$default_pic;	
						}
						$i++;
						$this->content[] = array("Title"=>$title, "Description"=>$con, "PicUrl"=>$picurl, "Url" =>$url);	
					}
				}
			}
		}else{
			$this->content="����δ��".$_CFG['site_name']."�ʺţ����ڿ�ʼ�󶨣�<a href='".WAP_DOMAIN."binding.php?from=".$object->FromUserName."'>�����ʼע��/���ʺ�</a>";
		}
	}
	// ˢ��ְλ
	private function clickJobsRefresh($object)
	{
		global $_CFG;
		$usinfo = $this->get_user_info($object->FromUserName);
		if(!empty($usinfo)){
			if($usinfo['utype']!=1)
			{
				$this->content = "��������Ҫ����ҵ�ʺţ�";
			}
			else
			{
				$uid = $usinfo['uid'];
				$jobs_num= $this->get_total("select count(*) as num from ".table('jobs')." where uid=$uid ");
				//����ģʽ
				if($_CFG['operation_mode']=='1')
				{
					$mode = 1;
					//����ˢ��ʱ��
					//���һ�ε�ˢ��ʱ��
					$refrestime=$this->get_last_refresh_date($uid,"1001",1);
					$duringtime=time()-$refrestime['max(addtime)'];
					$space = $_CFG['com_pointsmode_refresh_space']*60;
					$refresh_time = $this->get_today_refresh_times($uid,"1001",1);
					if($_CFG['com_pointsmode_refresh_time']!=0&&($refresh_time['count(*)']>=$_CFG['com_pointsmode_refresh_time']))
					{
					$this->content = "ÿ�����ֻ��ˢ��".$_CFG['com_pointsmode_refresh_time']."��,�������ѳ������ˢ�´������ƣ�";
					}
					elseif($duringtime<=$space)
					{
					$this->content = $_CFG['com_pointsmode_refresh_space']."�����ڲ����ظ�ˢ��ְλ��";
					}
					else 
					{
						$points_rule=$this->get_points_rule();
						if($points_rule['jobs_refresh']['value']>0)
						{
							$user_points=$this->get_user_points();
							$total_point=$jobs_num*$points_rule['jobs_refresh']['value'];
							if ($total_point>$user_points && $points_rule['jobs_refresh']['type']=="2")
							{
							$this->content = "����".$_CFG['points_byname']."���㣬���ȳ�ֵ��";
							}
							//��/�� ����
							$this->report_deal($uid,$points_rule['jobs_refresh']['type'],$total_point);
							$user_points=$this->get_user_points($uid);
							$operator=$points_rule['jobs_refresh']['type']=="1"?"+":"-";
							$this->write_memberslog($uid,1,9001,$usinfo['username'],"ˢ����{$jobs_num}��ְλ��({$operator}{$total_point})��(ʣ��:{$user_points})",1,1003,"ˢ��ְλ","{$operator}{$total_point}","{$user_points}");
							for ($i=0; $i < $jobs_num; $i++) { 
								$this->write_refresh_log($_SESSION['uid'],1001,$mode);
							}
							$this->refresh_jobs($uid);
							$this->content = "ˢ�³ɹ���";
						}
						else
						{
							for ($i=0; $i < $jobs_num; $i++) { 
								$this->write_refresh_log($_SESSION['uid'],1001,$mode);
							}
							$this->refresh_jobs($uid);
							$this->content = "ˢ�³ɹ���";
						}
					}
				}	
				//�ײ�ģʽ
				elseif($_CFG['operation_mode']=='2') 
				{
					$mode = 2;
					//����ˢ��ʱ��
					$setmeal=$this->get_user_setmeal($uid);
					if (empty($setmeal))
					{			
						$this->content = "����û�п�ͨ�����뿪ͨ";
					}
					elseif ($setmeal['endtime']<time() && $setmeal['endtime']<>"0")
					{		
						$this->content = "���ķ����Ѿ����ڣ������¿�ͨ";
					}
					else
					{
						//���һ�ε�ˢ��ʱ��
						$refrestime=$this->get_last_refresh_date($uid,"1001",2);
						$duringtime=time()-$refrestime['max(addtime)'];
						$space = $setmeal['refresh_jobs_space']*60;
						$refresh_time = $this->get_today_refresh_times($uid,"1001",2);
						if($setmeal['refresh_jobs_time']!=0&&($refresh_time['count(*)']>=$setmeal['refresh_jobs_time']))
						{
							$this->content = "ÿ�����ֻ��ˢ��".$setmeal['refresh_jobs_time']."��,�������ѳ������ˢ�´������ƣ�";
						}
						elseif($duringtime<=$space){
							$this->content = $setmeal['refresh_jobs_space']."�����ڲ����ظ�ˢ��ְλ��";
						}
						else
						{
							for ($i=0; $i < $jobs_num; $i++) { 
								$this->write_refresh_log($_SESSION['uid'],1001,$mode);
							}
							$this->refresh_jobs($uid);
							$this->content = "ˢ�³ɹ���";
						}
					}
				}
				//���ģʽ
				elseif($_CFG['operation_mode']=='3') 
				{
					$setmeal=$this->get_user_setmeal($_SESSION['uid']);
					//�û�Ա�ײ͹��� (�ײ͹��ں���û�����ˢ)
					if($setmeal['endtime']<time() && $setmeal['endtime']<>"0")
					{
						//��̨��ͨ  ������ʱ���û�������
						if($_CFG['setmeal_to_points']=='1')
						{
							$mode = 1;
							//�û�����ˢ��ְλ�Ļ�->���ջ���ģʽ����->�ȿ����Ƿ񳬹��������ƺ�ʱ����
							$refrestime=$this->get_last_refresh_date($uid,"1001",1);
							$duringtime=time()-$refrestime['max(addtime)'];
							$space = $setmeal['refresh_jobs_space']*60;
							$refresh_time = $this->get_today_refresh_times($uid,"1001",1);
							if($_CFG['com_pointsmode_refresh_time']!=0&&($refresh_time['count(*)']>=$_CFG['com_pointsmode_refresh_time']))
							{
								$this->content = "�����ײ��Ѿ����ڣ�ˢ��ְλ�����Ļ��֣������û���ˢ��ְλÿ�����ֻ��ˢ��".$_CFG['com_pointsmode_refresh_time']."��,�������ѳ������ˢ�´������ƣ������������ײͣ�";
							}
							elseif($duringtime<=$space)
							{
								$this->content = "�����ײ��Ѿ����ڣ�ˢ��ְλ�����Ļ��֣������û���ˢ��ְλ".$_CFG['com_pointsmode_refresh_space']."�����ڲ����ظ�ˢ�£�";
							}
							else
							{
								$points_rule=$this->get_points_rule;
								if($points_rule['jobs_refresh']['value']>0)
								{
									$user_points=$this->get_user_points($_SESSION['uid']);
									$total_point=$jobs_num*$points_rule['jobs_refresh']['value'];
									if ($total_point>$user_points && $points_rule['jobs_refresh']['type']=="2")
									{
										$this->content ="�����ײ��ѹ��ڣ������Ļ�����ˢ��ְλ����Ŀǰ����".$_CFG['points_byname']."���㣬���ȳ�ֵ���ֻ����������ײͣ�";
									}
									//��/�� ����
									$this->report_deal($uid,$points_rule['jobs_refresh']['type'],$total_point);
									$user_points=$this->get_user_points($uid);
									$operator=$points_rule['jobs_refresh']['type']=="1"?"+":"-";
									$this->write_memberslog($uid,1,9001,$usinfo['username'],"ˢ����{$jobs_num}��ְλ��({$operator}{$total_point})��(ʣ��:{$user_points})",1,1003,"ˢ��ְλ","{$operator}{$total_point}","{$user_points}");
									for ($i=0; $i < $jobs_num; $i++) { 
										$this->write_refresh_log($_SESSION['uid'],1001,$mode);
									}
									$this->refresh_jobs($uid);
									$this->content = "ˢ�³ɹ���";
								}
								else
								{
									for ($i=0; $i < $jobs_num; $i++) { 
										$this->write_refresh_log($_SESSION['uid'],1001,$mode);
									}
									$this->refresh_jobs($uid);
									$this->content = "ˢ�³ɹ���";
								}
							}
						}
						//��̨û�п�ͨ  ������ʱ���û�������
						else
						{
							$this->content = "���ķ����Ѿ����ڣ������¿�ͨ";
						}
					}
					//�û�Ա�ײ�δ���� 
					else
					{
						$mode = 2;
						$points_rule=$this->get_points_rule();
						$user_points=$this->get_user_points($uid);
						//��ȡ����ˢ�µ�ְλ��(���ײ�ģʽ��ˢ�µ�)
						$refresh_time = $this->get_today_refresh_times($uid,"1001",2);
						//����ʣ��ˢ��ְλ��(���ײ�ģʽ��ˢ�µ�)
						$surplus_time =  $setmeal['refresh_jobs_time'] - $refresh_time['count(*)'];
						//ˢ��ְλ�� ���� ʣ��ˢ��ְλ�� (����)
						if($setmeal['refresh_jobs_time']!=0&&($jobs_num>$surplus_time))
						{
							//��̨��ͨ  ������ʱ���û�������
							if($_CFG['setmeal_to_points']=='1')
							{
								//�жϵ������ˢ��ְλ�� �Ƿ� ���������ͼ������
								$refrestime=$this->get_last_refresh_date($uid,"1001",1);
								$duringtime=time()-$refrestime['max(addtime)'];
								$space = $_CFG['com_pointsmode_refresh_space']*60;
								$refresh_time = $this->get_today_refresh_times($uid,"1001",1);
								if($_CFG['com_pointsmode_refresh_time']!=0&&($refresh_time['count(*)']>=$_CFG['com_pointsmode_refresh_time']))
								{
								$this->content = "ˢ��ְλ���������ײʹ������ƣ�ˢ��ְλ�����Ļ��֣�ÿ���û���ˢ�����ֻ��ˢ��".$_CFG['com_pointsmode_refresh_time']."��,�������ѳ������ˢ�´������ƣ�";
								}
								elseif($duringtime<=$space)
								{
									$this->content = "ˢ��ְλ���������ײʹ������ƣ�ˢ��ְλ�����Ļ��֣�����".$_CFG['com_pointsmode_refresh_space']."�����ڲ����ظ�ˢ��ְλ��";			
								}
								else
								{
									if($points_rule['jobs_refresh']['value']>0)
									{
										//������ְλ����ˢ�� ����Ļ���
										$beyond = $jobs_num - $surplus_time;
										$surplus_total_point=$beyond*$points_rule['jobs_refresh']['value'];
										//��Ա���ֲ��������� �������
										if ($surplus_total_point>$user_points && $points_rule['jobs_refresh']['type']=="2")
										{
											$this->content = "��ˢ��ְλ���������ײʹ������ƣ������Ĵ������������Ļ��֣���������".$_CFG['points_byname']."���㣬���ȳ�ֵ��";
										}
										//�жϳ�����ְλ���Ƿ� ���� �������ƴ���
										if($beyond > $_CFG['com_pointsmode_refresh_time'] && $_CFG['com_pointsmode_refresh_time']!=0)
										{
											$this->content = "��ˢ��ְλ���������ײʹ������ƣ�������ְλ�����������Ļ��֣�����Ҳ������".$_CFG['points_byname']."���ƴ���!";
										}
										for ($i=0; $i < $surplus_time; $i++) 
										{ 
											$this->refresh_jobs($uid);
											$this->$this->write_memberslog($uid,1,2004,$usinfo['username'],"ˢ��ְλ");
											$this->write_refresh_log($_SESSION['uid'],1001,2);
										}
										for ($i=$surplus_time; $i < $jobs_num; $i++) 
										{ 
											$this->refresh_jobs($uid);
											$this->write_memberslog($_SESSION['uid'],1,2004,$_SESSION['username'],"ˢ��ְλ");
											$this->write_refresh_log($uid,1001,1);
										}
										//���»�Ա����
										//��/�� ����
										$this->report_deal($uid,$points_rule['jobs_refresh']['type'],$surplus_total_point);
										$user_points=$this->get_user_points($uid);
										$operator=$points_rule['jobs_refresh']['type']=="1"?"+":"-";
										$this->write_memberslog($uid,1,9001,$usinfo['username'],"ˢ����{$jobs_num}��ְλ��({$operator}{$total_point})��(ʣ��:{$user_points})",1,1003,"ˢ��ְλ","{$operator}{$total_point}","{$user_points}");
										$this->content = "ˢ�³ɹ�!";
									}
									else
									{
										for ($i=0; $i < $jobs_num; $i++) { 
											$this->write_refresh_log($_SESSION['uid'],1001,$mode);
										}
										$this->refresh_jobs($uid);
										$this->content = "ˢ�³ɹ���";
									}
								}
							}
							//��̨û�п�ͨ  ������ʱ���û�������
							else
							{
								$this->content = "��ˢ��ְλ���������ײʹ�������!";
							}
						}
						//ˢ��ְλ�� С�� ʣ��ˢ��ְλ�� (û��)
						else
						{
							//���һ�ε�ˢ��ʱ��
							$refrestime=$this->get_last_refresh_date($uid,"1001",2);
							$duringtime=time()-$refrestime['max(addtime)'];
							$space = $setmeal['refresh_jobs_space']*60;
							$refresh_time = $this->get_today_refresh_times($uid,"1001",2);
							if($setmeal['refresh_jobs_time']!=0&&($refresh_time['count(*)']>=$setmeal['refresh_jobs_time']))
							{
								$this->content = "ÿ�����ֻ��ˢ��".$setmeal['refresh_jobs_time']."��,�������ѳ������ˢ�´������ƣ�";
							}
							elseif($duringtime<=$space)
							{
								$this->content = $setmeal['refresh_jobs_space']."�����ڲ����ظ�ˢ��ְλ��";	
							}
							else
							{
								for ($i=0; $i < $jobs_num; $i++) { 
									$this->write_refresh_log($_SESSION['uid'],1001,$mode);
								}
								$this->refresh_jobs($uid);
								$this->content = "ˢ�³ɹ���";
							}
						}
					}
				}
			}
		}else{
			$this->content="����δ��".$_CFG['site_name']."�ʺţ����ڿ�ʼ�󶨣�<a href='".WAP_DOMAIN."binding.php?from=".$object->FromUserName."'>�����ʼע��/���ʺ�</a>";
		}
	}
	// ÿ��ǩ��
	private function clickSignDay($object)
	{
		global $_CFG;
		$usinfo = $this->get_user_info($object->FromUserName);
		if(!empty($usinfo))
		{
			// ���ֲ������� 
			$uid = $usinfo['uid'];
			$time =time();
			$today=mktime(0, 0, 0,date('m'), date('d'), date('Y'));
			$info=$this->getone("SELECT uid FROM ".table('members_handsel')." WHERE uid =".$uid." AND htype='sign_day' AND addtime>{$today}  LIMIT 1");
			if(!empty($info))
			{
				$this->content = "������ǩ��������������!";
			}
			else
			{
				$members_handsel_arr['uid']=$uid;
				$members_handsel_arr['htype']="sign_day";
				$members_handsel_arr['points']=$_CFG['weixin_signday'];
				$members_handsel_arr['addtime']=$time;
				$this->inserttable(table("members_handsel"),$members_handsel_arr);
				/*
					���ӻ���
				*/
				$this->report_deal($uid,$i_type=1,$_CFG['weixin_signday']);
				$this->content = "ǩ���ɹ������ ".$_CFG['weixin_signday']." ���֣��ظ���ǩ����¼���鿴��ʷǩ����
ÿ��ǩ������ѻ�ȡ���֣��̳Ǻ���Ҳ�ͣ����������Ѽ���ɣ�";
			}
		}
		else{
			$this->content="����δ��".$_CFG['site_name']."�ʺţ����ڿ�ʼ�󶨣�<a href='".WAP_DOMAIN."binding.php?from=".$object->FromUserName."'>�����ʼע��/���ʺ�</a>";
		}
	}
    //ɨ���¼�
	private function actionScan($object)
	{
		global $_CFG;
		$event_key = $object->EventKey;
		if($event_key<=10000000)
		{
			$usinfo = $this->get_user_info($object->FromUserName);
			if(!empty($usinfo)){
				$this->content = "<a href='".WAP_DOMAIN."login.php?act=weixin_login&openid=".$object->FromUserName."&uid=".$usinfo['uid']."&event_key=".$event_key."'>���������¼".SITE_NAME."��ҳ</a>";
			}else{
				$this->content="����δ��".$_CFG['site_name']."�ʺţ����ڿ�ʼ�󶨣�<a href='".WAP_DOMAIN."binding.php?from=".$object->FromUserName."'>�����ʼע��/���ʺ�</a>";
			}
		}
		elseif($event_key>10000000 && $event_key<=20000000)
		{
			$this->content = "��ѡ���Աע������.<a href='".WAP_DOMAIN."login.php?act=weixin_reg&openid=".$object->FromUserName."&event_key=".$event_key."&utype=1'>��ҵ��Ա</a>��<a href='".WAP_DOMAIN."login.php?act=weixin_reg&openid=".$object->FromUserName."&event_key=".$event_key."&utype=2'>���˻�Ա</a>";
		}
		elseif($event_key>20000000 && $event_key<=30000000)
		{
			$usinfo = $this->get_user_info($object->FromUserName);
			if($usinfo){
				
				$this->content="���Ѱ󶨹�".$_CFG['site_name']."�ʺš�".$usinfo['username']."������������ظ������";
			}else{
				$fp = @fopen(QISHI_ROOT_PATH . 'data/weixin/'.($event_key%10).'/'.$event_key.'.txt', 'wb+');
				@fwrite($fp, $object->FromUserName);
				@fclose($fp);
				$this->content="��ϲ�����ѳɹ���".$_CFG['site_name'].",�´ο�ֱ��ɨ���¼��Ŷ����������ظ������";
			}
		}
	}
	
    //����ؼ�������ְλ
	private function enterSearch($object,$keyword){
		if($keyword=="���")
		{
			$usinfo = $this->get_user_info($object->FromUserName);
			if(!empty($usinfo))
			{
				$this->query("UPDATE ".table('members')." SET weixin_openid=null,weixin_nick='' WHERE weixin_openid='".$object->FromUserName."'");
				$this->content="����󶨳ɹ���";
			}
			else
			{
				$this->content="����û�а��ʺţ�";
			}
		}
		elseif($keyword=="ǩ����¼")
		{
			$usinfo = $this->get_user_info($object->FromUserName);
			if(!empty($usinfo))
			{
				$uid = $usinfo['uid'];
				$time =time();
				$month_time = strtotime(date('Y-m-01',$time));
				$all_num=$this->get_total("SELECT count(*) as num FROM ".table('members_handsel')." WHERE uid =".$uid." AND htype='sign_day'");
				$month_num=$this->get_total("SELECT count(*) as num FROM ".table('members_handsel')." WHERE uid =".$uid." AND htype='sign_day' and addtime>=".$month_time." and addtime<=".$time);

				$points =$this->getone("SELECT sum(points) as all_points FROM ".table('members_handsel')." WHERE uid =".$uid." AND htype='sign_day'");
				$info =$this->getone("SELECT addtime FROM ".table('members_handsel')." WHERE uid =".$uid." AND htype='sign_day' order by addtime desc limit 1");
				
				$this->content="���ۼ���ǩ��: ".$all_num." ��
���������ۼ�ǩ��:".$month_num." ��
���ϴ�ǩ��ʱ��:".date('Y-m-d H:i:s',$info['addtime'])."
��Ŀǰ��õ��ܽ���Ϊ:".$points['all_points']."����";
			}
			else
			{
				$this->content="����û�а��ʺţ�";
			}
		}
		else
		{
			$default_pic=ROOT."/data/images/".DEFAULT_PIC;
			$first_pic=ROOT."/data/images/".FIRST_PIC;
			$limit=" LIMIT 5";
			$orderbysql=" ORDER BY refreshtime DESC";
			if($keyword=="n")
			{
				$jobstable=table('jobs_search_rtime');			 
			}
			else if($keyword=="j")
			{
				$jobstable=table('jobs_search_rtime');
				$wheresql=" where `emergency`=1 ";	
			}
			else
			{
			$jobstable=table('jobs_search_key');
			$wheresql.=" where likekey LIKE '%{$keyword}%' ";
			}
			$list = $id = array();
			$idresult = $this->query("SELECT id FROM {$jobstable} ".$wheresql.$orderbysql.$limit);
			while($row = $this->fetch_array($idresult))
			{
			$id[]=$row['id'];
			}
			if (!empty($id))
			{
				$wheresql=" WHERE id IN (".implode(',',$id).") ";
				$result = $this->query("SELECT * FROM ".table('jobs').$wheresql.$orderbysql);
				$i=1;
				while($row = $this->fetch_array($result))
				{
					$jobs_name=$row['jobs_name'];				
				    $companyname=$row['companyname'];
				    $title=$jobs_name."--".$companyname;
				    $url=WAP_DOMAIN."jobs-show.php?id=".$row['id']."&from=".$object->FromUserName;
				    if($i==1){
	                    $picurl=$first_pic;	
					}else{
						$picurl=$default_pic;	
					}
					$i++;
					$this->content[] = array("Title"=>$title, "Description"=>$con, "PicUrl"=>$picurl, "Url" =>$url);	
				}
			}
			if(empty($this->content))
			{
				$this->content="û���ҵ������ؼ��� {$keyword} ����Ϣ�����������ؼ���";
			}
		}
	}
}

$wechatObj = new wechatCallbackapiTest($dbhost,$dbuser,$dbpass,$dbname);
if (!isset($_REQUEST['echostr'])) {
    $wechatObj->responseMsg();
}else{
    $wechatObj->valid();
}		
?>