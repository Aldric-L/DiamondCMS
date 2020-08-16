<?php 
/**
 * DiamondCore - Noyau de DiamondCMS (boite à outils PHP)
 * Ces classes ont pour but de créer une architecture MVC rapidement et efficacement, de manipuler intelligeament des fichiers, et de dialoguer facilement avec la base de données.
 * 
 * @version 3.0
 * @author Aldric L.
 * @copyright 2020
 */

//On charge le model des models pour faciliter les requettes SQL-PDO
require_once('core.php');
require_once('files.php');
require_once('db.class.php');

//Attention, spécialement pour le projet DiamondCMS, on utilise une classe fille de DB, qu'on doit appeller tout de suite
require_once(ROOT. 'models/bdd_connexion.php');

//Récupération du fichier source "Controleur" qui sera la base de la partie contollers de l'architecture MVC
//ATTENTION la class contrôleur a besoin des fonctions du modèle core, files et bddconnexion et doit donc être inclu en dernier.
require_once(ROOT.'models/DiamondCore/controleur.class.php');
