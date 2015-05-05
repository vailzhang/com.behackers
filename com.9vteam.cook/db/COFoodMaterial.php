<?php

/**
 * 菜品材料
 * @author zhanghailong
 *
 */
class COFoodMaterial extends DBEntity{
	
	/**
	 * 
	 * @var int
	 */
	public $fmid;
	/**
	 * 菜品ID
	 * @var int
	 */
	public $foodId;
	/**
	 * 类型
	 * @var COMaterialType
	 */
	public $type;
	/**
	 * 分类ID
	 * @var int
	 */
	public $mid;
	/**
	 * 创建时间
	 * @var int
	 */
	public $createTime;
	
	public static function tableName(){
		return "cook_food_material";
	}
	
	public static function primaryKeys(){
		return array("fmid");
	}
	
	public static function autoIncrmentFields(){
		return array("fmid");
	}
	
	public static function tableField($field){
		return $field;
	}
	
	public static function tableFieldType($field){
		if($field == "fmid"){
			return "BIGINT NOT NULL";
		}
		if($field == "foodId"){
			return "BIGINT NULL";
		}
		if($field == "mid"){
			return "BIGINT NULL";
		}
		if($field == "type"){
			return "INT NULL";
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
		return array("foodId"=>array(array("field"=>"foodId","order"=>"asc")),"mid"=>array(array("field"=>"mid","order"=>"asc")));
	}
	
}

?>