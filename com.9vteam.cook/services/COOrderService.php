<?php

/**
 *　订单 服务
 * @author zhanghailong
 *
 */
class COOrderService extends Service{
	
	public function handle($taskType,$task){
	
		if($task instanceof COOrderCreateTask){
			
			$context = $this->getContext();
				
			$dbContext = $context->dbContext(DB_COOK);
			
			$chefId = intval($task->chefId);
			
			if(!$chefId){
				throw new COException("没有设置厨师",ERROR_COOK_NOT_FOUND_CHEF);
			}
			
			$tradeEntity = null;
			
			$t = new O2OTradeEntityGetTask();
			$t->pid = $chefId;
			
			$context->handle("O2OTradeEntityGetTask", $t);
			
			if($t->results){
				$tradeEntity = $t->results;
			}
			else {
				
				$item = $dbContext->get("COChef",array("pid"=>$chefId));
				
				if($item){
					
					$t = new O2OTradeEntityCreateTask();
					$t->pid = $chefId;
					
					$context->handle("O2OTradeEntityCreateTask", $t);
					
					$tradeEntity = $t->results;
					
				}
				else {
					throw new COException("没有找到厨师",ERROR_COOK_NOT_FOUND_CHEF);
				}
			}
			
			$packageId = intval($task->packageId);
			
			if(!$packageId){
				throw new COException("没有设置套餐",ERROR_COOK_NOT_FOUND_PACKAGE);
			}
			
			$t = new COChefGetPackageTask();
			$t->chefId = $chefId;
			$t->packageId = $packageId;
			
			$context->handle("COChefGetPackageTask", $t);
			
			$package = $t->results;
			
			if(!$package){
				throw new COException("没有找到套餐",ERROR_COOK_NOT_FOUND_PACKAGE);
			}
			
			$orderDay = null;
			
			if(is_int($task->orderDay)){
				$orderDay = intval($task->orderDay);
			}
			else {
				$orderDay = strtotime($task->orderDay);
			}
			
			if(!$orderDay){
				throw new COException("没有设置订单日期",ERROR_COOK_NOT_FOUND_ORDERDAY);
			}
			
			$orderMeal = null;
			
			if(is_int($task->orderMeal)){
				$orderMeal = intval($task->orderMeal);
			}
			else if("noon" == $task->orderMeal){
				$orderMeal = COOrderMealNoon;
			}
			else if("dinner" == $task->orderMeal){
				$orderMeal = COOrderMealDinner;
			}
			
			if(!$orderMeal){
				throw new COException("没有设置订单时间",ERROR_COOK_NOT_FOUND_ORDERMEAL);
			}
			
			$orderFood = COOrderFoodNone;
			
			if(is_int($task->orderFood)){
				$orderFood = intval($task->orderFood);
			}
			else if($task->orderFood == "normal"){
				$orderFood = COOrderFoodNormal;
			}
			
			$city = trim($task->city);
			$cityId = intval($task->cityId);
			
			if(!$city){
				throw new COException("没有设置城市",ERROR_COOK_NOT_FOUND_CITY);
			}
			
			$area = trim($task->area);
			$areaId = intval($task->areaId);
			
			if(!$area){
				throw new COException("没有设置区域",ERROR_COOK_NOT_FOUND_AREA);
			}
			
			$address = trim($task->address);
			
			if(!$address){
				throw new COException("没有设置地址",ERROR_COOK_NOT_FOUND_ADDRESS);
			}
			
			$name = trim($task->name);
			
			if(!$name){
				throw new COException("没有设置联系人姓名",ERROR_COOK_NOT_FOUND_NAME);
			}

			$phone = trim($task->phone);
			
			if(!$phone){
				throw new COException("没有设置联系人手机号",ERROR_COOK_NOT_FOUND_PHONE);
			}
			
			$code = ''.$cityId;
			
			while(strlen($code) < 3){
				$code = '0'.$code;
			}
			
			$code .= date("Ymd",time());
			
			$index = $dbContext->countForEntity("COOrder","createTime>".strtotime(date('Y-m-d',time()))) + 1;
			
			while(1) {
				
				$codeId = $index;
				
				while(strlen($codeId) < 5){
					$codeId = '0'.$codeId;
				}
				
				$codeId = $code.$codeId;
				
				if($dbContext->countForEntity("COOrder","code='".$codeId."'") ==0){
					$code = $codeId;
					break;
				}
				
				$index ++;
			}
			
			
			
			$t = new O2OTradeOrderCreateTask();
			$t->eid = $tradeEntity->eid;
			
			$propertys = array();
			$propertys["code"] = $code;
			$propertys["did"] = $context->getInternalDataValue("device-did");
			$propertys["chefId"] = $chefId;
			$propertys["packageId"] = $packageId;
			$propertys["orderDay"] = $orderDay;
			$propertys["orderMeal"] = $orderMeal;
			$propertys["orderFood"] = $orderFood;
			$propertys["price"] = $package["chefPrice"];
			$propertys["city"] = $city;
			$propertys["cityId"] = $cityId;
			$propertys["area"] = $area;
			$propertys["areaId"] = $areaId;
			$propertys["address"] = $address;
			$propertys["name"] = $name;
			$propertys["phone"] = $phone;
			
			$t->propertys = $propertys;
			
			$context->handle("O2OTradeOrderCreateTask", $t);
			
			if($t->results){
				
				$order = $t->results;
				
				$t = new COPackageGetFoodsTask();
				$t->pid =  $packageId;
				
				$context->handle("COPackageGetFoodsTask", $t);
				
				if($t->results){
					
					foreach($t->results as $food){
						
						$i = new COOrderFood();
						$i->orderId = $order->oid;
						$i->foodId = $food->fid;
						$i->createTime = time();
						$dbContext->insert($i);
						
					}
					
				}
				
				$t = new COOrderPullTask();
				$t->oid = $order->oid;
				
				$context->handle("COOrderPullTask", $t);
				
				$context->setOutputDataValue("orderId", $order->oid);
				
			}
			else {
				throw new COException("创建订单失败",ERROR_COOK_CREATE_ORDER);
			}
			
			return false;
		}
		
		if($task instanceof COOrderLockTask){
			
			$context = $this->getContext();
			
			$dbContext = $context->dbContext(DB_COOK);
				
			
			$item = $dbContext->get("COOrder",array("oid"=>$task->oid));
			
			if($item){
				
				$item->lockUid = $context->getInternalDataValue("auth");
				
				$dbContext->update($item);
				
				$task->lockUid = $item->lockUid;
				
			}
			
			return false;
		}
		
		if($task instanceof COOrderUnLockTask){
				
			$context = $this->getContext();
				
			$dbContext = $context->dbContext(DB_COOK);
		
				
			$item = $dbContext->get("COOrder",array("oid"=>$task->oid));
				
			if($item && $item->lockUid && $context->getInternalDataValue("auth") == $item->lockUid){
		
				$item->lockUid = 0;
		
				$dbContext->update($item);
		
				$task->results = true;
		
			}
				
			return false;
		}
		
		if($task instanceof COOrderTryLockTask){
		
			$context = $this->getContext();
		
			$dbContext = $context->dbContext(DB_COOK);
		
			$item = $dbContext->get("COOrder",array("oid"=>$task->oid));
			
			if($item && intval($item->lockUid) == 0 ){
				
				$item->lockUid = $context->getInternalDataValue("auth");
				
				$dbContext->update($item);
				
				$task->lockUid = $item->lockUid;
				
				$task->results = true;
			}
			else if($item && $item->lockUid == $context->getInternalDataValue("auth")){
				
				$task->results = true;
				
			}
		
			return false;
		}
		
		if($task instanceof COOrderUpdateTask){
			
			$context = $this->getContext();
			
			$dbContext = $context->dbContext(DB_COOK);
			
			$item = $dbContext->get("COOrder",array("oid"=>$task->oid));

			if($item){
				
				$remark = "";
				
				$alert = null;
				$sound = null;
				
				$status = intval($item->status);
				
				if($status == COOrderStatusNone){
					
					$remark .= "状态： 等待确认 -> 已确认\n";
					
					$status = COOrderStatusWait;
					
					$alert = "您的订单已确认";
					$sound = "default";
					
				}
				else if($status == COOrderStatusWait){
					
				}
				else if($status == COOrderStatusConfirm){
					throw new COException("已完成订单不能修改",ERROR_COOK_ORDER_STATUS);
				}
				else if($status == COOrderStatusUserCancel){
					throw new COException("用户已取消订单，不能修改",ERROR_COOK_ORDER_STATUS);
				}
				else if($status == COOrderStatusCancel){
					throw new COException("已取消订单，不能修改",ERROR_COOK_ORDER_STATUS);
				}
				else {
					throw new COException("未知订单状态[{$status}]，不能修改",ERROR_COOK_ORDER_STATUS);
				}
				
				if($task->chefId !== null && $item->chefId != $task->chefId){
					
					$name1 = "[{$item->chefId}]";
					$name2 = "[{$task->chefId}]";
					
					$rs = $dbContext->query("SELECT pid,code,name FROM ".COChef::tableName()." WHERE pid IN (".intval($task->chefId).','.intval($item->chefId).')');
					
					if($rs){
						while($row = $dbContext->next($rs)){
							$pid = intval($row["pid"]);
							if($pid == $item->chefId){
								$name1 = $row["name"].'、'.$row["code"];	
							}
							else if($pid == $task->chefId){
								$name2 = $row["name"].'、'.$row["code"];
							}
						}
						$dbContext->free($rs);
					}
					
					$remark .= "厨师： {$name1} -> {$name2}\n";
					
					$item->chefId = $task->chefId;
					
				}
				
				$orderDay = null;
				
				if($task->orderDay !== null){
						
					$orderDay = $task->orderDay;
						
					if(!is_int($orderDay)){
						$orderDay = strtotime($orderDay);
					}
					
				}
				
				$orderMeal = null;
				
				if($task->orderMeal !== null){
				
					$orderMeal = $task->orderMeal;
				
					if(!is_int($orderDay)){
						if($orderMeal == 'noon'){
							$orderMeal = COOrderMealNoon;
						}
						else if($orderMeal == 'dinner'){
							$orderMeal = COOrderMealDinner;
						}
						else {
							$orderMeal = COOrderMealNone;
						}
					}
						
				}
				
				if(($orderDay !== null && $orderDay != $item->orderDay) || ($orderMeal !== null && $orderMeal != $item->orderMeal)){
					
					if($orderDay === null){
						$orderDay = $item->orderDay;
					}
					
					if($orderMeal === null){
						$orderMeal = $item->orderMeal;
					}
					
					$day1 = COOrder::day($item->orderDay, $item->orderMeal);
					$day2 = COOrder::day($orderDay, $orderMeal);
					
					$remark .= "订单时间: {$day1} -> {$day2}\n";
					
					$item->orderDay = $orderDay;
					$item->orderMeail = $orderMeal;
				}
				
				if($task->cityId !== null && $task->cityId != $item->cityId){
					
					$city1 = "{$item->city}[$item->cityId]";
					$city2 = "{$task->city}[{$task->cityId}]";
					
					$remark .= "城市: {$city1} -> {$city2}\n";
					
					$item->cityId = $task->cityId;
					$item->city = $task->city;
					
				}
				
				if($task->areaId !== null && $task->areaId != $item->areaId){
						
					$area1 = "{$item->area}[$item->areaId]";
					$area2 = "{$task->area}[{$task->areaId}]";
						
					$remark .= "区域: {$area1} -> {$area2}\n";
						
					$item->areaId = $task->areaId;
					$item->area = $task->area;
						
				}
				
				if($task->address !== null && $task->address != $item->address){
					
					$remark .= "地址: {$item->address} -> {$task->address}\n";
					
					$item->address = $task->address;
				}
				
				if($task->summary !== null && $task->summary != $item->summary){
						
					$remark .= "备注: {$item->summary} -> {$task->summary}\n";
						
					$item->summary = $task->summary;
				}
				
				if($task->price !== null && doubleval($task->price) != doubleval($item->price)){
				
					$remark .= "价格: {$item->price} -> {$task->price}\n";
				
					$item->price = $task->price;
				}
				
				if($task->userCount !== null && intval($task->userCount) != intval( $item->userCount)){
					
					$remark .= "人数: {$item->userCount} -> {$task->userCount}\n";
					
					$item->userCount = $task->userCount;
				}
				
				if($task->orderFood !== null){
					
					$orderFood = $task->orderFood;
					
					if(!is_int($orderFood)){
						if($orderFood == 'normal'){
							$orderFood = COOrderFoodNormal;
						}
						else{
							$orderFood = COOrderFoodNone;
						}
					}
					
					if($orderFood != intval($item->orderFood)){
						
						$remark .="食材: ".COOrder::orderFood(intval($item->orderFood)).'->'.COOrder::orderFood($orderFood)."\n";
						
						$item->orderFood = $orderFood;
						
					}
					
				}
				
				if($task->foodIds !== null){
					
					$foodIds = $task->foodIds;
					
					if(! is_array($foodIds)){
						$foodIds = CKSplit($foodIds);
					}
					
					$foods = array();
					
					$t = new COOrderGetFoodsTask();
					$t->oid = $item->oid;
					
					$context->handle("COOrderGetFoodsTask", $t);
					
					$food1 = "";
					$food2 = "";
					
					if($t->results){
						foreach ($t->results as $food){
							$foods[$food->fid] = $food;
							if(strlen($food1)){
								$food1 .= '、'.$food->title;
							}
							else {
								$food1 .= $food->title;
							}
						}
					}
					
					$hasChanged = false;
					
					foreach ($foodIds as $foodId){
						
						if(isset($foods[$foodId])){
							$food = $foods[$foodId];
							if(strlen($food2)){
								$food2 .= '、'.$food->title;
							}
							else {
								$food2 .= $food->title;
							}
							$foods[$foodId] = null;
						}
						else {
							
							$food = $dbContext->get("COFood",array("fid"=>$foodId));
							
							if($food){
								
								if(strlen($food2)){
									$food2 .= '、'.$food->title;
								}
								else {
									$food2 .= $food->title;
								}
								
								$hasChanged = true;
								
								$i = new COOrderFood();
								$i->orderId = $item->oid;
								$i->foodId = $foodId;
								$i->createTime = time();
								
								$dbContext->insert($i);
								
							}
						
						}
					}
					
					foreach($foods as $food){
						if($food !== null){
							$hasChanged = true;
							$dbContext->delete("COOrderFood","orderId={$item->oid} AND foodId={$food->fid}");
						}
					}
					
					if($hasChanged){
						$remark .= "菜单从: {$food1}\n";
						$remark .= "变更到: {$food2}\n";
					}
					
				}
			
				$t = new O2OTradeOrderStatusSetTask();
				
				$t->order = $item;
				$t->remark = $remark;
				$t->status = $status;
				
				$context->handle("O2OTradeOrderStatusSetTask", $t);
				
				$task->results = $item;
				
				if($item->uid){
					
					$t = new AppUserPushTask();
					$t->uid = $item->uid;
					$t->alert = $alert;
					$t->sound = $sound;
					$t->data = array("t"=>"o","i"=>$item->oid);;
					
					$context->handle("AppUserPushTask", $t);
					
				}
				else if($item->did){
					$t = new AppDevicePushTask();
					$t->did = $item->did;
					$t->alert = $alert;
					$t->sound = $sound;
					$t->data = array("t"=>"o","i"=>$item->oid);;
						
					$context->handle("AppDevicePushTask", $t);
				}
			}
			
			
		}
		
		
		if($task instanceof COOrderChangedTask){
			
			$context = $this->getContext();
			
			$dbContext = $context->dbContext(DB_COOK);

			$where = null;
			
			if($task->oid !== null){
				$where = "oid=".intval($task->oid);
			}
			else if($task->code !== null){
				$where = "code=".$dbContext->parseValue($task->code);
			}
			else if($task->chefId !== null){
				$where = "chefId=".intval($task->chefId);
			}
			else if($task->packageId !== null){
				$where = "packageId=".intval($task->packageId);
			}
			
			if($where){
				
				$dbContext->query("UPDATE ".COOrder::tableName()." SET updateTime=".time()." WHERE ".$where);
				
				$rs = $dbContext->query("SELECT oid,uid,did FROM ".COOrder::tableName(). " WHERE ".$where." ORDER BY oid ASC");
				
				if($rs){
					
					while($row = $dbContext->next($rs)){
						
						$uid = $row["uid"];
						$did = $row["did"];
						$oid = $row["oid"];
						
						if($uid){
							
							$t = new AppUserPushTask();
							$t->uid = $uid;
							$t->alert = null;
							$t->sound = null;
							$t->badge = null;
							$t->data = array("t"=>"o","i"=>$oid);
							
							$context->handle("AppUserPushTask", $t);
							
							
						}
						else if($did){
							
							$t = new AppDevicePushTask();
							$t->did = $did;
							$t->alert = null;
							$t->sound = null;
							$t->badge = null;
							$t->data = array("t"=>"o","i"=>$oid);
								
							$context->handle("AppDevicePushTask", $t);
								
							
						}
					}
					
					$dbContext->free($rs);
				}
				
			}
			
			return false;
		}
		
		if($task instanceof COOrderConfirmTask){
			
			$context = $this->getContext();
			
			$dbContext = $context->dbContext(DB_COOK);
			
			$item = $dbContext->get("COOrder",array("oid"=>$task->oid));

			if($item){
				
				$status = intval($item->status);
				
				if($status == COOrderStatusNone){
					
					throw new COException("订单客服未确认, 不可反馈",ERROR_COOK_ORDER_STATUS);
					
				}
				else if($status == COOrderStatusWait){
					
					$t = new O2OTradeOrderStatusSetTask();
					$t->order = $item;
					$t->status = COOrderStatusConfirm;
					
					$remark = "状态: 已确认 -> 已完成\n";
					$remark .= "厨师反馈: ";
							
					if($task->remark){
						$remark .= $task->remark."\n";
					}
					else {
						$remark .= "无\n";
					}
					
					$t->remark = $remark;
					
					$context->handle("O2OTradeOrderStatusSetTask", $t);
					
					$task->results = $item;
					
					if($item->uid){
						$t = new AppUserPushTask();
						$t->uid = $item->uid;
						$t->alert = "您的订单已完成, 快去给厨师个评价吧!!!";
						$t->sound = null;
						$t->badge = null;
						$t->data = array("t"=>"o","i"=>$item->oid);
						$context->handle("AppUserPushTask",$t);
					}
					else if($item->did) {
						$t = new AppDevicePushTask();
						$t->did = $item->did;
						$t->alert = "您的订单已完成, 快去给厨师个评价吧!!!";
						$t->sound = null;
						$t->badge = null;
						$t->data = array("t"=>"o","i"=>$item->oid);
						$context->handle("AppDevicePushTask",$t);
					}
					
				}
				else if($status == COOrderStatusConfirm){
					throw new COException("已完成订单,不可反馈",ERROR_COOK_ORDER_STATUS);
				}
				else if($status == COOrderStatusComment){
					throw new COException("已完成的订单,不可反馈",ERROR_COOK_ORDER_STATUS);
				}
				else if($status == COOrderStatusUserCancel){
					throw new COException("用户已取消订单，不可反馈",ERROR_COOK_ORDER_STATUS);
				}
				else if($status == COOrderStatusCancel){
					throw new COException("已取消订单，不可反馈",ERROR_COOK_ORDER_STATUS);
				}
				else {
					throw new COException("未知订单状态[{$status}]，不可反馈",ERROR_COOK_ORDER_STATUS);
				}
			}	
			
			return false;
		}
		
		if($task instanceof COOrderCancelTask){
				
			$context = $this->getContext();
				
			$dbContext = $context->dbContext(DB_COOK);
				
			$item = $dbContext->get("COOrder",array("oid"=>$task->oid));
		
			if($item){
		
				$status = intval($item->status);
		
				$remark = "";
				
				if($status == COOrderStatusNone){
						
					$remark .= "状态: 未确认 -> 确认取消\n";
					
				}
				else if($status == COOrderStatusWait){
							
					$remark = "状态: 已确认 -> 确认取消\n";
					
						
				}
				else if($status == COOrderStatusConfirm){
					throw new COException("已完成订单,不可取消",ERROR_COOK_ORDER_STATUS);
				}
				else if($status == COOrderStatusComment){
					throw new COException("已完成的订单,不可取消",ERROR_COOK_ORDER_STATUS);
				}
				else if($status == COOrderStatusUserCancel){
					throw new COException("用户已取消订单，不可取消",ERROR_COOK_ORDER_STATUS);
				}
				else if($status == COOrderStatusCancel){
					throw new COException("已取消订单，不可取消",ERROR_COOK_ORDER_STATUS);
				}
				else {
					throw new COException("未知订单状态[{$status}]，不可取消",ERROR_COOK_ORDER_STATUS);
				}
				
				$t = new O2OTradeOrderStatusSetTask();
				$t->order = $item;
				$t->status = COOrderStatusCancel;
				
				$remark .= "取消理由: ";
				
				if($task->remark){
					$remark .= $task->remark."\n";
				}
				else {
					$remark .= "无\n";
				}
				
				$t->remark = $remark;
				
				$context->handle("O2OTradeOrderStatusSetTask", $t);
				
				$task->results = $item;
				
				if($item->uid){
					$t = new AppUserPushTask();
					$t->uid = $item->uid;
					$t->alert = null;
					$t->sound = null;
					$t->badge = null;
					$t->data = array("t"=>"o","i"=>$item->oid);
					$context->handle("AppUserPushTask",$t);
				}
				else if($item->did) {
					$t = new AppDevicePushTask();
					$t->did = $item->did;
					$t->alert = null;
					$t->sound = null;
					$t->badge = null;
					$t->data = array("t"=>"o","i"=>$item->oid);
					$context->handle("AppDevicePushTask",$t);
				}
			}
				
			return false;
		}
		
		if($task instanceof COOrderUserCancelTask){
		
			$context = $this->getContext();
		
			$dbContext = $context->dbContext(DB_COOK);
		
			$item = $dbContext->get("COOrder",array("oid"=>$task->oid));
		
			$uid = $context->getInternalDataValue("auth");
			$did = $context->getInternalDataValue("device-did");
			
			if($item){
				
				if($item->uid ){
					if($item->uid != $uid){
						throw new COException("您无权处理此订单",ERROR_COOK_ORDER_STATUS);
					}
				}
				else if($item->did){
					if($item->did != $did){
						throw new COException("您无权处理此订单",ERROR_COOK_ORDER_STATUS);
					}
				}
		
				$status = intval($item->status);
		
				$remark = "";
				
				$hasChanged = false;
		
				if($status == COOrderStatusNone){
		
					$remark .= "状态: 未确认 -> 用户取消\n";
					$hasChanged = true;
					
				}
				else if($status == COOrderStatusWait){
					throw new COException("订单已确认,无法取消,请联系客服",ERROR_COOK_ORDER_STATUS);	
				}
				else if($status == COOrderStatusConfirm){
					throw new COException("已完成的订单,不可取消",ERROR_COOK_ORDER_STATUS);
				}
				else if($status == COOrderStatusComment){
					throw new COException("已完成的订单,不可取消",ERROR_COOK_ORDER_STATUS);
				}
				else if($status == COOrderStatusUserCancel){
					
				}
				else if($status == COOrderStatusCancel){
					
				}
				else {
					throw new COException("未知订单状态[{$status}]，不可取消",ERROR_COOK_ORDER_STATUS);
				}
		
				if($hasChanged){
					
					$t = new O2OTradeOrderStatusSetTask();
					$t->order = $item;
					$t->status = COOrderStatusUserCancel;
			
					$remark .= "用户取消反馈: ";
			
					if($task->feedback){
						$remark .= $task->feedback."\n";
					}
					else {
						$remark .= "无\n";
					}
			
					$t->remark = $remark;
			
					$context->handle("O2OTradeOrderStatusSetTask", $t);
			
					$task->results = $item;
			
					if($item->uid){
						$t = new AppUserPushTask();
						$t->uid = $item->uid;
						$t->alert = null;
						$t->sound = null;
						$t->badge = null;
						$t->data = array("t"=>"o","i"=>$item->oid);
						$context->handle("AppUserPushTask",$t);
					}
					else if($item->did) {
						$t = new AppDevicePushTask();
						$t->did = $item->did;
						$t->alert = null;
						$t->sound = null;
						$t->badge = null;
						$t->data = array("t"=>"o","i"=>$item->oid);
						$context->handle("AppDevicePushTask",$t);
					}
				}
				
				$t = new COOrderPullTask();
				$t->oid = $item->oid;
				
				$context->handle("COOrderPullTask", $t);

			}
		
			return false;
		}
		
		if($task instanceof COOrderCommentTask){
			
			$context = $this->getContext();
			
			$dbContext = $context->dbContext(DB_COOK);
			
			$item = $dbContext->get("COOrder",array("oid"=>$task->oid));
			
			$uid = $context->getInternalDataValue("auth");
			$did = $context->getInternalDataValue("device-did");
				
			if($item){
			
				if($item->uid ){
					if($item->uid != $uid){
						throw new COException("您无权处理此订单",ERROR_COOK_ORDER_STATUS);
					}
				}
				else if($item->did){
					if($item->did != $did){
						throw new COException("您无权处理此订单",ERROR_COOK_ORDER_STATUS);
					}
				}
				
				$status = intval($item->status);
		
				$remark = "";
				
				if($status == COOrderStatusNone){
					throw new COException("订单未完成，无法评论");
				}
				else if($status == COOrderStatusWait){
					throw new COException("订单未完成，无法评论");	
				}
				else if($status == COOrderStatusConfirm){
					
					$remark .= "状态: 已完成 -> 已评论\n";
					
				}
				else if($status == COOrderStatusComment){
					throw new COException("订单已评论过了",ERROR_COOK_ORDER_STATUS);
				}
				else if($status == COOrderStatusUserCancel){
					throw new COException("订单已取消，无法评论",ERROR_COOK_ORDER_STATUS);
				}
				else if($status == COOrderStatusCancel){
					throw new COException("订单已取消，无法评论",ERROR_COOK_ORDER_STATUS);
				}
				else {
					throw new COException("未知订单状态[{$status}]，无法评论",ERROR_COOK_ORDER_STATUS);
				}
				
				$t = new O2OTradeOrderStatusSetTask();
				$t->order = $item;
				$t->status = COOrderStatusComment;
					
				$remark .= "用户评论: ";
					
				if($task->comment){
					$remark .= $task->comment."\n";
				}
				else {
					$remark .= "无\n";
				}
					
				$t->remark = $remark;
					
				$context->handle("O2OTradeOrderStatusSetTask", $t);
					
				if($item->uid){
					$t = new AppUserPushTask();
					$t->uid = $item->uid;
					$t->alert = null;
					$t->sound = null;
					$t->badge = null;
					$t->data = array("t"=>"o","i"=>$item->oid);
					$context->handle("AppUserPushTask",$t);
				}
				else if($item->did) {
					$t = new AppDevicePushTask();
					$t->did = $item->did;
					$t->alert = null;
					$t->sound = null;
					$t->badge = null;
					$t->data = array("t"=>"o","i"=>$item->oid);
					$context->handle("AppDevicePushTask",$t);
				}
				
				$t = new COChefScoreTask();
				$t->orderId = $item->oid;
				$t->pid = $item->chefId;
				$t->score = $task->score;
				
				$context->handle("COChefScoreTask", $t);
				
				$t = new CommentCreateTask();
				$t->etype = CommentEntityOrderType;
				$t->eid = $item->oid;
				$t->ttype = CommentEntityChefType;
				$t->tid = $item->chefId;
				$t->body = $task->comment;
				
				$context->handle("CommentCreateTask", $t);
				
				$t = new COOrderPullTask();
				$t->oid = $item->oid;
				
				$context->handle("COOrderPullTask", $t);
				
			}
			
			return false;
		}
		
		return true;
	}
	
}

?>