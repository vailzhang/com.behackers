<?php

/**
 * 用户地址
 * @author zhanghailong
 *
 */
class COUserAddress extends DBEntity{
	
	/**
	 * 用户地址ID
	 * @var int
	 */
	public $uaid;
	/**
	 * 用户ID
	 * @var int
	 */
	public $uid;
	/**
	 * 城市
	 * @var String
	 */
	public $city;
	/**
	 * 城区
	 * @var String
	 */
	public $area;
	/**
	 * 地址
	 * @var String
	 */
	public $address;
	/**
	 * 联系人名
	 * @var String
	 */
	public $name;
	/**
	 * 手机号
	 * @var String
	 */
	public $phone;
	/**
	 * 修改时间
	 * @var int
	 */
	public $updateTime;
	/**
	 * 修改时间
	 * @var int
	 */
	public $createTime;
	
	public static function primaryKeys(){
		return array("uaid");
	}
	
	public static function autoIncrmentFields(){
		return array("uaid");
	}
	
	public static function tableName(){
		return "cook_user_address";
	}
	
	public static function tableField($field){
		return $field;
	}
	
	public static function tableFieldType($field){
		if($field == "uaid"){
			return "BIGINT NOT NULL";
		}
		if($field == "uid"){
			return "BIGINT NULL";
		}
		if($field == "city"){
			return "VARCHAR(64) NULL";
		}
		if($field == "area"){
			return "VARCHAR(64) NULL";
		}
		if($field == "address"){
			return "VARCHAR(128) NULL";
		}
		if($field == "name"){
			return "VARCHAR(64) NULL";
		}
		if($field == "phone"){
			return "VARCHAR(32) NULL";
		}
		if($field == "updateTime"){
			return "INT(11) NULL";
		}
		if($field == "createTime"){
			return "INT(11) NULL";
		}
		return "VARCHAR(45) NULL";
	}
	
}

?>