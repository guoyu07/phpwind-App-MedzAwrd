<?php
defined('WEKIT_VERSION') or exit(403);
Wind::import('SRV:user.bo.PwUserBo');
Wind::import('SRV:credit.bo.PwCreditBo');
/**
 * 发送数据
 *
 * @package phpwind\Medz\Award\Send
 * @author Medz Seven <lovevipdsw@vip.qq.com>
 **/
class AwardController extends PwBaseController
{

	/**
	 * 积分类型名称
	 *
	 * @var string
	 **/
	protected $cname;

	/**
	 * 积分类型单位
	 *
	 * @var string
	 **/
	protected $cunit;

	/**
	 * 用户最大积分数量
	 *
	 * @var int
	 **/
	protected $umax;

	/**
	 * 最低打赏积分
	 *
	 * @var int
	 **/
	protected $min;

	/**
	 * 最高打赏积分
	 *
	 * @var int
	 **/
	protected $max;

	/**
	 * 允许拥有打赏的用户组，用于校验楼主权限
	 *
	 * @var array
	 **/
	protected $gids;

	/**
	 * 允许的积分类型
	 *
	 * @var int
	 **/
	protected $cid;

	/**
	 * 楼主uid
	 *
	 * @var int
	 **/
	protected $uid;

	/**
	 * 用户打赏的数量
	 *
	 * @var int
	 **/
	protected $num;

	/**
	 * 帖子用户对象
	 *
	 * @var object
	 **/
	protected $PwUserBo;

	/**
	 * 帖子标题
	 *
	 * @var string
	 **/
	protected $title;

	/**
	 * 实体操作数据模型
	 *
	 * @var object
	 **/
	protected $service;

	/**
	 * 帖子ID
	 *
	 * @var int
	 **/
	protected $tid;

	/**
	 * 前置方法
	 *
	 * @return void
	 * @author Medz Seven <lovevipdsw@vip.qq.com>
	 **/
	public function beforeAction($handlerAdapter)
	{
		parent::beforeAction($handlerAdapter);

		$tid       = intval($this->getInput('tid', 'post'));
		$this->num = intval($this->getInput('number', 'post'));

		/* # 验证是否传入了参数 */
		if (!$tid or !$this->num) {
			$this->showError('非法请求  ╮(๑•́ ₃•̀๑)╭');

		/* # 验证是否登陆 */
		} elseif (!$this->loginUser->uid) {
			$this->showError(' (¬､¬) 你居然还没有登陆！');
		}

		/* # 有权限的用户组 */
		$this->gids = Wekit::C('MedzAward', 'groups');

		/* # 积分类型ID */
		$this->cid = Wekit::C('MedzAward', 'credit');

		/* # 最小打赏积分 */
		$this->min = Wekit::C('MedzAward', 'min');

		/* # 最大打赏积分 */
		$this->max = Wekit::C('MedzAward', 'max');

		/* # 获取帖子楼主UID */
		$this->uid = Wekit::load('SRV:forum.PwThread')->getThread($tid);
		if (!$this->uid) {
			$this->showError('打赏的帖子不存在( ´◔ ‸◔`)');
		}
		$this->tid   = $this->uid['tid'];
		$this->title = $this->uid['subject'];
		$this->uid   = $this->uid['created_userid'];

		/* # 设置帖子用户组单例对象 */
		$this->PwUserBo = PwUserBo::getInstance($this->uid);

		/* # 设置当前用户积分数量 */
		$this->umax = $this->loginUser->getCredit($this->cid);

		/* # 如果后台设置不限制，则调用当前用户做多积分 */
		$this->max or $this->max = $this->umax;

		$this->cname = $credit = Wekit::C('credit', 'credits');
		$this->cname = $this->cname[$this->cid];
		$this->cunit = $this->cname['unit'];
		$this->cname = $this->cname['name'];

		$this->service = Wekit::load('EXT:MedzAward.service.AppMedzAward');
	}

	/**
	 * 打赏
	 *
	 * @return void
	 * @author Medz Seven <lovevipdsw@vip.qq.com>
	 **/
	public function sendAction()
	{
		/* # 判断楼主是否有权限得到打赏 */
		if (!$this->PwUserBo->inGroup($this->gids)) {
			$this->showError('哎呀(；′⌒`)，楼主没有接收打赏的权限！');

		/* # 验证积分是否小于最低打赏 */
		} elseif ($this->num < $this->min) {
			$this->showError('最低打赏' . $this->min . $this->cunit . $this->cname . '哦！(●\'◡\'●)');

		/* # 验证是否积分不足 */
		} elseif ($this->umax < $this->num) {
			$this->showError('你觉得' . $this->cname . '够吗？(；′⌒`)');

		/* # 验证打赏积分是否大于最大值 */
		} elseif ($this->num > $this->max) {
			$this->showError('(⊙o⊙)？最多只能打赏' . $this->max . $this->cunit . $this->cname . '哦！');

		/* # 验证楼主是否是自己打赏自己 */
		} elseif ($this->PwUserBo->uid == $this->loginUser->uid) {
			$this->showError('๑乛◡乛๑你居然给自己打赏！');

		/* # 验证是否添加打赏失败 */
		} elseif (($result = $this->service->add($this->tid, $this->loginUser->uid, $this->num)) and $result instanceof PwError) {
			$this->showError($result->getError());
		}

		/* # 积分记录 */
		/* # 当前用户减积分 */
		PwCreditBo::getInstance()->set($this->loginUser->uid, $this->cid, -$this->num, true);
		/* # 当前用户，积分记录 */
		PwCreditBo::getInstance()->addLog('DoMedzAward', array($this->cid => -$this->num), $this->loginUser, array('title' => $this->title));
		/* # 被打赏用户积分操作 */
		PwCreditBo::getInstance()->set($this->PwUserBo->uid, $this->cid, +$this->num, true);
		/* # 当前用户积分记录 */
		PwCreditBo::getInstance()->addLog('MedzAward', array($this->cid => +$this->num), $this->PwUserBo, array('title' => $this->title));
		PwCreditBo::getInstance()->execute();

		/* # 发送打赏信息 */
		Wekit::load('message.srv.PwNoticeService')->sendNotice($this->PwUserBo->uid, 'app', 0, array(
			'username' => $this->PwUserBo->username,

			'title'    => 'o(^▽^)o，<a href="' . WindUrlHelper::createUrl('space/index/run?uid=' . $this->loginUser->uid) . '" target="_blank">' . $this->loginUser->username . '</a>打赏了你<font color="#ff0505">' . $this->num . '</font>' . $this->cunit . $this->cname . '。',

			'content'  => '(～￣▽￣)～*你的帖子<a href="' . WindUrlHelper::createUrl('bbs/read/jump?tid=' . $this->tid) . '">《' . $this->title . '》</a>太优秀啦(●\'◡\'●)，<a href="' . WindUrlHelper::createUrl('space/index/run?uid=' . $this->loginUser->uid) . '" target="_blank">' . $this->loginUser->username . '</a>非常喜欢，于是爽快的打赏了你<font color="#ff0505">' . $this->num . '</font>' . $this->cunit . $this->cname . '，<a href="' . WindUrlHelper::createUrl('bbs/read/jump?tid=' . $this->tid) . '">点击这里</a>查看帖子吧！'
		), true);

		$this->showMessage('o(*￣▽￣*)ブ打赏成功啦！');
	}

} // END class AwardController extends PwBaseController