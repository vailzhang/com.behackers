<?php

class OrderEditController extends ViewController{
	
	private $editForm;
	
	private $msgLabel;
	
	private $foodsLabel;
	private $cityListView;
	private $areaListView;
	private $statusLabel;
	private $codeLabel;
	private $createTimeLabel;
	private $phoneLabel;
	private $nameLabel;
	private $toolsLabel;
	private $packageLabel;
	private $lockLabel;
	private $logLabel;
	
	private $unlockButton;
	
	private $closeButton;
	
	
	private $confirmView;
	private $confirmForm;
	
	private $cancelView;
	private $cancelForm;
	
	private $chefTemplate;
	
	public function __construct($context,$isPostback=false){
		parent::__construct($context,$isPostback);
		
		$this->editForm = new Form("editForm");
		
		
		$this->msgLabel = new Html("msgLabel");

		$this->foodsLabel = new Html("foodsLabel");
		$this->statusLabel = new Html("statusLabel");
		$this->codeLabel = new Html("codeLabel");
		$this->createTimeLabel = new Html("createTimeLabel");
		$this->phoneLabel = new Html("phoneLabel");
		$this->nameLabel = new Html("nameLabel");
		$this->toolsLabel = new Html("toolsLabel");
		$this->packageLabel = new Html("packageLabel");
		
		$this->cityListView = new ListView("city");
		$this->areaListView = new ListView("area");
		$this->lockLabel = new Html("lockLabel");
		$this->logLabel = new Html("logLabel");
		
		$this->unlockButton = new Button("unlockButton");
		$this->closeButton = new Button("closeButton");
		
		$this->confirmView = new View("confirmView");
		$this->confirmForm = new Form("confirmForm");
		
		$this->cancelView = new View("cancelView");
		$this->cancelForm = new Form("cancelForm");
		
		$this->chefTemplate = new Template("chefTemplate");
		
		$task = new AuthorityEntityValidateTask("cook/admin/order/edit");
		
		try{
			$context->handle("AuthorityEntityValidateTask",$task);
		}
		catch(Exception $ex){
			getCurrentViewContext()->redirect("active.php");
			return ;
		}
		
		if(!$isPostback){
			
			$this->editForm->setSubmitAction(new Action($this,"EditFormSubmit"));
			$this->closeButton->setClickAction(new Action($this,"CloseAction"));
			$this->unlockButton->setClickAction(new Action($this,"UnLockAction"));
			$this->confirmForm->setSubmitAction(new Action($this,"ConfirmFormSubmit"));
			$this->cancelForm->setSubmitAction(new Action($this,"CancelFormSubmit"));
			
			$this->msgLabel->setText("");
		
			$this->loadContent();
		}

	}
	
	public function onCloseAction(){
		
		$context = $this->getContext();
		
		$oid = $context->getInputDataValue("oid");
		
		$task = new COOrderUnLockTask();
		
		$task->oid = $oid;
		
		$context->handle("COOrderUnLockTask",$task);
		
		getCurrentViewContext()->pushFunction("window.close",null);
		
	}
	
	public function onUnLockAction(){
		
		$context = $this->getContext();
		
		$oid = $context->getInputDataValue("oid");
		
		$task = new COOrderLockTask();
		
		$task->oid = $oid;
		
		$context->handle("COOrderLockTask",$task);
		
		$this->loadContent();
	}
	
	public function onCancelFormSubmit(){
		
		$context = $this->getContext();
		
		$fields = $this->cancelForm->getFields();
		
		$oid = isset($fields["oid"]) ? trim($fields["oid"]) : null;
		$remark = isset($fields["remark"]) ? trim($fields["remark"]) : null;;
		
		if(!$remark){
			getCurrentViewContext()->pushFunction("window.alert","请输入取消理由");
			return ;
		}
		
		if($oid){
				
			$task =new COOrderCancelTask();
			$task->oid = $oid;
			$task->remark = $remark;
				
			try{
		
				$context->handle("COOrderCancelTask",$task);
		
				$this->loadContent();
					
			}
			catch(Exception $ex){
		
				getCurrentViewContext()->pushFunction("window.alert",$ex->getMessage());
		
				return ;
			}
				
		}
		
	}
	
