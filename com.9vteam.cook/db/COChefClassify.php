<?php

/**
 * 厨师分类/菜系
 * @author zhanghailong
 *
 */
class COChefClassify extends DBEntity{
	
	/**
	 * 
	 * @var int
	 */
	public $ccid;
	/**
	 * 
	 * @var int
	 */
	public $target;
	/**
	 * 厨师ID
	 * @var int
	 */
	public $chefId;
	/**
	 * 分类ID
	 * @var int
	 */
	public $classifyId;
	/**
	 * 创建时间
	 * @var int
	 */
	public $createTime;
	
	public static function tableName(){
		return "cook_chef_classify";
	}
	
	public static function primaryKeys(){
		return array("ccid");
	}
	
	public static function autoIncrmentFields(){
		return array("ccid");
	}
	
	public static function tableField($field){
		return $field;
	}
	
	public static function tableFieldType($field){
		if($field == "ccid"){
			return "BIGINT NOT NULL";
		}
		if($field == "target"){
			return "INT NULL";
		}
		if($field == "chefId"){
			return "BIGINT NULL";
		}
		if($field == "classifyId"){
			return "BIGINT NULL";
		}
		if($field == "createTime"){
			return "INT(11) NULL";
		}
		return "VARCHAR(45) NULL";
	}
	
	/**
	 * @return array("index_name"=>array(array("field"=>"field1","order"="desc"),array("field"=>"field2","order"="asc")))
	 */
	public static function indexs(){
		return array("chefId"=>array(array("field"=>"chefId","order"=>"asc")),"classifyId"=>array(array("field"=>"classifyId","order"=>"asc")));
	}
	
}

?>