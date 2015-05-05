<?php
if($comment && $chef){
	
	$dbContext = $context->dbContext(DB_COOK);
	
	$score = null;
	
	$order = null;
	$package = null;
	
	if($comment->etype == CommentEntityOrderType){
		
		$t = new COChefScoreGetTask();
		$t->pid = $chef["pid"];
		$t->orderId = $comment->eid;
		
		$context->handle("COChefScoreGetTask", $t);
		
		if($t->score !== null){
			$score = $t->score;
		}
		
		$order = $dbContext->get("COOrder", array("oid"=>$comment->eid));
		
		if($order){
			
			$t = new COChefGetPackageTask();
			$t->chefId = $chef["pid"];
			$t->packageId = $order->packageId;
			
			$context->handle("COChefGetPackageTask", $t);
			
			if($t->results){
				$package = $t->results;
			}
		}
	}
	
	$title = "匿名用户";
	
	if($comment->uid){
		
		$t = new AccountInfoGetTask();
		$t->uid = $comment->uid;
		$t->keys = array(AccountInfoKeyNick);
		
		$context->handle("AccountInfoGetTask", $t);
		
		if(isset($t->infos[AccountInfoKeyNick]["value"])){
			$title = $t->infos[AccountInfoKeyNick]["value"];
		}
	}
	

?>

<div width="100%" height="auto" layout="flow" background-color="#f7f8f0">
	<div width="100%" height="auto" padding="6" layout="flow">
		<?php if($score !== null) {?>
		<view id="scoreView" viewClass="CKStarView" score="<?php echo $score;?>" width="80" height="16"></view>
		<?php }?>
		<?php if($package !== null) {?>
		<label margin-left="12" width="auto" height="auto" color="#69584f" font="12"><?php echo $package["format"];?></label>
		<?php }?>
		<label <?php echo $score !== null || $package !== null ? 'margin-left="12"' : '';?> width="auto" height="auto" color="#69584f" font="12"><?php echo CKHTMLContent($title);?></label>
	</div>
	<div width="100%" height="auto" padding="12">
		<label width="100%" height="auto" color="#333333" font="14"><?php echo CKHTMLContent($comment->body);?></label>
	</div>
	<label width="100%" height="auto" padding="6" align="right" color="#69584f" font="10"><?php echo CKHTMLContent(CODateString($comment->createTime));?></label>
	<img width="100%" height="1" src="@l.png" gravity="resize"></img>
</div>

<?php 
}
?>