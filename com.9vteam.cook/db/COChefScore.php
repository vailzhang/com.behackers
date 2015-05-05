<?php

/**
 * 厨师评分
 * @author zhanghailong
 *
 */
class COChefScore extends DBEntity{
	
	/**
	 * 
	 * @var int
	 */
	public $csid;
	/**
	 * 厨师ID
	 * @var int
	 */
	public $chefId;
	/**
	 * 订单ID
	 * @var int
	 */
	public $orderId;
	/**
	 * 用户ID
	 * @var int
	 */
	public $uid;
	/**
	 * 设备ID
	 * @var int
	 */
	public $did;
	/**
	 * 评分
	 * @var int
	 */
	public $score;
	/**
	 * 创建时间
	 * @var int
	 */
	public $createTime;
	
	public static function tableName(){
		return "cook_chef_score";
	}
	
	public static function primaryKeys(){
		return array("csid");
	}
	
	public static function autoIncrmentFields(){
		return array("csid");
	}
	
	public static function tableField($field){
		return $field;
	}
	
	public static function tableFieldType($field){
		if($field == "csid"){
			return "BIGINT NOT NULL";
		}
		if($field == "chefId"){
			return "BIGINT NULL";
		}
		if($field == "orderId"){
			return "BIGINT NULL";
		}
		if($field == "uid"){
			return "BIGINT NULL";
		}
		if($field == "did"){
			return "BIGINT NULL";
		}
		if($field == "score"){
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
		return array("chefId"=>array(array("field"=>"chefId","order"=>"asc")),"orderId"=>array(array("field"=>"orderId","order"=>"asc")));
	}
	
}

?>