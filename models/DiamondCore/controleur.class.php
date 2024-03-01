<?php
/**
 * Controleur - Une class de "base" pour tous les controleurs et l'architecture MVC
 * Cette est abstraite : elle doit donc être accompagnée d'une classe fille, qui elle est spécifique à chaque projet (comme Manager dans DiamondCMS)
 * @author Aldric.L 
 * @copyright Copyright 2016-2017-2020-2022 Aldric L.
 */
abstract class Controleur extends Errors {
    protected $Serveur_Config = array();
    protected $bdd;
    protected $css;
    protected $js = array();
    protected $title;
    protected $paths=array();
    private $bddconf = array();
    private $errors_manager;

      //Le constructeur pour récupérer Serveur_Config
      public function __construct(&$Serveur_Config, $paths){
          $this->paths = $paths;
          $this->Serveur_Config = &$Serveur_Config;
          $this->errors_manager = new Errors($this->paths['config'], $this->paths['logs']);

      }

      //Pour charger un model
      public function loadModel($model){
        //Si le fichier existe...
        if (file_exists($this->paths['models'] . $model . '.php')){
          //... on le charge
          require_once($this->paths['models'] . $model . '.php');
        }
      }

      //Pour charger une vue
      public function loadView($view, $lcss=false, $ltitle=false){
        //Si un CSS est demandé :
        if (isset($lcss) && $lcss != false){
          //on le charge pour le réutiliser dans le header
          $this->css = $this->loadCSS($lcss);
        }

        //Si un titre est demandé :
        if (isset($ltitle) && $ltitle != false){
          //on le sauvegarde pour le réutiliser dans le header
          $this->title = $ltitle;
        }

        //Si le fichier existe ...
        if (file_exists($this->paths['views'] . $view . '.php')){
          //... on charge les vues "génériques" : header et footer et on charge la vue passée en paramètre
          if (!(array_key_exists("DIAMOND_CACHE_PROCESSING", $GLOBALS) && $GLOBALS['DIAMOND_CACHE_PROCESSING']))
            require($this->paths['views'] . 'include/header.inc');
          require($this->paths['views'] . $view . '.php');
          if (!(array_key_exists("DIAMOND_CACHE_PROCESSING", $GLOBALS) && $GLOBALS['DIAMOND_CACHE_PROCESSING']))
            require($this->paths['views'] . 'include/footer.inc');
        }

      }

       //Pour charger un fichier JS particulier
       public function loadJS($js){
        //Si le fichier existe ...
        if (file_exists(ROOT . $this->paths['js'] . $js . '.js')){
          array_push($this->js, $this->paths['link'] . $this->paths['js'] . $js . '.js');
        }

      }

      //Pour charger une vue admin
      public function loadViewAdmin($view, $lcss=false, $ltitle=false){
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
        if (file_exists($this->paths['views'] . $view . '.php')){
          //... on charge les vues "génériques" : header et footer et on charge la vue passée en paramètre
          if (!(array_key_exists("DIAMOND_CACHE_PROCESSING", $GLOBALS) && $GLOBALS['DIAMOND_CACHE_PROCESSING']))
            require($this->paths['views'] . 'include/header_admin.inc');
          require($this->paths['views'] . $view . '.php');
          if (!(array_key_exists("DIAMOND_CACHE_PROCESSING", $GLOBALS) && $GLOBALS['DIAMOND_CACHE_PROCESSING']))
            require($this->paths['views'] . 'include/footer_admin.inc');
        }

      }

      //Pour ce connecter à la base de données
      public function bddConnexion(){
        if (empty($this->bddconf)){
          $this->bddconf = parse_ini_file($this->paths['config'] . "bdd.ini", true);
        }
        //On crée une connexion PDO
        $this->bdd = new BDD($this->bddconf);
        return $this->bdd->getPDO();
      }

      //Pour récuperer une instance de la class BDD (pour par exemple acceder à la config)
      public function getBDD(){
        return $this->bdd;
      }

      public function loadCSS($css, $admin=false){
          if ($admin){
            //Si le fichier CSS existe ...
            if (file_exists($this->paths['views'] . 'css/admin/' . $css . '.css')){
              //On le retourne pour le réutiliser dans le header
              return $css;
            }
          }else {
            //Si le fichier CSS existe ...
            if (file_exists($this->paths['views'] . 'css/' . $css . '.css')){
              //On le retourne pour le réutiliser dans le header
              return $css;
            }
          }
          
      }

     public function log($error_code, $user=null) {
          return $this->errors_manager->log($error_code, $user);
     }

    public function addError($error_code, $user=null){
      return $this->errors_manager->addError($error_code, $user);
    }

    public function getContentError($error_code){
      return $this->errors_manager->getContentError($error_code);
    }

    public function getError($error_code){
      return $this->errors_manager->getError($error_code);
    }

    public function getErrors(){
      return $this->errors_manager->getErrors();
    }

    public function purgeErrors(){
      return $this->errors_manager->purgeErrors();
    }

    public function getErrorsInLog(){
      return $this->errors_manager->getErrorsInLog();
    }

    public function extendErrorsKnown($errors){
      return $this->errors_manager->extendErrorsKnown($errors);
    }

    public function getErrorHandler(){
      return $this->errors_manager;
    }

    public function getPaths(){
      return $this->paths;
    }

    public function customlog($string){ 
      file_put_contents($this->paths['logs'] . 'custom_log.log',  date("j/m/y à H:i:s") . " - Envoyé sur la page : ". str_replace(WEBROOT, '', $_SERVER['REQUEST_URI']) . ' - ' . $string . " \r\n".file_get_contents($this->paths['logs']. "custom_log.log"));
    }
  }
