ALTER TABLE `mini_program` DROP COLUMN `user_version`;

ALTER TABLE `mini_program` ADD COLUMN `authorization_status` TINYINT NOT NULL DEFAULT 10  COMMENT '授权状态:10正常;20授权失效';


ALTER TABLE `release_audit` ADD COLUMN `screenshot` VARCHAR(45) NOT NULL DEFAULT '' COMMENT '审核失败的截图';

ALTER TABLE `release` ADD COLUMN `release_on_audited` tinyint(4) NOT NULL DEFAULT 1 COMMENT '审核通过时立即发布';
ALTER TABLE `release` ADD COLUMN `release_status` tinyint(4) NOT NULL DEFAULT 0 COMMENT '发布状态: 待发布/已发布:1/发布失败:2';