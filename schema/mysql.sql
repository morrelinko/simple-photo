CREATE TABLE `photos` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`storage_name` VARCHAR(50) NOT NULL COLLATE 'utf8_bin',
	`file_name` VARCHAR(255) NOT NULL COLLATE 'utf8_bin',
	`file_extension` VARCHAR(10) NOT NULL COLLATE 'utf8_bin',
	`file_size` VARCHAR(255) NOT NULL DEFAULT '0' COLLATE 'utf8_bin',
	`file_path` VARCHAR(100) NOT NULL COLLATE 'utf8_bin',
	`file_mime` VARCHAR(50) NOT NULL COLLATE 'utf8_bin',
	`created_at` TIMESTAMP NULL DEFAULT NULL,
	`updated_at` TIMESTAMP NOT NULL ON UPDATE CURRENT_TIMESTAMP,
	PRIMARY KEY (`photo_id`),
	UNIQUE INDEX `photo_id` (`photo_id`)
)
COLLATE='utf8_bin'
ENGINE=InnoDB;
