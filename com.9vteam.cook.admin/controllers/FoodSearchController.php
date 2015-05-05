<?php

class FoodSearchController extends ViewController{
	
	private $searchTable;
	private $searchPageListView;
	private $pageSize = 50;
	private $rowCountLabel;
	
	private $fidText;
	private $titleText;
	private $searchButton;
	
	public function __construct($context,$isPostback=false){
		parent::__construct($context,$isPostback);
		
		$this->searchTable = new TableView("search_table");
		$this->searchPageListView = new ListView("search_page");
		$this->rowCountLabel = new Label("rowCount");
		
		$this->fidText = new TextView("fidText");
		$this->titleText = new TextView("titleText");
		
		$this->searchButton = new Button("searchButton");
		
		$task = new AuthorityEntityValidateTask("cook/admin/food/edit");
		
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
		
		$fid = trim($this->fidText->getText());
		
		if($fid){
			$sql .= " AND fid = ".intval($fid);
		}
		
		$title = trim($this->titleText->getText());
		
		if($title){
			$sql .= " AND title LIKE ".$dbContext->parseValue('%'.$title.'%');
		}
		
		$rowCount = $dbContext->countForEntity("COFood",$sql);
	
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
	
		$rs = $dbContext->queryEntitys("COFood",$sql." ORDER BY fid ASC LIMIT {$offset},{$this->pageSize}");
	
		if($rs){
	
			while($food = $dbContext->nextObject($rs,"COFood")){
				$item = array();
				$item["key"] = $food->fid;
				$item["title"] = $food->title;
				$item["createTime"] = date("Y-m-d H:i:s",$food->createTime);
				$item["command"] =  "<input type='button' value='修改' onclick=\"window.location = 'food_edit.php?fid={$food->fid}';\" />"
							."<input type='button' value='删除' action='delete' key='{$food->fid}' />";
				
				$cuisines = "";
				$classifys = "";
				
				$sql = "SELECT fc.*,c.title FROM ".COFoodClassify::tableName()." as fc LEFT JOIN ".DBClassify::tableName()." as c ON fc.classifyId = c.cid WHERE fc.foodId="
						.$food->fid." AND fc.target IN (".ClassifyTargetCuisine.','.ClassifyTargetDefault.') ORDER BY fc.fcid ASC';
				
				$rss = $dbContext->query($sql);

				if($rss){
					
					while($row = $dbContext->next($rss)){
						$target = intval($row["target"]);
						
						if($target == ClassifyTargetCuisine){
							if(strlen($cuisines)){
								$cuisines .= ','.$row["title"];
							}
							else{
								$cuisines .= $row["title"];
							}
						}
						else if($target == ClassifyTargetDefault){
							if(strlen($classifys)){
								$classifys .= ','.$row["title"];
							}
							else{
								$classifys .= $row["title"];
							}
						}
					}

					$dbContext->free($rss);
				}
				
				$item["cuisine"] = $cuisines;
				$item["classify"] = $classifys;
				
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
			
			$t = new COFoodRemoveTask();
			$t->fid = $key;
			
			try{
				$context->handle("COFoodRemoveTask",$t);
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