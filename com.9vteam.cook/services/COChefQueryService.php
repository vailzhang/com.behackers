<?php

/**
 *　厨师 查询 服务
 * @author zhanghailong
 *
 */
class COChefQueryService extends Service{
	
	public function handle($taskType,$task){
	
		if($task instanceof COChefSearchTask){
			
			$context = $this->getContext();
			
			$dbContext = $context->dbContext(DB_COOK);
				
			$pageIndex = intval( $task->pageIndex);
			$pageSize = intval( $task->pageSize);
			
			if($pageIndex < 1){
				$pageIndex = 1;
			}
			
			if($pageSize <1){
				$pageSize = 10;
			}
				
			$offset = ($pageIndex - 1) * $pageSize;
			
			$sql = "SELECT * FROM ".COChef::tableName()." WHERE 1=1";
			
			if($task->cuisineId || $task->classifyId || $task->areaId){
				
				$s = "SELECT chefId FROM ".COChefClassify::tableName()." WHERE 1=1";
				
				if($task->cuisineId){
					$s .=" AND classifyId=".intval($task->cuisineId)." AND target=".ClassifyTargetCuisine;
				}
				if($task->classifyId){
					$s .=" AND classifyId=".intval($task->classifyId)." AND target=".ClassifyTargetDefault;
				}
				if($task->areaId){
					$s .=" AND classifyId=".intval($task->areaId)." AND target=".ClassifyTargetArea;
				}
				
				$sql .= " AND pid IN (".$s.")";
			}
			
			if($task->rank){
				$sql .= " AND rank >= ".intval($task->rank);
			}
			
			if($task->minPrice){
				$sql .=" AND price >= ".doubleval($task->minPrice);	
			}
			
			if($task->maxPrice){
				$sql .=" AND price <= ".doubleval($task->maxPrice);
			}
			
			if($task->sort == "hot"){
				$sql .=" ORDER BY orderCount DESC";
			}
			else if($task->sort == "score"){
				$sql .=" ORDER BY score DESC";
			}
			else if($task->sort == "price"){
				$sql .=" ORDER BY price ASC";
			}
			else {
				$sql .= " ORDER BY commentCount DESC";
			}
			
			$sql .= " LIMIT $offset,$pageSize";
			
			$rs = $dbContext->query($sql);
			
			$results = array();
			
			if($rs){
				
				while($chef = $dbContext->nextObject($rs,"COChef")){
					
					$item = array();
					
					$item["pid"] = $chef->pid;
					$item["name"] = $chef->name;
					
					if($chef->score !== null){
						$item["score"] = $chef->score;
					}
					
					if($chef->rank !== null){
						$item["rank"] = $chef->rank;
					}
					
					if($chef->title !== null){
						$item["title"] = $chef->title;
					}
					
					if($chef->logo !== null){
						$item["logo"] = CKURL($chef->logo,160);
					}
					
					if($chef->verify !== null){
						$item["verify"] = $chef->verify;
					}
					
					if($chef->gender !== null){
						$item["gender"] = $chef->gender;
					}

					if($chef->hometown !== null){
						$item["hometown"] = $chef->hometown;
					}
					
					if($chef->birthYear !== null){
						$item["birthYear"] = $chef->birthYear;
					}
					
					if($chef->orderCount !== null){
						$item["orderCount"] = $chef->orderCount;
					}
					
					if($chef->confirmCount !== null){
						$item["confirmCount"] = $chef->confirmCount;
					}
					
					if($chef->commentCount !== null){
						$item["commentCount"] = $chef->commentCount;
					}
					
					if($chef->scoreCount !== null){
						$item["scoreCount"] = $chef->scoreCount;
					}
					
					if($chef->summary !== null){
						$item["summary"] = $chef->summary;
					}
					
					if($chef->office !== null){
						$item["office"] = $chef->office;
					}
					
					if($chef->price !== null){
						$item["price"] = $chef->price;
					}
					
					if(intval($chef->verify)){
						if($chef->securityIDCode){
							$item["hasSecurityIDCode"] = true;
						}
					}
					
					if($chef->packageId){
						
						$t = new COChefGetPackageTask();
						$t->chefId = $chef->pid;
						$t->packageId = $chef->packageId;
						$context->handle("COChefGetPackageTask", $t);
						
						if($t->results){
							
							$package = $t->results;
							
							
							$t = new COPackageGetFoodsTask();
							$t->pid = $chef->packageId;
							
							$context->handle("COPackageGetFoodsTask", $t);
							
							if($t->results){
								$package["foods"] = $t->results;
							}
							
							$item["package"] = $package;
						}
					}
					
					$t = new COChefGetClassifysTask();
					$t->target = ClassifyTargetCuisine;
					$t->pid = $chef->pid;
					
					$context->handle("COChefGetClassifysTask", $t);
					
					if($t->results){
						$item["cuisines"] = $t->results;
					}
					
					$t = new COChefGetClassifysTask();
					$t->target = ClassifyTargetArea;
					$t->pid = $chef->pid;
						
					$context->handle("COChefGetClassifysTask", $t);
						
					if($t->results){
						$item["areas"] = $t->results;
					}

					$results[] = $item;
				}
				
				$dbContext->free($rs);
			}
			
			$context->setOutputDataValue("chef-search-results", $results);
			
			return false;
		}
		
		if($task instanceof COChefGetFoodsTask){
			
			$context = $this->getContext();
				
			$dbContext = $context->dbContext(DB_COOK);

			$pid = intval($task->pid);
			
			$sql = "SELECT f.* FROM ".COChefFood::tableName()." as cf LEFT JOIN ".COFood::tableName()." as f ON cf.foodId=f.fid WHERE cf.chefId=".$pid." ORDER BY cf.cfid ASC";
			
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
		
		if($task instanceof COChefGetClassifysTask){
				
			$context = $this->getContext();
		
			$dbContext = $context->dbContext(DB_COOK);
		
			$pid = intval($task->pid);
			$target = intval($task->target);	
			
			$sql = "SELECT c.* FROM ".COChefClassify::tableName()." as cc LEFT JOIN ".DBClassify::tableName()." as c ON cc.classifyId=c.cid WHERE cc.chefId={$pid} AND cc.target={$target} ORDER BY cc.ccid ASC";
				
			$rs = $dbContext->query($sql);
				
			if($rs){
		
				$results = array();
		
				while($classify = $dbContext->nextObject($rs,"DBClassify")){
					$results[] = $classify;
				}
		
				$dbContext->free($rs);
		
				$task->results = $results;
			}
				
			return false;
		}
		
		if($task instanceof COChefGetTask){
			
			$context = $this->getContext();
			
			$dbContext = $context->dbContext(DB_COOK);
			
			$pid = intval($task->pid);
			$code = trim($task->code);

			$chef = null;
			
			if($task->pid === null){
				$chef = $dbContext->querySingleEntity("COChef","code=".$dbContext->parseValue($code));
			}
			else {
				$chef = $dbContext->get("COChef",array("pid"=>$pid));
			}
			
			if($chef){

				$item = array();
				
				$item["pid"] = $chef->pid;
				$item["name"] = $chef->name;
				$item["code"] = $chef->code;
					
				if($chef->score !== null){
					$item["score"] = $chef->score;
				}
					
				if($chef->rank !== null){
					$item["rank"] = $chef->rank;
				}
					
				if($chef->title !== null){
					$item["title"] = $chef->title;
				}
					
				if($chef->logo !== null){
					$item["logo"] = CKURL($chef->logo,160);
				}
					
				if($chef->verify !== null){
					$item["verify"] = $chef->verify;
				}
					
				if($chef->gender !== null){
					$item["gender"] = $chef->gender;
				}
				
				if($chef->hometown !== null){
					$item["hometown"] = $chef->hometown;
				}
					
				if($chef->birthYear !== null){
					$item["birthYear"] = $chef->birthYear;
				}
					
				if($chef->orderCount !== null){
					$item["orderCount"] = $chef->orderCount;
				}
					
				if($chef->confirmCount !== null){
					$item["confirmCount"] = $chef->confirmCount;
				}
					
				if($chef->commentCount !== null){
					$item["commentCount"] = $chef->commentCount;
				}
				
				if($chef->scoreCount !== null){
					$item["scoreCount"] = $chef->scoreCount;
				}
					
				if($chef->summary !== null){
					$item["summary"] = $chef->summary;
				}
					
				if($chef->office !== null){
					$item["office"] = $chef->office;
				}
					
				if($chef->price !== null){
					$item["price"] = $chef->price;
				}
					
				if(intval($chef->verify)){
					if($chef->securityIDCode){
						$item["hasSecurityIDCode"] = true;
					}
				}
				
				$t = new COChefGetClassifysTask();
				$t->target = ClassifyTargetCuisine;
				$t->pid = $chef->pid;
					
				$context->handle("COChefGetClassifysTask", $t);
					
				if($t->results){
					$item["cuisines"] = $t->results;
				}
					
				$t = new COChefGetClassifysTask();
				$t->target = ClassifyTargetArea;
				$t->pid = $chef->pid;
				
				$context->handle("COChefGetClassifysTask", $t);
				
				if($t->results){
					$item["areas"] = $t->results;
				}
				
				$uid = $context->getInternalDataValue("auth");
				$did = $context->getInternalDataValue("device-did");
				
				if($uid !== null){
					
					$count = $dbContext->countForEntity("COOrder","chefId=".intval($chef->pid)." AND uid=".intval($uid));
					
					if($count > 0){
						$item["phone"] = $chef->phone;
					}
					
				}
				else if($did !== null){
					
					$count = $dbContext->countForEntity("COOrder","chefId=".intval($chef->pid)." AND did=".intval($did));
						
					if($count > 0){
						$item["phone"] = $chef->phone;
					}
						
				}
				
				$task->results = $item;
			}
			
			return false;
		}
		
		if($task instanceof COChefGetPackagesTask){
			
			$context = $this->getContext();
				
			$dbContext = $context->dbContext(DB_COOK);
				
			$pid = intval($task->chefId);
			
			$sql = "SELECT p.*,cp.price as chefPrice FROM ".COChefPackage::tableName()." as cp LEFT JOIN ".COPackage::tableName()." as p ON cp.packageId=p.pid WHERE cp.chefId={$pid} ORDER BY cpid ASC";
			
			$rs = $dbContext->query($sql);
			
			if($rs){
				
				$results = array();
				
				while($row = $dbContext->next($rs)){
					
					$item = array();
					$item["pid"] = $row["pid"];
					$item["title"] = COObjectStringValueForKey($row, "title");
					$item["summary"] = COObjectStringValueForKey($row, "summary");
					$item["format"] = COObjectStringValueForKey($row, "format");
					$item["price"] = COObjectStringValueForKey($row, "price");
					$item["chefPrice"] = COObjectStringValueForKey($row, "chefPrice");

					$results[] = $item;
				}
				
				$dbContext->free($rs);
				
				$task->results = $results;
			}
			
			return false;
		}
		
		if($task instanceof COChefGetPackageTask){
			
			$context = $this->getContext();
			
			$dbContext = $context->dbContext(DB_COOK);
				
			$chefId = intval($task->chefId);
			$packageId = intval($task->packageId);
			
			$sql = "SELECT p.*,cp.price as chefPrice FROM ".COChefPackage::tableName()." as cp LEFT JOIN ".COPackage::tableName()." as p ON cp.packageId=p.pid WHERE cp.chefId={$chefId} AND cp.packageId={$packageId} ORDER BY cpid ASC";
				
			$rs = $dbContext->query($sql);
				
			if($rs){
			
				$results = array();
			
				if($row = $dbContext->next($rs)){
			
					$results["pid"] = $row["pid"];
					$results["title"] = COObjectStringValueForKey($row, "title");
					$results["summary"] = COObjectStringValueForKey($row, "summary");
					$results["format"] = COObjectStringValueForKey($row, "format");
					$results["price"] = COObjectStringValueForKey($row, "price");
					$results["chefPrice"] = COObjectStringValueForKey($row, "chefPrice");
			
				}
			
				$dbContext->free($rs);
			
				$task->results = $results;
			}
			
			return false;
		}
		
		if($task instanceof COChefScoreGetTask){
			
			$context = $this->getContext();
				
			$dbContext = $context->dbContext(DB_COOK);
			
			$chefId = intval($task->pid);
			
			if($task->orderId === null){
				$chef = $dbContext->get("COChef",array("pid"=>$chefId));
				if($chef){
					$task->score = $chef->score;
				}
			}
			else {
				$rs = $dbContext->query("SELECT AVG(score) as score FROM ".COChefScore::tableName()." WHERE chefId=".$chefId." AND orderId=".intval($task->orderId));
				
				if($rs){
					if($row = $dbContext->next($rs)){
						$task->score = intval($row["score"]);
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