<?php

/**
 * @author hailongz
 *
 */
class COOrderChangedTask extends COTask{
	
	/**
	 * 订单ID
	 * @var int
	 */
	public $oid;
	/**
	 * 订单编号
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
}

?>