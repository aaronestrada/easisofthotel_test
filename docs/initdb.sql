-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Schema mydb
-- -----------------------------------------------------
-- -----------------------------------------------------
-- Schema easisofthotel_db
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema easisofthotel_db
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `easisofthotel_db` DEFAULT CHARACTER SET latin1 ;
USE `easisofthotel_db` ;

-- -----------------------------------------------------
-- Table `status`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `status` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(50) NOT NULL,
  `parent_id` INT(10) UNSIGNED NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_status_parent_id_idx` (`parent_id` ASC),
  CONSTRAINT `fk_status_parent_id`
    FOREIGN KEY (`parent_id`)
    REFERENCES `status` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 7
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `role`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `role` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(75) NOT NULL,
  `internal_code` VARCHAR(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `internal_code_UNIQUE` (`internal_code` ASC))
ENGINE = InnoDB
AUTO_INCREMENT = 4
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `user`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `user` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(50) NULL DEFAULT NULL,
  `lastname` VARCHAR(50) NULL DEFAULT NULL,
  `username` VARCHAR(50) NOT NULL,
  `pwd` VARCHAR(50) NOT NULL,
  `role_id` INT(10) UNSIGNED NOT NULL,
  `status_id` INT(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `username_UNIQUE` (`username` ASC),
  INDEX `fk_user_role_idx` (`role_id` ASC),
  INDEX `fk_user_status_idx` (`status_id` ASC),
  CONSTRAINT `fk_user_status`
    FOREIGN KEY (`status_id`)
    REFERENCES `status` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_user_role`
    FOREIGN KEY (`role_id`)
    REFERENCES `role` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 7
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `hotel`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `hotel` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `starnumber` DECIMAL(10,2) UNSIGNED NOT NULL,
  `user_id` INT(10) UNSIGNED NOT NULL,
  `status_id` INT(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_hotel_status_id_idx` (`status_id` ASC),
  INDEX `fk_hotel_user_id_idx` (`user_id` ASC),
  CONSTRAINT `fk_hotel_status_id`
    FOREIGN KEY (`status_id`)
    REFERENCES `status` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_hotel_user_id`
    FOREIGN KEY (`user_id`)
    REFERENCES `user` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 4
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `hotel_price`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `hotel_price` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `hotel_id` INT(10) UNSIGNED NOT NULL,
  `description` TEXT NOT NULL,
  `price` DECIMAL(10,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id`),
  INDEX `fk_hotel_price_hotel_id_idx` (`hotel_id` ASC),
  CONSTRAINT `fk_hotel_price_hotel_id`
    FOREIGN KEY (`hotel_id`)
    REFERENCES `hotel` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 6
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `hotel_user`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `hotel_user` (
  `hotel_id` INT(10) UNSIGNED NOT NULL,
  `user_id` INT(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`hotel_id`, `user_id`),
  INDEX `fk_hotel_user_user_id_idx` (`user_id` ASC),
  CONSTRAINT `fk_hotel_user_hotel_id`
    FOREIGN KEY (`hotel_id`)
    REFERENCES `hotel` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_hotel_user_user_id`
    FOREIGN KEY (`user_id`)
    REFERENCES `user` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `permission`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `permission` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(50) NOT NULL,
  `internal_code` VARCHAR(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `internal_code_UNIQUE` (`internal_code` ASC))
ENGINE = InnoDB
AUTO_INCREMENT = 11
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `role_permission`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `role_permission` (
  `role_id` INT(10) UNSIGNED NOT NULL,
  `permission_id` INT(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`role_id`, `permission_id`),
  INDEX `fk_role_permission_permission_id_idx` (`permission_id` ASC),
  CONSTRAINT `fk_role_permission_permission_id`
    FOREIGN KEY (`permission_id`)
    REFERENCES `permission` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_role_permission_role_id`
    FOREIGN KEY (`role_id`)
    REFERENCES `role` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;

USE `easisofthotel_db` ;

-- -----------------------------------------------------
-- Placeholder table for view `roles_noroot`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `roles_noroot` (`id` INT, `name` INT);

-- -----------------------------------------------------
-- View `roles_noroot`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `roles_noroot`;
USE `easisofthotel_db`;
CREATE  OR REPLACE ALGORITHM=UNDEFINED DEFINER=`usreasisofthotel`@`%` SQL SECURITY DEFINER VIEW `roles_noroot` AS select distinct `r`.`id` AS `id`,`r`.`name` AS `name` from `role` `r` where (not(exists(select distinct `rp`.`role_id` from (`role_permission` `rp` join `permission` `p` on((`rp`.`permission_id` = `p`.`id`))) where ((`p`.`internal_code` = 'root_permissions') and (`rp`.`role_id` = `r`.`id`)))));

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
