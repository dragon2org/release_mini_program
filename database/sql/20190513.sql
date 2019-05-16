ALTER TABLE `release_audit` MODIFY COLUMN `reason` TEXT COMMENT '审核失败原因';
ALTER TABLE `component_ext` ADD COLUMN `config_version` varchar(64) NOT NULL DEFAULT '' COMMENT '配置版本';
ALTER TABLE `mini_program_ext` ADD COLUMN `config_version` varchar(64) NOT NULL DEFAULT '' COMMENT '配置版本';
ALTER TABLE `release` ADD COLUMN `config_version` varchar(64) NOT NULL DEFAULT '' COMMENT '配置版本';

ALTER TABLE `release_audit` MODIFY COLUMN `release_id` int(11) NOT NULL DEFAULT 0 COMMENT '关联发版id';


ALTER TABLE `release` ADD COLUMN `category` varchar(45) NOT NULL DEFAULT '' COMMENT '构建任务类型;setting/commit/audit/release';
ALTER TABLE `release` DROP COLUMN `release_on_audited`;

create table `failed_jobs` (
`id` bigint unsigned not null auto_increment primary key COMMENT '主键',
`connection` text  COMMENT '连接信息',
`queue` text COMMENT 'queue',
`payload` longtext COMMENT 'payload',
`exception` longtext COMMENT 'exception',
`failed_at` timestamp default CURRENT_TIMESTAMP not null COMMENT '失败时间'
) ENGINE = InnoDB CHARACTER SET = utf8  COMMENT = '失败任务表';