	public function onConfirmFormSubmit(){
		
		$context = $this->getContext();
		
	
		$fields = $this->confirmForm->getFields();
		
		$oid = isset($fields["oid"]) ? trim($fields["oid"]) : null;
		$remark = isset($fields["remark"]) ? trim($fields["remark"]) : null;;
		
		if($oid){
			
			$task =new COOrderConfirmTask();
			$task->oid = $oid;
			$task->remark = $remark;
			
			try{
				
				$context->handle("COOrderConfirmTask",$task);
				
				$this->loadContent();
			
			}
			catch(Exception $ex){
				
				getCurrentViewContext()->pushFunction("window.alert",$ex->getMessage());
				
				return ;
			}
			
		}
	}
	
	public function onEditFormSubmit(){
		
		$context = $this->getContext();
		
		$dbContext = $context->dbContext(DB_CLASSIFY);
		
		$fields = $this->editForm->getFields();
		
		$oid = isset($fields["oid"]) ? trim($fields["oid"]) : null;
		
		$orderDay = isset($fields["orderDay"]) ? trim($fields["orderDay"]) : null;
		$orderMeal = isset($fields["orderMeal"]) ? intval($fields["orderMeal"]) : null;
		$orderFood = isset($fields["orderFood"]) ? intval($fields["orderFood"]) : null;
		$address = isset($fields["address"]) ? trim($fields["address"]) : null;
		$price = isset($fields["price"]) ? doubleval($fields["price"]) : null;
		$userCount = isset($fields["userCount"]) ? intval($fields["userCount"]) : null;
		$summary = isset($fields["summary"]) ? trim($fields["summary"]) : null;
		$chefId = isset($fields["chefId"]) ? intval($fields["chefId"]) : null;
		
		$cityId = intval($this->cityListView->getSelectedValue());
		$areaId = intval($this->areaListView->getSelectedValue());
		
		$city = "[$cityId]";
		$area = "[$areaId]";
		
		$classify = $dbContext->get("DBClassify",array("cid"=>$cityId));
		
		if($classify){
			$city = $classify->title;
		}
		
		$classify = $dbContext->get("DBClassify",array("cid"=>$areaId));
		
		if($classify){
			$area = $classify->title;
		}
		
		if($oid){
			
			$task = new COOrderUpdateTask();
			
			$task->oid = $oid;
			$task->orderDay = $orderDay;
			$task->orderMeal = $orderMeal;
			$task->orderFood = $orderFood;
			$task->cityId = $cityId;
			$task->city = $city;
			$task->areaId = $areaId;
			$task->area = $area;
			$task->address = $address;
			$task->summary = $summary;
			$task->price = $price;
			$task->userCount = $userCount;
			$task->chefId = $chefId;
			
			$foodIds = array();
			
			foreach($fields as $key =>$value){
				
				if(strpos($key, "food_") === 0){
					
					$title = trim($value);
					
					if(strlen($title)){
						
						$t = new COFoodGetByTitleTask();
						$t->title = $title;
						
						$context->handle("COFoodGetByTitleTask",$t);
						
						if($t->results){
							$foodIds[] = $t->results->fid;
						}
						
					}
				}
				
			}
			
			$task->foodIds = $foodIds;
			
			try{
				$context->handle("COOrderUpdateTask",$task);
			}
			catch(Exception $ex){
				$this->msgLabel->setText($ex->getMessage());
				return ;
			}
	
			$task = new COOrderUnLockTask();
			
			$task->oid = $oid;
			
			$context->handle("COOrderUnLockTask",$task);
			
			
			getCurrentViewContext()->pushFunction("window.alert","成功处理订单");
			getCurrentViewContext()->pushFunction("window.close",null);
		
				
		}
	
	}
	
	public function loadLogContent(){
		
		$context = $this->getContext();
		$dbContext = $context->dbContext(DB_COOK);
		
		$oid = $context->getInputDataValue("oid");
		
		$rs = $dbContext->queryEntitys("O2ODBTradeOrderStatus","oid=".intval($oid)." ORDER BY osid DESC");
		
		$html = "";
		
		if($rs){
			
			$users = array();
			
			while($item = $dbContext->nextObject($rs,"O2ODBTradeOrderStatus")){
				
				$title = "[$item->uid]";
				
				if(isset($users[$item->uid])){
					$title = $users[$item->uid];
				}
				else {
					$t = new AccountInfoGetTask();
					$t->uid = $item->uid;
					$context->handle("AccountInfoGetTask",$t);
					if(isset($t->infos["title"])){
						$title = $t->infos["title"];
						$users[$item->uid] = $title;					
					}
					else {
						$title = "匿名用户";
					}
				}
				
				$remark = $item->remark;
				
				$remark = str_replace(" ", "&nbsp;", $remark);
				$remark = str_replace("\n", "<br />", $remark);
				
				$html .= "<div class='log-item'><div class='time'>".date("Y-m-d H:i:s",$item->createTime)."</div>"
					."<div class='title'>".$title."</div>"
					."<div class='remark'>".$remark."</div>"
					."</div>";
			}
			
			$dbContext->free($rs);
		}
		
		$this->logLabel->setText($html);
		
	}
	
