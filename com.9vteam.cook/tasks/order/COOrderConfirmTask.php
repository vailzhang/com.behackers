<?php

/**
 * 
 * 订单 厨师确认
 * @author hailongz
 *
 */
class COOrderConfirmTask extends COAuthTask{
	
	/**
	 * 订单ID
	 * @var int
	 */
	public $oid;
	
	/**
	 * 厨师反馈
	 * @var String
	 */
	public $remark;

	/**
	 * 
	 * @var COOrder
	 */
	public $results;
}

?>