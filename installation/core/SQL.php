<?php
//Table du forum
$sql_forum->exec("CREATE TABLE IF NOT EXISTS `d_forum`
(
  `id` INT NOT NULL AUTO_INCREMENT ,
  `titre_post` VARCHAR(255) NOT NULL ,
  `last_user` VARCHAR(255) NOT NULL ,
  `user` VARCHAR(255) NOT NULL ,
  `resolu` BOOLEAN NOT NULL DEFAULT FALSE ,
  `date_last_post` DATETIME NOT NULL ,
  `date_post` DATE NOT NULL ,
  PRIMARY KEY (`id`)
)
ENGINE = MyISAM;");
CREATE TABLE `test`.`d_forum_com` ( `id` INT NOT NULL AUTO_INCREMENT , `content_com` TEXT NOT NULL , `date_post` DATETIME NOT NULL , `user` VARCHAR(255) NOT NULL , PRIMARY KEY (`id`)) ENGINE = MyISAM
//Il manque le dernier champs de la table
