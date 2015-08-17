<?php
defined('WEKIT_VERSION') or exit(403);
Wind::import('ADMIN:library.AdminBaseController');
/**
 * 打赏后台管理
 *
 * @package PHPWind\Medz\Award\Admin\Manage
 * @author Medz Seven <lovevipdsw@vip.qq.com>
 **/
class ManageController extends AdminBaseController
{
	/**
	 * 打赏设置
	 *
	 * @return void
	 * @author Medz Seven <lovevipds@vip.qq.com>
	 **/
	public function run()
	{
		/*======================取得用户组====================*/
		/* # 组类型名称 */
		$groupNames = Wekit::load('SRV:usergroup.PwUserGroups')->getTypeNames();
		/* # 按组排列 */
		$groups     = array();
		foreach (Wekit::load('usergroup.PwUserGroups')->getAllGroups() as $group) {
			/* # 跳过默认组 */
			if ($group['type'] == 'default') {
				continue;
			}
			/* # 添加组名称 */
			$groups[$group['type']]['name'] = $groupNames[$group['type']];
			/* # 添加组子节点 */
			$groups[$group['type']]['child'][$group['gid']] = $group['name'];
		}
		unset($group, $groupNames);
		
		/* # 设置用户组数据到模板 */
		$this->setOutput($groups, 'groups');
		unset($groups);

		/* # 获取储存的数据 */
		$this->setOutput(Wekit::C('MedzAward', 'groups'), 'gids');
		/*=======================End============================*/

		/*===================积分类型设置========================*/
		/* # 获取积分类型 */
		$credits = array();
		foreach (Wekit::C('credit', 'credits') as $cid => $credit) {
			/* # 获取只开启的积分 */
			isset($credit['open']) and $credit['open'] == 1 and $credits[$cid] = $credit['name'];
		}

		/* # 设置积分类型到模板 */
		$this->setOutput($credits, 'credits');
		unset($credit, $cid, $credits);

		/* # 取得储存的数据 */
		$this->setOutput(Wekit::C('MedzAward', 'credit'), 'cid2');
		/*=======================End=============================*/

		/* # 默认打赏数量 */
		$this->setOutput(Wekit::C('MedzAward', 'default'), 'default');

		/* # 最小打赏数量 */
		$this->setOutput(Wekit::C('MedzAward', 'min'), 'min');

		/* # 最大打赏数量 */
		$this->setOutput(Wekit::C('MedzAward', 'max'), 'max');

		/* # 设置模板 */
		$this->setTemplate('manage_run');
	}

	/**
	 * 设置后台配置参数
	 *
	 * @return void
	 * @author Medz Seven <lovevipdsw@vip.qq.com>
	 **/
	public function settingAction()
	{
		/* # 设置权限用户组 */
		$groups = $this->getInput('groups', 'post');
		$groups = array_map('intval', $groups);
		Wekit::C()->setConfig('MedzAward', 'groups', $groups) or $this->showError('设置用户组失败！');

		/* # 设置积分类型 */
		$credit = $this->getInput('credit', 'post');
		$credit = intval($credit);
		Wekit::C()->setConfig('MedzAward', 'credit', $credit) or $this->showError('设置积分类型失败');

		/* # 设置默认打赏积分数量 */
		$default = intval($this->getInput('default', 'post'));
		Wekit::C()->setConfig('MedzAward', 'default', $default) or $this->showError('设置默认打赏积分数量失败');

		/* # 设置最小打赏数量 */
		$min = intval($this->getInput('min', 'post'));
		Wekit::C()->setConfig('MedzAward', 'min', $min) or $this->showError('设置最小打赏数量失败');

		/* # 设置最多打赏数量 */
		$max = intval($this->getInput('max', 'post'));
		Wekit::C()->setConfig('MedzAward', 'max', $max) or $this->showError('设置最多打赏数量失败');

		/* # 全部设置成功 */
		$this->showMessage('设置成功！');
	}

} // END class ManageController extends AdminBaseController