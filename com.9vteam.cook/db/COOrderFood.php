<?php

/**
 * 订单菜品
 * @author zhanghailong
 *
 */
class COOrderFood extends DBEntity{
	
	/**
	 * 
	 * @var int
	 */
	public $ofid;
	/**
	 * 订单ID
	 * @var int
	 */
	public $orderId;
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
		return "cook_order_food";
	}
	
	public static function primaryKeys(){
		return array("ofid");
	}
	
	public static function autoIncrmentFields(){
		return array("ofid");
	}
	
	public static function tableField($field){
		return $field;
	}
	
	public static function tableFieldType($field){
		if($field == "ofid"){
			return "BIGINT NOT NULL";
		}
		if($field == "orderId"){
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
		return array("orderId"=>array(array("field"=>"orderId","order"=>"asc")),"foodId"=>array(array("field"=>"foodId","order"=>"asc")));
	}
	
}

?>