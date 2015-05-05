<?php

/**
 * 菜
 * @author zhanghailong
 *
 */
class COFood extends DBEntity{
	
	/**
	 * 菜ID
	 * @var int
	 */
	public $fid;
	
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
	 * 摘要
	 * @var String
	 */
	public $summary;
	
	/**
	 * 特殊厨具
	 * @var String
	 */
	public $tools;
	
	/**
	 * 附加费用
	 * @var double
	 */
	public $additional;
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
		return array("fid");
	}
	
	public static function autoIncrmentFields(){
		return array("fid");
	}
	
	public static function tableName(){
		return "cook_food";
	}
	
	public static function tableField($field){
		return $field;
	}
	
	public static function tableFieldType($field){
		if($field == "fid"){
			return "BIGINT NOT NULL";
		}
		if($field == "uid"){
			return "BIGINT NULL";
		}
		if($field == "title"){
			return "VARCHAR(64) NULL";
		}
		if($field == "image"){
			return "VARCHAR(128) NULL";
		}
		if($field == "additional"){
			return "DOUBLE NULL";
		}
		if($field == "summary"){
			return "VARCHAR(256) NULL";
		}
		if($field == "tools"){
			return "VARCHAR(256) NULL";
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