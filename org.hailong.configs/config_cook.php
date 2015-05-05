<?php

global $library;

if(!$library){
	$library = "..";
}

$dbConfig = require "$library/org.hailong.configs/db_default.php";

defaultDBAdapter($dbConfig["type"],$dbConfig["servername"],$dbConfig["database"],$dbConfig["username"],$dbConfig["password"]);

getDefaultDBAdapter()->setCharset($dbConfig["charset"]);

defaultDBContext(new DBContext());

date_default_timezone_set("PRC");

require_once "$library/org.hailong.service/service.php";
require_once "$library/org.hailong.apple/apple.php";
require_once "$library/org.hailong.account/account.php";
require_once "$library/org.hailong.log/log.php";
require_once "$library/org.hailong.device/device.php";
require_once "$library/org.hailong.uri/uri.php";
require_once "$library/org.hailong.app/app.php";
require_once "$library/org.hailong.authority/authority.php";
require_once "$library/org.hailong.service.async/async.php";
require_once "$library/com.9vteam.cook/cook.php";
require_once "$library/org.hailong.statistics/statistics.php";
require_once "$library/org.hailong.feedback/feedback.php";

function config_cook(){
	
	global $library;
	
	return array(
		"begin-tasks" => array(
			array(
				"taskType" => "DeviceAuthTask",
				"default" => array()
			),
			array(
				"taskType" => "AppDeviceTask",
                "default" => array()
			),
			array(
				"taskType" => "AppAuthTask",
				"default" => array()
			),
			array(
				"taskType" => "StatisticsTask",
				"default" => array(
					"target" => "cook/api",
					"key" => "pv"
				)
			),
		),
		"services" => array(
			array(
				"class" => "DeviceAuthService",
				"tasks" => array(
					"DeviceAuthTask"
				),
				"config" => array(),
				"security" => true,
			),
			array(
				"class" => "AppAuthService",
				"tasks" => array(
					"AppAuthTask","AppAutoAuthTask"
				),
				"config" => array(
					"appid"=>600,
					"anonymous"=>true
				),
				"security" => true,
			),
			array(
				"class" => "AppAuthService",
				"tasks" => array(
					"AppAuthRemoveTask"
				),
				"config" => array(
					"appid"=>600,
				)
			),
			array(
				"class" => "AppDeviceService",
				"tasks" => array(
					"AppDeviceTask"
				),
				"config" => array(
					"appid"=>600
				),
				"security" => true,
			),
			array(
				"class" => "AppVersionService",
				"tasks" => array(
					"AppVersionTask"
				),
				"config" => array(
					"appid"=>600
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
				"security" => true,
			),
			array(
				"class" => "AppUserPushService",
				"tasks" => array(
					"AppUserPushTask"
				),
				"config" => array(
					"appid"=>600,
				),
				"security" => true,
			),
			array(
				"class" => "AsyncTaskService",
				"tasks" => array(
					"AsyncActiveTask"
				),
				"config" => array(
					"config"=>"config_cook"
				),
				"security" => true,
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
				"security" => true,
			),
			array(
				"class" => "DBService",
				"tasks" => array(
					"DBContextTask"
				),
				"config" => array(
					"entitys"=>array("DBAccount","DBAccountBind","DBAccountInfo","DBLog","DBDevice"
						,"DBApp","DBAppAuth","DBAppDevice","DBAppVersion","DBAsyncTask"
						,"DBTag","DBURI"
						,"DBAuthority","DBAuthorityEntity","DBAuthorityRole","DBCache"
						,"DBClassify","DBClassifyKeyword","DBComment"
						,"COChef","COChefClassify","COChefFood","COChefPackage"
						,"COFood","COFoodClassify","COFoodMaterial"
						,"COMaterial","COOrder","COPackage","COPackageFood","COUserAddress"
						,"O2ODBTradeEntity","O2ODBTradeOrderStatus","COOrderFood"
						,"COChefScore","DBFeedback"
					)
				),
				"createInstance" =>true,
				"security" => true,
			),
			array(
				"class" => "AuthService",
				"tasks" => array(
					"AuthTask"
				),
				"config" => array(),
				"security" => true,
			),
			array(
				"class" => "LoginService",
				"tasks" => array(
					"LoginTask"
				),
				"config" => array(),
				"security" => true,
			),
			array(
				"class" => "URIService",
				"tasks" => array(
					"URITask"
				),
				"config" => array(),
				"security" => true,
			),
			array(
				"class" => "AccountBindService",
				"tasks" => array(
					"AccountBindGetTask","AccountBindTask","AccountTelBindTask","AccountTelUnBindTask"
				),
				"config" => array(),
				"security" => true,
			),
			array(
				"class" => "AccountInfoService",
				"tasks" => array(
					"AccountInfoGetTask"
				),
				"config" => array(),
				"security" => true,
			),
			array(
				"class" => "AccountService",
				"tasks" => array(
					"AccountByIDTask","AccountIDCheckNickTask","AccountIDByBindTask","AccountIDCheckTelTask"
				),
				"config" => array(),
				"security" => true,
			),
			array(
				"class" => "AccountInfoService",
				"tasks" => array(
					"AccountInfoGetTask","AccountInfoPutTask"
				),
				"config" => array(),
				"security" => true,
			),
			array(
				"class" => "AccountRegisterService",
				"tasks" => array(
					"AccountRegisterTask","AccountTelRegisterTask","AccountTelVerifyTask"
				),
				"config" => array(),
				"security" => true,
			),
			array(
				"class" => "AuthorityValidateService",
				"tasks" => array(
					"AuthorityEntityValidateTask","AuthorityRoleValidateTask"
				),
				"config" => array(),
				"security" => true,
			),
			array(
				"class" => "CacheService",
				"tasks" => array(
					"CacheGetTask","CachePutTask","CacheRemoveTask"
				),
				"config" => array(),
				"security" => true,
			),
			array(
				"class" => "LogService",
				"tasks" => array(
					"LogTask"
				),
				"config" => array(
					"filter"=>LogLevelError
				),
				"security" => true,
			),
			array(
				"class" => "StatisticsService",
				"tasks" => array(
					"StatisticsTask"
				),
				"security" => true,
			),
			array(
				"class" => "ClassifyService",
				"tasks" => array(
					"ClassifyCreateTask","ClassifyChildTask","ClassifyMatchTask"
					,"ClassifyParentTask","ClassifyQueryTask","ClassifyRemoveTask","ClassifyUpdateTask"
					,"ClassifyKeywordAssignTask","ClassifyQueryTopTask"
				),
				"config" => array(),
				"security" => true,
			),
			array(
				"class" => "SMSService",
				"tasks" => array(
					"SMSSendTask"
				),
				"config" => array(
					"url" => "http://www.smsadmin.cn/smsmarketing/wwwroot/api/get_send/",
					"method" => "GET",
					"charset" => "gb2312",
					"body" => array(
						"uid"=>"richland",
						"pwd"=>"123456",
						"mobile"=>"{tel}",
						"msg"=>"{body}"
					)	
				),
				"security" => true,
			),
			array(
				"class" => "TriggerService",
				"tasks" => array(
					"TriggerTask"
				),
				"config" => array(
					
					"triggers"=>array(
					
					)
						
				),
				"security" => true,
			),
			array(
				"class" => "FeedbackService",
				"tasks" => array(
					"FeedbackTask"
				),
			),
			array(
				"class" => "CommentService",
				"tasks" => array(
					"CommentCreateTask","CommentQueryTask"
				),
				"security" => true,
			),
			array(
				"class" => "O2OService",
				"tasks" => array(
					"O2OTradeEntityCreateTask","O2OTradeEntityGetTask","O2OTradeEntityRemoveTask"
					,"O2OTradeOrderCreateTask","O2OTradeOrderStatusSetTask"
				),
				"config"=> array(
					"dbTradeOrder" => "COOrder",
					"dbKey" => "cook"
				),
				"security" => true,
			),
			array(
				"class" => "COAppService",
				"tasks" => array(
					"COAppTask"
				),
			),
			array(
				"class" => "COChefService",
				"tasks" => array(
					"COChefScoreTask"
				),
				"security" => true,
			),
			array(
				"class" => "COChefQueryService",
				"tasks" => array(
					"COChefSearchTask","COChefGetFoodsTask","COChefGetClassifysTask","COChefGetTask","COChefGetPackagesTask"
						,"COChefGetPackageTask","COChefScoreGetTask"
				),
			),
			array(
				"class" => "COPackageService",
				"tasks" => array(
					"COPackageGetTask","COPackageGetFoodsTask"
				),
			),
			array(
				"class" => "COOrderService",
				"tasks" => array(
					"COOrderCreateTask","COOrderUserCancelTask","COOrderCommentTask"
				),
			),
			array(
				"class" => "COOrderQueryService",
				"tasks" => array(
					"COOrderGetFoodsTask","COOrderGetTask","COOrderPullTask"
				),
			),
		),
	);
}

?>