	public function loadChefItem($chef,$cityId){
		
		$title ="";
		$area = "";
		
		$title = isset($chef["title"]) ? $chef["title"] :"";
		$cuisines = isset($chef["cuisines"]) ? $chef["cuisines"] : array();
		foreach ($cuisines as $cuisine){
			if(strlen($title)){
				$title .= "、".$cuisine->title;
			}
			else {
				$title .= $cuisine->title;
			}
		}
		$areas = isset($chef["areas"]) ? $chef["areas"] : array();
		$area ="";
		foreach ($areas as $a){
		
			if($cityId && $a->pcid != $cityId){
				continue;
			}
				
			if(strlen($area)){
				$area .= "、".$a->title;
			}
			else {
				$area .= $a->title;
			}
		}
		
		$chef["title"] = $title;
		$chef["area"] = $area;
		
		return $chef;
		
	}
	public function loadChefContent($order){
		
		$items = array();
		
		$context = $this->getContext();
		$dbContext = $context->dbContext(DB_COOK);
		
		$t = new COChefGetTask();
		$t->pid = COObjectIntValueForKey($order,"chefId");
		
		$context->handle("COChefGetTask",$t);
		
		if($t->results){
			$chef = $this->loadChefItem($t->results,COObjectIntValueForKey($order,"cityId"));
			$chef["checked"] = "checked";
			$items[] = $chef;
		}
		
		$sql = "SELECT p.chefId,p.price FROM ".COChefPackage::tableName()." as p WHERE "
				."p.packageId=".COObjectIntValueForKey($order,"packageId")
				." AND p.chefId <> ".COObjectIntValueForKey($order,"chefId")
				." ORDER BY p.price ASC";
		
		
		$rs = $dbContext->query($sql);
		
		if($rs){
			
			while($row = $dbContext->next($rs)){
				
				$t = new COChefGetTask();
				$t->pid = $row["chefId"];
				
				$context->handle("COChefGetTask",$t);
				
				if($t->results){
					$chef = $this->loadChefItem($t->results,COObjectIntValueForKey($order,"cityId"));
					$chef["price"] = $row["price"];
					$items[] = $chef;
				}
				
			}
			
			$dbContext->free($rs);
		}
		
		$this->chefTemplate->setItems($items);
	}
	
