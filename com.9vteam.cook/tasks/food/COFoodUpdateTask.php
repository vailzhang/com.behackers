<?php

/**
 * @author hailongz
 *
 */
class COFoodUpdateTask extends COAuthTask{

	/**
	 * 菜ID
	 * @var int
	 */
	public $fid;
	
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
	 * 菜系
	 * @var array,String
	 */
	public $cuisineIds;
	/**
	 * 口味
	 * @var array,String
	 */
	public $classifyIds;
	
	/**
	 * 主材
	 * @var array,String
	 */
	public $primarys;
	/**
	 * 辅材
	 * @var array,String
	 */
	public $secondarys;
	/**
	 * 调料
	 * @var array,String
	 */
	public $flavorings;
	
	/**
	 * 
	 * @var COFood
	 */
	public $results;
}

?>