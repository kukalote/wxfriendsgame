CREATE DATABASE IF NOT EXISTS wx_friends_game DEFAULT CHARSET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS `wx_user`;
CREATE TABLE `wx_user`(
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT NOT NULL COMMENT '用户ID',
  `nick_name` varchar(100) NOT NULL COMMENT '微信昵称',
  `photo` varchar(200) NOT NULL COMMENT '用户头像',
  `open_id` varchar(50) NOT NULL COMMENT 'open_id',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  PRIMARY KEY(`id`)
)ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COMMENT='微信用户表';

CREATE TABLE `wx_room`(
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT NOT NULL COMMENT '主键ID',
  `room_no` varchar(40) NOT NULL COMMENT '房间号',
  `room_user_count` int unsigned NOT NULL DEFAULT 50 COMMENT '房间限制人数,默认50人',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  PRIMARY KEY(`id`)
)ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COMMENT='游戏房间';

CREATE TABLE `wx_room_user`(
  `room_id` INT UNSIGNED NOT NULL COMMENT `房间ID`,
  `user_id` INT UNSIGNED NOT NULL COMMENT `用户ID`,
)ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COMMENT='房间用户表';


CREATE TABLE `wx_question`(
  `id` int unsigned not null comment '题目ID',
  `title` varchar(100) NOT NULL COMMENT '题目标题',
  `description` varchar(255) NOT NULL COMMENT '标题描述',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  PRIMARY KEY(`id`)
)ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COMMENT='题目表';

CREATE TABLE `wx_question_answer`(
  `id` INT UNSIGNED NOT NULL COMMENT '答案ID',
  `question_id` INT UNSIGNED not null comment '题目ID',
  `answer` varchar(20) NOT NULL COMMENT  '答案',
  `is_true` tinyint UNSIGNED NOT NULL default 0 COMMENT '1 正确答案 默认为0',
  PRIMARY KEY(`id`)
)ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COMMENT='题目答案表';

CREATE TABLE `wx_user_answer`(
  `user_id` INT UNSIGNED NOT NULL COMMENT '用户ID',
  `question_id` INT UNSIGNED not null comment '题目ID',
  `answer_id` varchar(20) NOT NULL COMMENT  '答案ID',
  `is_true` tinyint UNSIGNED NOT NULL default 0 COMMENT '1 答对了',
  `answer_time` datetime not null comment '答题时间'
)ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COMMENT='用户答题表';

CREATE TABLE `wx_user_redpackets`(
  `user_id` INT UNSIGNED NOT NULL COMMENT '用户ID',
  `question_id` INT UNSIGNED not null comment '题目ID',
  `money` int unsigned COMMENT  '分得的红包，单位为分',
  `is_send` tinyint UNSIGNED NOT NULL default 0 COMMENT '红包是否已发送给用户',
  `answer_time` datetime not null comment '发送红包时间'
)ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COMMENT='用户答题表';

CREATE TABLE `wx_user_chipin`(
  `user_id` INT UNSIGNED NOT NULL COMMENT '用户ID',
  `question_id` INT UNSIGNED not null comment '题目ID',
  `chip_money` int unsigned COMMENT  '下注金额，单位为分',
  `is_chip` tinyint UNSIGNED NOT NULL default 0 COMMENT '是否已下注',
  `answer_time` datetime not null comment '下注时间'
)ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COMMENT='用户下注表';
