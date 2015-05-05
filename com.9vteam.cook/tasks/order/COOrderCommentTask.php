<?php

/**
 * 
 * 订单 用户评论
 * @author hailongz
 *
 */
class COOrderCommentTask extends COAuthTask{
	
	/**
	 * 订单ID
	 * @var int
	 */
	public $oid;
	
	/**
	 * 评论
	 * @var String
	 */
	public $comment;

	/**
	 * 评分
	 * @var int
	 */
	public $score;

}

?>