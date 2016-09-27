<?php
/**
 * Controleur - Une class de "base" pour tous les controleurs
 * @author Aldric.L
 * @copyright Copyright 2016-2017 Aldric L.
 */
//Cette classe sert de base à tous les controleurs
  class Controleur{
    private $Serveur_Config = array();
    public $css;
    public $title;

      //Le constructeur pour récupérer Serveur_Config
      public function __construct($Serveur_Config){
          $this->Serveur_Config = $Serveur_Config;
      }

      //Pour charger un model
      function loadModel($model){
        //Si le fichier existe...
        if (file_exists(ROOT . 'models/' . $model . '.php')){
          //... on le charge
          require_once(ROOT . 'models/' . $model . '.php');
        }
      }

      //Pour charger une vue
      function loadView($view, $lcss=false, $ltitle=false){
        //Si un CSS est demandé :
        if (isset($lcss) && $lcss != false){
          //on le charge pour le réutiliser dans le header
          $this->css = $this->loadCSS($lcss);
        }

        //Si un CSS est demandé :
        if (isset($ltitle) && $ltitle != false){
          //on le sauvegarde pour le réutiliser dans le header
          $this->title = $ltitle;
        }

      //Si le fichier existe ...
        if (file_exists(ROOT . '/views/themes/'. $this->Serveur_Config['theme'] . '/' . $view . '.php')){
          //... on charge les vues "génériques" : header et footer et on charge la vue passée en paramètre
          require(ROOT . 'views/themes/' . $this->Serveur_Config['theme'] . '/include/header.php');
          require(ROOT . '/views/themes/'. $this->Serveur_Config['theme'] . '/' . $view . '.php');
          require(ROOT . 'views/themes/' . $this->Serveur_Config['theme'] . '/include/footer.php');
          //require_once(ROOT.'controllers/config_server.php');
        }

      }

      //Pour ce connecter à la base de donnée
      function bddConnexion(){
        //On charge les identifiants d'un fichier config
        $reader = new Load(ROOT . 'models/config_YAML/file/bdd.yml');
        require_once(ROOT . 'models/bdd_connexion.php');
        //On crée une connexion PDO
        $bdd = new BDD($reader->GetContentYml());
        return $bdd->getPDO();
      }

      /**
      * @access private
      **/
      function loadCSS($css){
          //Si le fichier CSS existe ...
          if (file_exists(ROOT . '/views/themes/'. $this->Serveur_Config['theme'] . '/CSS/' . $css . '.css')){
            //On le retourne pour le réutiliser dans le header
            return $css;
          }
      }

  }
