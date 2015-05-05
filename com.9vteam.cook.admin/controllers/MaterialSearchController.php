<?php

class MaterialSearchController extends ViewController{
	
	private $searchTable;
	private $searchPageListView;
	private $pageSize = 50;
	private $rowCountLabel;
	
	private $titleText;
	private $typeText;
	private $searchButton;
	
	public function __construct($context,$isPostback=false){
		parent::__construct($context,$isPostback);
		
		$this->searchTable = new TableView("search_table");
		$this->searchPageListView = new ListView("search_page");
		$this->rowCountLabel = new Label("rowCount");
		
		$this->titleText = new TextView("titleText");
		$this->typeText = new TextView("typeText");
		
		$this->searchButton = new Button("searchButton");
		
		$task = new AuthorityEntityValidateTask("cook/admin/material");
		
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
			
		$type = intval($this->typeText->getText());
		
		if($type){
			$sql .= " AND type=".$type;
		}
		
		$title = trim($this->titleText->getText());
		
		if($title){
			$sql .= " AND title LIKE ".$dbContext->parseValue('%'.$title.'%');
		}
		
		$rowCount = $dbContext->countForEntity("COMaterial",$sql);
	
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
	
		$rs = $dbContext->queryEntitys("COMaterial",$sql." ORDER BY mid ASC LIMIT {$offset},{$this->pageSize}");
	
		if($rs){
	
			while($material = $dbContext->nextObject($rs,"COMaterial")){
				$item = array();
				$item["key"] = $material->mid;
				$item["title"] = $material->title;
				$item["type"] = COMaterial::typeTitle( intval( $material->type ) );
				$item["createTime"] = date("Y-m-d H:i:s",$material->createTime);
				$item["command"] =  "";
				
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
		
		
	}
}

?>