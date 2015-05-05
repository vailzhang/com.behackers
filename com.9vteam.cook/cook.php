<?php
/**
 * 好厨师
 */
if($library){

	define("DB_COOK","cook");

	define("ClassifyTargetDefault",0);	// 口味分类
	define("ClassifyTargetCuisine",1);	// 菜系
	define("ClassifyTargetArea",2);		// 区域
	
	define("CommentEntityOrderType",1);	// 评论订单实体
	define("CommentEntityChefType",2);	// 评论厨师实体
	
	
	require_once "$library/org.hailong.configs/error_code.php";
	
	require_once "$library/org.hailong.o2o/o2o.php";
	require_once "$library/org.hailong.classify/classify.php";
	require_once "$library/org.hailong.comment/comment.php";
	require_once "$library/org.hailong.sms/sms.php";
	require_once "$library/org.hailong.trigger/trigger.php";
	require_once "$library/org.hailong.app/app.php";
	require_once "$library/org.hailong.apple/apple.php";
	require_once "$library/org.hailong.service.async/async.php";
	
	require_once "$library/com.9vteam.cook/functions.php";
	require_once "$library/com.9vteam.cook/COException.php";
	
	require_once "$library/com.9vteam.cook/db/COChef.php";
	require_once "$library/com.9vteam.cook/db/COChefClassify.php";
	require_once "$library/com.9vteam.cook/db/COChefFood.php";
	require_once "$library/com.9vteam.cook/db/COChefPackage.php";
	require_once "$library/com.9vteam.cook/db/COFood.php";
	require_once "$library/com.9vteam.cook/db/COFoodClassify.php";
	require_once "$library/com.9vteam.cook/db/COFoodMaterial.php";
	require_once "$library/com.9vteam.cook/db/COMaterial.php";
	require_once "$library/com.9vteam.cook/db/COPackage.php";
	require_once "$library/com.9vteam.cook/db/COPackageFood.php";
	require_once "$library/com.9vteam.cook/db/COUserAddress.php";
	require_once "$library/com.9vteam.cook/db/COOrder.php";
	require_once "$library/com.9vteam.cook/db/COOrderFood.php";
	require_once "$library/com.9vteam.cook/db/COChefScore.php";
	
	require_once "$library/com.9vteam.cook/tasks/COTask.php";
	require_once "$library/com.9vteam.cook/tasks/COAuthTask.php";
	require_once "$library/com.9vteam.cook/tasks/chef/COChefCodeCheckTask.php";
	require_once "$library/com.9vteam.cook/tasks/chef/COChefCreateTask.php";
	require_once "$library/com.9vteam.cook/tasks/chef/COChefStateSetTask.php";
	require_once "$library/com.9vteam.cook/tasks/chef/COChefUpdateTask.php";
	require_once "$library/com.9vteam.cook/tasks/chef/COChefRemoveTask.php";
	require_once "$library/com.9vteam.cook/tasks/chef/COChefSearchTask.php";
	require_once "$library/com.9vteam.cook/tasks/chef/COChefGetClassifysTask.php";
	require_once "$library/com.9vteam.cook/tasks/chef/COChefGetFoodsTask.php";
	require_once "$library/com.9vteam.cook/tasks/chef/COChefGetTask.php";
	require_once "$library/com.9vteam.cook/tasks/chef/COChefGetPackagesTask.php";
	require_once "$library/com.9vteam.cook/tasks/chef/COChefGetPackageTask.php";
	require_once "$library/com.9vteam.cook/tasks/chef/COChefScoreTask.php";
	require_once "$library/com.9vteam.cook/tasks/chef/COChefScoreGetTask.php";
	require_once "$library/com.9vteam.cook/tasks/food/COFoodCreateTask.php";
	require_once "$library/com.9vteam.cook/tasks/food/COFoodUpdateTask.php";
	require_once "$library/com.9vteam.cook/tasks/food/COFoodRemoveTask.php";
	require_once "$library/com.9vteam.cook/tasks/food/COFoodTitleCheckTask.php";
	require_once "$library/com.9vteam.cook/tasks/food/COFoodGetByTitleTask.php";
	require_once "$library/com.9vteam.cook/tasks/material/COMaterialSetTask.php";
	require_once "$library/com.9vteam.cook/tasks/package/COPackageCreateTask.php";
	require_once "$library/com.9vteam.cook/tasks/package/COPackageUpdateTask.php";
	require_once "$library/com.9vteam.cook/tasks/package/COPackageRemoveTask.php";
	require_once "$library/com.9vteam.cook/tasks/package/COPackageGetTask.php";
	require_once "$library/com.9vteam.cook/tasks/package/COPackageGetFoodsTask.php";
	require_once "$library/com.9vteam.cook/tasks/order/COOrderCreateTask.php";
	require_once "$library/com.9vteam.cook/tasks/order/COOrderUpdateTask.php";
	require_once "$library/com.9vteam.cook/tasks/order/COOrderGetTask.php";
	require_once "$library/com.9vteam.cook/tasks/order/COOrderGetFoodsTask.php";
	require_once "$library/com.9vteam.cook/tasks/order/COOrderLockTask.php";
	require_once "$library/com.9vteam.cook/tasks/order/COOrderTryLockTask.php";
	require_once "$library/com.9vteam.cook/tasks/order/COOrderUnLockTask.php";
	require_once "$library/com.9vteam.cook/tasks/order/COOrderPullTask.php";
	require_once "$library/com.9vteam.cook/tasks/order/COOrderChangedTask.php";
	require_once "$library/com.9vteam.cook/tasks/order/COOrderConfirmTask.php";
	require_once "$library/com.9vteam.cook/tasks/order/COOrderCancelTask.php";
	require_once "$library/com.9vteam.cook/tasks/order/COOrderUserCancelTask.php";
	require_once "$library/com.9vteam.cook/tasks/order/COOrderCommentTask.php";
	require_once "$library/com.9vteam.cook/tasks/app/COAppTask.php";
	
	
	require_once "$library/com.9vteam.cook/services/COChefService.php";
	require_once "$library/com.9vteam.cook/services/COFoodService.php";
	require_once "$library/com.9vteam.cook/services/COMaterialService.php";
	require_once "$library/com.9vteam.cook/services/COPackageService.php";
	require_once "$library/com.9vteam.cook/services/COOrderService.php";
	require_once "$library/com.9vteam.cook/services/COOrderQueryService.php";
	require_once "$library/com.9vteam.cook/services/COAppService.php";
	require_once "$library/com.9vteam.cook/services/COChefQueryService.php";
	
}
?>