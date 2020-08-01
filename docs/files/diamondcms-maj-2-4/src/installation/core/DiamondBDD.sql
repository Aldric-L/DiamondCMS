

-- BDD de DiamondCMS - Version 1.1 Build A (Béta) - Licence Créative Commons Aldric L.
-- Ce fichier ne devrait plus être utilisé, au profit de son frère -fromscratch qui efface les tables si elles existes
-- Il est donc déprécié. @deprecated 


SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- --------------------------------------------------------

--
-- Structure de la table `d_boutique_achats`
--

CREATE TABLE `d_boutique_achats` (
  `id` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_article` int(11) NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `price` int(11) NOT NULL,
  `date` date NOT NULL,
  `success` tinyint(1) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `d_boutique_articles`
--

CREATE TABLE `d_boutique_articles` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `img` varchar(255) NOT NULL,
  `prix` int(11) NOT NULL,
  `cat` int(11) NOT NULL,
  `date_ajout` date DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `d_boutique_cat`
--

CREATE TABLE `d_boutique_cat` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `nb_articles` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `d_boutique_cmd`
--

CREATE TABLE `d_boutique_cmd` (
  `id` int(11) NOT NULL,
  `cmd` varchar(255) NOT NULL,
  `connexion_needed` tinyint(1) NOT NULL,
  `server` varchar(255) NOT NULL,
  `id_article` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `d_boutique_dedipass`
--

CREATE TABLE `d_boutique_dedipass` (
  `id` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `code` varchar(255) NOT NULL,
  `payout` float NOT NULL,
  `virtual_currency` int(11) NOT NULL,
  `date` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `d_boutique_paypal`
--

CREATE TABLE `d_boutique_paypal` (
  `id` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `payment_id` varchar(255) NOT NULL,
  `payment_status` text NOT NULL,
  `payment_amount` text NOT NULL,
  `payment_date` datetime NOT NULL,
  `payment_currency` text NOT NULL,
  `payer_email` text,
  `money_get` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `d_boutique_paypal_offres`
--

CREATE TABLE `d_boutique_paypal_offres` (
  `id` int(11) NOT NULL,
  `price` float NOT NULL,
  `tokens` int(11) NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `d_boutique_todolist`
--

CREATE TABLE `d_boutique_todolist` (
  `id` int(11) NOT NULL,
  `id_commande` int(11) NOT NULL,
  `cmd` varchar(255) NOT NULL,
  `done` tinyint(1) NOT NULL DEFAULT '0',
  `date_send` datetime NOT NULL,
  `date_done` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `d_contact`
--

CREATE TABLE `d_contact` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `date` datetime NOT NULL,
  `text` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `d_faq`
--

CREATE TABLE `d_faq` (
  `id` int(11) NOT NULL,
  `question` varchar(255) NOT NULL,
  `reponse` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `d_faq`
--

INSERT INTO `d_faq` (`id`, `question`, `reponse`) VALUES
(0, 'Comment puis-je désactiver cette page ?', 'En allant sur l\'interface d\'administration.');

-- --------------------------------------------------------

--
-- Structure de la table `d_forum`
--

CREATE TABLE `d_forum` (
  `id` int(11) NOT NULL,
  `titre_post` varchar(255) NOT NULL,
  `user` varchar(255) NOT NULL,
  `resolu` tinyint(1) NOT NULL DEFAULT '0',
  `date_post` date NOT NULL,
  `content_post` text NOT NULL,
  `id_scat` int(11) NOT NULL,
  `nb_rep` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `d_forum_cat`
--

CREATE TABLE `d_forum_cat` (
  `id` int(11) NOT NULL,
  `titre` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `d_forum_com`
--

CREATE TABLE `d_forum_com` (
  `id` int(11) NOT NULL,
  `content_com` text NOT NULL,
  `date_comment` datetime NOT NULL,
  `user` varchar(255) NOT NULL,
  `id_post` int(11) NOT NULL,
  `admin` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `d_forum_sous_cat`
--

CREATE TABLE `d_forum_sous_cat` (
  `id` int(11) NOT NULL,
  `id_cat` int(11) NOT NULL,
  `titre` varchar(255) NOT NULL,
  `nb_sujets` int(11) DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `d_membre`
--

CREATE TABLE `d_membre` (
  `id` int(11) NOT NULL,
  `pseudo` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `salt` text,
  `date_last_vote` varchar(30) DEFAULT NULL,
  `votes` int(11) DEFAULT '0',
  `news` tinyint(1) NOT NULL DEFAULT '1',
  `date_inscription` datetime DEFAULT NULL,
  `money` int(11) NOT NULL DEFAULT '0',
  `role` int(11) NOT NULL DEFAULT '1',
  `ip` varchar(60) NOT NULL,
  `is_ban` tinyint(1) DEFAULT '0',
  `r_ban` varchar(255) DEFAULT NULL,
  `profile_img` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `d_news`
--

CREATE TABLE `d_news` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `content_new` text NOT NULL,
  `date` date NOT NULL,
  `img` varchar(255) NOT NULL DEFAULT 'news.png',
  `user` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `d_notify`
--

CREATE TABLE `d_notify` (
  `id` int(11) NOT NULL,
  `content` varchar(255) NOT NULL,
  `user` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `link` varchar(255) DEFAULT NULL,
  `type` int(255) NOT NULL DEFAULT '0',
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `view` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `d_roles`
--

CREATE TABLE `d_roles` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `level` int(1) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `d_roles`
--

INSERT INTO `d_roles` (`id`, `name`, `level`) VALUES
(1, 'Membre', 0),
(2, 'Support', 2),
(3, 'Modérateur', 3),
(4, 'Admin', 4),
(5, 'V.I.P.', 0),
(6, 'diamond_master', 5);

-- --------------------------------------------------------

--
-- Structure de la table `d_support_rep`
--

CREATE TABLE `d_support_rep` (
  `id` int(11) NOT NULL,
  `contenu_reponse` text NOT NULL,
  `id_ticket` int(11) NOT NULL,
  `pseudo` varchar(255) NOT NULL,
  `date_reponse` datetime NOT NULL,
  `role` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `d_support_tickets`
--

CREATE TABLE `d_support_tickets` (
  `id` int(11) NOT NULL,
  `titre_ticket` varchar(80) NOT NULL,
  `contenu_ticket` text NOT NULL,
  `pseudo` int(255) NOT NULL,
  `date_ticket` date NOT NULL,
  `status` int(11) NOT NULL DEFAULT '0',
  `role` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

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

--
-- Index pour les tables exportées
--

--
-- Index pour la table `d_boutique_achats`
--
ALTER TABLE `d_boutique_achats`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `d_boutique_articles`
--
ALTER TABLE `d_boutique_articles`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `d_boutique_cat`
--
ALTER TABLE `d_boutique_cat`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id` (`id`);

--
-- Index pour la table `d_boutique_cmd`
--
ALTER TABLE `d_boutique_cmd`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `d_boutique_dedipass`
--
ALTER TABLE `d_boutique_dedipass`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `d_boutique_paypal`
--
ALTER TABLE `d_boutique_paypal`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `d_boutique_paypal_offres`
--
ALTER TABLE `d_boutique_paypal_offres`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `d_boutique_todolist`
--
ALTER TABLE `d_boutique_todolist`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `d_contact`
--
ALTER TABLE `d_contact`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`),
  ADD KEY `id_2` (`id`);

--
-- Index pour la table `d_faq`
--
ALTER TABLE `d_faq`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `d_forum`
--
ALTER TABLE `d_forum`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `d_forum_cat`
--
ALTER TABLE `d_forum_cat`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `d_forum_com`
--
ALTER TABLE `d_forum_com`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `d_forum_sous_cat`
--
ALTER TABLE `d_forum_sous_cat`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `d_membre`
--
ALTER TABLE `d_membre`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `d_news`
--
ALTER TABLE `d_news`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `d_notify`
--
ALTER TABLE `d_notify`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `d_roles`
--
ALTER TABLE `d_roles`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `d_support_rep`
--
ALTER TABLE `d_support_rep`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `d_support_tickets`
--
ALTER TABLE `d_support_tickets`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables exportées
--

--
-- AUTO_INCREMENT pour la table `d_boutique_achats`
--
ALTER TABLE `d_boutique_achats`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `d_boutique_articles`
--
ALTER TABLE `d_boutique_articles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `d_boutique_cat`
--
ALTER TABLE `d_boutique_cat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `d_boutique_cmd`
--
ALTER TABLE `d_boutique_cmd`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `d_boutique_dedipass`
--
ALTER TABLE `d_boutique_dedipass`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `d_boutique_paypal`
--
ALTER TABLE `d_boutique_paypal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `d_boutique_paypal_offres`
--
ALTER TABLE `d_boutique_paypal_offres`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `d_boutique_todolist`
--
ALTER TABLE `d_boutique_todolist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `d_contact`
--
ALTER TABLE `d_contact`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT pour la table `d_faq`
--
ALTER TABLE `d_faq`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT pour la table `d_forum`
--
ALTER TABLE `d_forum`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `d_forum_cat`
--
ALTER TABLE `d_forum_cat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT pour la table `d_forum_com`
--
ALTER TABLE `d_forum_com`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `d_forum_sous_cat`
--
ALTER TABLE `d_forum_sous_cat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `d_membre`
--
ALTER TABLE `d_membre`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
--
-- AUTO_INCREMENT pour la table `d_news`
--
ALTER TABLE `d_news`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `d_notify`
--
ALTER TABLE `d_notify`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `d_roles`
--
ALTER TABLE `d_roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT pour la table `d_support_rep`
--
ALTER TABLE `d_support_rep`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `d_support_tickets`
--
ALTER TABLE `d_support_tickets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `d_boutique_todolist` ADD `stopped` BOOLEAN NOT NULL DEFAULT FALSE AFTER `date_done`; 
ALTER TABLE `d_boutique_todolist` ADD `stopped_reason` VARCHAR(255) NULL AFTER `stopped`; 
ALTER TABLE `d_boutique_articles` ADD `archive` BOOLEAN NOT NULL DEFAULT FALSE AFTER `date_ajout`; 
ALTER TABLE `d_boutique_cmd` ADD `is_manual` BOOLEAN NOT NULL DEFAULT FALSE AFTER `id_article`;
ALTER TABLE `d_boutique_cmd` CHANGE `server` `server` VARCHAR(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL;
ALTER TABLE `d_boutique_cmd` CHANGE `server` `server` INT NULL DEFAULT NULL; 
ALTER TABLE `d_boutique_cmd` CHANGE `cmd` `cmd` VARCHAR(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL;
ALTER TABLE `d_boutique_cmd` ADD `archive` BOOLEAN NOT NULL DEFAULT FALSE AFTER `is_manual`; 
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
