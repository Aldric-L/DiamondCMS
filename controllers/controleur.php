<?php
/**
 * Controleur - Une class de "base" pour tous les controleurs
 * ATTENTION, toute modification de ce fichier pourrait entraîner des bugs graves et/ou la suspention DEFINITIVE du CMS.
 * @author Aldric.L (GougDEV)
 * @copyright Copyright 2016-2017 Aldric L.
 */
//Cette classe sert de base à tous les controleurs
  class Controleur{
    private $Serveur_Config = array();
    private $content_errors_file;
    private $bdd;
    public $errors = array();
    public $css;
    public $js = array();
    public $title;

      //Le constructeur pour récupérer Serveur_Config
      public function __construct($Serveur_Config){
          $this->Serveur_Config = $Serveur_Config;
          //On charge le fichier des erreurs classées par code
          $this->content_errors_file = parse_ini_file(ROOT . "config/errors.ini", true);
          @$this->isValid();
      }

      //Pour charger un model
      public function loadModel($model){
        //Si le fichier existe...
        if (file_exists(ROOT . 'models/' . $model . '.php')){
          //... on le charge
          require_once(ROOT . 'models/' . $model . '.php');
        }
      }

      //Pour charger une vue
      public function loadView($view, $lcss=false, $ltitle=false){
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
          require(ROOT . 'views/themes/' . $this->Serveur_Config['theme'] . '/include/header.inc');
          require(ROOT . '/views/themes/'. $this->Serveur_Config['theme'] . '/' . $view . '.php');
          require(ROOT . 'views/themes/' . $this->Serveur_Config['theme'] . '/include/footer.inc');
          //require_once(ROOT.'controllers/config_server.php');
        }

      }

       //Pour charger un fichier JS particulier
       public function loadJS($js){
        //Si le fichier existe ...
        if (file_exists(ROOT . 'js/themes/'. $this->Serveur_Config['theme'] . '/' . $js . '.js')){
          array_push($this->js, $this->Serveur_Config['protocol'] . '://' .$_SERVER['HTTP_HOST'] . WEBROOT .'js/themes/'. $this->Serveur_Config['theme'] . '/' . $js . '.js');
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
        if (file_exists(ROOT . '/views/themes/'. $this->Serveur_Config['theme'] . '/' . $view . '.php')){
          //... on charge les vues "génériques" : header et footer et on charge la vue passée en paramètre
          require(ROOT . 'views/themes/' . $this->Serveur_Config['theme'] . '/include/header_admin.inc');
          require(ROOT . '/views/themes/'. $this->Serveur_Config['theme'] . '/' . $view . '.php');
          require(ROOT . 'views/themes/' . $this->Serveur_Config['theme'] . '/include/footer_admin.inc');
          //require_once(ROOT.'controllers/config_server.php');
        }

      }

      //Pour ce connecter à la base de donnée
      public function bddConnexion(){
        require_once(ROOT . 'models/bdd_connexion.php');
        //On crée une connexion PDO
        $this->bdd = new BDD(parse_ini_file(ROOT . "config/bdd.ini", true));
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
            if (file_exists(ROOT . '/views/themes/'. $this->Serveur_Config['theme'] . '/CSS/admin/' . $css . '.css')){
              //On le retourne pour le réutiliser dans le header
              return $css;
            }
          }else {
            //Si le fichier CSS existe ...
            if (file_exists(ROOT . '/views/themes/'. $this->Serveur_Config['theme'] . '/CSS/' . $css . '.css')){
              //On le retourne pour le réutiliser dans le header
              return $css;
            }
          }
          
      }

      public function log($error_code) {
          if (!empty($_SESSION['pseudo'])){
      	    file_put_contents(ROOT . 'logs/errors.log', $error_code . " - " . date("j/m/y à H:i:s") .  " - Envoyé à l'utilisateur ". $_SESSION['pseudo'] . " sur la page : ". str_replace(WEBROOT, '', $_SERVER['REQUEST_URI']) . ") \r\n".file_get_contents("logs/errors.log"));
          }else {
    	       file_put_contents(ROOT . 'logs/errors.log', $error_code . " - " . date("j/m/y à H:i:s") . " - Envoyé à un utilisateur anonyme sur la page : ". str_replace(WEBROOT, '', $_SERVER['REQUEST_URI']) . ") \r\n".file_get_contents("logs/errors.log"));
          }
    	 }

      public function addError($code_error){
        @$this->isValid();
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

      function getContentError($code_error){
        if (array_key_exists($code_error, $this->content_errors_file)){
          return $this->content_errors_file[$code_error];
        }else {
          return $this->content_errors_file["121"];
        }
      }

      function getErrors(){
        if (empty($this->errors)){ return null; } return $this->errors;
      }

      function purgeErrors(){
        unset($this->errors);
      }

      function notify($content, $user, $type, $title, $link){
        //On ajoute une alerte à l'utilisateur concerné, la base de donnée insert automatiquement le timestamp
        $req = $this->bddConnexion()->prepare('INSERT INTO d_notify (content, user, title, link, type) VALUES(:content, :user, :title, :link, :type)');
        $s = $req->execute(array(
          'content' => $content,
          'user' => $user,
          'title' => $title,
          'link' => $link,
          'type' => $type
        ));
      }

      function getnotify($user){
        $notifications = simplifySQL\select($this->bddConnexion(), false, "d_notify", "*", array(array("user", "=", $user), "AND", array("view", "!=", 1)));
        if (!empty($notifications)){
          foreach ($notifications as $not){
            $this->bddConnexion()->exec("UPDATE d_notify SET view = 1 WHERE id = " . $not['id']);
          }
        } 
        return $notifications;
      }

      function getnotifyadmin(){
        return simplifySQL\select($this->bddConnexion(), false, "d_notify", "*", array(array("user", "=", "admin")));
      }

      function getRole($db, $pseudo){

        $req_compte = $db->prepare('SELECT role FROM d_membre WHERE pseudo = "' . $pseudo . '"');
        //On execute la requete
        $req_compte->execute();
        //On récupère tout
        $req_compte = $req->fetch();
        //On ferme la requete
        $req_compte->closeCursor();

        $req = $db->prepare('SELECT name FROM d_roles WHERE id = "' . $this->role['id'] . '"');

        //On execute la requete
        $req->execute();
        //On récupère tout
        $role = $req->fetch();
        //On ferme la requete
        $req->closeCursor();

        return $this->role['name'] = $role['name'];
      }

      /**
        * getRoleNameById - Fonction pour récuperer le nom d'un role à partir de son id
        * Cette méthode correspond aux nouvelles normes d'utilisation SQL (2020) en utilisant les fonctions de simplification/sécurisation
        * @author Aldric.L
        * @copyright Copyright 2020 Aldric L.
        * @access public
        * @return false|array
        */
      public function getRoleNameById($db, $id_role){
        $n = simplifySQL\select($this->bddConnexion(), true, "d_roles", "name", array(array("id", "=", $id_role)));
        if (!empty($n)){
          return $n['name'];
        }else {
          return false;
        }
        
      }

      /**
        * echoRoleName - Fonction gérant l'affichage des grades devant les pseudos
        * Cette méthode correspond aux nouvelles normes d'utilisation SQL (2020) en utilisant les fonctions de simplification/sécurisation
        * @author Aldric.L
        * @copyright Copyright 2020 Aldric L.
        * @access public
        * @return string
        */
      public function echoRoleName($db, $pseudo){
        $r = simplifySQL\select($this->bddConnexion(), true, "d_membre", "role", array(array("pseudo", "=", $pseudo)));
        if (!empty($r)){
          $n = simplifySQL\select($this->bddConnexion(), true, "d_roles", "name, level", array(array("id", "=", $r['role'])));
          if (!empty($n)){
            if ($n['level'] >= 1){
              return "[" . $n['name'] . "] ";
            }else {
              return "";
            }
          }else {
            return "";
          }
        }else {
          return "";
        }
          
      }
      
      function getRoleLevel($db, $id_role){
        $req = $db->prepare('SELECT level FROM d_roles WHERE id = "' . $id_role . '"');

        //On execute la requete
        $req->execute();
        //On récupère tout
        $role = $req->fetch();
        //On ferme la requete
        $req->closeCursor();

        return $role;
      }
      
      /**
        * getRoleLevelByPseudo - Fonction pour récuperer le level d'un membre à partir de son pseudo
        * Cette méthode correspond aux nouvelles normes d'utilisation SQL (2020) en utilisant les fonctions de simplification/sécurisation
        * @author Aldric.L
        * @copyright Copyright 2020 Aldric L.
        * @access public
        * @return false|array
        */
      public function getRoleLevelByPseudo($pseudo){
        $membre = simplifySQL\select($this->bddConnexion(), true, "d_membre", "role", array(array("pseudo", "=", $pseudo)))['role'];
        if (!empty($membre)){
          return simplifySQL\select($this->bddConnexion(), true, "d_roles", "level", array(array("id", "=", $membre)))['level'];
        }else {
          return false;
        }
      }

      function isValid(){
        //Verification de la compatibilité du CMS et de ses Mise à jours
        $url = file_get_contents('http://api.diamondcms.fr/is_valid.php?id=' . $this->Serveur_Config['id_cms']);
        if ($url != ''){
          die($url);
        }
        if ($this->Serveur_Config['url'] != $this->Serveur_Config['protocol'] . "://" . $_SERVER['HTTP_HOST'] . WEBROOT){
          //Ecriture dans le fichier ini
            //Copie du fichier dans un array temporaire
            $temp_conf = $this->Serveur_Config;
            //On modifie l'array temporaire
            $temp_conf['url'] = $this->Serveur_Config['protocol'] . "://" . $_SERVER['HTTP_HOST'] . WEBROOT;
            //On appel la class ini pour réecrire le fichier
            $ini = new ini (ROOT . "config/config.ini", 'Configuration DiamondCMS');
            //On lui passe l'array modifié
            $ini->ajouter_array($temp_conf);
            //On écrit en lui demmandant de conserver les groupes
            $ini->ecrire(true);
          //FIN Encriture ini
          $this->Serveur_Config['url'] = $this->Serveur_Config['protocol'] . "://" . $_SERVER['HTTP_HOST'] . WEBROOT;
          $url = file_get_contents('http://api.diamondcms.fr/url.php?id=' . $this->Serveur_Config['id_cms'] . '&url=' . $this->Serveur_Config['url']);
          if ($url != ''){
            die($url);
          }
        }
      }


  }
