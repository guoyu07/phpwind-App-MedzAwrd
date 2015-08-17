<?php
defined('WEKIT_VERSION') or exit(403);
/**
 * 配置管理
 *
 * @package phpwind\Medz\Config\Do
 * @author Medz Seven <lovevipdsw@vip.qq.com>
 **/
class AppMedzAwardConfigDo
{

	/**
	 * 获取打赏后台导航配置
	 *
	 * @return array
	 * @author Medz Seven <lovevipdsw@vip.qq.com>
	 **/
	public function getAdminMenu(array $config = array())
	{
		return array_merge($config, array(
			'MedzAward' => array(
				'打赏设置',
				'app/manage/*?app=MedzAward',
				'',
				'',
				'appcenter'
			)
		));
	}

	/**
	 * 获取打赏积分记录设置
	 *
	 * @return array
	 * @author Medz Seven <lovevipdsw@vip.qq.com>
	 **/
	public function getPwCreditOperation(array $config = array())
	{
		$config['MedzAward'] = array('打赏', 'global', '帖子《{$title}》被{$username}打赏了;积分变化【{$cname}:{$affect}】', false);
		$config['DoMedzAward'] = array('打赏', 'global', '打赏了帖子《{$title}》;积分变化【{$cname}:{$affect}】', false);
		return $config;
	}

} // END class AppMedzAwardConfigDo