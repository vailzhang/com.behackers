<?php

/**
 * 
 * 订单 客服确认取消订单
 * @author hailongz
 *
 */
class COOrderCancelTask extends COAuthTask{
	
	/**
	 * 订单ID
	 * @var int
	 */
	public $oid;
	
	/**
	 * 取消理由
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