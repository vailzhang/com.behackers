<?php

/**
 * 厨师菜品
 * @author zhanghailong
 *
 */
class COChefFood extends DBEntity{
	
	/**
	 * 
	 * @var int
	 */
	public $cfid;
	/**
	 * 厨师ID
	 * @var int
	 */
	public $chefId;
	/**
	 * 菜品ID
	 * @var int
	 */
	public $foodId;
	/**
	 * 创建时间
	 * @var int
	 */
	public $createTime;
	
	public static function tableName(){
		return "cook_chef_food";
	}
	
	public static function primaryKeys(){
		return array("cfid");
	}
	
	public static function autoIncrmentFields(){
		return array("cfid");
	}
	
	public static function tableField($field){
		return $field;
	}
	
	public static function tableFieldType($field){
		if($field == "cfid"){
			return "BIGINT NOT NULL";
		}
		if($field == "chefId"){
			return "BIGINT NULL";
		}
		if($field == "foodId"){
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
		return array("chefId"=>array(array("field"=>"chefId","order"=>"asc")),"foodId"=>array(array("field"=>"foodId","order"=>"asc")));
	}
	
}

?>