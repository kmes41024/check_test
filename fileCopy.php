<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'include/function_debug.php');
	header("Content-type: text/html;charset=utf-8");
	require_once($_SERVER['DOCUMENT_ROOT'].'encoding.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'db/conn.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'db/jsonResponse.php');
	session_start();
	
	$sqlAttr = array();
	
	$sqlAttr['userID'] = $_POST['userID'];
	$sqlAttr['companyID'] = $_POST['companyID'];
	$sqlAttr['marketName'] = $_POST['marketName'];
	$sqlAttr['time'] = date("Ymd-His");
	
	
	/***************************复制文件***************************/
	$copyUrl_1 = "D:/WinAuraApp/AuraMarketDB/". $sqlAttr['marketName'] ."/BAR";
	$moveToUrl_1 = "C:/Program Files/WinAura/Lang/CHI/HTML";
	
	dir_copy($copyUrl_1,$moveToUrl_1);
	
	
	$copyUrl_2 = "C:/Program Files/WinAura/WInAuraPro/V7R0B1/HTML/WLLG7Y";
	$moveToUrl_2 = "D:/AuraUserData/company_".$sqlAttr['companyID']."/uid_".$sqlAttr['userID']."/".$sqlAttr['time'];
	
	dir_copy($copyUrl_2,$moveToUrl_2);
	/***************************复制文件***************************/
	
	
	/***************************彩光结果写入资料库***************************/
	exec("testWinAuraPro.exe", $output, $return_var);
	$json = array();
	
	if ($return_var == 0)
	{	
		$str = $output[0];
		$str = str_replace(" ","",$str);
		
		$json = (array)json_decode($str, true);
	}
	
	$data = array();
	
	$sql = "SELECT *  FROM `t_aura_registry` WHERE `f_user_id` = ".$sqlAttr['userID']." AND `f_company_id` = ".$sqlAttr['companyID'];
	$rs = $conn->execute($sql);
	
	$data['f_key_id'] = $rs[0]['f_authorKey_id'];
	$data['f_user_id'] = $sqlAttr['userID'];
	
	$category = array_keys($json);
	for($i = 0; $i < count($category); $i++)
	{
		$info = array_keys($json[$category[$i]]);
		for($j = 0; $j < count($info); $j++)
		{
			$data[$category[$i].'_'.$info[$j]] = $json[$category[$i]][$info[$j]];
		}
	}
	$new_aurl_data_id = $conn->insert('t_aura_data', $data);
	
	/***************************彩光结果写入资料库***************************/
	
	
	/***************************修改资料库***************************/
	$tmpArr = array();
	$tmpArr['f_exam_datetime'] = $sqlAttr['time'];
	
	$place = "WHERE f_user_id = ".$sqlAttr['userID'];
	$conn->update('t_aura_registry',$tmpArr , $place);
	/***************************修改资料库***************************/
	
	/**文件夹文件拷贝	 @param string $src 来源文件夹	@param string $dst 目的地文件夹	@return bool*/
	function dir_copy($src = '', $dst = '')
	{
		if (empty($src) || empty($dst))
		{
			echo 'wrong';
		}
		$dir = opendir($src);
		dir_mkdir($dst);		
		while (false !== ($file = readdir($dir)))
		{
			if (($file != '.') && ($file != '..'))
			{
				if (is_dir($src . '/' . $file))
				{
					dir_copy($src . '/' . $file, $dst . '/' . $file);
				}
				else
				{
					copy($src . '/' . $file, $dst . '/' . $file);
				}
			}
		}
		
		closedir($dir);
	}
	
	/**    创建文件夹     @param string $path 文件夹路径     @param int $mode 访问权限	 @param bool $recursive 是否递归创建	 @return bool*/
	function dir_mkdir($path = '', $mode = 0777, $recursive = true)
	{
		clearstatcache();
		if (!is_dir($path))
		{
			mkdir($path, $mode, $recursive);
			return chmod($path, $mode);
		}
		return true;
	}
	
	$resp = array('state'=>'success');
	echo json_encode($resp, JSON_UNESCAPED_UNICODE);
	exit;
?>