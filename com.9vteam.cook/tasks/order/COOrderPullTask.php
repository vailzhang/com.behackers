<?php

/**
 * 拉取订单信息
 * @author hailongz
 *
 */
class COOrderPullTask extends COAuthTask{
	
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
	 * 时间戳
	 * @var int
	 */
	public $timestamp;
	
	/**
	 * 状态
	 * @var String
	 */
	public $status;
	
	public $pageIndex;
	
	public $pageSize;
	
}

?>