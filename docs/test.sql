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
  `see_nsfw` TINYINT(1) NULL DEFAULT 0,
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
  `excerpt` VARCHAR(255) NOT NULL,
  `text` TEXT NULL DEFAULT NULL,
  `sfw` TINYINT(1) NOT NULL DEFAULT 1,
  `anon` TINYINT(1) NOT NULL DEFAULT 0,
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
-- Table `post_comments`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `post_comments` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `comment` TEXT NOT NULL,
  `created` DATETIME NULL,
  `modified` DATETIME NULL,
  `post_id` INT UNSIGNED NOT NULL,
  `user_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_post_comments_posts1_idx` (`post_id` ASC),
  INDEX `fk_post_comments_users1_idx` (`user_id` ASC),
  CONSTRAINT `fk_post_comments_posts`
    FOREIGN KEY (`post_id`)
    REFERENCES `posts` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_post_comments_users`
    FOREIGN KEY (`user_id`)
    REFERENCES `users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `tags`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `tags` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `post_tags`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `post_tags` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `post_id` INT UNSIGNED NOT NULL,
  `tag_id` INT UNSIGNED NOT NULL,
  INDEX `fk_posts_has_tags_tags1_idx` (`tag_id` ASC),
  INDEX `fk_posts_has_tags_posts1_idx` (`post_id` ASC),
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_posts_tags_posts`
    FOREIGN KEY (`post_id`)
    REFERENCES `posts` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_posts_tags_tags`
    FOREIGN KEY (`tag_id`)
    REFERENCES `tags` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `project_states`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `project_states` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NOT NULL,
  `description` VARCHAR(255) NULL DEFAULT NULL,
  `value` INT(2) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `projects`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `projects` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NOT NULL,
  `short_description` VARCHAR(255) NULL DEFAULT NULL,
  `description` TEXT NULL DEFAULT NULL,
  `sfw` TINYINT(1) NOT NULL DEFAULT 1,
  `github_link` VARCHAR(255) NULL DEFAULT NULL,
  `trello_link` VARCHAR(255) NULL DEFAULT NULL,
  `demo_link` VARCHAR(255) NULL DEFAULT NULL,
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
  `title` VARCHAR(45) NULL,
  `text` VARCHAR(45) NULL,
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
-- Table `project_tags`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `project_tags` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `tag_id` INT UNSIGNED NOT NULL,
  `project_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_project_notes_has_tags_tags1_idx` (`tag_id` ASC),
  INDEX `fk_projects_tags_projects1_idx` (`project_id` ASC),
  CONSTRAINT `project_tags_tags`
    FOREIGN KEY (`tag_id`)
    REFERENCES `tags` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `project_tags_projects`
    FOREIGN KEY (`project_id`)
    REFERENCES `projects` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `project_note_comments`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `project_note_comments` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `comment` TEXT NOT NULL,
  `created` DATETIME NULL,
  `modified` DATETIME NULL,
  `user_id` INT UNSIGNED NOT NULL,
  `project_note_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_post_comments_users1_idx` (`user_id` ASC),
  INDEX `fk_project_note_comments_project_notes1_idx` (`project_note_id` ASC),
  CONSTRAINT `fk_project_note_comments_users`
    FOREIGN KEY (`user_id`)
    REFERENCES `users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_project_note_comments_project_notes`
    FOREIGN KEY (`project_note_id`)
    REFERENCES `project_notes` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `likes`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `likes` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(100) NOT NULL,
  `adress` VARCHAR(255) NOT NULL,
  `sfw` TINYINT(1) NOT NULL DEFAULT 1,
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
-- Table `like_tags`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `like_tags` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `tag_id` INT UNSIGNED NOT NULL,
  `like_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_tags_has_liked_liked1_idx` (`like_id` ASC),
  INDEX `fk_tags_has_liked_tags1_idx` (`tag_id` ASC),
  CONSTRAINT `fk_like_tags_tags`
    FOREIGN KEY (`tag_id`)
    REFERENCES `tags` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_like_tags_likes`
    FOREIGN KEY (`like_id`)
    REFERENCES `likes` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `like_comments`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `like_comments` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `comment` TEXT NOT NULL,
  `created` DATETIME NOT NULL,
  `modified` DATETIME NOT NULL,
  `user_id` INT UNSIGNED NOT NULL,
  `like_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_post_comments_users1_idx` (`user_id` ASC),
  INDEX `fk_like_comments_likes1_idx` (`like_id` ASC),
  CONSTRAINT `fk_like_comments_users`
    FOREIGN KEY (`user_id`)
    REFERENCES `users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_like_comments_likes`
    FOREIGN KEY (`like_id`)
    REFERENCES `likes` (`id`)
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
  `sfw` TINYINT(1) NOT NULL DEFAULT 1,
  `anon` TINYINT(1) NOT NULL DEFAULT 0,
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
-- Table `link_tags`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `link_tags` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `link_id` INT UNSIGNED NOT NULL,
  `tag_id` INT UNSIGNED NOT NULL,
  INDEX `fk_links_has_tags_tags1_idx` (`tag_id` ASC),
  INDEX `fk_links_has_tags_links1_idx` (`link_id` ASC),
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_links_tags_links`
    FOREIGN KEY (`link_id`)
    REFERENCES `links` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_links_tags_tags`
    FOREIGN KEY (`tag_id`)
    REFERENCES `tags` (`id`)
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
  `sfw` TINYINT(1) NOT NULL DEFAULT 1,
  `anon` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Gallery is anonymous',
  `public` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Other can post',
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
  `sfw` TINYINT(1) NOT NULL DEFAULT 1,
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
-- Table `gallery_item_tags`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `gallery_item_tags` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `gallery_item_id` INT UNSIGNED NOT NULL,
  `tag_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_files_has_tags_tags1_idx` (`tag_id` ASC),
  INDEX `fk_files_has_tags_files1_idx` (`gallery_item_id` ASC),
  CONSTRAINT `fk_gallery_item_tags_gallery_items`
    FOREIGN KEY (`gallery_item_id`)
    REFERENCES `gallery_items` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_gallery_item_tags_tags`
    FOREIGN KEY (`tag_id`)
    REFERENCES `tags` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `gallery_item_comments`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `gallery_item_comments` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `comment` TEXT NOT NULL,
  `created` DATETIME NOT NULL,
  `modified` DATETIME NOT NULL,
  `user_id` INT UNSIGNED NOT NULL,
  `gallery_item_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_post_comments_users1_idx` (`user_id` ASC),
  INDEX `fk_file_comments_files1_idx` (`gallery_item_id` ASC),
  CONSTRAINT `fk_gallery_item_comments_users`
    FOREIGN KEY (`user_id`)
    REFERENCES `users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_gallery_item_comments_gallery_items`
    FOREIGN KEY (`gallery_item_id`)
    REFERENCES `gallery_items` (`id`)
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
  `sfw` TINYINT(1) NOT NULL DEFAULT 1,
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
