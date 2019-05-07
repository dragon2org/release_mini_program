ALTER TABLE `mini_program` DROP COLUMN `user_version`;

ALTER TABLE `mini_program` ADD COLUMN `authorization_status` TINYINT NOT NULL DEFAULT 10  COMMENT '授权状态:10正常;20授权失效';