ALTER TABLE `mini_program_publish`.`mini_program` DROP COLUMN `user_version`,ADD COLUMN `authorization_status` tinyint(4) NOT NULL DEFAULT 10 COMMENT '授权状态:10正常;20授权失效';

ALTER TABLE `mini_program_publish`.`mini_program_ext` DROP COLUMN `component_app_id`,DROP INDEX `idx_component_app_id`,ADD COLUMN `component_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '关联的三方平台ID',ADD INDEX `idx_component_id`(`component_id`) USING BTREE;

ALTER TABLE `mini_program_publish`.`mini_program_template_draft` DROP COLUMN `component_app_id`,ADD COLUMN `component_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '关联的三方平台ID';

ALTER TABLE `mini_program_publish`.`release` DROP COLUMN `component_app_id`,DROP COLUMN `ext_json`,DROP INDEX `idx_component_app_id`,ADD COLUMN `component_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '关联的三方平台ID',ADD COLUMN `config` text  COMMENT '构建配置',ADD COLUMN `trade_no` varchar(45)  NOT NULL DEFAULT '' COMMENT '事务流水号',ADD COLUMN `is_release` tinyint(4) NOT NULL DEFAULT 1 COMMENT '立即发布',ADD COLUMN `release_status` tinyint(4) NOT NULL DEFAULT 0 COMMENT '发布状态: 待发布/已发布:1/发布失败:2',ADD COLUMN `release_on_audited` tinyint(4) NOT NULL DEFAULT 1 COMMENT '审核通过时立即发布',ADD INDEX `idx_component_id`(`component_id`) USING BTREE,ADD INDEX `idx_trade_no`(`trade_no`) USING BTREE;


ALTER TABLE `mini_program_publish`.`release_audit` DROP COLUMN `component_app_id`,DROP INDEX `idx_component_app_id`,ADD COLUMN `component_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '关联的三方平台ID' AFTER `release_audit_id`,ADD COLUMN `screenshot` varchar(45)  NOT NULL DEFAULT '' COMMENT '审核失败的截图',ADD INDEX `idx_component_id`(`component_id`) USING BTREE;

ALTER TABLE `mini_program_publish`.`template_ext` DROP COLUMN `component_app_id`,DROP INDEX `idx_component_app_id`,ADD COLUMN `component_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '关联的三方平台ID',ADD INDEX `idx_component_id`(`component_id`) USING BTREE;

CREATE TABLE `component_template`  (
  `component_template_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `component_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '关联的三方平台ID',
  `template_id` int(11) NOT NULL DEFAULT 0 COMMENT '三方平台模板id',
  `user_version` varchar(45)  NOT NULL DEFAULT '' COMMENT '模版版本号，开发者自定义字段',
  `user_desc` varchar(45)  NOT NULL DEFAULT '' COMMENT '模版描述开发者自定义字段',
  `create_time` DATETIME NOT NULL DEFAULT '1970-01-01 08:00:01' COMMENT '开发者上传草稿时间',
  `branch` varchar(45)  NOT NULL DEFAULT '' COMMENT '分支',
  `source_miniprogram` varchar(45)  NOT NULL DEFAULT '' COMMENT '来源小程序名称',
  `source_miniprogram_appid` varchar(45)  NOT NULL DEFAULT '' COMMENT '来源小程序appid',
  `developer` varchar(45)  NOT NULL DEFAULT '' COMMENT '开发者',
  `field1` varchar(45)  NOT NULL DEFAULT '' COMMENT '备用字段',
  `field2` varchar(45)  NOT NULL DEFAULT '' COMMENT '备用字段2',
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0 COMMENT '软删除标记',
  `create_user` varchar(45)  NOT NULL DEFAULT '0' COMMENT '新建记录的用户',
  `update_user` varchar(45)  NOT NULL DEFAULT '0' COMMENT '最后一次操作的用户',
  `created_at` DATETIME NOT NULL DEFAULT '1970-01-01 08:00:01' COMMENT '记录添加时间',
  `updated_at` DATETIME NOT NULL DEFAULT '1970-01-01 08:00:01' COMMENT '记录更新时间',
  PRIMARY KEY (`component_template_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8  COMMENT = '平台模板表';

CREATE TABLE `release_item`  (
  `release_item_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `release_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'release_id',
  `name` varchar(45)  NOT NULL DEFAULT '' COMMENT '条目名称',
  `original_config` text  NULL COMMENT '原始配置内容',
  `online_config` text  NULL COMMENT '微信服务器配置内容',
  `push_config` text  NULL COMMENT '推送的配置内容',
  `response` text  NULL COMMENT '微信返回内容',
  `status` tinyint(4) NOT NULL DEFAULT 0 COMMENT '1构建成功;0构建失败;',
  `field1` varchar(45)  NOT NULL DEFAULT '' COMMENT '备用字段',
  `field2` varchar(45)  NOT NULL DEFAULT '' COMMENT '备用字段2',
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0 COMMENT '软删除标记',
  `create_user` varchar(45)  NOT NULL DEFAULT '0' COMMENT '新建记录的用户',
  `update_user` varchar(45)  NOT NULL DEFAULT '0' COMMENT '最后一次操作的用户',
  `created_at` DATETIME NOT NULL DEFAULT '1970-01-01 08:00:01' COMMENT '记录添加时间',
  `updated_at` DATETIME NOT NULL DEFAULT '1970-01-01 08:00:01' COMMENT '记录更新时间',
  PRIMARY KEY (`release_item_id`) USING BTREE,
  INDEX `idx_status`(`status`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COMMENT = '发版详情';

CREATE TABLE `tester`  (
  `tester_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `mini_program_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '小程序id',
  `app_id` varchar(255)  NOT NULL DEFAULT '' COMMENT '小程序app_id',
  `userstr` varchar(255)  NOT NULL DEFAULT '' COMMENT 'wechat_id别名',
  `wechat_id` varchar(255)  NOT NULL DEFAULT '' COMMENT '微信id',
  `field1` varchar(45)  NOT NULL DEFAULT '' COMMENT '备用字段',
  `field2` varchar(45)  NOT NULL DEFAULT '' COMMENT '备用字段2',
  `is_deleted` tinyint(4) NOT NULL DEFAULT 0 COMMENT '软删除标志',
  `create_user` varchar(45)  NOT NULL DEFAULT '' COMMENT '新建记录的用户',
  `update_user` varchar(45)  NOT NULL DEFAULT '' COMMENT '最后一次操作的用户',
  `created_at` DATETIME NOT NULL DEFAULT '1970-01-01 08:00:01' COMMENT '记录添加时间',
  `updated_at` DATETIME NOT NULL DEFAULT '1970-01-01 08:00:01' COMMENT '记录更新时间',
  PRIMARY KEY (`tester_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8  COMMENT = '体验者列表';

DROP TABLE `mini_program_template`;

