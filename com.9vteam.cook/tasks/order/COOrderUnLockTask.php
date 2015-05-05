<?php

/**
 * 解锁订单
 * @author hailongz
 *
 */
class COOrderUnLockTask extends COAuthTask{
	
	/**
	 * 订单ID
	 * @var int
	 */
	public $oid;

	/**
	 * 是否锁定成功
	 * @var boolean
	 */
	public $results;
}

?>