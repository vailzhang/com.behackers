<?php

class OrderSearchController extends ViewController{
	
	private $searchTable;
	private $searchPageListView;
	private $pageSize = 50;
	private $rowCountLabel;
	
	private $codeText;
	private $nameText;
	private $statusText;
	private $chefCodeText;
	private $searchButton;
	
	public function __construct($context,$isPostback=false){
		parent::__construct($context,$isPostback);
		
		$this->searchTable = new TableView("search_table");
		$this->searchPageListView = new ListView("search_page");
		$this->rowCountLabel = new Label("rowCount");
		
		$this->codeText = new TextView("codeText");
		$this->nameText = new TextView("nameText");
		$this->statusText = new TextView("statusText");
		$this->chefCodeText = new TextView("chefCodeText");
		$this->searchButton = new Button("searchButton");
		
		$task = new AuthorityEntityValidateTask("cook/admin/order");
		
		try{
			$context->handle("AuthorityEntityValidateTask",$task);
		}
		catch(Exception $ex){
			getCurrentViewContext()->redirect("active.php");
			return ;
		}
		
		if(!$isPostback){
			$this->searchPageListView->setSelectedChangeAction(new Action($this,"SearchPageAction"));
			$this->searchTable->setClickAction(new Action($this,"TableAction"));
			$this->searchButton->setClickAction(new Action($this,"SearchAction"));
			$this->loadContent();
		}
	}
	
	public function onSearchPageAction(){
		$this->loadContent();
	}
	
	public function onSearchAction(){
		$this->loadContent();
	}
	
	public function loadContent(){
		
		$context = $this->getContext();
		$dbContext = $context->dbContext(DB_COOK);
	
		$sql = "SELECT o.*,c.name,c.code as chefCode,p.title,p.format FROM "
				.COOrder::tableName()." as o LEFT JOIN "
				.COChef::tableName()." as c ON o.chefId=c.pid LEFT JOIN "
				.COPackage::tableName()." as p ON o.packageId=p.pid";
		
		$sql .= " WHERE 1=1";
		
		$code = trim($this->codeText->getText());
		
		if($code){
			$sql .=" AND o.code LIKE ".$dbContext->parseValue('%'.$code);
		}
		
		$code = trim($this->chefCodeText->getText());
		
		if($code){
			$sql .=" AND c.code LIKE ".$dbContext->parseValue('%'.$code);
		}
		
		$name = trim($this->nameText->getText());
		
		if($name){
			$sql .=" AND c.name LIKE ".$dbContext->parseValue('%'.$name.'%');
		}
		
		$status = trim($this->statusText->getText());
		
		if(strlen($status)){
			$sql .=" AND o.status=".intval($status);
		}
			
		$rowCount = $dbContext->countForEntity("COOrder");
		$noneCount = $dbContext->countForEntity("COOrder","status=0");
		$waitCount = $dbContext->countForEntity("COOrder","status=1");
		$confirmCount = $dbContext->countForEntity("COOrder","status=2");
		$commentCount = $dbContext->countForEntity("COOrder","status=3");
		$userCancelCount = $dbContext->countForEntity("COOrder","status=100");
		$cancelCount = $dbContext->countForEntity("COOrder","status=101");
		
		$this->rowCountLabel->setText("总数:{$rowCount},等待确认:{$noneCount},已确认:{$waitCount},已完成:{$confirmCount},用户取消:{$userCancelCount},确认取消:{$cancelCount}");
		
		$pageIndex = $this->searchPageListView->getSelectedValue();
		if(!$pageIndex){
			$pageIndex = 1;
			$this->searchPageListView->setSelectedValue("1");
		}
	
		$pageCount = $rowCount % $this->pageSize ? intval($rowCount / $this->pageSize) + 1 : intval($rowCount / $this->pageSize);
	
		$items = array();
	
		for($i=0;$i<$pageCount;$i++){
			$items[] = array("value"=>($i +1),"text"=>"第".($i +1)."页");
		}
	
		$this->searchPageListView->setItems($items);
	
		$items = array();
	
		$offset = ($pageIndex -1) *  $this->pageSize;
	
		$sql .= " ORDER BY o.oid DESC LIMIT {$offset},{$this->pageSize}";
		
		$rs = $dbContext->query($sql);
	
		if($rs){
	
			while($row = $dbContext->next($rs)){
				
				$item = array();
				$item["key"] = $row["oid"];
				$item["code"] = $row["code"];
				
				$status = intval($row["status"]);
				
				if($status == COOrderStatusNone){
					
					$t = time() - $row["createTime"];
					
					$item["status"] = "新订单<br />等待确认<br />".(intval($t / 3600)).':'.(intval(($t % 3600) / 60)).':'.($t % 60);
					
					
				}
				else if($status == COOrderStatusWait){
					$item["status"] = "已确认";
				}
				else if($status == COOrderStatusConfirm){
					$item["status"] = "已完成";
				}
				else if($status == COOrderStatusUserCancel){
					$item["status"] = "用户取消";
				}
				else if($status == COOrderStatusCancel){
					$item["status"] = "确认取消";
				}
				else if($status == COOrderStatusComment){
					$item["status"] = "已评论";
				}
				
				$item["format"] = $row["format"];
				$item["title"] = $row["title"].'、'.$row['format'];
				$item["name"] = $row["name"].'、'.$row['chefCode'];
				$item["price"] = $row["price"];
				$item["day"] = date("Y-m-d",$row["orderDay"]);
				
				$meal = intval($row["orderMeal"]);
				
				if($meal == COOrderMealNoon){
					$item["day"] .="午餐";
				}
				else if($meal == COOrderMealDinner){
					$item["day"] .="晚餐";
				}
				
				$item["createTime"] = date("Y-m-d H:i:s",$row["createTime"]);
				$item["command"] =  "<input type='button' value='操作' onclick=\"window.open(  'order_edit.php?oid={$row["oid"]}','order');\" />";
				
				$items[] = $item;
			}
			$dbContext->free($rs);
		}
	
		$this->searchTable->setItems($items);
	}
	
	public function onTableAction(){
		
		$key = $this->searchTable->getActionKey();
		$action = $this->searchTable->getAction();
		$actionData = $this->searchTable->getActionData();
		
		if($action == "delete"){
			
			$context = $this->getContext();
			
			$t = new COPackageRemoveTask();
			$t->pid = $key;
			
			try{
				$context->handle("COPackageRemoveTask",$t);
				$this->loadContent();
			}
			catch(Exception $ex){
				$this->msgLabel->setText($ex->getMessage());
				return ;
			}
			
		}
		
	}
}

?>