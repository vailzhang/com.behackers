<?php

define("COChefGenderNone",0);
define("COCherGenderMale",1);
define("COChefGenderFemale",2);

define("COChefRankNone",0);		
define("COChefRankGeneral",1);		// 家庭厨师
define("COChefRankProfession",2);	// 专业厨师

define("COChefStateNone",0);
define("COChefStateDisabled",100);

/**
 * 厨师
 * @author zhanghailong
 *
 */
class COChef extends O2ODBProvider{
	
	/**
	 * 创建者ID
	 * @var int
	 */
	public $uid;
	
	/**
	 * 厨师ID
	 * @var String
	 */
	public $code;
	/**
	 * 姓名
	 * @var String
	 */
	public $name;
	/**
	 * 头衔/等级
	 * @var String
	 */
	public $title;
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
	 * 籍贯
	 * @var String
	 */
	public $hometown;
	/**
	 * 性别
	 * @var COChefGender
	 */
	public $gender;
	
	/**
	 * 等级
	 * @var COChefRank
	 */
	public $rank;
	
	/**
	 * 评分 0 ~ 10
	 * @var int
	 */
	public $score;
	
	/**
	 * 工作
	 * @var String
	 */
	public $office;
	
	/**
	 * 认证的
	 * @var int
	 */
	public $verify;
	
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
	 * 擅长
	 * @var String
	 */
	public $speciality;
	
	/**
	 * 摘要
	 * @var String
	 */
	public $summary;
	
	/**
	 * 服务区域
	 * @var String
	 */
	public $area;
	
	/**
	 * 电话
	 * @var String
	 */
	public $phone;
	
	/**
	 * 服务时间
	 * @var String
	 */
	public $time;
	
	/**
	 * QQ
	 * @var String
	 */
	public $qq;
	
	/**
	 * 微信
	 * @var String
	 */
	public $weixin;
	
	/**
	 * 微博
	 * @var String
	 */
	public $weibo;
	
	/**
	 * 默认套餐价格
	 * @var double
	 */
	public $price;
	/**
	 * 默认套餐
	 * @var int
	 */
	public $packageId;
	
	/**
	 * 订单数
	 * @var int
	 */
	public $orderCount;
	/**
	 * 完成订单数
	 * @var int
	 */
	public $confirmCount;
	/**
	 * 评论数
	 * @var int
	 */
	public $commentCount;
	/**
	 * 评分数
	 * @var int
	 */
	public $scoreCount;
	/**
	 * 状态
	 * @var COChefState
	 */
	public $state;
	/**
	 * 修改时间
	 * @var int
	 */
	public $updateTime;
	
	public static function tableName(){
		return "cook_chef";
	}
	
	public static function tableField($field){
		return $field;
	}
	
	public static function tableFieldType($field){
		if($field == "uid"){
			return "BIGINT NULL";
		}
		if($field == "code"){
			return "VARCHAR(32) NULL";
		}
		if($field == "name"){
			return "VARCHAR(32) NULL";
		}
		if($field == "title"){
			return "VARCHAR(32) NULL";
		}
		if($field == "logo"){
			return "VARCHAR(256) NULL";
		}
		if($field == "birthYear"){
			return "INT NULL";
		}
		if($field == "hometown"){
			return "VARCHAR(64) NULL";
		}
		if($field == "office"){
			return "VARCHAR(64) NULL";
		}
		if($field == "gender"){
			return "INT NULL";
		}
		if($field == "rank"){
			return "INT NULL";
		}
		if($field == "score"){
			return "INT NULL";
		}
		if($field == "verify"){
			return "INT NULL";
		}
		if($field == "securityIDCode"){
			return "VARCHAR(64) NULL";
		}
		if($field == "securityHealthIDCode"){
			return "VARCHAR(64) NULL";
		}
		if($field == "speciality"){
			return "VARCHAR(256) NULL";
		}
		if($field == "summary"){
			return "VARCHAR(256) NULL";
		}
		if($field == "area"){
			return "VARCHAR(256) NULL";
		}
		if($field == "phone"){
			return "VARCHAR(32) NULL";
		}
		if($field == "time"){
			return "VARCHAR(32) NULL";
		}
		if($field == "qq"){
			return "VARCHAR(32) NULL";
		}
		if($field == "weixin"){
			return "VARCHAR(64) NULL";
		}
		if($field == "weibo"){
			return "VARCHAR(128) NULL";
		}
		if($field == "state"){
			return "INT NULL";
		}
		if($field == "price"){
			return "DOUBLE NULL";
		}
		if($field == "packageId"){
			return "BIGINT NULL";
		}
		if($field == "orderCount"){
			return "INT NULL";
		}
		if($field == "confirmCount"){
			return "INT NULL";
		}
		if($field == "commentCount"){
			return "INT NULL";
		}
		if($field == "scoreCount"){
			return "INT NULL";
		}
		if($field == "updateTime"){
			return "INT(11) NULL";
		}
		return parent::tableFieldType($field);
	}
	
	/**
	 * @return array("index_name"=>array(array("field"=>"field1","order"="desc"),array("field"=>"field2","order"="asc")))
	 */
	public static function indexs(){
		return array("code"=>array(array("field"=>"code","order"=>"asc")));
	}
	
	public static function genderTitle($gender){
		$v = intval($gender);
		if($v == COCherGenderMale){
			return "男";
		}
		if($v == COChefGenderFemale){
			return "女";
		}
		return "";
	}
	
	public static function rankTitle($rank){
		$v = intval($rank);
		if($v == COChefRankGeneral){
			return "家庭主厨";
		}
		if($v == COChefRankProfession){
			return "餐厅大厨";
		}
		return "";
	}
}

?>