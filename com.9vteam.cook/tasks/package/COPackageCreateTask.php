<?php

/**
 * @author hailongz
 *
 */
class COPackageCreateTask extends COAuthTask{

	/**
	 * 标题
	 * @var String
	 */
	public $title;
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
	 * 菜品
	 * @var array,String
	 */
	public $foodIds;
	
	/**
	 * 
	 * @var COPackage
	 */
	public $results;
}

?>