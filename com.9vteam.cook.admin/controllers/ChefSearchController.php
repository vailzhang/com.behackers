<?php

class ChefSearchController extends ViewController{
	
	private $searchTable;
	private $searchPageListView;
	private $pageSize = 50;
	private $rowCountLabel;
	
	private $codeText;
	private $nameText;
	private $phoneText;
	private $searchButton;
	
	
	public function __construct($context,$isPostback=false){
		parent::__construct($context,$isPostback);
		
		$this->searchTable = new TableView("search_table");
		$this->searchPageListView = new ListView("search_page");
		$this->rowCountLabel = new Label("rowCount");
		
		$this->codeText = new TextView("codeText");
		$this->nameText = new TextView("nameText");
		$this->phoneText = new TextView("phoneText");
		
		$this->searchButton = new Button("searchButton");
		
		$task = new AuthorityEntityValidateTask("cook/admin/chef");
		
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
		
		$code = trim($this->codeText->getText());
		
		if($code){
			$sql .= " AND code LIKE ".$dbContext->parseValue($code."%");
		}
		
		$name = trim($this->nameText->getText());
		
		if($name){
			$sql .= " AND name LIKE ".$dbContext->parseValue( $name."%");
		}
		
		$phone = trim($this->phoneText->getText());
		
		if($phone){
			$sql .= " AND phone LIKE ".$dbContext->parseValue($phone."%");
		}
		
		$rowCount = $dbContext->countForEntity("COChef",$sql);
	
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
	
		$rs = $dbContext->queryEntitys("COChef",$sql." ORDER BY code ASC LIMIT {$offset},{$this->pageSize}");
	
		if($rs){
	
			while($chef = $dbContext->nextObject($rs,"COChef")){
				$item = array();
				$item["key"] = $chef->pid;
				$item["code"] = $chef->code;
				$item["name"] = $chef->name;
				$item["gender"] = COChef::genderTitle( $chef->gender );
				$item["rank"] = COChef::rankTitle( $chef->rank );
				$item["phone"] = $chef->phone;
				$item["title"] = $chef->title;
				$item["verify"] = $chef->verify ? "是" :"否";
				$item["createTime"] = date("Y-m-d H:i:s",$chef->createTime);
				$item["command"] =  "<a href='chef_edit.php?pid={$chef->pid}'>修改</a>";
				$item["command"] =  "<input type='button' value='修改' onclick=\"window.location = 'chef_edit.php?pid={$chef->pid}';\" />"
						."<input type='button' value='删除' action='delete' key='{$chef->pid}' />";
				
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
				
			$t = new COChefRemoveTask();
			$t->pid = $key;
				
			try{
				$context->handle("COChefRemoveTask",$t);
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