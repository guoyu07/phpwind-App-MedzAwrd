--安装或更新时需要注册的sql写在这里--

--
-- 表结构 `pw_medz_award`
--

-- 安装应用前，删除low表 --
DROP TABLE IF EXISTS `pw_medz_award`;

-- 创建表结构 --
CREATE TABLE IF NOT EXISTS `pw_medz_award` (
	`tid`    int(10)     NOT NULL COMMENT '被打赏帖子ID',
	`uid`    int(10)     NOT NULL COMMENT '打赏用户UID' ,
	`number` int(10)     NOT NULL COMMENT '打赏积分数量',
	`time`   varchar(20) NOT NULL COMMENT '打赏时间'    ,
	KEY `idx_tid_uid` (`tid`, `uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='打赏记录表';

