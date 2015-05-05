<?php

/**
 *　App 服务
 * @author zhanghailong
 *
 */
class COAppService extends Service{
	
	public function handle($taskType,$task){
	
		if($task instanceof COAppTask){
			
			$context = $this->getContext();
			
			$dbContext = $context->dbContext(DB_COOK);
			
			
			$results = array();
			
			$results["ranks"] = array(
				
					array("title"=>"家庭厨师","rank"=>1,"type"=>"rank")
					,array("title"=>"专业厨师","rank"=>2,"type"=>"rank")
			);
			
			$results["sorts"] = array(
				array("title"=>"好评优先","sort"=>"score","type"=>"sort")
				,array("title"=>"人均最低","sort"=>"price","type"=>"sort")
				,array("title"=>"热度最高","sort"=>"hot","type"=>"sort")
			);
			
			$results["prices"] = array(
				array("title"=>"60元以下","maxPrice"=>"60","type"=>"price"),
				array("title"=>"60~80元","minPrice"=>"60","maxPrice"=>"80","type"=>"price"),
				array("title"=>"80~100元","minPrice"=>"80","maxPrice"=>"100","type"=>"price"),
				array("title"=>"100元以上","minPrice"=>"100","type"=>"price"),
			);
			
			$results["feedbacks"] = array(
					
				"cancelOrder" => array("title"=>"取消订单","items"=>array( "等待时间过长","想重新下单","误操作" ))
					
			);
			
			$results["callCenter"] = "13910428191";
			
			$results["appCommentUrl"] = "https://itunes.apple.com/us/app/hao-chu-shi/id882132701?l=zh&ls=1&mt=8";
			
			$results["appInviteUrl"] = "https://cook.mmqdd.com/downloads/";
			
			$t = new ClassifyQueryTask();
			
			$t->target = ClassifyTargetCuisine;
			
			$context->handle("ClassifyQueryTask", $t);
			
			if($t->results){
				
				$cuisine = array();
				
				foreach ($t->results as $classify){
					
					$item =array();
					$item["cid"] = $classify["cid"];
					$item["title"] = $classify["title"];
					$item["type"] = "cuisine";
					
					$cuisine[] = $item;
				}
				
				$results["cuisine"] = $cuisine;
				
			}
			
			$t = new ClassifyQueryTask();
				
			$t->target = ClassifyTargetDefault;
				
			$context->handle("ClassifyQueryTask", $t);
				
			if($t->results){
			
				$classifys = array();
			
				foreach ($t->results as $classify){
						
					$item =array();
					$item["cid"] = $classify["cid"];
					$item["title"] = $classify["title"];
					$item["type"] = "classify";
					
					$classifys[] = $item;
				}
			
				$results["classifys"] = $classifys;
			
			}
			
			$t = new ClassifyQueryTask();
			
			$t->target = ClassifyTargetArea;
			
			$context->handle("ClassifyQueryTask", $t);
			
			if($t->results){
					
				$areas = array();
					
				foreach ($t->results as $classify){
			
					$item =array();
					$item["cid"] = $classify["cid"];
					$item["title"] = $classify["title"];
					$item["type"] = "city";
					
					$tt = new ClassifyQueryTask();
					$tt->target = ClassifyTargetArea;
					$tt->pcid = $classify["cid"];
					
					$context->handle("ClassifyQueryTask", $tt);
					
					if($tt->results){
						
						$childs = array();
						
						foreach($tt->results as $cc){
							
							$ii = array();
							$ii["cid"] = $cc["cid"];
							$ii["title"] = $cc["title"];
							$ii["type"] = "area";
							
							$childs[] = $ii;
							
						}
						
						$item["items"] = $childs;
						
					}
					
					$areas[] = $item;
				}
					
				$results["areas"] = $areas;
					
			}
			
			$context->setOutputDataValue("app-results", $results);
			
			return false;
		}
		
		return true;
	}
	
}

?>