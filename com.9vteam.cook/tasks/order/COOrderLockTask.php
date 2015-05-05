<?php

/**
 * 强制锁定订单
 * @author hailongz
 *
 */
class COOrderLockTask extends COAuthTask{
	
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
	
}

?>