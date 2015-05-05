<?php

class FoodEditController extends ViewController{
	
	private $editForm;
	private $msgLabel;
	private $imageView;
	

	private $cuisineLabel;
	private $classifyLabel;
	private $mpLabel;
	private $msLabel;
	private $mfLabel;
	
	public function __construct($context,$isPostback=false){
		parent::__construct($context,$isPostback);
		
		$this->editForm = new Form("editForm");
		
		$this->msgLabel = new Label("msgLabel");

		$this->imageView = new Image("imageView");
		
		$this->cuisineLabel = new Label("cuisineLabel");
		$this->classifyLabel = new Label("classifyLabel");
		
		$this->mpLabel = new Label("mpLabel");
		$this->msLabel = new Label("msLabel");
		$this->mfLabel = new Label("mfLabel");
		
		$task = new AuthorityEntityValidateTask("cook/admin/food/edit");
		
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
			$this->imageView->setHidden(true);
			
			$this->loadContent();
		}

	}
	
	public function onEditFormSubmit(){
		
		$context = $this->getContext();
		
		$fields = $this->editForm->getFields();
		
		$fid = isset($fields["fid"]) ? trim($fields["fid"]) : null;
		
		$title = isset($fields["title"]) ? trim($fields["title"]) : null;
		$summary = isset($fields["summary"]) ? trim($fields["summary"]) : null;
		$tools = isset($fields["tools"]) ? trim($fields["tools"]) : null;
		$additional = isset($fields["additional"]) ? doubleval($fields["additional"]) : null;
		
		$image = $context->getInputDataValue("image");
		
		if(!$title || strlen($title) ==0){
			$this->msgLabel->setText("请输入菜品名称");
			return;
		}
		
		$taskType = null;
		$task = null;
		
		if($fid){
			$task = new COFoodUpdateTask();
			$task->fid = $fid;
			$taskType = "COFoodUpdateTask";
		}
		else{
			$task = new COFoodCreateTask();
			$taskType = "COFoodCreateTask";
		}
		
		$task->title = $title;
		$task->summary = $summary;
		$task->tools = $tools;
		$task->additional = $additional;
		
		if($image != null){
			$task->image = $image;
		}
		
		$cuisineIds = array();
		$classifyIds = array();
		$primarys = array();
		$secondarys = array();
		$flavorings = array();
		
		foreach($fields as $key=>$value){
			
			if(strpos($key, "cuisine_") ===0){
				$cuisineIds[] = substr($key, 8);
			}
			else if(strpos($key, "classify_") ===0){
				$classifyIds[] = substr($key, 9);
			}
			else if(strpos($key, "mp_") ===0){
				$v = trim($value);
				if(strlen($v)){
					$primarys[] = $v;
				}
			}
			else if(strpos($key, "ms_") ===0){
				$v = trim($value);
				if(strlen($v)){
					$secondarys[] = $v;
				}
			}
			else if(strpos($key, "mf_") ===0){
				$v = trim($value);
				if(strlen($v)){
					$flavorings[] = $v;
				}
			}
		}
		
		$task->cuisineIds = $cuisineIds;
	
		$task->classifyIds = $classifyIds;
		
		$task->primarys = $primarys;
		
		$task->secondarys = $secondarys;
		
		$task->flavorings = $flavorings;
		
		try{
			$context->handle($taskType,$task);
		}
		catch(Exception $ex){
			$this->msgLabel->setText($ex->getMessage());
			return ;
		}

		getCurrentViewContext()->redirect("food.php");
	
	}
	
	public function loadContent(){
		
		$context = $this->getContext();
		$dbContext = $context->dbContext();
	
		$fid = $context->getInputDataValue("fid");
		
		$this->editForm->setFields(array());

		$cids = array();
		
		if($fid){
			
			$food = $dbContext->get("COFood",array("fid"=>$fid));
			
			if($food){
					
				$fields = array();
				
				foreach ($food as $key=>$value){
					if($value !== null){
						$fields[$key] = $value;
					}
				}
				
				if($food->image){
					$fields["imageUrl"] = $food->image;
					$this->imageView->setSrc(CKURL($food->image,600));
					$this->imageView->setHidden(false);
				}
				else {
					$this->imageView->setHidden(true);
				}
				
				$this->editForm->setFields($fields);
				
				$rs = $dbContext->queryEntitys("COFoodClassify","target IN (".ClassifyTargetCuisine.",".ClassifyTargetDefault.") AND foodId=".$food->fid);
				
				if($rs){
					
					while($item = $dbContext->nextObject($rs,"COFoodClassify")){
						$cids[$item->classifyId] = $item;
					}
					
					$dbContext->free($rs);
				}
				
				
				$sql = "SELECT fm.* , m.title FROM ".COFoodMaterial::tableName()." as fm LEFT JOIN ".COMaterial::tableName()." as m ON fm.mid = m.mid WHERE fm.foodId=".$food->fid;
				
				$rs = $dbContext->query($sql);
				
				$mpHtml = "";
				$msHtml = "";
				$mfHtml = "";
				
				$mpIndex = 0;
				$msIndex = 0;
				$mfIndex = 0;
				
				if($rs){
				
					while($row = $dbContext->next($rs)){
						$type = intval($row["type"]);
						if($type == COMaterialTypePrimary){
							$mpHtml .="<li><input type='text' name='mp_".$mpIndex."' value='".$row["title"]."' /></li>";
							$mpIndex ++;
						}
						else if($type == COMaterialTypeSecondary){
							$msHtml .="<li><input type='text' name='ms_".$msIndex."' value='".$row["title"]."' /></li>";
							$msIndex ++;
						}
						else if($type == COMaterialTypeFlavoring){
							$mfHtml .="<li><input type='text' name='mf_".$mfIndex."' value='".$row["title"]."' /></li>";
							$mfIndex ++;
						}
					}
				
					$dbContext->free($rs);
				}
				
				while($mpIndex < 5){
						
					$mpHtml .="<li><input type='text' name='mp_".$mpIndex."' /></li>";
					$mpIndex ++;
				}
				
				while($msIndex < 5){
				
					$msHtml .="<li><input type='text' name='ms_".$msIndex."' /></li>";
					$msIndex ++;
				}
				
				while($mfIndex < 5){
				
					$mfHtml .="<li><input type='text' name='mf_".$mfIndex."' /></li>";
					$mfIndex ++;
				}
				
				$mpHtml .="<li><input type='button' value='增加' onclick=\"addTextInput(this,'mp_')\" /></li>";
				$msHtml .="<li><input type='button' value='增加' onclick=\"addTextInput(this,'ms_')\" /></li>";
				$mfHtml .="<li><input type='button' value='增加' onclick=\"addTextInput(this,'mf_')\" /></li>";
				
				$this->mpLabel->setText($mpHtml);
				$this->msLabel->setText($msHtml);
				$this->mfLabel->setText($msHtml);
				
			}

		}
		
		$html = "";
		
		$t = new ClassifyQueryTask();
		
		$t->target = ClassifyTargetCuisine;
		
		$context->handle("ClassifyQueryTask",$t);
		
		if($t->results){
			foreach ($t->results as $classify){
				$html .= "<input name='cuisine_".$classify["cid"]."' type='checkbox'".(isset($cids[$classify["cid"]]) ? " checked='checked'" :"")
					."'/><span>".$classify["title"]."</span>";
			}
		}
		
		$this->cuisineLabel->setText($html);
		
		
		$html = "";
		
		$t = new ClassifyQueryTask();
		
		$t->target = ClassifyTargetDefault;
		
		$context->handle("ClassifyQueryTask",$t);
		
		if($t->results){
			foreach ($t->results as $classify){
				$html .= "<input name='classify_".$classify["cid"]."' type='checkbox'".(isset($cids[$classify["cid"]]) ? " checked='checked'" :"")
					."'/><span>".$classify["title"]."</span>";
			}
		}
		
		$this->classifyLabel->setText($html);
	}
	
}

?>