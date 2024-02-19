ALTER TABLE `d_boutique_todolist` ADD `stopped` BOOLEAN NOT NULL DEFAULT FALSE AFTER `date_done`; 
ALTER TABLE `d_boutique_todolist` ADD `stopped_reason` VARCHAR(255) NULL AFTER `stopped`; 
ALTER TABLE `d_boutique_articles` ADD `archive` BOOLEAN NOT NULL DEFAULT FALSE AFTER `date_ajout`; 
ALTER TABLE `d_boutique_cmd` ADD `is_manual` BOOLEAN NOT NULL DEFAULT FALSE AFTER `id_article`;
ALTER TABLE `d_boutique_cmd` CHANGE `server` `server` VARCHAR(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL;
ALTER TABLE `d_boutique_cmd` CHANGE `server` `server` INT NULL DEFAULT NULL; 
ALTER TABLE `d_boutique_cmd` CHANGE `cmd` `cmd` VARCHAR(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL;
ALTER TABLE `d_boutique_cmd` ADD `archive` BOOLEAN NOT NULL DEFAULT FALSE AFTER `is_manual`; 

-- --------------------------------------------------------

--
-- Structure de la table `d_footer`
--

DROP TABLE IF EXISTS `d_footer`;
CREATE TABLE IF NOT EXISTS `d_footer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_page` int(11) DEFAULT NULL,
  `titre` varchar(255) DEFAULT NULL,
  `disabled` tinyint(1) NOT NULL,
  `link` varchar(255) DEFAULT NULL,
  `pos` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=30 DEFAULT CHARSET=latin1;

--
-- Contenu de la table `d_footer`
--

INSERT INTO `d_footer` (`id`, `id_page`, `titre`, `disabled`, `link`, `pos`) VALUES
(1, NULL, 'Accueil', 0, '/', 0),
(2, NULL, 'F.A.Q.', 0, 'faq', 2),
(3, NULL, 'Contact', 0, 'contact', 1),
(4, 1, NULL, 0, NULL, 4),
(5, 2, NULL, 0, NULL, 3),
(6, 3, NULL, 0, NULL, 5),
(10, NULL, 'Jouer', 1, 'jouer', NULL),
(11, NULL, 'Voter', 1, 'voter', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `d_header`
--

DROP TABLE IF EXISTS `d_header`;
CREATE TABLE IF NOT EXISTS `d_header` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_page` int(11) DEFAULT NULL,
  `titre` varchar(255) DEFAULT NULL,
  `link` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=32 DEFAULT CHARSET=latin1;

--
-- Contenu de la table `d_header`
--

INSERT INTO `d_header` (`id`, `id_page`, `titre`, `link`) VALUES
(1, NULL, 'Accueil', '/'),
(2, NULL, 'F.A.Q.', 'faq'),
(3, NULL, 'Contact', 'contact'),
(4, 1, NULL, NULL),
(5, 2, NULL, NULL),
(6, 3, NULL, NULL),
(13, NULL, 'Voter', 'voter'),
(12, NULL, 'Jouer', 'jouer');

-- --------------------------------------------------------

--
-- Structure de la table `d_header_menus`
--

DROP TABLE IF EXISTS `d_header_menus`;
CREATE TABLE IF NOT EXISTS `d_header_menus` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `is_menu` tinyint(1) NOT NULL DEFAULT '1',
  `link` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=latin1;

--
-- Contenu de la table `d_header_menus`
--

INSERT INTO `d_header_menus` (`id`, `name`, `is_menu`, `link`) VALUES
(1, 'Serveur', 1, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `d_header_menus_pages`
--

DROP TABLE IF EXISTS `d_header_menus_pages`;
CREATE TABLE IF NOT EXISTS `d_header_menus_pages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_page` int(11) NOT NULL,
  `pos` int(11) NOT NULL,
  `id_menu` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=latin1;

--
-- Contenu de la table `d_header_menus_pages`
--

INSERT INTO `d_header_menus_pages` (`id`, `id_page`, `pos`, `id_menu`) VALUES
(5, 4, 2, 1),
(4, 2, 0, 1),
(6, 5, 5, 1),
(13, 12, 3, 1),
(12, 13, 1, 1);

-- --------------------------------------------------------

--
-- Structure de la table `d_pages`
--

DROP TABLE IF EXISTS `d_pages`;
CREATE TABLE IF NOT EXISTS `d_pages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `name_raw` varchar(255) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `fa_icon` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=29 DEFAULT CHARSET=latin1;

--
-- Contenu de la table `d_pages`
--

INSERT INTO `d_pages` (`id`, `name`, `name_raw`, `file_name`, `fa_icon`) VALUES
(1, 'CGU / CGV', 'cgu', 'cgu', NULL),
(2, 'Règlement', 'reglement', 'reglement', NULL),
(3, 'Mentions légales', 'mentions-legales', 'm-legal', NULL);

