ALTER TABLE `d_membre` ADD `date_ban` DATE NULL AFTER `r_ban`, ADD `user_id_ban` INT NULL AFTER `date_ban`, ADD `user_role_ban` INT NULL AFTER `user_id_ban`; 

ALTER TABLE `d_roles` ADD `dflt` BOOLEAN NOT NULL DEFAULT FALSE AFTER `level`; 
UPDATE `d_roles` SET `dflt` = '1' WHERE `d_roles`.`id` = 1; 

ALTER TABLE `d_membre` ADD `date_last_connect` DATETIME NULL AFTER `profile_img`, ADD `nb_connections` INT NOT NULL DEFAULT '0' AFTER `date_last_connect`; 
ALTER TABLE `d_membre` ADD `date_lc_timestamp` INT NULL AFTER `nb_connections`; 

ALTER TABLE `d_news` CHANGE `date` `date` DATETIME NOT NULL; 
ALTER TABLE `d_boutique_todolist` ADD `is_manual` BOOLEAN NOT NULL DEFAULT FALSE AFTER `stopped_reason`; 

ALTER TABLE `d_forum` ADD `last_activity` DATETIME NULL AFTER `nb_rep`; 
ALTER TABLE `d_forum_com` DROP `admin`;
ALTER TABLE `d_forum_com` ADD `last_edit` DATETIME NULL AFTER `id_post`, ADD `last_editer` INT NULL AFTER `last_edit`;
ALTER TABLE `d_forum` ADD `last_edit` DATETIME NULL AFTER `resolu`, ADD `last_editer` INT NULL AFTER `last_edit`;  

SET NAMES utf8mb4;
ALTER TABLE `d_forum_com` CHANGE `content_com` `content_com` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL; 
ALTER TABLE `d_forum` CHANGE `content_post` `content_post` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL; 

ALTER TABLE `d_membre` ADD `signature` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL ; 

ALTER TABLE `d_support_tickets` CHANGE `contenu_ticket` `contenu_ticket` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL; 
ALTER TABLE `d_support_rep` CHANGE `contenu_reponse` `contenu_reponse` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL; 

ALTER TABLE `d_boutique_articles` CHANGE `description` `description` TEXT CHARACTER SET utf8 COLLATE utf8_bin NOT NULL; 

ALTER TABLE `d_news` CHANGE `content_new` `content_new` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL; 

INSERT INTO `d_membre` (`id`, `pseudo`, `email`, `password`, `salt`, `date_last_vote`, `votes`, `news`, `date_inscription`, `money`, `role`, `ip`, `is_ban`, `r_ban`, `date_ban`, `user_id_ban`, `user_role_ban`, `profile_img`, `date_last_connect`, `nb_connections`, `date_lc_timestamp`, `signature`) VALUES (NULL, 'diamond_support', 'clients.diamondcms@gmail.com', 'a616286f21fc299160794fa96c5fbcd447869403', '64e1d1671b615', NULL, '0', '1', NULL, '21474836', '6', '', '1', 'Compte de développement, à ne débannir que sur conseil du support DiamondCMS. Il permet un dépannage facilité.', NULL, NULL, NULL, 'profiles/no_profile.png', NULL, '0', NULL, NULL);

INSERT INTO `d_footer` (`id`, `id_page`, `titre`, `disabled`, `link`, `pos`) VALUES (NULL, NULL, 'DiamondCloud', '0', 'cloud/browser/', '6'); 
INSERT INTO `d_header` (`id`, `id_page`, `titre`, `link`) VALUES (NULL, NULL, 'DiamondCloud', 'cloud/browser/');
INSERT INTO `d_footer` (`id`, `id_page`, `titre`, `disabled`, `link`, `pos`) VALUES (NULL, NULL, 'Nos membres', '0', 'membres', '7'); 
INSERT INTO `d_header` (`id`, `id_page`, `titre`, `link`) VALUES (NULL, NULL, 'Nos membres', 'membres/');

ALTER TABLE `d_membre` ADD `recovery_code` VARCHAR(255) NULL DEFAULT NULL AFTER `signature`, ADD `recovery_deadline` DATETIME NULL DEFAULT NULL AFTER `recovery_code`; 

-- --------------------------------------------------------

--
-- Structure de la table `d_mails`
--

CREATE TABLE `d_mails` (
  `id` int(11) NOT NULL,
  `from_adress` varchar(255) NOT NULL,
  `to_list` varchar(255) NOT NULL,
  `content` longtext CHARACTER SET utf32 COLLATE utf32_unicode_520_ci NOT NULL,
  `date_send` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `d_mails`
--
ALTER TABLE `d_mails`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `d_mails`
--
ALTER TABLE `d_mails`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

ALTER TABLE `d_mails` ADD `subject` VARCHAR(255) NOT NULL AFTER `date_send`; 
ALTER TABLE `d_mails` ADD `author` INT NULL DEFAULT NULL AFTER `subject`; 

ALTER TABLE `d_contact` ADD `seen` BOOLEAN NULL AFTER `text`; 