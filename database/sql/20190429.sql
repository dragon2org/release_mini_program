create table `tester` (
  `tester_id` int unsigned not null auto_increment primary key comment '自增id',
  `mini_program_id` int unsigned not null default '0' comment '小程序id',
  `app_id` varchar(100) not null default '' comment '小程序app_id',
  `userstr` varchar(255) not null default '' comment 'wechat_id别名',
  `wechat_id` varchar(255) not null default '' comment '微信id',
  `field1` varchar(45) not null default '' comment '备用字段',
  `field2` varchar(45) not null default '' comment '备用字段2',
  `is_deleted` tinyint not null default '0' comment '软删除标志',
  `create_user` varchar(45) not null default '' comment '新建记录的用户',
  `update_user` varchar(45) not null default '' comment '最后一次操作的用户',
  `created_at` datetime not null default '1970-01-01 08:00:01' comment '记录添加时间',
  `updated_at` datetime not null default '1970-01-01 08:00:01' comment '记录更新时间'
) ENGINE = InnoDB DEFAULT CHARSET = utf8 COMMENT = '体验者列表';