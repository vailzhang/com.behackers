<?php

/**
 *　订单 服务
 * @author zhanghailong
 *
 */
class COOrderQueryService extends Service{
	
	public function handle($taskType,$task){
		
		if($task instanceof COOrderGetTask){
			
			$context = $this->getContext();
			
			$dbContext = $context->dbContext(DB_COOK);

			$item = null;
			
			if($task->oid !== null){
				$item = $dbContext->get("COOrder",array("oid"=>intval($task->oid)));
			}
			else {
				$item = $dbContext->querySingleEntity("COOrder","code=".$dbContext->parseValue($task->code));
			}
			
			if($item){
				
				$results = array();
				
				foreach($item as $key=>$value){
					if($value !== null){
						$results[$key] = $value;
					}
				}
				
				$t = new COChefGetTask();
				
				$t->pid = $item->chefId;
				
				$context->handle("COChefGetTask",$t);
				
				if($t->results){
					$results["chef"] = $t->results;
				}
				
				$t = new COChefGetPackageTask();
				
				$t->chefId = $item->chefId;
				$t->packageId = $item->packageId;
				
				$context->handle("COChefGetPackageTask", $t);
				
				if($t->results){
					$results["package"] = $t->results;
				}
				
				$task->results = $results;
			}
			
			return false;
		}
		
		if($task instanceof COOrderGetFoodsTask){
			
			$context = $this->getContext();
				
			$dbContext = $context->dbContext(DB_COOK);

			$sql = "SELECT f.* FROM ".COOrderFood::tableName()." as of LEFT JOIN ".COFood::tableName()." as f ON of.foodId=f.fid WHERE of.orderId=".intval($task->oid)." AND NOT isnull(f.fid) ORDER BY ofid ASC";
			
			$rs = $dbContext->query($sql);
			
			if($rs){
				
				$results = array();
				
				while($food = $dbContext->nextObject($rs,"COFood")){
					$results[] = $food;
				}
				
				$dbContext->free($rs);
				
				$task->results = $results;
			}
			
			return false;
			
		}
		
		
		if($task instanceof COOrderPullTask){
			
			$context = $this->getContext();
				
			$dbContext = $context->dbContext(DB_COOK);
				
			$sql = "1=1";
			
			$uid = $context->getInternalDataValue("auth");
			$did = $context->getInternalDataValue("device-did");
			
			if($uid !== null){
				$sql = "uid=".intval($uid);
			}
			else if($did !== null){
				$sql = "isnull(uid) AND did=".intval($did);
			}
			else {
				return false;	
			}
			
			if($task->oid !== null){
				$sql .= " AND oid=".intval($task->oid);
			}
			else if($task->code !== null){
				$sql .= " AND code=".$dbContext->parseValue(trim($task->code));
			}
			else{

				if($task->status !== null){
					$status = CKSplit($task->status);
					$sql .= " AND status IN ".$dbContext->parseArrayValue($status);
				}
				
				if($task->timestamp !== null){
					
					$timestamp = intval($task->timestamp);
					
					if($timestamp === 0){
						if($task->status === null){
							$sql .= " AND status IN (0,1,2)";
						}
					}
					else {
						$sql .= " AND updateTime > ".$timestamp;
					}
					
					$sql .= " ORDER BY updateTime ASC";
				}
				else {
					$sql .= " ORDER BY oid DESC";
				}
				
				$pageIndex = intval($task->pageIndex);
				$pageSize =  intval($task->pageSize);
				
				if($pageIndex < 1){
					$pageIndex = 1;
				}
				
				if($pageSize < 1){
					$pageSize = 20;
				}
				
				$offset = ($pageIndex - 1) * $pageSize;
				
				$sql .= " LIMIT {$offset},{$pageSize}";
				
			}
			
			$rs = $dbContext->queryEntitys("COOrder",$sql);
			
			$results = array();
			
			if($rs){
				
				$keySet = array("lockUid");

				while($order = $dbContext->nextObject($rs,"COOrder")){
					
					$status = intval($order->status);
					
					if($uid === null){
						
						if($order->phone && ( $status == COOrderStatusWait || $status == COOrderStatusConfirm )){
							
							$t = new AccountIDCheckTelTask();
							$t->tel = $order->phone;
							
							$context->handle("AccountIDCheckTelTask", $t);
							
							if($t->uid){
								$uid = $t->uid;
							}
							else {
								$t = new AccountTelVerifyTask();
								$t->tel = $order->phone;
								$context->handle("AccountTelVerifyTask", $t);
								$verify = $t->verify;
								$t = new AccountTelRegisterTask();
								$t->tel = $order->phone;
								$t->verify = $verify;
								$t->password = $verify;
								$context->handle("AccountTelRegisterTask", $t);
							}
							
							if($uid){
								
								$context->setInternalDataValue("auth", $uid);
								
								$t = new AppAutoAuthTask();
								
								$context->handle("AppAutoAuthTask", $t);
								
								$dbContext->query("UPDATE ".COOrder::tableName()." SET uid=".intval($uid)." WHERE isnull(uid) AND did=".intval($did));
								
							}
						}
						
					}
					
					$item = array();
				
					foreach ($order as $key=>$value){
						
						if(!isset($keySet[$key]) && $value !== null){
							$item[$key] = $value;
						}
						
					}
					
					$t = new COChefGetTask();
					
					$t->pid = $order->chefId;
					
					$context->handle("COChefGetTask", $t);
					
					if($t->results){
						
						$item["chef"] = $t->results;
						
					}
					
					$t = new COOrderGetFoodsTask();
					$t->oid = $order->oid;
					
					$context->handle("COOrderGetFoodsTask", $t);
					
					if($t->results){
						$item["foods"] = $t->results;
					}
					
					$t = new COChefGetPackageTask();
					$t->chefId = $order->chefId;
					$t->packageId = $order->packageId;
					
					$context->handle("COChefGetPackageTask", $t);
					
					if($t->results){
						$item["package"] = $t->results;
					}
					
					$t = new COChefScoreGetTask();
					$t->pid = $order->chefId;
					$t->orderId = $order->oid;
					
					$context->handle("COChefScoreGetTask", $t);
					
					if($t->score !== null){
						$item["score"] = $t->score;
					}
				
					$results[] = $item;
					
				}
				
				$dbContext->free($rs);
	
			}

			$context->setOutputDataValue("order-pull-results", $results);
			
			return false;
		}
		
		
		return true;
	}
	
}

?>