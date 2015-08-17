<?php
defined('WEKIT_VERSION') or exit(403);
/**
 * AppMedzAwardDao - dao
 *
 * @author Medz Seven <lovevipdsw@vip.qq.com>
 * @copyright //medz.cn
 * @license //medz.cn
 */
class AppMedzAwardDao extends PwBaseDao {
	
	/**
	 * table name
	 */
	protected $_table = 'medz_award';
	/**
	 * primary key
	 */
	protected $_pk = 'tid';
	/**
	 * table fields
	 */
	protected $_dataStruct = array('tid', 'uid', 'number', 'time');
	
	public function add($fields) {
		$this->_add($fields, true);
		if ($this->isAward($fields['tid'], $fields['uid'])) {
			return true;
		}
		return new PwError('꒰๑´•.̫ • `๑꒱打赏失败了！');
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
		$sql = 'SELECT COUNT(`tid`) as %s FROM %s WHERE %s = ? AND %s = ?';
		$sql = $this->_bindSql($sql, 'num', $this->getTable(), 'tid', 'uid');
		return $this->getConnection()->createStatement($sql)->getValue(array($tid, $uid));
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
		$sql = 'SELECT COUNT(`uid`) as %s FROM %s WHERE %s = ?';
		$sql = $this->_bindSql($sql, 'num', $this->getTable(), 'tid');
		return $this->getConnection()->createStatement($sql)->getValue(array(intval($tid)));
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
		$sql = 'SELECT %s FROM %s WHERE %s = ?';
		$sql = $this->_bindSql($sql, 'uid', $this->getTable(), 'tid');
		$uids= $this->getConnection()->createStatement($sql)->queryAll(array(intval($tid)));

		foreach ($uids as $key => $uid) {
			$uids[$key] = $uid['uid'];
		}

		return $uids;
	}
}