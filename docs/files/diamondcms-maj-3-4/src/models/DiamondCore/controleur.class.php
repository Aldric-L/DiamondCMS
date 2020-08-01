<?php
/**
 * Controleur - Une class de "base" pour tous les controleurs et l'architecture MVC
 * Cette est abstraite : elle doit donc être accompagnée d'une classe fille, qui elle est spécifique à chaque projet (comme Manager dans DiamondCMS)
 * @author Aldric.L 
 * @copyright Copyright 2016-2017-2020 Aldric L.
 */
abstract class Controleur{
    protected $Serveur_Config = array();
    protected $content_errors_file;
    protected $bdd;
    protected $errors = array();
    protected $css;
    protected $js = array();
    protected $title;
    protected $paths=array();
    private $bddconf = array();

      //Le constructeur pour récupérer Serveur_Config
      public function __construct(&$Serveur_Config, $paths){
          $this->paths = $paths;
          $this->Serveur_Config = &$Serveur_Config;
          //On charge le fichier des erreurs classées par code
          $this->content_errors_file = parse_ini_file($this->paths['config'] . "errors.ini", true);

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
          require($this->paths['views'] . 'include/header.inc');
          require($this->paths['views'] . $view . '.php');
          require($this->paths['views'] . 'include/footer.inc');
        }

      }

       //Pour charger un fichier JS particulier
       public function loadJS($js){
        //Si le fichier existe ...
        if (file_exists($this->paths['js'] . $js . '.js')){
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
          require($this->paths['views'] . 'include/header_admin.inc');
          require($this->paths['views'] . $view . '.php');
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

      /**
      * @access private
      **/
      private function loadCSS($css, $admin=false){
          if ($admin){
            //Si le fichier CSS existe ...
            if (file_exists($this->paths['views'] . 'CSS/admin/' . $css . '.css')){
              //On le retourne pour le réutiliser dans le header
              return $css;
            }
          }else {
            //Si le fichier CSS existe ...
            if (file_exists($this->paths['views'] . 'CSS/' . $css . '.css')){
              //On le retourne pour le réutiliser dans le header
              return $css;
            }
          }
          
      }

      public function log($error_code) {
          //Environ une fois sur 60, on purge le log
          if (rand ( 1 , 60 ) == 42 ){
              $tabfile = file($this->paths['logs'] . 'errors.log');
              $txt = "";
              // Boucle pour ne conserver que les "n" premières ligne(s)
              for ($i = 1; $i <= 100; $i++) {
                $txt .=  $tabfile[$i];
              }
              $open=fopen($this->paths['logs'] . 'errors.log',"w+" );
              fwrite($open,$txt);
              fclose($open);
          }
          if (!empty($_SESSION['pseudo'])){
      	    file_put_contents($this->paths['logs'] . 'errors.log', $error_code . " - " . date("j/m/y à H:i:s") .  " - Envoyé à l'utilisateur ". $_SESSION['pseudo'] . " sur la page : ". str_replace(WEBROOT, '', $_SERVER['REQUEST_URI']) . ") \r\n".file_get_contents("logs/errors.log"));
          }else {
    	       file_put_contents($this->paths['logs'] . 'errors.log', $error_code . " - " . date("j/m/y à H:i:s") . " - Envoyé à un utilisateur anonyme sur la page : ". str_replace(WEBROOT, '', $_SERVER['REQUEST_URI']) . ") \r\n".file_get_contents("logs/errors.log"));
          }
    	 }

      public function addError($code_error){
        if (array_key_exists($code_error, $this->content_errors_file)){
          array_push($this->errors, $this->content_errors_file[$code_error] . "</span> <span>(Code d'erreur " . $code_error . ".)");
          $this->log($code_error);
          return true;
        }else {
          array_push($this->errors, "Une erreur inconnue est survenue.</span> <span>(Code d'erreur 121.)");
          $this->log("121 (Erreur : " . $code_error . ")");
          return false;
        }
      }

      public function getContentError($code_error){
        if (array_key_exists($code_error, $this->content_errors_file)){
          return $this->content_errors_file[$code_error];
        }else {
          return $this->content_errors_file["121"];
        }
      }

      public function getErrors(){
        if (empty($this->errors)){ return null; } return $this->errors;
      }

      public function purgeErrors(){
        unset($this->errors);
      }
  }
