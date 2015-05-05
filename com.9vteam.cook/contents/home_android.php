<?php

$library = "../../";

require_once "$library/com.9vteam.cook/COContext.php";

$context = new COContext();

$t = new ClassifyQueryTask();

$t->target = ClassifyTargetCuisine;

$context->handle("ClassifyQueryTask", $t);

$cuisines = $t->results ? $t->results : array();

$cityId = $context->getInternalDataValue("cityId");

$task = new COChefSearchTask();
$task->sort = "score";
$task->pageSize = 5;

$context->handle("COChefSearchTask", $task);

$chefs = $context->getOutputDataValue("chef-search-results");

if(!$chefs){
	$chefs = array();
}


header("Content-Type: text/xml; charset=utf-8");
header("VTDOMDocumentVersion: 1.0.0");

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<list width="100%" height="100%" background-color="#eae9e0">
	
	<div width="100%" height="160">
		<page width="100%" height="100%" loops="3" page-id="pageControl">
			<action width="100%" height="100%" action-name="url" uri="root:///root/tab/chef?chefId=1&amp;packageId=2&amp;title=<?php echo urlencode("预约厨师");?>">
				<img width="100%" height="100%" src="<?php echo RESURL("res:///images/2014-06-05/14ceda77154254ac7c7ac2507e6262d5.jpg",600);?>"></img>
				
			</action>
			<action width="100%" height="100%" action-name="url" uri="root:///root/tab/chef?chefId=2&amp;packageId=2&amp;title=<?php echo urlencode("预约厨师");?>">
				<img width="100%" height="100%" src="<?php echo RESURL("res:///images/2014-06-05/d06064b69c6b00ffdaffa988fde01710.jpg",600);?>"></img>
				
			</action>
			<action width="100%" height="100%" action-name="url" uri="root:///root/tab/chef?chefId=3&amp;packageId=2&amp;title=<?php echo urlencode("预约厨师");?>">
				<img width="100%" height="100%" src="<?php echo RESURL("res:///images/2014-06-05/184ec0d71a215c9bdd13e41c97f979fd.jpg",600);?>"></img>
			</action>
		</page>
		<view id="pageControl" viewClass="CKPageControl" width="100%" height="20" top="auto" bottom="4"></view>
	</div>
		
	<div width="100%" height="auto" layout="flow" background-color="#f7f3e8">
		<div background-color="#eadfcc 0.4" width="100%" height="1"></div>
		<?php $index = 0;?>
		<div width="100%" height="80" layout="flow">
			<?php while($index < 4 && $index< count($cuisines)) {
					$cuisine = $cuisines[$index];
				?>
			<action width="25%-1" height="100%" action-name="url" uri="root:///root/tab/chef-search?title=<?php echo urlencode($cuisine["title"]);?>&amp;cuisineId=<?php echo $cuisine["cid"];?>">
				<div background-color="#f2ebdc" width="54" height="54" left="auto" right="auto" top="4" corner-radius="27">
					<img width="44" height="44" left="auto" right="auto" top="auto" bottom="auto" background-color="#e3a478" corner-radius="22"
						src="<?php echo $cuisine["logo"]; ?>"></img>
				</div>
				<label width="100%" height="22" align="center" color="#6b533f" font="14" top="auto" bottom="0"><?php echo ($cuisine["title"]);?></label>
			</action>
			<?php 
					$index ++;
				}
			
			?>
		</div>
		<div background-color="#eadfcc 0.4" width="100%" height="1"></div>
		<div width="100%" height="80" layout="flow">
			<?php while($index < 8 && $index< count($cuisines)) {
					$cuisine = $cuisines[$index];
				?>
			<action width="25%-1" height="100%" action-name="url" uri="root:///root/tab/chef-search?title=<?php echo urlencode($cuisine["title"]);?>&amp;cuisineId=<?php echo $cuisine["cid"];?>">
				<div background-color="#f2ebdc" width="54" height="54" left="auto" right="auto" top="4" corner-radius="27">
					<img width="44" height="44" left="auto" right="auto" top="auto" bottom="auto" background-color="#e3a478" corner-radius="22"
						src="<?php echo $cuisine["logo"];?>"></img>
				</div>
				<label width="100%" height="22" align="center" color="#6b533f" font="14" top="auto" bottom="0"><?php echo ($cuisine["title"]);?></label>
			</action>
			<?php 
					$index ++;
				}
			
			?>
		</div>
		<div background-color="#eadfcc 0.4" width="100%" height="1"></div>
	</div>
	
	<div width="100%" height="auto" layout="flow" padding="10" padding-bottom="0">
		<label width="100%" height="36" font="16" color="#6b533f">了解我们的服务</label>
		<div width="100%" height="auto" layout="flow">
			<div margin-left="5" width="33%-10" height="80" corner-radius="10"
				 border-width="1" border-color="#eadfcc 0.4" background-color="#f7f3e8"
				 action-name="URL" url="http://chef.mmqdd.com/wap/service/chef.html">
				<label width="auto" height="auto"  color="#6b533f" font="16 bold" top="auto" bottom="auto" left="auto" right="auto">厨师认证</label>
			</div>
			<div margin-left="10" width="34%-10" height="80" corner-radius="10"
				 border-width="1" border-color="#eadfcc 0.4" background-color="#f7f3e8"
				 action-name="URL" url="http://chef.mmqdd.com/wap/service/security.html" >
				<label width="auto" height="auto"  color="#6b533f" font="16 bold" top="auto" bottom="auto" left="auto" right="auto">安全保障</label>
			</div>
			<div margin-left="10" width="33%-10" height="80" corner-radius="10"
				 border-width="1" border-color="#eadfcc 0.4" background-color="#f7f3e8"
				 action-name="URL" url="http://chef.mmqdd.com/wap/service/vip.html">
				<label width="auto" height="auto"  color="#6b533f" font="16 bold" top="auto" bottom="auto" left="auto" right="auto">星级服务</label>
			</div>
		</div>
	</div>
	
	<div width="100%" height="auto" layout="flow" padding="10" padding-bottom="0">
		<label width="100%" height="36" font="16" color="#6b533f">精选厨师</label>
	</div>
	
	<?php 
		
		$baseUri = "root:///root/tab/";
		
		foreach($chefs as $chef) {
		
			require "chef_item_android.php";
		}	
	?>
	
	<div width="100%" height="44"></div>
	
</list>
