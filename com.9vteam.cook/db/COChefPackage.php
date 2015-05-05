<?php

/**
 * 厨师套餐
 * @author zhanghailong
 *
 */
class COChefPackage extends DBEntity{
	
	/**
	 * 
	 * @var int
	 */
	public $cpid;
	/**
	 * 厨师ID
	 * @var int
	 */
	public $chefId;
	/**
	 * 套餐ID
	 * @var int
	 */
	public $packageId;
	/**
	 * 价格
	 * @var double
	 */
	public $price;
	/**
	 * 创建时间
	 * @var int
	 */
	public $createTime;
	
	public static function tableName(){
		return "cook_chef_package";
	}
	
	public static function primaryKeys(){
		return array("cpid");
	}
	
	public static function autoIncrmentFields(){
		return array("cpid");
	}
	
	public static function tableField($field){
		return $field;
	}
	
	public static function tableFieldType($field){
		if($field == "cpid"){
			return "BIGINT NOT NULL";
		}
		if($field == "chefId"){
			return "BIGINT NULL";
		}
		if($field == "packageId"){
			return "BIGINT NULL";
		}
		if($field == "price"){
			return "DOUBLE NULL";
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
		return array("chefId"=>array(array("field"=>"chefId","order"=>"asc")),"packageId"=>array(array("field"=>"packageId","order"=>"asc")));
	}
	
}

?>