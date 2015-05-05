<?php

/**
 *　材料 服务
 * @author zhanghailong
 *
 */
class COMaterialService extends Service{
	
	public function handle($taskType,$task){
	
		if($task instanceof COMaterialSetTask){
			
			$context = $this->getContext();
			
			$dbContext = $context->dbContext(DB_COOK);
			
			$title = trim($task->title);
			$type = intval($task->type);
			
			if($title && $type){
				
				$item = $dbContext->querySingleEntity("COMaterial","type=".$type." AND title=".$dbContext->parseValue($title));
				
				if($item == null){
					$item = new COMaterial();
					$item->uid = $context->getInternalDataValue("auth");
					$item->title = $title;
					$item->type = $type;
					$item->createTime = $item->updateTime = time();
					$dbContext->insert($item);
				}
				else {
					$item->updateTime = time();
					$dbContext->update($item);
				}
				
				$task->results = $item;
			}
			
			return false;
		}

		return true;
	}
	
}

?>