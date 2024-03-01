<?php 

/**
 * Projet PageBuilder - Version Admin - Implémentation dans le thème Default
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
 * Le projet est sous la même licence que celle du Thème DiamondCMS
 * @author Aldric L.
 * @copyright 2023
 * @version 1.0
 * @since 2.0
 */

require_once("DefaultAdminAlert.class.php");
require_once("DefaultAdminAPIButton.class.php");
require_once("DefaultAdminAreaChart.class.php");
require_once("DefaultAdminBuilder.class.php");
require_once("DefaultAdminButton.class.php");
require_once("DefaultAdminCard.class.php");
require_once("DefaultAdminDropdownButton.class.php");
require_once("DefaultAdminForm.class.php");
require_once("DefaultAdminList.class.php");
require_once("DefaultAdminModal.class.php");
require_once("DefaultAdminModalButton.class.php");
require_once("DefaultAdminPanel.class.php");
require_once("DefaultAdminPieChart.class.php");
require_once("DefaultAdminTable.class.php");