	public function loadContent(){
		
		$context = $this->getContext();
		$dbContext = $context->dbContext(DB_COOK);
	
		$oid = $context->getInputDataValue("oid");
		
		$this->editForm->setFields(array());

		$lockUser = false;
		
		$order = null;
		
		if($oid){
			
			$task = new COOrderGetTask();
			
			$task->oid = $oid;
			
			$context->handle("COOrderGetTask",$task);
			
			$order = $task->results;
			
			if($order){
				
				$task = new COOrderTryLockTask();
				
				$task->oid = $oid;
				
				$context->handle("COOrderTryLockTask",$task);
				
				$lockUser = $task->lockUid;
				
				if($lockUser == $context->getInternalDataValue("auth")){
					$lockUser = false;
				}
				
				$task = new COOrderGetFoodsTask();
				$task->oid = $oid;
				
				$context->handle("COOrderGetFoodsTask",$task);
				
				$foods = $task->results;
				
				$fields = array();
				
				$fields["oid"] = $oid;
				$fields["address"] = COObjectStringValueForKey($order,"address");
				$fields["summary"] = COObjectStringValueForKey($order, "summary");
				$fields["orderDay"] = date("Y-m-d",COObjectIntValueForKey($order, "orderDay"));
				$fields["orderMeal"] = COObjectIntValueForKey($order, "orderMeal");
				$fields["orderFood"] = COObjectIntValueForKey($order, "orderFood");
				$fields["price"] = COObjectStringValueForKey($order,"price");
				$fields["userCount"] = COObjectStringValueForKey($order,"userCount");
				
				
				$this->editForm->setFields($fields);
				
				$this->cityListView->setSelectedValue(COObjectIntValueForKey($order,"cityId"));
				$this->areaListView->setSelectedValue(COObjectIntValueForKey($order,"areaId"));
				
				$status = COObjectIntValueForKey($order,"status");
				
				$this->confirmView->setHidden(true);
				$this->cancelView->setHidden(true);
				
				if($status == COOrderStatusNone){
					
					$t = time() - COObjectIntValueForKey($order,"createTime");
						
					$text = "<span style='color: red; font-size: 22px'>等待确认</span>"
							."<span>".(intval($t / 3600)).':'.(intval(($t % 3600) / 60)).':'.($t % 60)."</span>";
					
					if(!$lockUser){
						$text .= "<input class='confirm' type='submit' value='确认订单' />"; 
					}
					
					$this->statusLabel->setText($text);
					
					$this->cancelView->setHidden(false);
					$this->cancelForm->setFields(array("oid"=>$oid,"remark"=>""));
					
				}
				else if($status == COOrderStatusWait){
					
					$text = "已确认";
						
					if(!$lockUser){
						$text .= "<input class='confirm' type='submit' value='保存修改' />";
					}
						
					$this->statusLabel->setText($text);
						
					$this->confirmView->setHidden(false);
					
					$this->confirmForm->setFields(array("oid"=>$oid,"remark"=>""));
					
					$this->cancelView->setHidden(false);
					$this->cancelForm->setFields(array("oid"=>$oid,"remark"=>""));
					
				}
				else if($status == COOrderStatusConfirm){
					$this->statusLabel->setText("已完成");
				}
				else if($status == COOrderStatusComment){
					$this->statusLabel->setText("已评论");
				}
				else if($status == COOrderStatusUserCancel){
					$this->statusLabel->setText("用户取消");
				}
				else if($status == COOrderStatusCancel){
					$this->statusLabel->setText("确认取消");
				}
				
				$this->codeLabel->setText(COObjectStringValueForKey($order, "code"));
				$this->createTimeLabel->setText(date("Y-m-d H:i:s",COObjectIntValueForKey($order, "createTime")));
				$this->phoneLabel->setText("<span style='color: red;'>".COObjectStringValueForKey($order, "phone")."</span>");
				$this->nameLabel->setText("<span style='color: red;'>".COObjectStringValueForKey($order, "name")."</span>");
				$this->toolsLabel->setText(COObjectStringValueForKeyPath($order, "package.tools","无"));
				$this->packageLabel->setText(COObjectStringValueForKeyPath($order,"package.title")
						.'<br/>'.COObjectStringValueForKeyPath($order,"package.format")
						.'<br/>¥'.COObjectStringValueForKeyPath($order,"package.chefPrice"));
				
			
				$html = '';
				
				if($foods){
					
					$index = 0;
					
					foreach($foods as $food){
						$html .= "<li><input type='text' name='food_{$index}' value='{$food->title}' /><input type='button' value='删除' onclick='removeTextInput(this)' /></li>";	
						$index ++;
					}
					
					$html.="<li><input type='button' value='增加' onclick=\"addTextInput(this,'food_')\" /></li>";
					
				}
				
				$this->foodsLabel->setText($html);
			
			}
		
		}
		
		$task = new ClassifyQueryTask();
		$task->target = ClassifyTargetArea;
		
		$context->handle("ClassifyQueryTask",$task);
		
		$cityId = $this->cityListView->getSelectedValue();
		
		if($task->results){
			
			$items = array();
			
			foreach($task->results as $classify){
				$items[] = array('value'=>$classify["cid"],'text'=>$classify["title"]);
				
				if($cityId == null){
					$cityId = $classify["cid"];
					$this->cityListView->setSelectedValue($cityId);
				}
				
				if($cityId == $classify["cid"]){
					
					$t = new ClassifyQueryTask();
					$t->target = ClassifyTargetArea;
					$t->pcid = $cityId;
					
					$context->handle("ClassifyQueryTask",$t);
			
					$areas = array();
					
					if($t->results){
						
						foreach($t->results as $cl){
							$areas[] = array('value'=>$cl["cid"],'text'=>$cl["title"]);
						}

					}
					
					$this->areaListView->setItems($areas);
				}
				
			}
			
			$this->cityListView->setItems($items);
			
		}
		
		if($lockUser){
			$this->unlockButton->setHidden(false);
			
			$task = new AccountInfoGetTask();
			
			$task->uid =  $lockUser;
			
			$context->handle("AccountInfoGetTask",$task);
			
			$title = isset($task->infos["title"]) ? $task->infos["title"] : "[$lockUser]";
			
			$this->lockLabel->setText("{$title}&nbsp;正在操作...");
		}
		else {
			$this->lockLabel->setText("");
			$this->unlockButton->setHidden(true);
		}
		
		$this->loadLogContent();
		$this->loadChefContent($order);
	}
	
}

?>