ALTER TABLE `release_audit` MODIFY COLUMN `reason` TEXT COMMENT '审核失败原因';
ALTER TABLE `component_ext` ADD COLUMN `config_version` char(64) NOT NULL DEFAULT '' COMMENT '配置版本';
ALTER TABLE `mini_program_ext` ADD COLUMN `config_version` char(64) NOT NULL DEFAULT '' COMMENT '配置版本';
ALTER TABLE `release` ADD COLUMN `config_version` char(64) NOT NULL DEFAULT '' COMMENT '配置版本';
