SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Table `acos`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `acos` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `parent_id` INT(10) NULL DEFAULT NULL,
  `model` VARCHAR(255) NULL DEFAULT '',
  `foreign_key` INT(10) NULL DEFAULT NULL,
  `alias` VARCHAR(255) NULL DEFAULT '',
  `lft` INT(10) NULL DEFAULT NULL,
  `rght` INT(10) NULL DEFAULT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `aros_acos`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `aros_acos` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `aro_id` INT(10) UNSIGNED NOT NULL,
  `aco_id` INT(10) UNSIGNED NOT NULL,
  `_create` CHAR(2) NOT NULL DEFAULT 0,
  `_read` CHAR(2) NOT NULL DEFAULT 0,
  `_update` CHAR(2) NOT NULL DEFAULT 0,
  `_delete` CHAR(2) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `aros`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `aros` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `parent_id` INT(10) NULL DEFAULT NULL,
  `model` VARCHAR(255) NULL DEFAULT '',
  `foreign_key` INT(10) NULL DEFAULT NULL,
  `alias` VARCHAR(255) NULL DEFAULT '',
  `lft` INT(10) NULL DEFAULT NULL,
  `rght` INT(10) NULL DEFAULT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `groups`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `groups` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NOT NULL,
  `description` VARCHAR(255) NULL DEFAULT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `users`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(32) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `realname` VARCHAR(100) NULL,
  `created` DATETIME NOT NULL,
  `modified` DATETIME NOT NULL,
  `group_id` INT UNSIGNED NOT NULL,
  `nbposts` INT(11) UNSIGNED NULL DEFAULT 0,
  `bio` TEXT NULL DEFAULT NULL,
  `website` VARCHAR(45) NULL DEFAULT NULL,
  `status` TINYINT(1) NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  INDEX `fk_users_groups_idx` (`group_id` ASC),
  CONSTRAINT `fk_users_groups`
    FOREIGN KEY (`group_id`)
    REFERENCES `groups` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `post_categories`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `post_categories` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NOT NULL,
  `description` VARCHAR(255) NULL DEFAULT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `licenses`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `licenses` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(50) NOT NULL,
  `short_description` VARCHAR(255) NULL DEFAULT NULL,
  `link` VARCHAR(45) NULL DEFAULT NULL,
  `private_name` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `posts`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `posts` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(45) NOT NULL,
  `excerpt_fra` VARCHAR(255) NOT NULL,
  `excerpt_eng` VARCHAR(255) NOT NULL,
  `text_fra` TEXT NULL DEFAULT NULL,
  `text_eng` TEXT NULL DEFAULT NULL,
  `created` DATETIME NOT NULL,
  `modified` DATETIME NOT NULL,
  `published` TINYINT(1) NOT NULL DEFAULT 0,
  `publication_date` DATETIME NULL,
  `post_category_id` INT UNSIGNED NOT NULL,
  `user_id` INT UNSIGNED NOT NULL,
  `license_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_posts_post_categories1_idx` (`post_category_id` ASC),
  INDEX `fk_posts_users1_idx` (`user_id` ASC),
  INDEX `fk_posts_licenses1_idx` (`license_id` ASC),
  CONSTRAINT `fk_posts_post_categories`
    FOREIGN KEY (`post_category_id`)
    REFERENCES `post_categories` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_posts_users`
    FOREIGN KEY (`user_id`)
    REFERENCES `users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_posts_licenses`
    FOREIGN KEY (`license_id`)
    REFERENCES `licenses` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `project_states`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `project_states` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name_fra` VARCHAR(45) NOT NULL,
  `name_eng` VARCHAR(45) NOT NULL,
  `description_fra` VARCHAR(255) NULL DEFAULT NULL,
  `description_eng` VARCHAR(255) NULL DEFAULT NULL,
  `value` INT(2) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `projects`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `projects` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NOT NULL,
  `short_description_fra` VARCHAR(255) NULL DEFAULT NULL,
  `short_description_eng` VARCHAR(255) NULL DEFAULT NULL,
  `description_fra` TEXT NULL DEFAULT NULL,
  `description_eng` TEXT NULL DEFAULT NULL,
  `download` VARCHAR(255) NULL DEFAULT NULL,
  `created` DATETIME NOT NULL,
  `modified` DATETIME NOT NULL,
  `project_state_id` INT UNSIGNED NOT NULL,
  `license_id` INT UNSIGNED NOT NULL,
  `user_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_projects_project_states1_idx` (`project_state_id` ASC),
  INDEX `fk_projects_licenses1_idx` (`license_id` ASC),
  INDEX `fk_projects_users1_idx` (`user_id` ASC),
  CONSTRAINT `projects_project_states`
    FOREIGN KEY (`project_state_id`)
    REFERENCES `project_states` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `projects_licenses`
    FOREIGN KEY (`license_id`)
    REFERENCES `licenses` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `projects_users`
    FOREIGN KEY (`user_id`)
    REFERENCES `users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `project_notes`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `project_notes` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title_fra` VARCHAR(45) NOT NULL,
  `title_eng` VARCHAR(45) NOT NULL,
  `text_fra` VARCHAR(45) NOT NULL,
  `text_eng` VARCHAR(45) NOT NULL,
  `project_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_project_notes_projects1_idx` (`project_id` ASC),
  CONSTRAINT `fk_project_notes_project`
    FOREIGN KEY (`project_id`)
    REFERENCES `projects` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `likes`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `likes` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(100) NOT NULL,
  `link` VARCHAR(255) NOT NULL,
  `created` DATETIME NOT NULL,
  `modified` DATETIME NOT NULL,
  `user_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_likes_users1_idx` (`user_id` ASC),
  CONSTRAINT `fk_likes_users1`
    FOREIGN KEY (`user_id`)
    REFERENCES `users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `link_categories`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `link_categories` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NOT NULL,
  `description` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `links`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `links` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(100) NOT NULL,
  `link` VARCHAR(255) NOT NULL,
  `dead` TINYINT(1) NOT NULL DEFAULT 0,
  `created` DATETIME NOT NULL,
  `modified` DATETIME NOT NULL,
  `link_category_id` INT UNSIGNED NOT NULL,
  `user_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_links_link_categories1_idx` (`link_category_id` ASC),
  INDEX `fk_links_users1_idx` (`user_id` ASC),
  CONSTRAINT `fk_links_link_categories`
    FOREIGN KEY (`link_category_id`)
    REFERENCES `link_categories` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_links_users1`
    FOREIGN KEY (`user_id`)
    REFERENCES `users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `galleries`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `galleries` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NULL,
  `description` VARCHAR(255) NULL,
  `path` VARCHAR(255) NULL,
  `user_id` INT UNSIGNED NOT NULL COMMENT 'Owner',
  PRIMARY KEY (`id`),
  INDEX `fk_galleries_users1_idx` (`user_id` ASC),
  CONSTRAINT `fk_galleries_users1`
    FOREIGN KEY (`user_id`)
    REFERENCES `users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `gallery_items`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `gallery_items` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `file` VARCHAR(45) NOT NULL,
  `name` VARCHAR(45) NOT NULL,
  `description` VARCHAR(45) NULL,
  `gallery_id` INT UNSIGNED NOT NULL,
  `license_id` INT UNSIGNED NOT NULL,
  `user_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_files_files_categories1_idx` (`gallery_id` ASC),
  INDEX `fk_files_licenses1_idx` (`license_id` ASC),
  INDEX `fk_files_users1_idx` (`user_id` ASC),
  CONSTRAINT `fk_gallery_items_galleries`
    FOREIGN KEY (`gallery_id`)
    REFERENCES `galleries` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_gallery_items_licenses`
    FOREIGN KEY (`license_id`)
    REFERENCES `licenses` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_gallery_items_users`
    FOREIGN KEY (`user_id`)
    REFERENCES `users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `pages`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `pages` (
  `id` VARCHAR(16) NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `content` TEXT NOT NULL,
  `user_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_pages_users1_idx` (`user_id` ASC),
  CONSTRAINT `fk_pages_users1`
    FOREIGN KEY (`user_id`)
    REFERENCES `users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `report_reasons`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `report_reasons` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `reports`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `reports` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `url` VARCHAR(255) NOT NULL,
  `reason` VARCHAR(255) NULL,
  `report_reason_id` INT NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_reports_report_reasons1_idx` (`report_reason_id` ASC),
  CONSTRAINT `fk_reports_report_reasons1`
    FOREIGN KEY (`report_reason_id`)
    REFERENCES `report_reasons` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

-- -----------------------------------------------------
-- Data for table `groups`
-- -----------------------------------------------------
INSERT INTO `groups` (`id`, `name`, `description`) VALUES (1, 'Admin', 'Administrators');
INSERT INTO `groups` (`id`, `name`, `description`) VALUES (2, 'Users', 'Registered users');


-- -----------------------------------------------------
-- Data for table `users`
-- -----------------------------------------------------
INSERT INTO `users` (`id`, `username`, `password`, `email`, `realname`, `created`, `modified`, `group_id`, `nbposts`, `bio`, `website`, `status`) VALUES (NULL, 'Test', '6dc115021f08a0e2ae62d4fc446649f0028b0af6', 'test@example.com', 'Test guy', '2014-05-01 10:43:05', '2014-05-01 10:43:05', 1, 0, NULL, NULL, 1);


-- -----------------------------------------------------
-- Data for table `licenses`
-- -----------------------------------------------------
INSERT INTO `licenses` (`id`, `name`, `short_description`, `link`, `private_name`) VALUES (1, 'Test License', 'Test licence to protect your work', 'http://example.com', 'test');

-- -----------------------------------------------------
-- Data for table `project_states`
-- -----------------------------------------------------
INSERT INTO `project_states` (`id`, `name_fra`, `name_eng`, `description_fra`, `description_eng`, `value`) VALUES (NULL, 'Idée', 'Idea', 'Une idée de projet !', 'Just an idea', 0);
INSERT INTO `project_states` (`id`, `name_fra`, `name_eng`, `description_fra`, `description_eng`, `value`) VALUES (NULL, 'En cours', 'In dev', 'Projet en développement', 'Development started', 1);
INSERT INTO `project_states` (`id`, `name_fra`, `name_eng`, `description_fra`, `description_eng`, `value`) VALUES (NULL, 'Prêt', 'Ready', 'Une version stable existe', 'A stable version is available', 2);

