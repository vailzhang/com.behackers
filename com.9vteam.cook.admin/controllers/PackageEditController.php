<?php

class PackageEditController extends ViewController{
	
	private $editForm;
	private $msgLabel;
	
	private $foodsLabel;
	
	public function __construct($context,$isPostback=false){
		parent::__construct($context,$isPostback);
		
		$this->editForm = new Form("editForm");
		
		$this->msgLabel = new Label("msgLabel");

		$this->foodsLabel = new Label("foodsLabel");
		
		$task = new AuthorityEntityValidateTask("cook/admin/package/edit");
		
		try{
			$context->handle("AuthorityEntityValidateTask",$task);
		}
		catch(Exception $ex){
			getCurrentViewContext()->redirect("active.php");
			return ;
		}
		
		if(!$isPostback){
			
			$this->editForm->setSubmitAction(new Action($this,"EditFormSubmit"));
			
			$this->msgLabel->setText("");
			
			$this->loadContent();
		}

	}
	
	public function onEditFormSubmit(){
		
		$context = $this->getContext();
		
		$fields = $this->editForm->getFields();
		
		$pid = isset($fields["pid"]) ? trim($fields["pid"]) : null;
		
		$title = isset($fields["title"]) ? trim($fields["title"]) : null;
		$format = isset($fields["format"]) ? trim($fields["format"]) : null;
		$summary = isset($fields["summary"]) ? trim($fields["summary"]) : null;
		$price = isset($fields["price"]) ? doubleval($fields["price"]) : null;
		
		$image = $context->getInputDataValue("image");
		
		if(!$title || strlen($title) ==0){
			$this->msgLabel->setText("请输入套餐名称");
			return;
		}
		
		if(!$format || strlen($format) ==0){
			$this->msgLabel->setText("请输入规格");
			return;
		}
		
		$taskType = null;
		$task = null;
		
		if($pid){
			$task = new COPackageUpdateTask();
			$task->pid = $pid;
			$taskType = "COPackageUpdateTask";
		}
		else{
			$task = new COPackageCreateTask();
			$taskType = "COPackageCreateTask";
		}
		
		$task->title = $title;
		$task->summary = $summary;
		$task->format = $format;
		$task->price = $price;
		
		$foodIds = array();
		
		foreach($fields as $key=>$value){
			
			if(strpos($key, "food_") ===0){
				
				$food = trim($value);
				
				if($food){
					$t =  new COFoodGetByTitleTask();
					$t->title = $food;
					$context->handle("COFoodGetByTitleTask",$t);
					if($t->results){
						$foodIds[] = $t->results->fid;
					}
				}
			}
			
		}
		
		$task->foodIds = $foodIds;
	
		try{
			$context->handle($taskType,$task);
		}
		catch(Exception $ex){
			$this->msgLabel->setText($ex->getMessage());
			return ;
		}

		getCurrentViewContext()->redirect("package.php");
	
	}
	
	public function loadContent(){
		
		$context = $this->getContext();
		$dbContext = $context->dbContext();
	
		$pid = $context->getInputDataValue("pid");
		
		$this->editForm->setFields(array());

		$cids = array();
		
		if($pid){
			
			$package = $dbContext->get("COPackage",array("pid"=>$pid));
			
			if($package){
					
				$fields = array();
				
				foreach ($package as $key=>$value){
					if($value !== null){
						$fields[$key] = $value;
					}
				}
				
				$this->editForm->setFields($fields);
				
				$sql = "SELECT pf.* , f.title FROM ".COPackageFood::tableName()." as pf LEFT JOIN ".COFood::tableName()." as f ON pf.foodId = f.fid WHERE pf.packageId=".$package->pid." ORDER BY pfid ASC";
				
				$rs = $dbContext->query($sql);
				
				$html = "";
				$index = 0;
				
				if($rs){
				
					while($row = $dbContext->next($rs)){
						$html .="<li><input type='text' name='food_".$index."' value='".$row["title"]."' /></li>";
						$index ++;
					}
				
					$dbContext->free($rs);
				}
				
				while($index < 5){
						
					$html .="<li><input type='text' name='food_".$index."' /></li>";
					$index ++;
				}
				
				$html .="<li><input type='button' value='增加' onclick=\"addTextInput(this,'food_')\" /></li>";
				
				$this->foodsLabel->setText($html);
				
			}

		}
		
		
	}
	
}

?>