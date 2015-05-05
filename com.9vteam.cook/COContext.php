<?php

global $library;

if($library){
	
	require_once "$library/org.hailong.db/db.php";
	
	$dbConfig = require "$library/org.hailong.configs/db_default.php";

	defaultDBAdapter($dbConfig["type"],$dbConfig["servername"],$dbConfig["database"],$dbConfig["username"],$dbConfig["password"]);

	getDefaultDBAdapter()->setCharset($dbConfig["charset"]);

	defaultDBContext(new DBContext());

	date_default_timezone_set("PRC");

	require_once "$library/org.hailong.configs/xunsearch.php";
	require_once "$library/org.hailong.account/account.php";
	require_once "$library/org.hailong.authority/authority.php";
	require_once "$library/org.hailong.log/log.php";
	require_once "$library/org.hailong.uri/uri.php";
	require_once "$library/org.hailong.tag/tag.php";
	require_once "$library/org.hailong.classify/classify.php";
	require_once "$library/com.9vteam.cook/cook.php";
	require_once "$library/org.hailong.configs/config_cook.php";
}

/**
 * 
 * @author zhanghailong
 *
 */

class COContext extends ServiceContext{
	
	public function __construct(){

		$config = config_cook();
		
		$inputData = array();
		
		foreach($_GET as $key => $value){
			$inputData[$key] = $value;
		}
		foreach($_POST as $key => $value){
			$inputData[$key] = $value;
		}
		
		$data = input();
		
		if(is_array($data)){
			foreach($data as $key => $value){
				$inputData[$key] = $value;
			}
		}
		
		if($_FILES){
		
			$files = upload();
			if($files && is_array($files)){
				foreach($files as $name=>$value){
					$inputData[$name] = $value;
				}
			}
		}
	
		parent::__construct($inputData,$config);
		
		$this->setDbContext(getDefaultDBContext());
		
		$beginTasks = isset($config["begin-tasks"]) ? $config["begin-tasks"] : null;
		if($beginTasks){
			foreach($beginTasks as $beginTask){
				$taskType = isset($beginTask["taskType"]) ? $beginTask["taskType"] : null;
				$taskClass = isset($beginTask["taskClass"]) ? $beginTask["taskClass"] : null;
				if($taskClass == null){
					$taskClass = $taskType;
				}
				$default = isset($beginTask["default"]) ?  $beginTask["default"]:null ;
				if($taskType){
					$task = new $taskClass();
					$this->fillTask($task,$default);
					try{
						$this->handle($taskType,$task);
					}
					catch(Exception $ex){}
				}
			}
		}
	}
}

?>