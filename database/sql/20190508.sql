ALTER TABLE `component` DROP COLUMN `is_deleted`,ADD COLUMN `deleted_at` int(11) NOT NULL DEFAULT 0 COMMENT '软删除标记';
ALTER TABLE `component_ext` DROP COLUMN `is_deleted`,ADD COLUMN `deleted_at` int(11) NOT NULL DEFAULT 0 COMMENT '软删除标记';
ALTER TABLE `component_template` DROP COLUMN `is_deleted`,ADD COLUMN `deleted_at` int(11) NOT NULL DEFAULT 0 COMMENT '软删除标记';
ALTER TABLE `mini_program` DROP COLUMN `is_deleted`,ADD COLUMN `deleted_at` int(11) NOT NULL DEFAULT 0 COMMENT '软删除标记';
ALTER TABLE `mini_program_ext` DROP COLUMN `is_deleted`,ADD COLUMN `deleted_at` int(11) NOT NULL DEFAULT 0 COMMENT '软删除标记';
ALTER TABLE `release` DROP COLUMN `is_deleted`,ADD COLUMN `deleted_at` int(11) NOT NULL DEFAULT 0 COMMENT '软删除标记';
ALTER TABLE `release_audit` DROP COLUMN `is_deleted`,ADD COLUMN `deleted_at` int(11) NOT NULL DEFAULT 0 COMMENT '软删除标记';
ALTER TABLE `release_item` DROP COLUMN `is_deleted`,ADD COLUMN `deleted_at` int(11) NOT NULL DEFAULT 0 COMMENT '软删除标记';
ALTER TABLE `tester` DROP COLUMN `is_deleted`,ADD COLUMN `deleted_at` int(11) NOT NULL DEFAULT 0 COMMENT '软删除标记';
ALTER TABLE `validate_file` DROP COLUMN `is_deleted`,ADD COLUMN `deleted_at` int(11) NOT NULL DEFAULT 0 COMMENT '软删除标记';
DROP TABLE `template_ext`;
DROP TABLE `mini_program_template_draft`;






