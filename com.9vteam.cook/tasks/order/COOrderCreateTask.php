<?php

/**
 * @author hailongz
 *
 */
class COOrderCreateTask extends COAuthTask{
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
	 * 订单食材
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
}

?>