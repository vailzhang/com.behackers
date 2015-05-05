<?php

class PackageSearchController extends ViewController{
	
	private $searchTable;
	private $searchPageListView;
	private $pageSize = 50;
	private $rowCountLabel;
	
	private $titleText;
	private $searchButton;
	
	public function __construct($context,$isPostback=false){
		parent::__construct($context,$isPostback);
		
		$this->searchTable = new TableView("search_table");
		$this->searchPageListView = new ListView("search_page");
		$this->rowCountLabel = new Label("rowCount");
		
		$this->titleText = new TextView("titleText");
		
		$this->searchButton = new Button("searchButton");
		
		$task = new AuthorityEntityValidateTask("cook/admin/package");
		
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
	
		$sql = "1=1";

		
		$title = trim($this->titleText->getText());
		
		if($title){
			$sql .= " AND title LIKE ".$dbContext->parseValue('%'.$title.'%');
		}
		
		$rowCount = $dbContext->countForEntity("COPackage",$sql);
	
		$this->rowCountLabel->setText("总数: ".$rowCount);
		
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
	
		$rs = $dbContext->queryEntitys("COPackage",$sql." ORDER BY pid ASC LIMIT {$offset},{$this->pageSize}");
	
		if($rs){
	
			while($package = $dbContext->nextObject($rs,"COPackage")){
				$item = array();
				$item["key"] = $package->pid;
				$item["title"] = $package->title;
				$item["format"] = $package->format;
				$item["price"] = $package->price;
				$item["createTime"] = date("Y-m-d H:i:s",$package->createTime);
				$item["command"] =  "<input type='button' value='修改' onclick=\"window.location = 'package_edit.php?pid={$package->pid}';\" />"
							."<input type='button' value='删除' action='delete' key='{$package->pid}' />";
				
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