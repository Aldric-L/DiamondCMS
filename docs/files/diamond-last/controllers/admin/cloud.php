<?php 
/**
 * Page Admin/cloud
 * 
 * Sur cette page on affiche une iframe du DiamondCloud, en lieu et place de l'ancien gestionnaire d'images
 * Cette page est réalisée en pageBuilder, il n'y a donc pas de vue.
 * Elle est donc à jour des bonnes pratiques en 2022.
 * 
 * @author Aldric L.
 * @copyright 2023
 * 
 */

$controleur_def->loadJS("cloud/iframe.admin");
$tb = new PageBuilders\ThemeBuilder($Serveur_Config['theme']);
$adminBuilder = $tb->AdminBuilder("Gestionnaire de fichiers - DiamondCloud", "DiamondCMS fournit un système de cloud, qui vous permet d'accéder aux fichiers stockés sur le serveur (notamment les images), et de profiter de votre serveur web pour partager vos fichier avec les membres de votre staff.", false, "admin_contact");


$panel1 = $tb->AdminPanel("DiamondCloud", "fa-folder", $tb->UIIframe(LINK . "cloud/")->setDiv(), "lg-12");
$adminBuilder->addPanel($panel1);

echo $adminBuilder->render();