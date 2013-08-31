SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';


-- -----------------------------------------------------
-- Table `superbake`.`acos`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `superbake`.`acos` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `parent_id` INT(10) NULL DEFAULT NULL ,
  `model` VARCHAR(255) NULL DEFAULT '' ,
  `foreign_key` INT(10) NULL DEFAULT NULL ,
  `alias` VARCHAR(255) NULL DEFAULT '' ,
  `lft` INT(10) NULL DEFAULT NULL ,
  `rght` INT(10) NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `superbake`.`aros_acos`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `superbake`.`aros_acos` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `aro_id` INT(10) UNSIGNED NOT NULL ,
  `aco_id` INT(10) UNSIGNED NOT NULL ,
  `_create` CHAR(2) NOT NULL DEFAULT 0 ,
  `_read` CHAR(2) NOT NULL DEFAULT 0 ,
  `_update` CHAR(2) NOT NULL DEFAULT 0 ,
  `_delete` CHAR(2) NOT NULL DEFAULT 0 ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `superbake`.`aros`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `superbake`.`aros` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `parent_id` INT(10) NULL DEFAULT NULL ,
  `model` VARCHAR(255) NULL DEFAULT '' ,
  `foreign_key` INT(10) NULL DEFAULT NULL ,
  `alias` VARCHAR(255) NULL DEFAULT '' ,
  `lft` INT(10) NULL DEFAULT NULL ,
  `rght` INT(10) NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `superbake`.`groups`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `superbake`.`groups` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(45) NOT NULL ,
  `description` VARCHAR(255) NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `superbake`.`users`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `superbake`.`users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `username` VARCHAR(32) NOT NULL ,
  `password` VARCHAR(255) NOT NULL ,
  `email` VARCHAR(255) NOT NULL ,
  `realname` VARCHAR(100) NULL ,
  `created` DATETIME NOT NULL ,
  `modified` DATETIME NOT NULL ,
  `group_id` INT UNSIGNED NOT NULL ,
  `nbposts` INT(11) UNSIGNED NOT NULL DEFAULT 0 ,
  `bio` TEXT NULL DEFAULT NULL ,
  `website` VARCHAR(45) NULL DEFAULT NULL ,
  `see_nsfw` TINYINT(1) NOT NULL DEFAULT 0 ,
  `status` TINYINT(1) NOT NULL DEFAULT 0 ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_users_groups_idx` (`group_id` ASC) ,
  CONSTRAINT `fk_users_groups`
    FOREIGN KEY (`group_id` )
    REFERENCES `superbake`.`groups` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `superbake`.`post_categories`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `superbake`.`post_categories` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(45) NOT NULL ,
  `description` VARCHAR(255) NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `superbake`.`licenses`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `superbake`.`licenses` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(50) NOT NULL ,
  `short_description` VARCHAR(255) NULL DEFAULT NULL ,
  `link` VARCHAR(45) NULL DEFAULT NULL ,
  `private_name` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `superbake`.`posts`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `superbake`.`posts` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `title` VARCHAR(45) NOT NULL ,
  `excerpt` VARCHAR(255) NOT NULL ,
  `text` TEXT NULL DEFAULT NULL ,
  `sfw` TINYINT(1) NOT NULL DEFAULT 1 ,
  `created` DATETIME NOT NULL ,
  `modified` DATETIME NOT NULL ,
  `published` TINYINT(1) NOT NULL DEFAULT 0 ,
  `publication_date` DATETIME NULL ,
  `post_category_id` INT UNSIGNED NOT NULL ,
  `user_id` INT UNSIGNED NOT NULL ,
  `license_id` INT UNSIGNED NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_posts_post_categories1_idx` (`post_category_id` ASC) ,
  INDEX `fk_posts_users1_idx` (`user_id` ASC) ,
  INDEX `fk_posts_licenses1_idx` (`license_id` ASC) ,
  CONSTRAINT `fk_posts_post_categories`
    FOREIGN KEY (`post_category_id` )
    REFERENCES `superbake`.`post_categories` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_posts_users`
    FOREIGN KEY (`user_id` )
    REFERENCES `superbake`.`users` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_posts_licenses`
    FOREIGN KEY (`license_id` )
    REFERENCES `superbake`.`licenses` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `superbake`.`post_comments`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `superbake`.`post_comments` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `comment` TEXT NOT NULL ,
  `created` DATETIME NULL ,
  `modified` DATETIME NULL ,
  `post_id` INT UNSIGNED NOT NULL ,
  `user_id` INT UNSIGNED NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_post_comments_posts1_idx` (`post_id` ASC) ,
  INDEX `fk_post_comments_users1_idx` (`user_id` ASC) ,
  CONSTRAINT `fk_post_comments_posts`
    FOREIGN KEY (`post_id` )
    REFERENCES `superbake`.`posts` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_post_comments_users`
    FOREIGN KEY (`user_id` )
    REFERENCES `superbake`.`users` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `superbake`.`tags`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `superbake`.`tags` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(45) NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `superbake`.`post_tags`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `superbake`.`post_tags` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `post_id` INT UNSIGNED NOT NULL ,
  `tag_id` INT UNSIGNED NOT NULL ,
  INDEX `fk_posts_has_tags_tags1_idx` (`tag_id` ASC) ,
  INDEX `fk_posts_has_tags_posts1_idx` (`post_id` ASC) ,
  PRIMARY KEY (`id`) ,
  CONSTRAINT `fk_posts_tags_posts`
    FOREIGN KEY (`post_id` )
    REFERENCES `superbake`.`posts` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_posts_tags_tags`
    FOREIGN KEY (`tag_id` )
    REFERENCES `superbake`.`tags` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `superbake`.`project_states`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `superbake`.`project_states` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(45) NOT NULL ,
  `description` VARCHAR(255) NULL DEFAULT NULL ,
  `value` INT(2) UNSIGNED NOT NULL DEFAULT 0 ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `superbake`.`projects`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `superbake`.`projects` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(45) NOT NULL ,
  `short_description` VARCHAR(255) NULL DEFAULT NULL ,
  `description` TEXT NULL DEFAULT NULL ,
  `github_link` VARCHAR(255) NULL DEFAULT NULL ,
  `trello_link` VARCHAR(255) NULL DEFAULT NULL ,
  `demo_link` VARCHAR(255) NULL DEFAULT NULL ,
  `download` VARCHAR(255) NULL DEFAULT NULL ,
  `created` DATETIME NOT NULL ,
  `modified` DATETIME NOT NULL ,
  `project_state_id` INT UNSIGNED NOT NULL ,
  `license_id` INT UNSIGNED NOT NULL ,
  `user_id` INT UNSIGNED NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_projects_project_states1_idx` (`project_state_id` ASC) ,
  INDEX `fk_projects_licenses1_idx` (`license_id` ASC) ,
  INDEX `fk_projects_users1_idx` (`user_id` ASC) ,
  CONSTRAINT `projects_project_states`
    FOREIGN KEY (`project_state_id` )
    REFERENCES `superbake`.`project_states` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `projects_licenses`
    FOREIGN KEY (`license_id` )
    REFERENCES `superbake`.`licenses` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `projects_users`
    FOREIGN KEY (`user_id` )
    REFERENCES `superbake`.`users` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `superbake`.`project_notes`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `superbake`.`project_notes` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `title` VARCHAR(45) NULL ,
  `text` VARCHAR(45) NULL ,
  `project_id` INT UNSIGNED NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_project_notes_projects1_idx` (`project_id` ASC) ,
  CONSTRAINT `fk_project_notes_project`
    FOREIGN KEY (`project_id` )
    REFERENCES `superbake`.`projects` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `superbake`.`project_tags`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `superbake`.`project_tags` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `tag_id` INT UNSIGNED NOT NULL ,
  `project_id` INT UNSIGNED NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_project_notes_has_tags_tags1_idx` (`tag_id` ASC) ,
  INDEX `fk_projects_tags_projects1_idx` (`project_id` ASC) ,
  CONSTRAINT `project_tags_tags`
    FOREIGN KEY (`tag_id` )
    REFERENCES `superbake`.`tags` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `project_tags_projects`
    FOREIGN KEY (`project_id` )
    REFERENCES `superbake`.`projects` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `superbake`.`project_note_comments`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `superbake`.`project_note_comments` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `comment` TEXT NOT NULL ,
  `created` DATETIME NULL ,
  `modified` DATETIME NULL ,
  `user_id` INT UNSIGNED NOT NULL ,
  `project_note_id` INT UNSIGNED NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_post_comments_users1_idx` (`user_id` ASC) ,
  INDEX `fk_project_note_comments_project_notes1_idx` (`project_note_id` ASC) ,
  CONSTRAINT `fk_project_note_comments_users`
    FOREIGN KEY (`user_id` )
    REFERENCES `superbake`.`users` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_project_note_comments_project_notes`
    FOREIGN KEY (`project_note_id` )
    REFERENCES `superbake`.`project_notes` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `superbake`.`likes`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `superbake`.`likes` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `title` VARCHAR(100) NOT NULL ,
  `adress` VARCHAR(255) NOT NULL ,
  `sfw` TINYINT(1) NOT NULL DEFAULT 1 ,
  `created` DATETIME NOT NULL ,
  `modified` DATETIME NOT NULL ,
  `user_id` INT UNSIGNED NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_likes_users1_idx` (`user_id` ASC) ,
  CONSTRAINT `fk_likes_users1`
    FOREIGN KEY (`user_id` )
    REFERENCES `superbake`.`users` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `superbake`.`like_tags`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `superbake`.`like_tags` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `tag_id` INT UNSIGNED NOT NULL ,
  `like_id` INT UNSIGNED NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_tags_has_liked_liked1_idx` (`like_id` ASC) ,
  INDEX `fk_tags_has_liked_tags1_idx` (`tag_id` ASC) ,
  CONSTRAINT `fk_like_tags_tags`
    FOREIGN KEY (`tag_id` )
    REFERENCES `superbake`.`tags` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_like_tags_likes`
    FOREIGN KEY (`like_id` )
    REFERENCES `superbake`.`likes` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `superbake`.`like_comments`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `superbake`.`like_comments` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `comment` TEXT NOT NULL ,
  `created` DATETIME NOT NULL ,
  `modified` DATETIME NOT NULL ,
  `user_id` INT UNSIGNED NOT NULL ,
  `like_id` INT UNSIGNED NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_post_comments_users1_idx` (`user_id` ASC) ,
  INDEX `fk_like_comments_likes1_idx` (`like_id` ASC) ,
  CONSTRAINT `fk_like_comments_users`
    FOREIGN KEY (`user_id` )
    REFERENCES `superbake`.`users` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_like_comments_likes`
    FOREIGN KEY (`like_id` )
    REFERENCES `superbake`.`likes` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `superbake`.`link_categories`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `superbake`.`link_categories` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(45) NOT NULL ,
  `description` VARCHAR(255) NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `superbake`.`links`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `superbake`.`links` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `title` VARCHAR(100) NOT NULL ,
  `link` VARCHAR(255) NOT NULL ,
  `sfw` TINYINT(1) NOT NULL DEFAULT 1 ,
  `dead` TINYINT(1) NOT NULL DEFAULT 0 ,
  `created` DATETIME NOT NULL ,
  `modified` DATETIME NOT NULL ,
  `link_category_id` INT UNSIGNED NOT NULL ,
  `user_id` INT UNSIGNED NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_links_link_categories1_idx` (`link_category_id` ASC) ,
  INDEX `fk_links_users1` (`user_id` ASC) ,
  CONSTRAINT `fk_links_link_categories`
    FOREIGN KEY (`link_category_id` )
    REFERENCES `superbake`.`link_categories` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_links_users1`
    FOREIGN KEY (`user_id` )
    REFERENCES `superbake`.`users` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `superbake`.`link_tags`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `superbake`.`link_tags` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `link_id` INT UNSIGNED NOT NULL ,
  `tag_id` INT UNSIGNED NOT NULL ,
  INDEX `fk_links_has_tags_tags1_idx` (`tag_id` ASC) ,
  INDEX `fk_links_has_tags_links1_idx` (`link_id` ASC) ,
  PRIMARY KEY (`id`) ,
  CONSTRAINT `fk_links_tags_links`
    FOREIGN KEY (`link_id` )
    REFERENCES `superbake`.`links` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_links_tags_tags`
    FOREIGN KEY (`tag_id` )
    REFERENCES `superbake`.`tags` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `superbake`.`galleries`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `superbake`.`galleries` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(45) NULL ,
  `description` VARCHAR(255) NULL ,
  `path` VARCHAR(255) NULL ,
  `parent_id` INT UNSIGNED NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `superbake`.`gallery_items`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `superbake`.`gallery_items` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `file` VARCHAR(45) NOT NULL ,
  `name` VARCHAR(45) NOT NULL ,
  `description` VARCHAR(45) NULL ,
  `extension` VARCHAR(45) NOT NULL ,
  `gallery_id` INT UNSIGNED NOT NULL ,
  `license_id` INT UNSIGNED NOT NULL ,
  `user_id` INT UNSIGNED NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_files_files_categories1_idx` (`gallery_id` ASC) ,
  INDEX `fk_files_licenses1_idx` (`license_id` ASC) ,
  INDEX `fk_files_users1_idx` (`user_id` ASC) ,
  CONSTRAINT `fk_gallery_items_galleries`
    FOREIGN KEY (`gallery_id` )
    REFERENCES `superbake`.`galleries` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_gallery_items_licenses`
    FOREIGN KEY (`license_id` )
    REFERENCES `superbake`.`licenses` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_gallery_items_users`
    FOREIGN KEY (`user_id` )
    REFERENCES `superbake`.`users` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `superbake`.`gallery_item_tags`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `superbake`.`gallery_item_tags` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `gallery_item_id` INT UNSIGNED NOT NULL ,
  `tag_id` INT UNSIGNED NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_files_has_tags_tags1_idx` (`tag_id` ASC) ,
  INDEX `fk_files_has_tags_files1_idx` (`gallery_item_id` ASC) ,
  CONSTRAINT `fk_gallery_item_tags_gallery_items`
    FOREIGN KEY (`gallery_item_id` )
    REFERENCES `superbake`.`gallery_items` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_gallery_item_tags_tags`
    FOREIGN KEY (`tag_id` )
    REFERENCES `superbake`.`tags` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `superbake`.`gallery_item_comments`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `superbake`.`gallery_item_comments` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `comment` TEXT NOT NULL ,
  `created` DATETIME NOT NULL ,
  `modified` DATETIME NOT NULL ,
  `user_id` INT UNSIGNED NOT NULL ,
  `gallery_item_id` INT UNSIGNED NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_post_comments_users1_idx` (`user_id` ASC) ,
  INDEX `fk_file_comments_files1_idx` (`gallery_item_id` ASC) ,
  CONSTRAINT `fk_gallery_item_comments_users`
    FOREIGN KEY (`user_id` )
    REFERENCES `superbake`.`users` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_gallery_item_comments_gallery_items`
    FOREIGN KEY (`gallery_item_id` )
    REFERENCES `superbake`.`gallery_items` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;



SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
