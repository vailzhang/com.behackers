<?php

/**
 *　套餐 服务
 * @author zhanghailong
 *
 */
class COPackageService extends Service{
	
	public function handle($taskType,$task){
	
		if($task instanceof COPackageCreateTask){
			
			$context = $this->getContext();
			
			$dbContext = $context->dbContext(DB_COOK);
			
			$title = trim($task->title);
			
			if(!$title){
				throw new COException("请设置套餐名称",ERROR_COOK_NOT_TITLE);
			}
			
			$item = new COPackage();
			
			$item->title = $title;
			$item->format = $task->format;
			$item->price = $task->price;
			$item->summary = $task->summary;
			$item->uid = $context->getInternalDataValue("auth");
			$item->createTime = $item->updateTime = time();
			
			$dbContext->insert($item);
			
			$foodIds = $task->foodIds;
				
			if($foodIds){
			
				if(!is_array($foodIds)){
					$foodIds = CKSplit($foodIds);
				}
			
				foreach($foodIds as $foodId){
					$i = new COPackageFood();
					$i->packageId = $item->pid;
					$i->foodId = $foodId;
					$i->createTime = time();
					$dbContext->insert($i);
				}
			
			}
			
			$task->results = $item;
			
			return false;
		}
		
		if($task instanceof COPackageUpdateTask){
				
			$context = $this->getContext();
				
			$dbContext = $context->dbContext(DB_COOK);
				
			$pid = intval($task->pid);
			
			$item = $dbContext->get("COPackage",array("pid"=>$pid));
			
			if($item){
			
				if($task->title){	
					$item->title = $task->title;
				}
				if($task->format !== null){
					$item->format = $task->format;
				}
				if($task->price !== null){
					$item->price = $task->price;
				}
				if($task->summary !== null){
					$item->summary = $task->summary;
				}
				
				$item->updateTime = time();
					
				$dbContext->update($item);
					
				$foodIds = $task->foodIds;
			
				if($foodIds){
						
					if(!is_array($foodIds)){
						$foodIds = CKSplit($foodIds);
					}
						
					$dbContext->query("DELETE FROM ".COPackageFood::tableName()." WHERE packageId={$item->pid} AND foodId NOT IN ".$dbContext->parseArrayValue($foodIds));
						
					$fids = array();
						
					$rs = $dbContext->queryEntitys("COPackageFood"," packageId={$item->pid} AND foodId IN ".$dbContext->parseArrayValue($foodIds));
						
					if($rs){
							
						while($cc = $dbContext->nextObject($rs,"COPackageFood")){
							$fids[$cc->foodId] = $cc;
						}
							
						$dbContext->free($rs);
					}
						
					foreach($foodIds as $foodId){
						if(! isset($fids[$foodId])){
							$i = new COPackageFood();
							$i->packageId = $item->pid;
							$i->foodId = $foodId;
							$i->createTime = time();
							$dbContext->insert($i);
						}
					}
						
				}
					
				$task->results = $item;
			}
				
			return false;
		}
		
		if($task instanceof COPackageRemoveTask){
			
			$context = $this->getContext();
			
			$dbContext = $context->dbContext(DB_COOK);
			
			$pid = intval($task->pid);

			$dbContext->delete("COPackage","pid=".$pid);
			$dbContext->delete("COPackageFood","packageId=".$pid);
			
			return false;
		}
		
		if($task instanceof COPackageGetTask){
			
			$context = $this->getContext();
				
			$dbContext = $context->dbContext(DB_COOK);
				
			$pid = intval($task->pid);
				
			$item = $dbContext->get("COPackage",array("pid"=>$pid));
			
			if($item){
				$results = array();
				foreach ($item as $key=>$value){
					if($value !== null){
						$results[$key] = $value;
					}
				}
				$task->results = $results;
			}
			
			return false;
		}
		
		if($task instanceof COPackageGetFoodsTask){
			
			$context = $this->getContext();
			
			$dbContext = $context->dbContext(DB_COOK);
			
			$pid = intval($task->pid);
				
			$sql = "SELECT f.* FROM ".COPackageFood::tableName()." as pf LEFT JOIN ".COFood::tableName()." as f ON pf.foodId=f.fid WHERE pf.packageId={$pid} AND NOT isnull(f.fid)";
			
			$rs = $dbContext->query($sql);
			
			if($rs){
				
				$results = array();
				
				while($food = $dbContext->nextObject($rs,"COFood")){
					$results[] = $food;
				}
				
				$dbContext->free($rs);
				
				$task->results = $results;
			}
		}

		return true;
	}
	
}

?>