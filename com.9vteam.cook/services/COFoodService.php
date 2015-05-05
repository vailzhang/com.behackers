<?php

/**
 *　菜 服务
 * @author zhanghailong
 *
 */
class COFoodService extends Service{
	
	public function handle($taskType,$task){
	
		if($task instanceof COFoodTitleCheckTask){
			
			$context = $this->getContext();
				
			$dbContext = $context->dbContext(DB_COOK);
				
			$sql = "SELECT count(*) as count FROM ".COFood::tableName()." WHERE `title`=".$dbContext->parseValue($task->title);
				
			$rs = $dbContext->query($sql);
				
			if($rs){
			
				if($row = $dbContext->next($rs)){
					$task->results = intval($row["count"]) > 0;
				}
			
				$dbContext->free($rs);
			}
			
			
			return false;
		}
		
		if($task instanceof COFoodCreateTask){
			
			$context = $this->getContext();
			
			$dbContext = $context->dbContext(DB_COOK);
			
			$title = trim($task->title);
			
			if($title ==null || strlen($title) ==0){
				
				throw new COException("请设置菜品名",ERROR_COOK_NOT_TITLE);
				
			}
			
			$t = new COFoodTitleCheckTask();
			$t->title = $title;
			
			$context->handle("COFoodTitleCheckTask", $t);
			
			if($t->results){
				throw new COException("菜品名已经存在",ERROR_COOK_TITLE_EXISTS);
			}
			
			$item = new COFood();
			$item->uid = $context->getInternalDataValue("auth");
			$item->title = $title;
			$item->summary = $task->summary;
			$item->image = $task->image;
			$item->tools = $task->tools;
			$item->additional = $task->additional;
			$item->createTime = $item->updateTime = time();
			
			$dbContext->insert($item);
			
			$cuisineIds = $task->cuisineIds;
				
			if($cuisineIds){
			
				if(!is_array($cuisineIds)){
					$cuisineIds = CKSplit($cuisineIds);
				}
			
				foreach($cuisineIds as $cid){
					$i = new COFoodClassify();
					$i->target = ClassifyTargetCuisine;
					$i->foodId = $item->fid;
					$i->classifyId = $cid;
					$i->createTime = time();
					$dbContext->insert($i);
				}
			
			}
			
			$classifyIds = $task->classifyIds;
				
			if($classifyIds){
			
				if(!is_array($classifyIds)){
					$classifyIds = CKSplit($classifyIds);
				}
			
			
				foreach($classifyIds as $cid){
					$i = new COFoodClassify();
					$i->target = ClassifyTargetDefault;
					$i->foodId = $item->fid;
					$i->classifyId = $cid;
					$i->createTime = time();
					$dbContext->insert($i);
				}
			
			}
			
			$primarys = $task->primarys;
			
			if($primarys){
			
				if(!is_array($primarys)){
					$primarys = CKSplit($primarys);
				}
					
				$primaryIds = array();
					
				foreach($primarys as $primary){
					$t = new COMaterialSetTask();
					$t->type = COMaterialTypePrimary;
					$t->title = $primary;
					$context->handle("COMaterialSetTask", $t);
					if($t->results){
						$primaryIds[] = $t->results->mid;
					}
				}
				
				foreach($primaryIds as $mid){
					$i = new COFoodMaterial();
					$i->type = COMaterialTypePrimary;
					$i->foodId = $item->fid;
					$i->mid = $mid;
					$i->createTime = time();
					$dbContext->insert($i);
				}
			}
			
			$secondarys = $task->secondarys;
			
			if($secondarys){
			
				if(!is_array($secondarys)){
					$secondarys = CKSplit($secondarys);
				}
			
				$secondaryIds = array();
			
				foreach($secondarys as $secondary){
					$t = new COMaterialSetTask();
					$t->type = COMaterialTypeSecondary;
					$t->title = $secondary;
					$context->handle("COMaterialSetTask", $t);
					if($t->results){
						$secondaryIds[] = $t->results->mid;
					}
				}
			
				
			
				foreach($secondaryIds as $mid){
					$i = new COFoodMaterial();
					$i->type = COMaterialTypeSecondary;
					$i->foodId = $item->fid;
					$i->mid = $mid;
					$i->createTime = time();
					$dbContext->insert($i);
				}
			}
			
			$flavorings = $task->flavorings;
			
			if($flavorings){
			
				if(!is_array($flavorings)){
					$flavorings = CKSplit($flavorings);
				}
			
				$flavoringIds = array();
			
				foreach($flavorings as $flavoring){
					$t = new COMaterialSetTask();
					$t->type = COMaterialTypeFlavoring;
					$t->title = $flavoring;
					$context->handle("COMaterialSetTask", $t);
					if($t->results){
						$flavoringIds[] = $t->results->mid;
					}
				}
			
			
				foreach($flavoringIds as $mid){
					$i = new COFoodMaterial();
					$i->type = COMaterialTypeFlavoring;
					$i->foodId = $item->fid;
					$i->mid = $mid;
					$i->createTime = time();
					$dbContext->insert($i);
				}
			}
			
			$task->results = $item;
			
			return false;
		}
		
		if($task instanceof COFoodUpdateTask){
				
			$context = $this->getContext();
				
			$dbContext = $context->dbContext(DB_COOK);

			$item = $dbContext->querySingleEntity("COFood","fid=".intval($task->fid));;
			
			if($item){
				
				if($task->title !== null){
					$item->title = $task->title;
				}
				if($task->summary !== null){
					$item->summary = $task->summary;
				}
				if($task->image !== null){
					$item->image = $task->image;
				}
				if($task->tools !== null){
					$item->tools = $task->tools;
				}
				if($task->additional !== null){
					$item->additional = $task->additional;
				}
				
				$cuisineIds = $task->cuisineIds;
					
				if($cuisineIds){
				
					if(!is_array($cuisineIds)){
						$cuisineIds = CKSplit($cuisineIds);
					}
				
					$dbContext->query("DELETE FROM ".COFoodClassify::tableName()." WHERE target=".ClassifyTargetCuisine." AND foodId={$item->fid} AND classifyId NOT IN ".$dbContext->parseArrayValue($cuisineIds));
						
					$cids = array();
						
					$rs = $dbContext->queryEntitys("COFoodClassify","target=".ClassifyTargetCuisine." AND foodId={$item->fid} AND classifyId IN ".$dbContext->parseArrayValue($cuisineIds));
						
					if($rs){
				
						while($cc = $dbContext->nextObject($rs,"COFoodClassify")){
							$cids[$cc->classifyId] = $cc;
						}
				
						$dbContext->free($rs);
					}
						
					foreach($cuisineIds as $cid){
						if(! isset($cids[$cid])){
							$i = new COFoodClassify();
							$i->target = ClassifyTargetCuisine;
							$i->foodId = $item->fid;
							$i->classifyId = $cid;
							$i->createTime = time();
							$dbContext->insert($i);
						}
					}
				
				}
				
				$classifyIds = $task->classifyIds;
					
				if($classifyIds){
				
					if(!is_array($classifyIds)){
						$classifyIds = CKSplit($classifyIds);
					}
				
					$dbContext->query("DELETE FROM ".COFoodClassify::tableName()." WHERE target=".ClassifyTargetDefault." AND foodId={$item->fid} AND classifyId NOT IN ".$dbContext->parseArrayValue($classifyIds));
				
					$cids = array();
				
					$rs = $dbContext->queryEntitys("COFoodClassify","target=".ClassifyTargetDefault." AND foodId={$item->fid} AND classifyId IN ".$dbContext->parseArrayValue($classifyIds));
				
					if($rs){
				
						while($cc = $dbContext->nextObject($rs,"COFoodClassify")){
							$cids[$cc->classifyId] = $cc;
						}
				
						$dbContext->free($rs);
					}
				
					foreach($classifyIds as $cid){
						if(! isset($cids[$cid])){
							$i = new COFoodClassify();
							$i->target = ClassifyTargetDefault;
							$i->foodId = $item->fid;
							$i->classifyId = $cid;
							$i->createTime = time();
							$dbContext->insert($i);
						}
					}
				
				}
				
				$primarys = $task->primarys;
				
				if($primarys){
				
					if(!is_array($primarys)){
						$primarys = CKSplit($primarys);
					}
					
					$primaryIds = array();
					
					foreach($primarys as $primary){
						$t = new COMaterialSetTask();
						$t->type = COMaterialTypePrimary;
						$t->title = $primary;
						$context->handle("COMaterialSetTask", $t);
						if($t->results){
							$primaryIds[] = $t->results->mid;
						}
					}
					
					$dbContext->query("DELETE FROM ".COFoodMaterial::tableName()." WHERE type=".COMaterialTypePrimary." AND foodId={$item->fid} AND classifyId NOT IN ".$dbContext->parseArrayValue($primaryIds));
					
					$mids = array();
					
					$rs = $dbContext->queryEntitys("COFoodMaterial","type=".COMaterialTypePrimary." AND foodId={$item->fid} AND mid IN ".$dbContext->parseArrayValue($primaryIds));
					
					if($rs){
					
						while($cc = $dbContext->nextObject($rs,"COFoodMaterial")){
							$cids[$cc->mid] = $cc;
						}
					
						$dbContext->free($rs);
					}
					
					foreach($primaryIds as $mid){
						if(! isset($mids[$mid])){
							$i = new COFoodMaterial();
							$i->type = COMaterialTypePrimary;
							$i->foodId = $item->fid;
							$i->mid = $mid;
							$i->createTime = time();
							$dbContext->insert($i);
						}
					}
				}
				
				$secondarys = $task->secondarys;
				
				if($secondarys){
				
					if(!is_array($secondarys)){
						$secondarys = CKSplit($secondarys);
					}
						
					$secondaryIds = array();
						
					foreach($secondarys as $secondary){
						$t = new COMaterialSetTask();
						$t->type = COMaterialTypeSecondary;
						$t->title = $secondary;
						$context->handle("COMaterialSetTask", $t);
						if($t->results){
							$secondaryIds[] = $t->results->mid;
						}
					}
						
					$dbContext->query("DELETE FROM ".COFoodMaterial::tableName()." WHERE type=".COMaterialTypeSecondary." AND foodId={$item->fid} AND classifyId NOT IN ".$dbContext->parseArrayValue($secondaryIds));
						
					$mids = array();
						
					$rs = $dbContext->queryEntitys("COFoodMaterial","type=".COMaterialTypeSecondary." AND foodId={$item->fid} AND mid IN ".$dbContext->parseArrayValue($secondaryIds));
						
					if($rs){
							
						while($cc = $dbContext->nextObject($rs,"COFoodMaterial")){
							$cids[$cc->mid] = $cc;
						}
							
						$dbContext->free($rs);
					}
						
					foreach($secondaryIds as $mid){
						if(! isset($mids[$mid])){
							$i = new COFoodMaterial();
							$i->type = COMaterialTypeSecondary;
							$i->foodId = $item->fid;
							$i->mid = $mid;
							$i->createTime = time();
							$dbContext->insert($i);
						}
					}
				}
				
				$flavorings = $task->flavorings;
				
				if($flavorings){
				
					if(!is_array($flavorings)){
						$flavorings = CKSplit($flavorings);
					}
						
					$flavoringIds = array();
						
					foreach($flavorings as $flavoring){
						$t = new COMaterialSetTask();
						$t->type = COMaterialTypeFlavoring;
						$t->title = $flavoring;
						$context->handle("COMaterialSetTask", $t);
						if($t->results){
							$flavoringIds[] = $t->results->mid;
						}
					}
						
					$dbContext->query("DELETE FROM ".COFoodMaterial::tableName()." WHERE type=".COMaterialTypeFlavoring." AND foodId={$item->fid} AND classifyId NOT IN ".$dbContext->parseArrayValue($flavoringIds));
						
					$mids = array();
						
					$rs = $dbContext->queryEntitys("COFoodMaterial","type=".COMaterialTypeFlavoring." AND foodId={$item->fid} AND mid IN ".$dbContext->parseArrayValue($flavoringIds));
						
					if($rs){
							
						while($cc = $dbContext->nextObject($rs,"COFoodMaterial")){
							$cids[$cc->mid] = $cc;
						}
							
						$dbContext->free($rs);
					}
						
					foreach($flavoringIds as $mid){
						if(! isset($mids[$mid])){
							$i = new COFoodMaterial();
							$i->type = COMaterialTypeFlavoring;
							$i->foodId = $item->fid;
							$i->mid = $mid;
							$i->createTime = time();
							$dbContext->insert($i);
						}
					}
				}
				
				$item->updateTime = time();
				
				$dbContext->update($item);
				
				$task->results = $item;
			}
			
				
			return false;
		}

		if($task instanceof COFoodRemoveTask){
			
			$context = $this->getContext();
			
			$dbContext = $context->dbContext(DB_COOK);
			
			$fid = intval($task->fid);
			
			$dbContext->delete("COFood","fid=".$fid);
			
			$dbContext->delete("COFoodClassify","foodId=".$fid);
			
			$dbContext->delete("COFoodMaterial","foodId=".$fid);
			
			return false;
		}
		
		if($task instanceof COFoodGetByTitleTask){
			
			$context = $this->getContext();
				
			$dbContext = $context->dbContext(DB_COOK);
			
			$title = trim($task->title);

			$item = $dbContext->querySingleEntity("COFood","title=".$dbContext->parseValue($title));
			
			if($item == null){
				$item = new COFood();
				$item->uid = $context->getInternalDataValue("auth");
				$item->title = $title;
				$item->createTime = $item->updateTime = time();
				$dbContext->insert($item);
			}
			
			$task->results = $item;
			
			return false;
		}
		
		return true;
	}
	
}

?>