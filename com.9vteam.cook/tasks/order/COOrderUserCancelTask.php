<?php

/**
 * 
 * 订单 用户取消
 * @author hailongz
 *
 */
class COOrderUserCancelTask extends COAuthTask{
	
	/**
	 * 订单ID
	 * @var int
	 */
	public $oid;
	
	/**
	 * 取消反馈
	 * @var String
	 */
	public $feedback;


}

?>