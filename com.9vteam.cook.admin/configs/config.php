<?php


global $library;

if($library){

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
}

function config(){
	
	global $library;
	
	return array(
		"services" => array(
			array(
				"class" => "DBService",
				"tasks" => array(
					"DBContextTask"
					),
				"config" => array(
					"entitys"=>array("DBAccount","DBAccountBind","DBUserViewState"
						,"DBApp","DBAppAuth","DBAppDevice","DBAppVersion","DBAsyncTask"
						,"DBAuthority","DBAuthorityRole","DBAuthorityEntity","DBLog"
						,"DBTag","DBClassify","DBClassifyKeyword"
						,"COChef","COFood","COMaterial","COChefClassify"
						,"COFoodClassify","COFoodMaterial","COChefFood"
						,"COChefPackage","COPackage","COPackageFood"
						,"COOrder","COOrderFood","O2ODBTradeOrderStatus","O2ODBTradeEntity"
						,"COChefScore")
				),
				"createInstance" =>true,
			),
			array(
				"class" => "AuthService",
				"tasks" => array(
					"AuthTask","UserViewStateSaveTask","UserViewStateLoadTask","UserViewStateClearTask","AuthorityEntityValidateTask","AuthorityRoleValidateTask"
				),
			),
			array(
				"class" => "UserViewStateService",
				"tasks" => array(
					"UserViewStateSaveTask","UserViewStateLoadTask","UserViewStateClearTask"
				),
			),
			array(
				"class" => "AccountInfoService",
				"tasks" => array(
					"AccountInfoGetTask"
				),
			),
			array(
				"class" => "AuthorityValidateService",
				"tasks" => array(
					"AuthorityEntityValidateTask","AuthorityRoleValidateTask"
				),
			),
			array(
				"class" => "ClassifyService",
				"tasks" => array(
					"ClassifyQueryTask"
				),
			),
			array(
				"class" => "AppDevicePushService",
				"tasks" => array(
					"AppDevicePushTask"
				),
				"config" => array(
					"appid"=>600
				),
			),
			array(
				"class" => "AppUserPushService",
				"tasks" => array(
					"AppUserPushTask"
				),
				"config" => array(
					"appid"=>600,
				),
			),
			array(
				"class" => "AsyncTaskService",
				"tasks" => array(
					"AsyncActiveTask"
				),
				"config" => array(
					"config"=>"config_cook"
				),
			),
			array(
				"class" => "ApplePushService",
				"tasks" => array(
					"ApplePushTask"
				),
				"config" => array(
					"host"=>"gateway.sandbox.push.apple.com",
					"port"=>2195,
					"cert"=> "/$library/org.hailong.configs/cook-dev.pem",
					"password"=>"123456",
				),
			),
			array(
				"class" => "O2OService",
				"tasks" => array(
					"O2OTradeEntityCreateTask","O2OTradeOrderStatusSetTask"
				),
			),
			array(
				"class" => "COChefService",
				"tasks" => array(
					"COChefCodeCheckTask","COChefCreateTask","COChefUpdateTask","COChefStateSetTask","COChefRemoveTask"
				),
			),
			array(
				"class" => "COChefQueryService",
				"tasks" => array(
					"COChefSearchTask","COChefGetFoodsTask","COChefGetClassifysTask","COChefGetTask","COChefGetPackagesTask"
						,"COChefGetPackageTask","COChefScoreGetTask"
				),
			),
			array(
				"class" => "COFoodService",
				"tasks" => array(
					"COFoodCreateTask","COFoodUpdateTask","COFoodRemoveTask","COFoodTitleCheckTask","COFoodGetByTitleTask"
				),
			),
			array(
				"class" => "COPackageService",
				"tasks" => array(
					"COPackageCreateTask","COPackageUpdateTask","COPackageRemoveTask","COPackageGetTask","COPackageGetFoodsTask"
				),
			),
			array(
				"class" => "COMaterialService",
				"tasks" => array(
					"COMaterialSetTask"
				),
			),
			array(
				"class" => "COOrderService",
				"tasks" => array(
					"COOrderLockTask"
					,"COOrderTryLockTask","COOrderUnLockTask","COOrderUpdateTask"
					,"COOrderConfirmTask","COOrderCancelTask"
				),
			),
			array(
				"class" => "COOrderQueryService",
				"tasks" => array(
					"COOrderGetFoodsTask","COOrderGetTask"
				),
			),
			
		),
	);
}

?>