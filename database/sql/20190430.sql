CREATE TABLE `release_item` (
  `release_item_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `release_id` int(10) unsigned NOT NULL  COMMENT 'release_id',
  `name` varchar(45) NOT NULL DEFAULT '' COMMENT '条目名称',
  `original_config` TEXT  COMMENT '原始配置内容',
  `online_config` TEXT  COMMENT '微信服务器配置内容',
  `push_config` TEXT  COMMENT '推送的配置内容',
  `response` TEXT  COMMENT '微信返回内容',
  `status` tinyint(4) NOT NULL DEFAULT 0 COMMENT '1构建成功;0构建失败;',
  `field1` varchar(45) NOT NULL DEFAULT '' COMMENT '备用字段',
  `field2` varchar(45) NOT NULL DEFAULT '' COMMENT '备用字段2',
  `is_deleted` tinyint(1) NOT NULl DEFAULT 0 COMMENT '软删除标记',
  `create_user` varchar(45) NOT NULL DEFAULT 0 comment '新建记录的用户',
  `update_user` varchar(45) NOT NULL DEFAULT 0 comment '最后一次操作的用户',
  `created_at` timestamp NOT NULL DEFAULT '1970-01-01 08:00:01' COMMENT '记录添加时间',
  `updated_at` timestamp NOT NULL DEFAULT '1970-01-01 08:00:01' COMMENT '记录更新时间',
  PRIMARY KEY (`release_item_id`),
  KEY `idx_template_id` (`template_id`),
  KEY `idx_mini_program_id` (`mini_program_id`),
  KEY `idx_component_id` (`component_id`),
  KEY `idx_status` (`status`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8 COMMENT = '发版详情';

ALTER TABLE `component_ext` change  COLUMN  component_app_id component_id int(10) unsigned NOT NULL DEFAULT '0' COMMENT '关联的三方平台ID';
ALTER TABLE `mini_program_ext` change  COLUMN  component_app_id component_id int(10) unsigned NOT NULL DEFAULT '0' COMMENT '关联的三方平台ID';
ALTER TABLE `mini_program_template` change  COLUMN  component_app_id component_id int(10) unsigned NOT NULL DEFAULT '0' COMMENT '关联的三方平台ID';
ALTER TABLE `mini_program_template_draft` change  COLUMN  component_app_id component_id int(10) unsigned NOT NULL DEFAULT '0' COMMENT '关联的三方平台ID';
ALTER TABLE `release` change  COLUMN  component_app_id component_id int(10) unsigned NOT NULL DEFAULT '0' COMMENT '关联的三方平台ID';
ALTER TABLE `release_audit` change  COLUMN  component_app_id component_id int(10) unsigned NOT NULL DEFAULT '0' COMMENT '关联的三方平台ID';
ALTER TABLE `template_ext` change  COLUMN  component_app_id component_id int(10) unsigned NOT NULL DEFAULT '0' COMMENT '关联的三方平台ID';

ALTER TABLE `release` ADD COLUMN `trade_no` varchar(45) NOT NULL DEFAULT '' COMMENT '事务流水号';
ALTER TABLE `release` ADD INDEX  `idx_trade_no`(`trade_no`);

ALTER TABLE `release` change COLUMN `ext_json` `config` TEXT COMMENT '构建配置';