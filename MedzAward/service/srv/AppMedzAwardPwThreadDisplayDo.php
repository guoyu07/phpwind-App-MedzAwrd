<?php
defined('WEKIT_VERSION') or exit(403);
Wind::import('SRV:forum.srv.threadDisplay.do.PwThreadDisplayDoBase');
Wind::import('SRV:user.bo.PwUserBo');
/**
 * 帖子内容展示
 *
 * @package phpwind\Medz\Thread\display\Do
 * @author Medz Seven <lovevipdsw@vip.qq.com>
 **/
class AppMedzAwardPwThreadDisplayDo extends PwThreadDisplayDoBase
{
	/**
	 * 用户组ID
	 *
	 * @var array
	 **/
	protected $gids;

	/**
	 * 默认积分类型ID
	 *
	 * @var int
	 **/
	protected $cid;

	/**
	 * 默认悬赏积分
	 *
	 * @var int
	 **/
	protected $default;

	/**
	 * 最小悬赏积分
	 *
	 * @var int
	 **/
	protected $min;

	/**
	 * 最大悬赏积分
	 *
	 * @var int
	 **/
	protected $max;

	/**
	 * 当前帖子tid
	 *
	 * @var int
	 **/
	protected $tid;

	/**
	 * 当前帖子发布者UID
	 *
	 * @var int
	 **/
	protected $uid;

	/**
	 * 单个用户的业务对象
	 *
	 * @var object
	 **/
	protected $PwUserBo;

	/**
	 * 用户当前积分数量
	 *
	 * @var int
	 **/
	protected $userMax;

	/**
	 * 服务类
	 *
	 * @var object
	 **/
	protected $service;

	/**
	 * 构造方法
	 *
	 * @return void
	 * @author Medz Seven <lovevipdsw@vip.qq.com>
	 **/
	public function __construct()
	{
		/* # 有权限的用户组 */
		$this->gids = Wekit::C('MedzAward', 'groups');

		/* # 积分类型ID */
		$this->cid = Wekit::C('MedzAward', 'credit');

		/* # 默认积分数量 */
		$this->default = Wekit::C('MedzAward', 'default');

		/* # 最小打赏积分 */
		$this->min = Wekit::C('MedzAward', 'min');

		/* # 最大打赏积分 */
		$this->max = Wekit::C('MedzAward', 'max');

		/* # 获取服务类 */
		$this->service = Wekit::load('EXT:MedzAward.service.AppMedzAward');
	}

	/**
	 * 帖子内容区底部输出
	 *
	 * @param array $read 帖子数据
	 * @return void
	 * @author Medz Seven <lovevipdsw@vip.qq.com>
	 **/
	public function createHtmlContentBottom($read)
	{
		/* # 帖子ID */
		$this->tid = $read['tid'];

		/* # 楼主ID */
		$this->uid = $read['created_userid'];

		/* # 设置用户组单例对象 */
		$this->PwUserBo = PwUserBo::getInstance($this->uid);

		/*=============================常规逻辑计算 Start===============================*/
		/* #设置最大积分数量 */
		$this->max > $this->PwUserBo->getCredit($this->cid) and $this->max = $this->PwUserBo->getCredit($this->cid);

		/* # 当最大积分数量小于最小积分数量的时候 */
		$this->min > $this->max     and $this->min     = $this->max;

		/* # 当默认积分小于最小积分的时候 */
		$this->default < $this->min and $this->default = $this->mid;

		/* # 当默认积分大于最大积分的时候 */
		$this->default > $this->max and $this->default = $this->max;

		/* # 设置当前用户积分数量 */
		$this->userMax = Wekit::getLoginUser()->getCredit($this->cid);
		/*=============================常规逻辑计算 End  ===============================*/

		/* # 判断是否有权限 */
		if ($this->PwUserBo->inGroup($this->gids)) {
			PwHook::template('createHtmlContentBottom', 'EXT:MedzAward.template.hook.createHtmlContentBottom', true, $this->tid, $this->service->getUserAllByTid($this->tid), $this->service->getCountByTid($this->tid));
		}
	}

	public function runJs() {
		if ($this->PwUserBo->inGroup($this->gids)) {
			$credit = Wekit::C('credit', 'credits');
			$credit = $credit[$this->cid];
			PwHook::template('runJs', 'EXT:MedzAward.template.hook.runJs', true, $this->default, $this->min, $this->max, $this->userMax, $credit);
			unset($credit);
		}
	}

} // END class AppMedzAwardPwThreadDisplayDo extends PwThreadDisplayDoBase