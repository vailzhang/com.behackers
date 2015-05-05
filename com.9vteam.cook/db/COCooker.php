<?php

define("COCookerGenderNone",0);
define("COCookerGenderMale",1);
define("COCookerGenderFemale",2);

define("COCookerRankNone",0);
define("COCookerRankGeneral",1);

/**
 * 厨师
 * @author zhanghailong
 *
 */
class COCooker extends O2ODBProvider{
	
	/**
	 * 姓名
	 * @var String
	 */
	public $name;
	/**
	 * 照片
	 * @var String
	 */
	public $logo;
	/**
	 * 出生年
	 * @var int
	 */
	public $birthYear;

	/**
	 * 性别
	 * @var COCookerGender
	 */
	public $gender;
	
	/**
	 * 等级
	 * @var COCookerRank
	 */
	public $rank;
	
	/**
	 * 评分
	 * @var int
	 */
	public $rate;
	
	/**
	 * 认证身份证号
	 * @var String
	 */
	public $securityIDCode;
	
	/**
	 * 认证健康证号
	 * @var String
	 */
	public $securityHealthIDCode;
	
	/**
	 * 摘要
	 * @var String
	 */
	public $summary;
	
	/**
	 * 服务区域
	 * @var String
	 */
	public $targetArea;
	
	/**
	 * 修改时间
	 * @var int
	 */
	public $updateTime;
	
	public static function tableName(){
		return "cook_cooker";
	}
	
	public static function tableField($field){
		return $field;
	}
	
	public static function tableFieldType($field){
		if($field == "name"){
			return "VARCHAR(32) NULL";
		}
		if($field == "logo"){
			return "VARCHAR(256) NULL";
		}
		if($field == "birthYear"){
			return "INT NULL";
		}
		if($field == "gender"){
			return "INT NULL";
		}
		if($field == "rank"){
			return "INT NULL";
		}
		if($field == "rate"){
			return "INT NULL";
		}
		if($field == "securityIDCode"){
			return "VARCHAR(64) NULL";
		}
		if($field == "securityHealthIDCode"){
			return "VARCHAR(64) NULL";
		}
		if($field == "summary"){
			return "VARCHAR(256) NULL";
		}
		if($field == "targetArea"){
			return "VARCHAR(256) NULL";
		}
		if($field == "updateTime"){
			return "INT(11) NULL";
		}
		return parent::tableFieldType($field);
	}
	
}

?>