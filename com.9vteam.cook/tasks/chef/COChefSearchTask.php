<?php

/**
 * @author hailongz
 *
 */
class COChefSearchTask extends COTask{

	/**
	 * 菜系
	 * @var int
	 */
	public $cuisineId;
	/**
	 * 口味
	 * @var int
	 */
	public $classifyId;
	/**
	 * 区域
	 * @var int
	 */
	public $areaId;
	/**
	 * 等级
	 * @var int
	 */
	public $rank;
	/**
	 * 最小价格
	 * @var double
	 */
	public $minPrice;
	/**
	 * 最大价格
	 * @var double
	 */
	public $maxPrice;
	 
	/**
	 * 排序, hot,price,score
	 * @var String
	 */
	public $sort;
	
	public $pageIndex;
	public $pageSize;
}

?>