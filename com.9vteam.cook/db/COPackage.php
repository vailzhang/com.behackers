<?php


/**
 * 套餐
 * @author zhanghailong
 *
 */
class COPackage extends DBEntity{
	
	/**
	 * 套餐ID
	 * @var int
	 */
	public $pid;
	
	/**
	 * 创建者ID
	 * @var int
	 */
	public $uid;
	/**
	 * 标题
	 * @var String
	 */
	public $title;
	/**
	 * 图片
	 * @var String
	 */
	public $image;
	/**
	 * 规格
	 * @var String
	 */
	public $format;
	/**
	 * 摘要
	 * @var String
	 */
	public $summary;
	/**
	 * 价格
	 * @var double
	 */
	public $price;
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
		return array("pid");
	}
	
	public static function autoIncrmentFields(){
		return array("pid");
	}
	
	public static function tableName(){
		return "cook_package";
	}
	
	public static function tableField($field){
		return $field;
	}
	
	public static function tableFieldType($field){
		if($field == "pid"){
			return "BIGINT NOT NULL";
		}
		if($field == "uid"){
			return "BIGINT NULL";
		}
		if($field == "title"){
			return "VARCHAR(64) NULL";
		}
		if($field == "format"){
			return "VARCHAR(32) NULL";
		}
		if($field == "summary"){
			return "VARCHAR(256) NULL";
		}
		if($field == "price"){
			return "DOUBLE NULL";
		}
		if($field == "image"){
			return "VARCHAR(128) NULL";
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