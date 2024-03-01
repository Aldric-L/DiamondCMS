<?php 

/**
 * Projet PageBuilder - Version Admin
 * 
 * L'objectif du projet est de créer une library PHP qui génère du HTML propre et fiable.
 * Cette library est encore en développement mais semble fonctionnelle et pratique.
 * Elle est destinée à fonctionner avec d'autres libs JS implémentées dans DiamondCMS comme DIC
 * 
 * Le projet est à terme de l'étendre aux pages non administrateur.
 * Le fonctionnement par thème est assuré par l'objet ThèmeBuilder qui instancie les classes en fonction du thème.
 * En effet, ici, les classes sont toutes abstraites, et les thèmes écrivent des classes filles de celles ci selon le format <Nom du thème><Nom de la classe>
 * De ce fait, le système de thème est transparent pour l'utilisateur.
 * 
 * Le projet est sous la même licence que celle du Noyau DiamondCMS
 * @author Aldric L.
 * @copyright 2023
 * @version 1.0
 * @since 2.0
 */


require_once("renderUIComponents.php");
require_once("ThemeBuilder.class.php");
require_once("AdminBuilder.class.php");
require_once("AvailableIf.class.php");
require_once("AdminAlert.class.php");
require_once("AdminButton.class.php");
require_once("AdminAPIButton.class.php");
require_once("AdminDropdownButton.class.php");
require_once("AdminAreaChart.class.php");
require_once("AdminCard.class.php");
require_once("AdminForm.class.php");
require_once("AdminList.class.php");
require_once("AdminModal.class.php");
require_once("AdminModalButton.class.php");
require_once("AdminPanel.class.php");
require_once("AdminPieChart.class.php");
require_once("AdminTable.class.php");