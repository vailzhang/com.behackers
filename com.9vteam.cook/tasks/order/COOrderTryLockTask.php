<?php

/**
 * 尝试锁定订单
 * @author hailongz
 *
 */
class COOrderTryLockTask extends COAuthTask{
	
	/**
	 * 订单ID
	 * @var int
	 */
	public $oid;
	
	/**
	 * 锁定用户ID
	 * @var int
	 */
	public $lockUid;
	
	/**
	 * 是否锁定成功
	 * @var boolean
	 */
	public $results;
}

?>