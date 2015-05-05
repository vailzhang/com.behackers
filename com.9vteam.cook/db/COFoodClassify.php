<?php

/**
 * 菜品分类/菜系
 * @author zhanghailong
 *
 */
class COFoodClassify extends DBEntity{
	
	/**
	 * 
	 * @var int
	 */
	public $fcid;
	/**
	 * 
	 * @var int
	 */
	public $target;
	/**
	 * 菜品ID
	 * @var int
	 */
	public $foodId;
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
		return "cook_food_classify";
	}
	
	public static function primaryKeys(){
		return array("fcid");
	}
	
	public static function autoIncrmentFields(){
		return array("fcid");
	}
	
	public static function tableField($field){
		return $field;
	}
	
	public static function tableFieldType($field){
		if($field == "fcid"){
			return "BIGINT NOT NULL";
		}
		if($field == "target"){
			return "INT NULL";
		}
		if($field == "foodId"){
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
		return array("foodId"=>array(array("field"=>"foodId","order"=>"asc")),"classifyId"=>array(array("field"=>"classifyId","order"=>"asc")));
	}
	
}

?>