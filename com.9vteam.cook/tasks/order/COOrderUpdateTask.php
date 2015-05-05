<?php

/**
 * @author hailongz
 *
 */
class COOrderUpdateTask extends COAuthTask{
	
	/**
	 * 订单ID
	 * @var int
	 */
	public $oid;
	/**
	 * 厨师ID
	 * @var int
	 */
	public $chefId;
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
	 * 备注
	 * @var String
	 */
	public $summary;
	
	/**
	 * 菜单
	 * @var array,String
	 */
	public $foodIds;
	
	/**
	 * 价格
	 * @var double
	 */
	public $price;
	
	/**
	 * 就餐人数
	 * @var int
	 */
	public $userCount;

	/**
	 * 
	 * @var COOrder
	 */
	public $results;
}

?>