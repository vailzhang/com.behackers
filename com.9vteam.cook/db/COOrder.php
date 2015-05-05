<?php

define("COOrderStatusNone",0);
define("COOrderStatusWait",1);			// 等待厨师服务
define("COOrderStatusConfirm",2);		// 厨师服务完成确认
define("COOrderStatusComment",3);		// 用户评论
define("COOrderStatusUserCancel",100);	// 用户取消订单
define("COOrderStatusCancel",101);		// 客服取消订单

define("COOrderMealNone",0);	
define("COOrderMealNoon",1);	// 午餐
define("COOrderMealDinner",2);	// 晚餐

define("COOrderFoodNone",0);	// 自备食材
define("COOrderFoodNormal",1);	// 代购食材

/**
 * 订单
 * @author zhanghailong
 *
 */
class COOrder extends O2ODBTradeOrder{
	
	/**
	 * 编号
	 * @var String
	 */
	public $code;
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
	 * 设备好
	 * @var int
	 */
	public $did;
	/**
	 * 价格
	 * @var double
	 */
	public $price;
	/**
	 * 订单日期
	 * @var int
	 */
	public $orderDay;
	/**
	 * 订单用餐
	 * @var COOrderMeal
	 */
	public $orderMeal;
	/**
	 * 代购食材
	 * @var COOrderFood
	 */
	public $orderFood;
	/**
	 * 城市
	 * @var String
	 */
	public $city;
	/**
	 * 城市ID
	 * @var int
	 */
	public $cityId;
	/**
	 * 城区
	 * @var String
	 */
	public $area;
	/**
	 * 区域ID
	 * @var int
	 */
	public $areaId;
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
	 * 备注
	 * @var String
	 */
	public $summary;
	/**
	 * 用户数
	 * @var int
	 */
	public $userCount;

	/**
	 * 锁定操作用户
	 * @var int
	 */
	public $lockUid;
	
	public static function tableName(){
		return "cook_order";
	}
	
	public static function tableFieldType($field){
		if($field == "chefId"){
			return "BIGINT NULL";
		}
		if($field == "packageId"){
			return "BIGINT NULL";
		}
		if($field == "did"){
			return "BIGINT NULL";
		}
		if($field == "price"){
			return "DOUBLE NULL";
		}
		if($field == "orderDay"){
			return "INT(11) NULL";
		}
		if($field == "orderMeal"){
			return "INT NULL";
		}
		if($field == "orderFood"){
			return "INT NULL";
		}
		if($field == "city"){
			return "VARCHAR(64) NULL";
		}
		if($field == "area"){
			return "VARCHAR(64) NULL";
		}
		if($field == "address"){
			return "VARCHAR(256) NULL";
		}
		if($field == "name"){
			return "VARCHAR(64) NULL";
		}
		if($field == "phone"){
			return "VARCHAR(32) NULL";
		}
		if($field == "cityId"){
			return "BIGINT NULL";
		}
		if($field == "areaId"){
			return "BIGINT NULL";
		}
		if($field == "summary"){
			return "VARCHAR(256) NULL";
		}
		if($field == "userCount"){
			return "INT NULL";
		}
		
		if($field == "lockUser"){
			return "BIGINT NULL";
		}
		
		return parent::tableFieldType($field);
	}
	
	/**
	 * @return array("index_name"=>array(array("field"=>"field1","order"="desc"),array("field"=>"field2","order"="asc")))
	 */
	public static function indexs(){
		return array("code"=>array(array("field"=>"code","order"=>"asc")),"updateTime"=>array(array("field"=>"updateTime","order"=>"asc")));
	}
	
	public static function day($orderDay,$orderMeal){
		$day = date("Y-m-d",$orderDay);
		if($orderMeal == COOrderMealNoon){
			$day .= "午餐";
		}			
		else if($orderMeal == COOrderMealDinner){
			$day .= "晚餐";
		}
		return $day;
	}
	
	public static function orderFood($orderFood){
		if($orderFood == COOrderFoodNormal){
			return "代买食材";
		}
		return "自备食材";
	}
}

?>