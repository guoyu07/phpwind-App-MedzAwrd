<?php
defined('WEKIT_VERSION') or exit(403);
/**
 * AppMedzAward - 数据服务接口
 *
 * @author Medz Seven <lovevipdsw@vip.qq.com>
 * @copyright //medz.cn
 * @license //medz.cn
 */
class AppMedzAward {

	/**
	 * 数据模型
	 *
	 * @var string
	 **/
	protected $dao;

	/**
	 * 构造方法
	 *
	 * @return void
	 * @author Medz Seven <lovevipdsw@vip.qq.com>
	 **/
	public function __construct()
	{
		$this->dao = Wekit::loadDao('EXT:MedzAward.service.dao.AppMedzAwardDao');
	}

	/**
	 * 添加打赏记录
	 *
	 * @return bool | PwError object
	 * @author Medz Seven <lovevipdsw@vip.qq.com>
	 **/
	public function add($tid, $uid, $number)
	{
		/* # 判断是否打赏过了 */
		if ($this->isAward($tid, $uid)) {
			return new PwError('(•‾̑⌣‾̑•)✧˖° 跪谢，你之前打赏过了！');
		}
		return $this->dao->add(array(
			'tid'    => intval($tid),
			'uid'    => intval($uid),
			'number' => intval($number),
			'time'   => Pw::getTime()
		));
	}

	/**
	 * 检查是否打赏过了
	 *
	 * @param int $tid 帖子ID
	 * @param int $uid 用户ID
	 * @return bool
	 * @author Medz Seven <lovevipdsw@vip.qq.com>
	 **/
	public function isAward($tid, $uid)
	{
		return $this->dao->isAward(intval($tid), intval($uid));
	}

	/**
	 * 统计帖子打赏人数
	 *
	 * @param int $tid 要统计的帖子ID
	 * @return int
	 * @author Medz Seven <lovevipdsw@vip.qq.com>
	 **/
	public function getCountByTid($tid)
	{
		return $this->dao->getCountByTid(intval($tid));
	}

	/**
	 * 获取全部打赏的用户UID
	 *
	 * @param int $tid 获取的帖子ID
	 * @return array
	 * @author Medz Seven <lovevipdsw@vip.qq.com>
	 **/
	public function getUserAllByTid($tid)
	{
		return $this->dao->getUserAllByTid($tid);
	}

}