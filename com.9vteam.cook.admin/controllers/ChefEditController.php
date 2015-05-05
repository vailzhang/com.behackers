<?php

class ChefEditController extends ViewController{
	
	private $editForm;
	private $msgLabel;
	private $logoView;
	

	private $cuisineLabel;
	private $areaLabel;
	private $foodsLabel;
	private $packageLabel;
	
	public function __construct($context,$isPostback=false){
		parent::__construct($context,$isPostback);
		
		$this->editForm = new Form("editForm");
		
		$this->msgLabel = new Label("msgLabel");

		$this->logoView = new Image("logoView");
		
		$this->cuisineLabel = new Label("cuisineLabel");
		$this->areaLabel = new Label("areaLabel");
		$this->foodsLabel = new Label("foodsLabel");
		$this->packageLabel = new Label("packageLabel");
		
		$task = new AuthorityEntityValidateTask("cook/admin/chef/edit");
		
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
			$this->logoView->setHidden(true);
			
			$this->loadContent();
		}

	}
	
	public function onEditFormSubmit(){
		
		$context = $this->getContext();
		
		$fields = $this->editForm->getFields();
		
		$pid = isset($fields["pid"]) ? trim($fields["pid"]) : null;
		
		$code = isset($fields["code"]) ? trim($fields["code"]) : null;
		$name = isset($fields["name"]) ? trim($fields["name"]) : null;
		$gender = isset($fields["gender"]) ? intval($fields["gender"]) : null;
		$hometown = isset($fields["hometown"]) ? trim($fields["hometown"]) : null;
		$birthYear = isset($fields["birthYear"]) ? intval($fields["birthYear"]) : null;
		$rank = isset($fields["rank"]) ? intval($fields["rank"]) : null;
		$title = isset($fields["title"]) ? trim($fields["title"]) : null;
		$score = isset($fields["score"]) ? intval($fields["score"]) : null;
		$office = isset($fields["office"]) ? trim($fields["office"]) : null;
		$summary = isset($fields["summary"]) ? trim($fields["summary"]) : null;
		$verify = isset($fields["verify"]) ? intval($fields["verify"]) : null;
		$time = isset($fields["time"]) ? trim($fields["time"]) : null;
		$phone = isset($fields["phone"]) ? trim($fields["phone"]) : null;
		$qq = isset($fields["qq"]) ? trim($fields["qq"]) : null;
		$weixin = isset($fields["weixin"]) ? trim($fields["weixin"]) : null;
		$weibo = isset($fields["weibo"]) ? trim($fields["weibo"]) : null;
		$securityIDCode = isset($fields["securityIDCode"]) ? trim($fields["securityIDCode"]) : null;
		
		$logo = $context->getInputDataValue("logo");
		
		if(!$code || strlen($code) ==0){
			$this->msgLabel->setText("请输入厨师编号");
			return;
		}
		
		if(!$name || strlen($name) ==0){
			$this->msgLabel->setText("请输入厨师姓名");
			return;
		}
		
		$taskType = null;
		$task = null;
		
		if($pid){
			$task = new COChefUpdateTask();
			$task->pid = $pid;
			$taskType = "COChefUpdateTask";
		}
		else{
			$task = new COChefCreateTask();
			$taskType = "COChefCreateTask";
		}
		
		$task->code = $code;
		$task->name = $name;
		$task->gender = $gender;
		$task->hometown = $hometown;
		$task->birthYear = $birthYear;
		$task->rank = $rank;
		$task->title = $title;
		$task->score = $score;
		$task->office = $office;
		$task->summary = $summary;
		$task->verify = $verify;
		$task->time = $time;
		$task->phone = $phone;
		$task->qq = $qq;
		$task->weixin = $weixin;
		$task->weibo = $weibo;
		$task->securityIDCode = $securityIDCode;
		
		if($logo != null){
			$task->logo = $logo;
		}
		
		$cuisineIds = array();
		$areaIds = array();
		$foods = array();
		$packageIds = array();
		$packagePrices = array();
		
		foreach($fields as $key=>$value){
			
			if(strpos($key, "cuisine_") ===0){
				$cuisineIds[] = substr($key, 8);
			}
			else if(strpos($key, "area_") ===0){
				$areaIds[] = substr($key, 5);
			}
			else if(strpos($key, "food_") ===0){
				$foods[] = $value;
			}
			else if(strpos($key, "package_") ===0){
				$packageIds[] = substr($key, 8);
			}
			else if(strpos($key, "ppackage_") === 0){
				$packagePrices[substr($key, 9)] = doubleval($value);
			}
		}
		
		$packages = array();
		
		foreach($packageIds as $packageId){
			$packages[] = $packageId;
			$packages[] = $packagePrices[$packageId];
		}
		
		$task->packages =  $packages;
		
		$task->cuisineIds = $cuisineIds;
	
		$task->areaIds = $areaIds;
		
		$foodIds = array();
		
		foreach ($foods as $food){
			
			$title = trim($food);
			
			if(strlen($title)){
				$t = new COFoodGetByTitleTask();
				$t->title = $title;
				$context->handle("COFoodGetByTitleTask",$t);
				if($t->results){
					$foodIds[] = $t->results->fid;
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

		getCurrentViewContext()->redirect("chef.php");
	
	}
	
	public function loadContent(){
		
		$context = $this->getContext();
		$dbContext = $context->dbContext();
	
		$pid = $context->getInputDataValue("pid");
		
		$this->editForm->setFields(array());

		$cids = array();
		
		$packages = array();
		
		if($pid){
			
			$chef = $dbContext->get("COChef",array("pid"=>$pid));
			
			if($chef){
				
					
				$fields = array();
				
				foreach ($chef as $key=>$value){
					if($value !== null){
						$fields[$key] = $value;
					}
				}
				
				if($chef->logo){
					$fields["logoUrl"] = $chef->logo;
					$this->logoView->setSrc(CKURL($chef->logo,160));
					$this->logoView->setHidden(false);
				}
				else {
					$this->logoView->setHidden(true);
				}
				
				$this->editForm->setFields($fields);
				
				$rs = $dbContext->queryEntitys("COChefClassify","target IN (".ClassifyTargetCuisine.",".ClassifyTargetArea.") AND chefId=".$chef->pid);
				
				if($rs){
					
					while($item = $dbContext->nextObject($rs,"COChefClassify")){
						$cids[$item->classifyId] = $item;
					}
					
					$dbContext->free($rs);
				}
				
				$sql = "SELECT cf.* , f.title FROM ".COChefFood::tableName()." as cf LEFT JOIN ".COFood::tableName()." as f ON cf.foodId = f.fid WHERE cf.chefId=".$chef->pid;
				
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
				
				$rs = $dbContext->queryEntitys("COChefPackage","chefId=".$chef->pid);
				
				if($rs){
						
					while($item = $dbContext->nextObject($rs,"COChefPackage")){
						$packages[$item->packageId] = $item;
					}
						
					$dbContext->free($rs);
				}
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
		
		$t->target = ClassifyTargetArea;
		
		$context->handle("ClassifyQueryTask",$t);
		
		if($t->results){
			
			foreach ($t->results as $c){
				$html .="<p><span>".$c["title"]."</span></p><p>";
				
				$tt = new ClassifyQueryTask();
				$tt->pcid = $c["cid"];
				$tt->target = ClassifyTargetArea;
				
				$context->handle("ClassifyQueryTask",$tt);
				
				if($tt->results){
					foreach ($tt->results as $classify){
						$html .= "<input name='area_".$classify["cid"]."' type='checkbox'".(isset($cids[$classify["cid"]]) ? " checked='checked'" :"")
						."'/><span>".$classify["title"]."</span>";
					}
				}
				
				$html .="</p>";
			}
		}
		
		$this->areaLabel->setText($html);
		
		$html = "";
		
		$rs = $dbContext->queryEntitys("COPackage","1=1 ORDER BY pid ASC");
		
		if($rs){
			
			while($package = $dbContext->nextObject($rs,"COPackage")){
				
				$checked = isset($packages[$package->pid]) ? "checked='checked'" : "";
				$price = isset($packages[$package->pid]) ? $packages[$package->pid]->price : $package->price;
				$name = "package_".$package->pid;
				$html .="<li><input type='checkbox' ".$checked." name='".$name."' /><span>".$package->title."</span>/<input type='text' value='".$price."' name='p".$name."' /></li>";
			}
			
			$dbContext->free($rs);
		}
		
		$this->packageLabel->setText($html);
		
	}
	
}

?>