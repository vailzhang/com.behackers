<?php

/**
 *　厨师 服务
 * @author zhanghailong
 *
 */
class COChefService extends Service{
	
	public function handle($taskType,$task){
	
		if($task instanceof COChefCodeCheckTask){
			
			$context = $this->getContext();
				
			$dbContext = $context->dbContext(DB_COOK);
				
			$sql = "SELECT count(*) as count FROM ".COChef::tableName()." WHERE `code`=".$dbContext->parseValue($task->code);
			
			$rs = $dbContext->query($sql);
			
			if($rs){
				
				if($row = $dbContext->next($rs)){
					$task->results = intval($row["count"]) > 0;	
				}
				
				$dbContext->free($rs);
			}
			
			return false;
		}
		
		if($task instanceof COChefCreateTask){
			
			$context = $this->getContext();
			
			$dbContext = $context->dbContext(DB_COOK);
			
			$code = trim($task->code);
			
			if($code ==null || strlen($code) ==0){
				
				throw new COException("请设置厨师编号",ERROR_COOK_NOT_CODE);
				
			}
			
			$t = new COChefCodeCheckTask();
			$t->code = $code;
			
			$context->handle("COChefCodeCheckTask", $t);
			
			if($t->results){
				throw new COException("厨师编号已经存在",ERROR_COOK_CODE_EXISTS);
			}
			
			$item = new COChef();
			$item->uid = $context->getInternalDataValue("auth");
			$item->code = $code;
			$item->name = $task->name;
			$item->logo = $task->logo;
			$item->birthYear = $task->birthYear;
			$item->hometown = $task->hometown;
			$item->gender = $task->gender;
			$item->rank = $task->rank;
			$item->score = $task->score;
			$item->office = $task->office;
			$item->securityIDCode = $task->securityIDCode;
			$item->securityHealthIDCode = $task->securityHealthIDCode;
			$item->speciality = $task->speciality;
			$item->summary = $task->summary;
			$item->area = $task->area;
			$item->phone = $task->phone;
			$item->time = $task->time;
			$item->qq = $task->qq;
			$item->weixin = $task->weixin;
			$item->weibo = $task->weibo;
			$item->state = COChefStateNone;
			$item->title = $task->title;
			$item->verify = $task->verify;
			$item->speciality = $task->speciality;
			$item->orderCount = $item->commentCount = $item->confirmCount = 0;
			$item->createTime = $item->updateTime = time();
			
			$dbContext->insert($item);
			
			$cuisineIds = $task->cuisineIds;
			
			if($cuisineIds){
				
				if(!is_array($cuisineIds)){
					$cuisineIds = CKSplit($cuisineIds);
				}
				
				foreach($cuisineIds as $cid){
					$i = new COChefClassify();
					$i->target = ClassifyTargetCuisine;
					$i->chefId = $item->pid;
					$i->classifyId = $cid;
					$i->createTime = time();
					$dbContext->insert($i);
				}
				
			}
			
			$areaIds = $task->areaIds;
				
			if($areaIds){
			
				if(!is_array($areaIds)){
					$areaIds = CKSplit($areaIds);
				}
			
				foreach($areaIds as $cid){
					$i = new COChefClassify();
					$i->target = ClassifyTargetArea;
					$i->chefId = $item->pid;
					$i->classifyId = $cid;
					$i->createTime = time();
					$dbContext->insert($i);
				}
			
			}
			
			$foodIds = $task->foodIds;
			
			if($foodIds){
					
				if(!is_array($foodIds)){
					$foodIds = CKSplit($foodIds);
				}
					
				foreach($foodIds as $foodId){
					$i = new COChefFood();
					$i->chefId = $item->pid;
					$i->foodId = $foodId;
					$i->createTime = time();
					$dbContext->insert($i);
				}
					
			}
			
			$packages = $task->packages;
				
			if($packages){
					
				if(!is_array($packages)){
					$packages = CKSplit($packages);
				}

				$c = count($packages);
				
				$packageId = null;
				$price = null;
				
				for($i = 0;$i < $c;$i += 2){
					$ii = new COChefPackage();
					$ii->chefId = $item->pid;
					$ii->packageId = $packages[$i];
					$ii->price = $packages[$i + 1];
					$ii->createTime = time();
					$dbContext->insert($ii);
					
					if($packageId == null){
						$packageId = $ii->packageId;
						$price = $ii->price;
					}
					else if($ii->price < $price){
						$packageId = $ii->packageId;
						$price = $ii->price;
					}
				}
				
				if($packageId !== null){
					$item->packageId = $packageId;
					$item->price = $price;
					$dbContext->update($item);
				}
			}
			
			$t = new O2OTradeEntityCreateTask();
			$t->pid = $item->pid;
			
			$context->handle("O2OTradeEntityCreateTask", $t);
			
			$task->results = $item;
			
			return false;
		}
		
		if($task instanceof COChefUpdateTask){
				
			$context = $this->getContext();
				
			$dbContext = $context->dbContext(DB_COOK);

			$item = null;
			
			if($task->pid){
				$item = $dbContext->querySingleEntity("COChef","pid=".intval($task->pid));
			}
			else if($task->code){
				$item = $dbContext->querySingleEntity("COChef","`code`=".$dbContext->parseValue(trim($task->code)));
			}
			
			if($item){
				
				if($task->name !== null){
					$item->name = $task->name;
				}
				if($task->logo !== null){
					$item->logo = $task->logo;
				}
				if($task->birthYear !== null){
					$item->birthYear = $task->birthYear;
				}
				if($task->hometown !== null){
					$item->hometown = $task->hometown;
				}
				if($task->gender !== null){
					$item->gender = $task->gender;
				}
				if($task->rank !== null){
					$item->rank = $task->rank;
				}
				if($task->score !== null){
					$item->score = $task->score;
				}
				if($task->office !== null){
					$item->office = $task->office;
				}
				if($task->securityIDCode !== null){
					$item->securityIDCode = $task->securityIDCode;
				}
				if($task->securityHealthIDCode !== null){
					$item->securityHealthIDCode = $task->securityHealthIDCode;
				}
				if($task->speciality !== null){
					$item->speciality = $task->speciality;
				}
				if($task->summary !== null){
					$item->summary = $task->summary;
				}
				if($task->area !== null){
					$item->area = $task->area;
				}
				if($task->phone !== null){
					$item->phone = $task->phone;
				}
				if($task->time !== null){
					$item->time = $task->time;
				}
				if($task->qq !== null){
					$item->qq = $task->qq;
				}
				if($task->weixin !== null){
					$item->weixin = $task->weixin;
				}
				if($task->weibo !== null){
					$item->weibo = $task->weibo;
				}
				if($task->title !== null){
					$item->title = $task->title;
				}
				if($task->verify !== null){
					$item->verify = $task->verify;
				}
				if($task->speciality !== null){
					$item->speciality = $task->speciality;
				}
				
				$item->updateTime = time();
				
				$cuisineIds = $task->cuisineIds;
					
				if($cuisineIds !== null){
				
					if(!is_array($cuisineIds)){
						$cuisineIds = CKSplit($cuisineIds);
					}
				
					$sql = "DELETE FROM ".COChefClassify::tableName()." WHERE target=".ClassifyTargetCuisine." AND chefId={$item->pid}";
					
					if(count($cuisineIds)){
						$sql .= " AND classifyId NOT IN ".$dbContext->parseArrayValue($cuisineIds);
					}
					
					$dbContext->query($sql);
					
					$cids = array();
					
					$rs = $dbContext->queryEntitys("COChefClassify","target=".ClassifyTargetCuisine." AND chefId={$item->pid} AND classifyId IN ".$dbContext->parseArrayValue($cuisineIds));
					
					if($rs){
						
						while($cc = $dbContext->nextObject($rs,"COChefClassify")){
							$cids[$cc->classifyId] = $cc;
						}
						
						$dbContext->free($rs);
					}
					
					foreach($cuisineIds as $cid){
						if(! isset($cids[$cid])){
							$i = new COChefClassify();
							$i->target = ClassifyTargetCuisine;
							$i->chefId = $item->pid;
							$i->classifyId = $cid;
							$i->createTime = time();
							$dbContext->insert($i);
						}
					}
		
				}
				
				$areaIds = $task->areaIds;
					
				if($areaIds !== null){
				
					if(!is_array($areaIds)){
						$areaIds = CKSplit($areaIds);
					}
				
					$sql = "DELETE FROM ".COChefClassify::tableName()." WHERE target=".ClassifyTargetArea." AND chefId={$item->pid}";
						
					if(count($areaIds)){
						$sql .= " AND classifyId NOT IN ".$dbContext->parseArrayValue($areaIds);
					}
					
					$dbContext->query($sql);
						
					$cids = array();
						
					$rs = $dbContext->queryEntitys("COChefClassify","target=".ClassifyTargetArea." AND chefId={$item->pid} AND classifyId IN ".$dbContext->parseArrayValue($areaIds));
						
					if($rs){
				
						while($cc = $dbContext->nextObject($rs,"COChefClassify")){
							$cids[$cc->classifyId] = $cc;
						}
				
						$dbContext->free($rs);
					}
						
					foreach($areaIds as $cid){
						if(! isset($cids[$cid])){
							$i = new COChefClassify();
							$i->target = ClassifyTargetArea;
							$i->chefId = $item->pid;
							$i->classifyId = $cid;
							$i->createTime = time();
							$dbContext->insert($i);
						}
					}
				
				}
				
				$foodIds = $task->foodIds;
					
				if($foodIds !== null){
				
					if(!is_array($foodIds)){
						$foodIds = CKSplit($foodIds);
					}
				
					$sql = "DELETE FROM ".COChefFood::tableName()." WHERE chefId={$item->pid}";
					
					if(count($foodIds)){
						$sql .= " AND foodId NOT IN ".$dbContext->parseArrayValue($foodIds);
					}
					
					$dbContext->query($sql);
				
					$fids = array();
				
					$rs = $dbContext->queryEntitys("COChefFood"," chefId={$item->pid} AND foodId IN ".$dbContext->parseArrayValue($foodIds));
				
					if($rs){
				
						while($cc = $dbContext->nextObject($rs,"COChefFood")){
							$fids[$cc->foodId] = $cc;
						}
				
						$dbContext->free($rs);
					}
				
					foreach($foodIds as $foodId){
						if(! isset($fids[$foodId])){
							$i = new COChefFood();
							$i->chefId = $item->pid;
							$i->foodId = $foodId;
							$i->createTime = time();
							$dbContext->insert($i);
						}
					}
				
				}
				
				$packages = $task->packages;
					
				if($packages !== null){
				
					if(!is_array($packages)){
						$packages = CKSplit($packages);
					}
					
					$packageIds = array();
					
					$c = count($packages);
					
					for($i=0;$i < $c;$i += 2){
						$packageIds[] = $packages[$i];
					}
				
					$sql = "DELETE FROM ".COChefPackage::tableName()." WHERE chefId={$item->pid}";
						
					if(count($packageIds)){
						$sql .= " AND packageId NOT IN ".$dbContext->parseArrayValue($packageIds);
					}
					
					$dbContext->query($sql);
				
					$pids = array();
				
					$rs = $dbContext->queryEntitys("COChefPackage"," chefId={$item->pid} AND packageId IN ".$dbContext->parseArrayValue($packageIds));
				
					if($rs){
				
						while($cc = $dbContext->nextObject($rs,"COChefPackage")){
							$pids[$cc->packageId] = $cc;
						}
				
						$dbContext->free($rs);
					}
					
					$packageId = null;
					$price = null;
					$ii = null;
					
					for($i=0;$i < $c;$i += 2){
						$pId = $packages[$i];
						if(isset($pids[$pId])){
							$ii = $pids[$pId];
							$ii->price = doubleval( $packages[$i +1] );
							$dbContext->update($ii);
						}
						else {
							$ii = new COChefPackage();
							$ii->chefId = $item->pid;
							$ii->packageId = $pId;
							$ii->price = doubleval( $packages[$i +1] );
							$ii->createTime = time();
							$dbContext->insert($ii);
						}
						if($packageId == null){
							$packageId = $ii->packageId;
							$price = $ii->price;
						}
						else if( $ii->price < $price ){
							$packageId = $ii->packageId;
							$price = $ii->price;
						}
					}
				
					if($packageId !== null){
						$item->packageId = $packageId;
						$item->price = $price;
					}
					
				}
				
				$dbContext->update($item);
			
				$task->results = $item;
				
			}
			
				
			return false;
		}
		
		if($task instanceof COChefStateSetTask){
			
			$context = $this->getContext();
			
			$dbContext = $context->dbContext(DB_COOK);
			
			$item = null;
				
			if($task->pid){
				$item = $dbContext->querySingleEntity("COChef","pid=".intval($task->pid));
			}
			else if($task->code){
				$item = $dbContext->querySingleEntity("COChef","`code`=".$dbContext->parseValue(trim($task->code)));
			}
				
			if($item){
			
				$item->state = $task->state;
				$item->updateTime = time();
				
				$dbContext->update($item);
			}
			
			return false;
		}
		
		if($task instanceof COChefRemoveTask){
			
			$context = $this->getContext();
				
			$dbContext = $context->dbContext(DB_COOK);
				
			$pid = intval($task->pid);
			
			$dbContext->delete("COChef","pid=".$pid);
			$dbContext->delete("COChefClassify","chefId=".$pid);
			
			$t = new O2OTradeEntityRemoveTask();
			$t->pid = $pid;
			
			$context->handle("O2OTradeEntityRemoveTask", $t);
			
			return false;
		}
		
		if($task instanceof COChefScoreTask){
			
			$context = $this->getContext();
			
			$dbContext = $context->dbContext(DB_COOK);
				
			$pid = intval($task->pid);
			
			$chef = $dbContext->get("COChef",array("pid"=>$pid));
			
			if($chef){
				
				$score = intval($task->score);
				
				if($score < 0){
					$score = 0;
				}
				if($score > 10){
					$score = 10;
				}
				
				$item = new COChefScore();
				$item->chefId = $pid;
				$item->orderId = $task->orderId;
				$item->score = $score;
				$item->uid = $context->getInternalDataValue("auth");
				$item->did = $context->getInternalDataValue("device-did");
				$item->createTime = time();
				
				$dbContext->insert($item);
				
				$sql = "SELECT AVG(score) as score FROM ".COChefScore::tableName()." WHERE chefId=".$pid;
				
				$rs = $dbContext->query($sql);
				
				if($rs){
					
					if($row = $dbContext->next($rs)){
						
						$chef->score = intval( $row["score"] );
						$chef->scoreCount = intval($chef->scoreCount) + 1;
						$chef->updateTime = time();
						
						$dbContext->update($chef);
						
					}
					
					$dbContext->free($rs);
					
				}
				
			}
			
			return false;
		}

		return true;
	}
	
}

?>