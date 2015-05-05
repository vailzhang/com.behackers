<?php


/**
 * 套餐 菜品
 * @author zhanghailong
 *
 */
class COPackageFood extends DBEntity{
	
	/**
	 * 套餐菜品ID
	 * @var int
	 */
	public $pfid;
	/**
	 * 套餐ID
	 * @var int
	 */
	public $packageId;
	/**
	 * 菜品ID
	 * @var int
	 */
	public $foodId;
	/**
	 * 修改时间
	 * @var int
	 */
	public $createTime;
	
	public static function primaryKeys(){
		return array("pfid");
	}
	
	public static function autoIncrmentFields(){
		return array("pfid");
	}
	
	public static function tableName(){
		return "cook_package_food";
	}
	
	public static function tableField($field){
		return $field;
	}
	
	public static function tableFieldType($field){
		if($field == "pfid"){
			return "BIGINT NOT NULL";
		}
		if($field == "packageId"){
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
	
}

?>