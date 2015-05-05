<?php

define("COMaterialTypeNone",0);
define("COMaterialTypePrimary",1); 		//主料
define("COMaterialTypeSecondary",2); 	//辅料
define("COMaterialTypeFlavoring",3);	//调料

/**
 * 材料
 * @author zhanghailong
 *
 */
class COMaterial extends DBEntity{
	
	/**
	 * 材料ID
	 * @var int
	 */
	public $mid;
	
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
	 * 类型
	 * @var COMaterialType
	 */
	public $type;
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
		return array("mid");
	}
	
	public static function autoIncrmentFields(){
		return array("mid");
	}
	
	public static function tableName(){
		return "cook_material";
	}
	
	public static function tableField($field){
		return $field;
	}
	
	public static function tableFieldType($field){
		if($field == "mid"){
			return "BIGINT NOT NULL";
		}
		if($field == "uid"){
			return "BIGINT NULL";
		}
		if($field == "type"){
			return "INT NULL";
		}
		if($field == "title"){
			return "VARCHAR(64) NULL";
		}
		if($field == "updateTime"){
			return "INT(11) NULL";
		}
		if($field == "createTime"){
			return "INT(11) NULL";
		}
		return "VARCHAR(45) NULL";
	}
	
	public static function typeTitle($type){
		if($type == COMaterialTypePrimary){
			return "主料";
		}
		if($type == COMaterialTypeSecondary){
			return "辅料";
		}
		if($type == COMaterialTypeFlavoring){
			return "调料";
		}
		return "";
	}
	
}

?>