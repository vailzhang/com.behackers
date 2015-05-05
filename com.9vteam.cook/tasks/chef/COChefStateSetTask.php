<?php

/**
 * @author hailongz
 *
 */
class COChefStateSetTask extends COAuthTask{

	/**
	 * ID
	 * @var int
	 */
	public $pid;
	
	/**
	 * 厨师ID
	 * @var String
	 */
	public $code;
	
	/**
	 * 状态
	 * @var COChefState
	 */
	public $state;
	 
}

?>