<?php

/**
 * @author hailongz
 *
 */
class COChefScoreGetTask extends COTask{

	/**
	 * ID
	 * @var int
	 */
	public $pid;
	
	/**
	 * 订单ID
	 * @var int
	 */
	public $orderId;
	
	/**
	 * 评分 0 ~ 10
	 * @var int
	 */
	public $score;
}

?>