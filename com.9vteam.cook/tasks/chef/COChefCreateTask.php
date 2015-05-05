<?php

/**
 * @author hailongz
 *
 */
class COChefCreateTask extends COAuthTask{

	/**
	 * 厨师ID
	 * @var String
	 */
	public $code;
	/**
	 * 姓名
	 * @var String
	 */
	public $name;
	/**
	 * 头衔/等级
	 * @var String
	 */
	public $title;
	/**
	 * 照片
	 * @var String
	 */
	public $logo;
	/**
	 * 出生年
	 * @var int
	 */
	public $birthYear;
	/**
	 * 籍贯
	 * @var String
	 */
	public $hometown;
	/**
	 * 性别
	 * @var COChefGender
	 */
	public $gender;
	
	/**
	 * 等级
	 * @var COChefRank
	 */
	public $rank;
	
	/**
	 * 评分 0 ~ 10
	 * @var int
	 */
	public $score;
	
	/**
	 * 工作
	 * @var String
	 */
	public $office;
	/**
	 * 认证的
	 * @var int
	 */
	public $verify;
	/**
	 * 认证身份证号
	 * @var String
	 */
	public $securityIDCode;
	
	/**
	 * 认证健康证号
	 * @var String
	 */
	public $securityHealthIDCode;
	
	/**
	 * 擅长
	 * @var String
	 */
	public $speciality;
	
	/**
	 * 摘要
	 * @var String
	 */
	public $summary;
	
	/**
	 * 服务区域
	 * @var String
	 */
	public $area;
	
	/**
	 * 电话
	 * @var String
	 */
	public $phone;
	
	/**
	 * 服务时间
	 * @var String
	 */
	public $time;
	
	/**
	 * QQ
	 * @var String
	 */
	public $qq;
	
	/**
	 * 微信
	 * @var String
	 */
	public $weixin;
	
	/**
	 * 微博
	 * @var String
	 */
	public $weibo;
	
	/**
	 * 菜系
	 * @var array,String
	 */
	public $cuisineIds;
	
	/**
	 * 区域
	 * @var array,String
	 */
	public $areaIds;
	
	/**
	 * 菜品ID
	 * @var array,String
	 */
	public $foodIds;
	
	/**
	 * 套餐, packageId,price,packageId,price,...
	 * @var array,String
	 */
	public $packages;
	/**
	 * 
	 * @var COChef
	 */
	public $results;
}

?>