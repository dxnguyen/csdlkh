CREATE TABLE IF NOT EXISTS `#__scientists` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`asset_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',

`state` TINYINT(1)  NULL  DEFAULT 1,
`ordering` INT(11)  NULL  DEFAULT 0,
`checked_out` INT(11)  UNSIGNED,
`checked_out_time` DATETIME NULL  DEFAULT NULL ,
`created_by` INT(11)  NULL  DEFAULT 0,
`modified_by` INT(11)  NULL  DEFAULT 0,
`name` VARCHAR(50)  NOT NULL ,
`gender` VARCHAR(255)  NULL  DEFAULT "0",
`birthday` DATETIME NULL  DEFAULT NULL ,
`address` VARCHAR(255)  NULL  DEFAULT "",
`phone` VARCHAR(255)  NULL  DEFAULT "",
`email` VARCHAR(255)  NOT NULL ,
`position` TEXT NULL ,
PRIMARY KEY (`id`)
,KEY `idx_state` (`state`)
,KEY `idx_checked_out` (`checked_out`)
,KEY `idx_created_by` (`created_by`)
,KEY `idx_modified_by` (`modified_by`)
) DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE INDEX `#__scientists_name` ON `#__scientists`(`name`);

CREATE INDEX `#__scientists_gender` ON `#__scientists`(`gender`);

CREATE INDEX `#__scientists_birthday` ON `#__scientists`(`birthday`);

CREATE INDEX `#__scientists_address` ON `#__scientists`(`address`);

CREATE INDEX `#__scientists_phone` ON `#__scientists`(`phone`);

CREATE INDEX `#__scientists_email` ON `#__scientists`(`email`);


INSERT INTO `#__content_types` (`type_title`, `type_alias`, `table`, `rules`, `field_mappings`, `content_history_options`)
SELECT * FROM ( SELECT 'Detail','com_scientists.detail','{"special":{"dbtable":"#__scientists","key":"id","type":"DetailTable","prefix":"Joomla\\\\Component\\\\Scientists\\\\Administrator\\\\Table\\\\"}}', CASE 
                                    WHEN 'rules' is null THEN ''
                                    ELSE ''
                                    END as rules, CASE 
                                    WHEN 'field_mappings' is null THEN ''
                                    ELSE ''
                                    END as field_mappings, '{"formFile":"administrator\/components\/com_scientists\/forms\/detail.xml", "hideFields":["checked_out","checked_out_time","params","language"], "ignoreChanges":["modified_by", "modified", "checked_out", "checked_out_time"], "convertToInt":["publish_up", "publish_down"], "displayLookup":[{"sourceColumn":"catid","targetTable":"#__categories","targetColumn":"id","displayColumn":"title"},{"sourceColumn":"group_id","targetTable":"#__usergroups","targetColumn":"id","displayColumn":"title"},{"sourceColumn":"created_by","targetTable":"#__users","targetColumn":"id","displayColumn":"name"},{"sourceColumn":"access","targetTable":"#__viewlevels","targetColumn":"id","displayColumn":"title"},{"sourceColumn":"modified_by","targetTable":"#__users","targetColumn":"id","displayColumn":"name"}]}') AS tmp
WHERE NOT EXISTS (
	SELECT type_alias FROM `#__content_types` WHERE (`type_alias` = 'com_scientists.detail')
) LIMIT 1;
