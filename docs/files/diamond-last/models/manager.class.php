<?php 
//PATH theme =ROOT . '/views/themes/'. $this->Serveur_Config['theme'] . '/'
final class Manager extends Controleur {
    use NotifyCenter;  
    
    /**
     * $adminPages : Cet attribut stocke les pages à rajouter notamment pour les addons dans l'interface admin
     * 
     * Structure :
     * $this->adminPages = array(
          array("fas fa-fw fa-tachometer-alt", "Tableau de bord", LINK . "admin/", $level),
          array("fas fa-fw fa-cog", "Configuration générale", array(
            array("", "Général et serveurs", LINK . "admin/config/" )
          ), $level),
        );
     * 
     */

    private array $adminPages;
    private array $adminIframes;
    private array $loadedAddons;

    private array $available_modules = array();
    protected $registered_modules_manager;

    protected array $attrSerializable = array("is_iframer", "css", "js", "title");

    public bool $is_iframer;

    public function __construct(&$Serveur_Config, $view_path){
        parent::__construct($Serveur_Config, $view_path);
        $this->adminPages = array();
        $this->adminIframes = array();
        $this->loadedAddons = array();
        $this->is_iframer = false;
        $this->registered_modules_manager = null;
    }

    public function getCSS(){
      return $this->css;
    }

    public function getTitle(){
      return $this->title;
    }

    public function getJS(){
      return $this->js;
    }

    /**
       * getPages - Fonction pour récuperer les pages enregistrées
       * @author Aldric.L
       * @copyright Copyright 2020 Aldric L.
       * @access public
       * @return array
       */
      public function getPages(){
        return simplifySQL\select($this->bddConnexion(), false, "d_pages", "*");
      }

      /**
       * getPage - Fonction pour récuperer une page enregistrée par son id
       * @author Aldric.L
       * @copyright Copyright 2020 Aldric L.
       * @access public
       * @param int id : identifiant de la page à chercher
       * @return array
       */
      public function getPage($id){
        return simplifySQL\select($this->bddConnexion(), true, "d_pages", "*", array(array("id", "=", $id)));
      }

      /**
       * getFooterPages - Fonction pour récuperer les liens des pages enregistrées pour le footer
       * @author Aldric.L
       * @copyright Copyright 2020 Aldric L.
       * @access public
       * @return array
       */
      public function getFooterPages(){
        return simplifySQL\select($this->bddConnexion(), false, "d_footer", "*", array(array("disabled", "=", 0)), "pos");
      }

      /**
       * getHeaderPages - Fonction pour récuperer les menus déroulants du header et les pages qu'ils contiennent
       * @author Aldric.L
       * @copyright Copyright 2020 Aldric L.
       * @access public
       * @return array|bool 
       */
      public function getHeaderPages(){
        //On récupère les menus déroulant du header
        $header_md = simplifySQL\select($this->bddConnexion(), false, "d_header_menus", "*", false);
        //Maintenant on travaille menu par menu
        foreach ($header_md as $k =>$md){
            //On récupère la référence des pages enregistrées
            $header_md[$k]['pages'] = simplifySQL\select($this->bddConnexion(), false, "d_header_menus_pages", "*", array(array("id_menu", "=", $md['id'])), "pos");
            foreach ($header_md[$k]['pages'] as $key => $hp) {
                // On convertit les références
                $header_md[$k]['pages'][$key]['header_page'] = simplifySQL\select($this->bddConnexion(), true, "d_header", "*", array(array("id", "=", $hp['id_page'])));
                //Si la requête aboutit
                if ($header_md[$k]['pages'][$key]['header_page'] != false){
                    $header_md[$k]['pages'][$key]['id_page'] = $header_md[$k]['pages'][$key]['header_page']['id_page'];
                    $header_md[$k]['pages'][$key]['titre'] = $header_md[$k]['pages'][$key]['header_page']['titre'];
                    $header_md[$k]['pages'][$key]['link'] = $header_md[$k]['pages'][$key]['header_page']['link'];
                    //Si l'id de la page n'est pas null, c'est que c'est encore une réference vers une page de d_page
                    if ($header_md[$k]['pages'][$key]['id_page'] != NULL){
                        //On va chercher dans la table d_pages + d'infos comme le titre de la page (en effet, dans d_header_menus_pages, on a que des réferences vers les autres pages)
                        $header_md[$k]['pages'][$key]['titre'] = simplifySQL\select($this->bddConnexion(), true, "d_pages", "name, name_raw", array(array("id", "=", $header_md[$k]['pages'][$key]['id_page'])));
                        //Si on trouve bien on complète les champs
                        if (isset($header_md[$k]['pages'][$key]['titre']['name'])){
                            $nr = $header_md[$k]['pages'][$key]['titre']['name_raw'];
                            $header_md[$k]['pages'][$key]['titre'] = $header_md[$k]['pages'][$key]['titre']['name'];
                            $header_md[$k]['pages'][$key]['link'] = $nr;
                        //Sinon on lève une erreur puisqu'il n'est pas normal de ne pas trouver une page dont on a la réference => la BDD est alors corrompue
                        }else {
                            $this->addError("343b");
                            return array();
                        }
                    }
                }else {
                    $this->addError("343b");
                    return array();
                }
                
            }
        }
        return $header_md;
      }


      /**
       * addPage - Fonction pour créer une page (ou un lien vers un controller qui pourra être ajouté au header/footer)
       * 
       * Cette fonction a été séparée du controller admin chargé d'assurer la création des pages pour permettre aux créateurs d'addons de l'utiliser
       * Pour les créateurs d'addons : NE TENTEZ SURTOUT PAS D'AJOUTER UNE PAGE SANS CETTE FONCTION : elle assure toutes les vérifications et permet de
       * bien ajouter la page dans les bonnes tables (notamment pour le footer et le header).
       * 
       * @author Aldric.L
       * @copyright Copyright 2020 Aldric L.
       * @access public
       * @param boolean $is_link : Ce paramètre permet d'indiquer si on souhaite créer une page dans laquelle l'administrateur peut écrire, ou si il s'agit simplement d'un lien vers un controller
       * @param string $name_raw : Ce string est "l'adresse" de la page, celle qui s'affiche dans l'URL. Il doit être UNIQUE. S'il s'agit d'un lien vers un controller, prévoir des slashs.
       * @param string $name : Ce string est le titre qui sera affiché
       * @param string $fa_icon : (optionnel) Ce string est le nom de l'icone font-awesome associée
       * @return bool 
       */
      public function addPage($is_link, $name_raw, $name, $fa_icon=null){
        if (!$is_link){
          $name_raw = mb_strtolower($name_raw);
          $name_raw = clearString($name_raw, true, true);
        }
        $check_exists = simplifySQL\select($this->bddConnexion(), false, "d_header", "titre, link", array(array("titre", "=", $name), "OR", array("link", "=", $name_raw)));
        if (!empty($check_exists) && is_array($check_exists)){
            $this->addError("351");
            return false;
        }

        $check_exists = simplifySQL\select($this->bddConnexion(), false, "d_footer", "titre, link", array(array("titre", "=", $name), "OR", array("link", "=", $name_raw)));
        if (!empty($check_exists) && $check_exists != false && $check_exists != true && is_array($check_exists)){
            $this->addError("351");
            return false;
        }

        if ($is_link){

          $check_exists = simplifySQL\select($this->bddConnexion(), false, "d_pages", "name, name_raw", array(array("name", "=", $name), "OR", array("name_raw", "=", $name_raw)));
          if (!empty($check_exists) && $check_exists != false && $check_exists != true && is_array($check_exists)){
              $this->addError("351");
              return false;
          }

          if (!simplifySQL\insert($this->bddConnexion(), "d_header", array('titre', 'link'), array($name, $name_raw))){
            $this->addError("342c");
            return false;
          }
          if (!simplifySQL\insert($this->bddConnexion(), "d_footer", array('titre', 'link', 'disabled'), array($name, $name_raw, 1))){
            $this->addError("342c");
            return false;
          }
          return true;
        }else {
          
          if (file_exists(ROOT . 'config/' . $name_raw . '.ftxt')){
              $name_raw = uniqid() . '-' . $name_raw;
          }

          if ($fa_icon != null){
              if (!@simplifySQL\insert($this->bddConnexion(), "d_pages", array('name', 'name_raw', 'file_name', 'fa_icon'), array($name, $name_raw, $name_raw, $fa_icon))){
                  $this->addError("342c");
                  return false;
              }
          }else {
              if (!@simplifySQL\insert($this->bddConnexion(), "d_pages", array('name', 'name_raw', 'file_name'), array($name, $name_raw, $name_raw))){
                  $this->addError("342c");
                  return false;
              }
          }
          //On récupère l'id de la page qu'on vient d'insérer
          $id = simplifySQL\select($this->bddConnexion(), true, "d_pages", "id", array(array("name_raw", "=", $name_raw)));
          if ($id != false && isset($id['id'])){
            $id = $id['id'];
            if (!@simplifySQL\insert($this->bddConnexion(), "d_footer", array('id_page', 'disabled'), array($id, 1))){
              $this->addError("342c");
              return false;
            }
            if (!@simplifySQL\insert($this->bddConnexion(), "d_header", array('id_page'), array($id))){
              $this->addError("342c");
              return false;
            }
          }else {
            $this->addError("343b");
            return false;
          }
          $fp = fopen(ROOT . 'config/' . $name_raw . '.ftxt', 'w');
          fputs ($fp, "<p>Cette page est vide. Ajoutez du contenu dans votre interface d'administration !</p>");
          fclose ($fp);
          return true;
        }
      }

      /**
       * delPage - Fonction pour supprimer une page (ou un lien vers un controller qui aurait été ajouté au header/footer)
       * 
       * Cette fonction a été séparée du controller admin chargé d'assurer la suppression des pages pour permettre aux créateurs d'addons de l'utiliser.
       * Pour les créateurs d'addons : NE TENTEZ SURTOUT PAS DE SUPPRIMER UNE PAGE SANS CETTE FONCTION : elle assure toutes les vérifications et permet de
       * bien supprimer la page dans toutes les tables (et ainsi ne pas faire des conflits de BDD qui rendraient le CMS INUTILISABLE)
       * 
       * @author Aldric.L
       * @copyright Copyright 2020 Aldric L.
       * @access public
       * @param boolean $is_link : Ce paramètre permet d'indiquer si on souhaite agir sur une page dans laquelle l'administrateur peut écrire, ou si il s'agit simplement d'un lien vers un controller
       * @param int|string $identificateur : Si il s'agit d'un lien, l'identificateur est l'adresse vers laquelle il pointe, sinon, l'identificateur est le champ "name_raw" dans la table d_pages qui permet de retrouver le fichier
       * @return bool 
       */
      public function delPage($is_link, $identificateur){
        if ($is_link){
          $header_pages = simplifySQL\select($this->bddConnexion(), false, "d_header", "id, link", array(array("link", "=", $identificateur)));
          //var_dump($header_pages);
          if (!empty($header_pages) && $header_pages != false){
            foreach ($header_pages as $hp){
              if (!simplifySQL\delete($this->bddConnexion(), "d_header_menus_pages", array(array("id_page", "=", $hp['id'])))) {
                  return false;
              }
            }
          }else {
            return false;
          }

          if (!simplifySQL\delete($this->bddConnexion(), "d_footer", array(array("link", "=", $identificateur)))
          || !simplifySQL\delete($this->bddConnexion(), "d_header", array(array("link", "=", $identificateur)))
          || !simplifySQL\delete($this->bddConnexion(), "d_header_menus", array(array("link", "=", $identificateur), "AND", array("is_menu", "=", 0)))){
            return false;
          }

          return true;
        }else {
          //On récupère l'id de la page qu'on veut supprimer
          $id = simplifySQL\select($this->bddConnexion(), true, "d_pages", "id", array(array("name_raw", "=", $identificateur)));
          if ($id != false && isset($id['id'])){
            $id = $id['id'];
          }else {
            return false;
          }

          $header_pages = simplifySQL\select($this->bddConnexion(), false, "d_header", "id, id_page", array(array("id_page", "=", $id)));
          if (!empty($header_pages) && $header_pages != false){
            foreach ($header_pages as $hp){
              if (!simplifySQL\delete($this->bddConnexion(), "d_header_menus_pages", array(array("id_page", "=", $hp['id'])))) {
                  return false;
              }
            }
          }else {
            return false;
          }

          if (!simplifySQL\delete($this->bddConnexion(), "d_footer", array(array("id_page", "=", $id)))
          || !simplifySQL\delete($this->bddConnexion(), "d_header", array(array("id_page", "=", $id)))
          || !simplifySQL\delete($this->bddConnexion(), "d_pages", array(array("id", "=", $id)))){
            return false;
          }

          if (@unlink(ROOT  . 'config/' . $identificateur . '.ftxt')){
            die ('Success');
          }

          return true;
        }
      }

      /**
       * getUnseenContacts - Méthode pour obtenir les demandes de contacts non vues (notamment pour le header admin)
       * 
       * @author Aldric.L
       * @copyright Copyright 2023 Aldric L.
       * @access public
       * @return array 
       */
      public function getUnseenContacts(){
        $tasks = simplifySQL\select($this->bddConnexion(), false, "d_contact", "*", array(array("seen", "=", 0), "OR", array("seen", "=", "null")));
        return $tasks;
      }

      /**
       * getManualTasks - Méthode pour obtenir les tâches manuelles qui n'ont pas encore été réalisées (fonction boutique)
       * 
       * @author Aldric.L
       * @copyright Copyright 2020 Aldric L.
       * @access public
       * @return array 
       */
      public function getManualTasks(){
        $tasks = simplifySQL\select($this->bddConnexion(), false, "d_boutique_todolist", "*", array(array("done", "=", 0), "AND", array("is_manual", "=", true)));
        return $tasks;
      }
      
      /**
       * loadPage - Fonction pour charger les pages "text simple", gérées par l'admin, et auquelles correspondent toujours des fichiers ftxt
       * @author Aldric.L
       * @copyright Copyright 2020 Aldric L.
       * @access public
       * @param string $page_name, qui correspond à $param[0]
       * @return void
       */
      public function loadPage($page_name=NULL) : void {
        if (@file_exists(ROOT . "installation/blocked.dcms")){
          define('DIAMOND_BLOCKED', true);
          require_once(ROOT . 'installation/infodiamondcms.php'); die;
        }
        
        if (isset($page_name) && !empty($page_name)){
          $result = simplifySQL\select($this->bddConnexion(), true, "d_pages", "*", array(array("name_raw", "=", $page_name)));
          if (is_array($result)){
              $file = $result['file_name'];
              $page_name = $result['name'];
              if (!file_exists(ROOT . 'config/' . $file . '.ftxt')){
                  $this->addError(132);
                  $this->loadView('pages/404', '404', 'Erreur 404');
                  return;
              }
              // Pour le partage de variables, on n'appelle pos la méthode loadView et on gère manuellement
              $this->title = $result['name'];
              require(ROOT . 'views/themes/' . $this->Serveur_Config['theme'] . '/include/header.inc');
              require(ROOT . '/views/themes/'. $this->Serveur_Config['theme'] . '/pages/pageloader.php');
              require(ROOT . 'views/themes/' . $this->Serveur_Config['theme'] . '/include/footer.inc');
              return;
          }
        }
        $this->nonifyPage("Impossible de poursuivre", "Erreur 404 : La page demandée n'existe pas");
      }

      /**
       * rconlog - Fonction qui log toutes les commandes Rcon
       * @author Aldric.L
       * @copyright Copyright 2021 Aldric L.
       * @access public
       * @param int $id, id du serveur
       * @param string $cmd, la commande
       * @param string $reponse, la réponse du serveur (ERREUR si ça n'a pas fonctionné)
       * @return void
       */
      public function rconlog($id, $cmd, $reponse) : void {
        //Environ une fois sur 60, on purge le log
        if (rand ( 1 , 60 ) == 42 ){
            $tabfile = file($this->paths['logs'] . 'rcon.log');
            $txt = "";
            // Boucle pour ne conserver que les "n" premières ligne(s)
            for ($i = 1; $i <= 100; $i++) {
              $txt .=  $tabfile[$i];
            }
            $open=fopen($this->paths['logs'] . 'rcon.log',"w+" );
            fwrite($open,$txt);
            fclose($open);
        }
        if (!empty($_SESSION['pseudo'])){
          file_put_contents($this->paths['logs'] . 'rcon.log', date("j/m/y à H:i:s") .  " - Par ". $_SESSION['pseudo'] . " : Serveur=" . $id . " CMD=". $cmd . " Response=" . $reponse . " \r\n".file_get_contents($this->paths['logs'] . "rcon.log"));
        }
     }

     /**
       * pluginloader - Fonction qui charge dans le header et le footer les plugins js et CSS
       * @author Aldric.L
       * @copyright Copyright 2022 Aldric L.
       * @access public
       * @param bool $load, true : load, false : listener (déterminant s'il faut l'incluer au début ou à la fin de la page)
       * @param bool $admin, charge-t-on les ressources admin ?
       * @return void
       */
     public function pluginloader($load, $admin=false) : void{
       if ($load){
        $root = ROOT . 'js/plugins/load';
        $webroot = LINK . 'js/plugins/load/';
        $theme_root = ROOT . 'views/themes/' . $this->Serveur_Config['theme'] .'/js/plugins/load';
        $theme_webroot = LINK . 'views/themes/' . $this->Serveur_Config['theme'] .'/js/plugins/load/';
        $main_file = "plugins_load.js";
       }else {
        $root = ROOT . 'js/plugins/listener';
        $webroot = LINK . 'js/plugins/listener/';
        $theme_root = ROOT . 'views/themes/' . $this->Serveur_Config['theme'] .'/js/plugins/listener';
        $theme_webroot = LINK . 'views/themes/' . $this->Serveur_Config['theme'] .'/js/plugins/listener/';
        $main_file = "plugins_listener.js";
       }
      //On ouvre le dossier de thème
      if($jsfolder = opendir($theme_root)){
        $count = 0;
        while(false !== ($plugin = readdir($jsfolder))){
          if($plugin != '.' && $plugin != '..' && $plugin != $main_file && (substr($plugin, -3) == '.js' || substr($plugin, -4) == '.css')){
              if (($admin && substr($plugin, -12) != '.notadmin.js' && substr($plugin, -13) != '.notadmin.css')
                  OR (!$admin && substr($plugin, -9) != '.admin.js' && substr($plugin, -10) != '.admin.css')) {
                $count++;
                if (substr($plugin, -3) == '.js'){
                  echo '<script src="'. $theme_webroot . $plugin . '"></script>';
                }else{
                  echo '<link rel="stylesheet" type="text/css" href="' . $theme_webroot . $plugin .'"/>';
                }
              }
          }
        }
        if ($count != 0)
          echo "<!--Inclusion de " . $count . " plugins de thème ! -->";
        else 
          echo '<!--Aucun plugin de thème trouvé, chargement des libs de "base" -->';
        
        echo '<script src="' . $theme_webroot . $main_file . '"></script>';
      }

      //On ouvre le dossier GLOBAL
      if($jsfolder = opendir($root)){
        $count = 0;
        while(false !== ($plugin = readdir($jsfolder))){
          if($plugin != '.' && $plugin != '..' && $plugin != $main_file 
          && (substr($plugin, -3) == '.js' || substr($plugin, -4) == '.css')){
              if ((!$admin &&substr($plugin, -9) != '.admin.js' && substr($plugin, -10) != '.admin.css')
                OR ($admin && substr($plugin, -12) != '.notadmin.js' && substr($plugin, -13) != '.notadmin.css')){
                $count++;
                if (substr($plugin, -3) == '.js')
                  echo '<script src="'. $webroot . $plugin . '"></script>';
                else
                  echo '<link rel="stylesheet" type="text/css" href="' . $webroot . $plugin .'"/>';
              }
          }
        }
        if ($count != 0)
          echo "<!--Inclusion de " . $count . " plugins global ! -->";
        else 
          echo '<!--Aucun plugin global trouvé, chargement des libs de "base" -->';

        echo '<script src="' . $webroot . $main_file . '"></script>';
      }
  }

  /**
    * getAdminPages - Fonction pour récuperer les pages affichées dans le système de navigation administrateur
    * Avant d'ajouter une page au menu il est préférable de d'abord récupérer les pages ajoutées pour ne pas écraser les ajouts précédents
    * @author Aldric.L
    * @copyright Copyright 2022 Aldric L.
    * @access public
    * @return array
    */
  public function getAdminPages() : array{
      return $this->adminPages;
  }

  /**
    * setAdminPages - Fonction pour définir les pages affichées dans le système de navigation administrateur
    * Désormais les addons doivent passer par un fichier adminPages.json pour ajouter des pages. Il a été décidé de ne pas laisser l'accès à cette fonction trop dangereuse
    * @author Aldric.L
    * @copyright Copyright 2022 Aldric L.
    * @access private
    * @param array $adminPages 
    * @return void
    */
  private function setAdminPages($adminPages) : void {
    $this->adminPages = $adminPages;  
    return;
  }

/**
 * Les méthodes qui suivent sont des anciennes fonctions du trait Users
 * ELles sont officiellement dépréciées et remplacées par des méthodes statiques de User
 * @deprecated 2022
 * 
 * Par soucis de compatibilité, on les conserve comme alias.
 */

 /**
  * getInfos - Fonction permettant de récuperer toutes les informations sur un membre stockées dans la table ("d_membre)
  * Cette méthode correspond aux nouvelles normes d'utilisation SQL (2020) en utilisant les fonctions de simplification/sécurisation
  * @author Aldric.L
  * @copyright Copyright 2020 2022 Aldric L.
  * @access public
  * @param string $pseudo : pseudo du membre
  * @return array
  */
  public function getInfos($db, $pseudo){
    return User::getInfosFromPseudo($db, $pseudo);
  }

 /**
  * getInfosFromId - Fonction permettant de récuperer toutes les informations sur un membre stockées dans la table ("d_membre)
  * Cette méthode correspond aux nouvelles normes d'utilisation SQL (2020) en utilisant les fonctions de simplification/sécurisation
  * @author Aldric.L
  * @copyright Copyright 2020 2022 Aldric L.
  * @access public
  * @param int $id : id du membre
  * @return array
  */
  public function getInfosFromId($db, $id){
    return User::getInfosFromId($db, $id);
  }

  /**
   * getRoleNameById - Fonction pour récuperer le nom d'un role à partir de son id
   * Cette méthode correspond aux nouvelles normes d'utilisation SQL (2020) en utilisant les fonctions de simplification/sécurisation
   * @author Aldric.L
   * @copyright Copyright 2020 2022 Aldric L.
   * @access public
   * @return false|array
   */
    public function getRoleNameById($db, $id_role){
      //On évite l'emploi de la méthode statique qui nous refait un appel en BDD et vérifie si on ne l'a pas en cache
      if (isset($_SESSION['user']) && $_SESSION['user'] instanceof User)
        return $_SESSION['user']->UserGetRoleName($id_role);
      return User::getRoleNameById($db, $id_role);
    }

    /**
     * echoRoleName - Fonction gérant l'affichage des grades devant les pseudos
     * Cette méthode correspond aux nouvelles normes d'utilisation SQL (2020) en utilisant les fonctions de simplification/sécurisation
     * @author Aldric.L
     * @copyright Copyright 2020 2022 Aldric L.
     * @access public
     * @return string
     */
    public function echoRoleName($db, $pseudo){
      return User::echoRoleName($db,$pseudo);
    }

    /**
     * getRoleLevel - Fonction permettant d'obtenir le level du role à partir de son id
     * Cette méthode correspond aux nouvelles normes d'utilisation SQL (2020) en utilisant les fonctions de simplification/sécurisation
     * @author Aldric.L
     * @copyright Copyright 2020 2022 Aldric L.
     * @access public
     * @param $db : PDO instance
     * @param string $id_role : OBLIGATOIRE si cette methode n'est pas appelée depuis la classe User (cette utilisation est dépreciée depuis la 1.1)
     * @return int
     */
    public function getRoleLevel($db, $id_role=false){
      //On évite l'emploi de la méthode statique qui nous refait un appel en BDD et vérifie si on ne l'a pas en cache
      if (isset($_SESSION['user']) && $_SESSION['user'] instanceof User)
        return $_SESSION['user']->UserGetRoleLevel($id_role);
      return User::getRoleLevel($db, $id_role);
    }

    /**
     * getRoleLevelByPseudo - Fonction pour récuperer le level d'un membre à partir de son pseudo
     * Cette méthode correspond aux nouvelles normes d'utilisation SQL (2020) en utilisant les fonctions de simplification/sécurisation
     * @author Aldric.L
     * @copyright Copyright 2020 2022 Aldric L.
     * @access public
     * @return false|array
     */
      public function getRoleLevelByPseudo($pseudo){
        return User::getRoleLevelByPseudo($this->bddConnexion(), $pseudo);
      }

    /**
     * getPseudo - Fonction pour récuperer le pseudo d'un membre
     * Cette méthode correspond aux nouvelles normes d'utilisation SQL (2020) en utilisant les fonctions de simplification/sécurisation
     * @author Aldric.L
     * @copyright Copyright 2020 2022 Aldric L.
     * @access public
     * @return false|array
     */
    public function getPseudo($id){
        return User::getPseudoById($this->bddConnexion(), intval($id));
    }

    /**
     * getPseudoById - Fonction pour récuperer le pseudo d'un membre, alias de getPseudo
     * Cette méthode correspond aux nouvelles normes d'utilisation SQL (2020) en utilisant les fonctions de simplification/sécurisation
     * @author Aldric.L
     * @copyright Copyright 2020 2022 Aldric L.
     * @access public
     * @return false|array
     */
    public function getPseudoById($id){
      return User::getPseudoById($this->bddConnexion(), intval($id));
    }

    /**
     * getRole - Fonction pour récuperer le nom du role par le pseudo d'un membre
     * Cette méthode correspond aux nouvelles normes d'utilisation SQL (2020) en utilisant les fonctions de simplification/sécurisation
     * @author Aldric.L
     * @copyright Copyright 2020 2022 Aldric L.
     * @access public
     * @return false|string
     */
    public function getRole($db, $pseudo){
        return User::getRoleByPseudo($db, $pseudo);
    }

  //Pour charger une vue d'un addon
  public function loadViewAddon(string $view, bool $admin, $lcss=false, $ltitle=false, $iframer=false){
    //Si un CSS est demandé :
    if (isset($lcss) && $lcss != false){
      //on le charge pour le réutiliser dans le header
      $this->css = $this->loadCSSAddon($lcss, $admin);
    }

    //Si un titre est demandé :
    if (isset($ltitle) && $ltitle != false){
      //on le sauvegarde pour le réutiliser dans le header
      $this->title = $ltitle;
    }
    $this->is_iframer = $iframer;

    //Si le fichier existe ...
    if (file_exists($view)){
      //... on charge les vues "génériques" : header et footer et on charge la vue passée en paramètre
      if ($admin){
        if (!(array_key_exists("DIAMOND_CACHE_PROCESSING", $GLOBALS) && $GLOBALS['DIAMOND_CACHE_PROCESSING']))
          require($this->paths['views'] . 'include/header_admin.inc');
        require($view);
        if (!(array_key_exists("DIAMOND_CACHE_PROCESSING", $GLOBALS) && $GLOBALS['DIAMOND_CACHE_PROCESSING']))
          require($this->paths['views'] . 'include/footer_admin.inc');
      }else {
        if (!(array_key_exists("DIAMOND_CACHE_PROCESSING", $GLOBALS) && $GLOBALS['DIAMOND_CACHE_PROCESSING']))
          require($this->paths['views'] . 'include/header.inc');
        require($view);
        if (!(array_key_exists("DIAMOND_CACHE_PROCESSING", $GLOBALS) && $GLOBALS['DIAMOND_CACHE_PROCESSING']))
          require($this->paths['views'] . 'include/footer.inc');
      }
    }

  }

  //Pour charger une vue d'un addon
  public function loadAsView(string $content, bool $admin, $lcss=false, $ltitle=false, $iframer=false){
    //Si un CSS est demandé :
    if (isset($lcss) && $lcss != false){
      //on le charge pour le réutiliser dans le header
      $this->css = $this->loadCSSAddon($lcss, $admin);
    }

    //Si un titre est demandé :
    if (isset($ltitle) && $ltitle != false){
      //on le sauvegarde pour le réutiliser dans le header
      $this->title = $ltitle;
    }
    $this->is_iframer = $iframer;

      //... on charge les vues "génériques" : header et footer et on charge la vue passée en paramètre
      if ($admin){
        if (!(array_key_exists("DIAMOND_CACHE_PROCESSING", $GLOBALS) && $GLOBALS['DIAMOND_CACHE_PROCESSING']))
          require($this->paths['views'] . 'include/header_admin.inc');
        echo $content;
        if (!(array_key_exists("DIAMOND_CACHE_PROCESSING", $GLOBALS) && $GLOBALS['DIAMOND_CACHE_PROCESSING']))
          require($this->paths['views'] . 'include/footer_admin.inc');
      }else {
        if (!(array_key_exists("DIAMOND_CACHE_PROCESSING", $GLOBALS) && $GLOBALS['DIAMOND_CACHE_PROCESSING']))
          require($this->paths['views'] . 'include/header.inc');
        echo $content;
        if (!(array_key_exists("DIAMOND_CACHE_PROCESSING", $GLOBALS) && $GLOBALS['DIAMOND_CACHE_PROCESSING']))
          require($this->paths['views'] . 'include/footer.inc');
      }

  }

  private function loadCSSAddon($css, $admin=false){
      //Si le fichier CSS existe ...
      if (file_exists($css)){
        //On le retourne pour le réutiliser dans le header
        return $css;
      } 
  }

  public function loadJSAddon($js){
      array_push($this->js, $js);
  }

  public function addAdminIframe($addon_name){
    array_push($this->adminIframes, $addon_name);
  }

  public function getAdminIframes(){
    return $this->adminIframes;
  }

  public function getAddons(){
    return $this->loadedAddons;
  }

  public function getAvailableAddons($raw=true){
    $addons = array();
    foreach ($this->loadedAddons as $key => $addon) {
      if (array_key_exists("enabled", $addon) && $addon['enabled'])
        array_push($addons, ($raw) ? $addon['name_raw'] : $addon['name']);
    }
    return $addons;
  }

  public function loadAddons(){
    //Chargement des addons
    if ($dir = opendir(ROOT . 'addons/')) {
      while($file = readdir($dir)) {
        //On ouvre les sous-dossiers
        if(is_dir(ROOT . 'addons/' . $file) && !in_array($file, array(".",".."))) {
          $d_list = scandir(ROOT . 'addons/' . $file);
            if (in_array("addon.ini", $d_list)){
              try {
                $addon = cleanIniTypes(parse_ini_file(ROOT . 'addons/' . $file . "/addon.ini"));
                $addon["path"] = ROOT . 'addons/' . $file . "/";
                $addon["name_raw"] = $file;
                if ((!array_key_exists("enabled", $addon) || $addon['enabled']) && !in_array("disabled.dcms", $d_list) && file_exists(ROOT . 'addons/' . $file . "/init.php")){
                  $controleur_def = $this;
                  if (file_exists(ROOT . 'addons/' . $file . "/errors.ini")){
                    $controleur_def->extendErrorsKnown(cleanIniTypes(parse_ini_file(ROOT . 'addons/' . $file . "/errors.ini", true)));
                  }
                  require_once(ROOT . 'addons/' . $file . "/init.php");
                  if (isset($addon["modules_to_register"])){
                    if ($dir_mod = opendir($addon["path"] . $addon["modules_to_register"])) {
                      $pth_mod = $addon["path"] . $addon["modules_to_register"];
                      while($file_mod = readdir($dir_mod)) {
                        if(is_dir($pth_mod . $file_mod) && !in_array($file_mod, array(".",".."))) {
                          $d_list_mod = scandir($pth_mod . $file_mod);
                            if (in_array($file_mod . ".module.class.php", $d_list_mod)){
                              require_once($pth_mod . $file_mod . '/' . $file_mod . ".module.class.php");
                              $this->addAvailableModules($file_mod, $pth_mod. $file_mod . '/');
                            }
                        }
                      }
                      closedir($dir_mod);
                    }
                  }
                }else {
                  $addon["enabled"] = false;
                }
                array_push($this->loadedAddons, $addon);
              }catch (Exception $e){
                $this->addError(139);
              }
              if ($addon["enabled"] && array_key_exists("loadAdminPages", $addon) && $addon['loadAdminPages'] == true && in_array("adminPages.json", $d_list)){
                $p = $this->getAdminPages();
                try {
                  $ap = json_decode(file_get_contents(ROOT . 'addons/' . $file . "/adminPages.json"));
                  if (is_array($ap)){
                    foreach ($ap as $key => $a) {
                      if (is_array($a))
                        array_push($p, $a);
                    }
                  }
                  $this->setAdminPages($p);
                }catch (Exception $e){
                  $this->addError(139);
                }
              }
              if (array_key_exists("loadAdminPages", $addon) && $addon['loadAdminPages'] == true){
                $this->addAdminIframe($addon['name']);
              }
            }
        }
      }
      closedir($dir);
    }
  }

  // Cette fonction existe le temps d'adapter le code du CMS aux nouvelles erreurs et en particulier l'externalisation des erreurs DSL
  public function addError($error_code, $user=null){
    $error_code = (string)$error_code;
    if (substr($error_code, 0,1) == "4")
      $error_code = "Diamond-ServerLink$" . $error_code;
    
    if ($user!=null)
      return parent::addError($error_code, $user);
    else
      return parent::addError($error_code, ((isset($_SESSION['user']) && $_SESSION['user'] instanceof User) ? $_SESSION['user']->getId() : null));
  }

  public function log($error_code, $user=null) {
    if ($user!=null)
      return parent::log($error_code, $user);
    else
      return parent::log($error_code, ((isset($_SESSION['user']) && $_SESSION['user'] instanceof User) ? $_SESSION['user']->getId() : null));
 }

 public function getAvailableModules() : array{
   return $this->available_modules;
 }

 public function addAvailableModules($mod, $path){
    array_push($this->available_modules, array("name" => $mod, "path" => $path));
 }

 public function getModulesManager($page_name){
   if ($this->registered_modules_manager == null || ($this->registered_modules_manager instanceof ModulesManager\ModulesManager && $this->registered_modules_manager->getName() != $page_name)){
    return $this->registered_modules_manager = $modulesmanager = $this->getOneModulesManager($page_name);
   }else{
    return $this->registered_modules_manager;
   }
 }

 public function getOneModulesManager($page_name){
   $mmclassname = str_replace(" ", "", "ModulesManager\ ") . ucfirst($this->Serveur_Config['theme']) . "ModulesManager"; 
   return new $mmclassname($this->bddConnexion(), $this->getAvailableModules(), $page_name, null);
}


  public function serialize() : string {
    $to_serial = array();
    $obj = new ReflectionObject($this);
    foreach ($this->attrSerializable as &$t){
      if ($obj->hasProperty($t) && (is_string($this->$t) || is_int($this->$t) || is_array($this->$t) || is_numeric($this->$t))){
        $to_serial[$t] = $this->$t;
      }
    }
    return json_encode($to_serial);
  }

  public function unSerialize($serialString) : void {
    $serialString = json_decode($serialString, true);
    $obj = new ReflectionObject($this);
    foreach ($serialString as $key => &$t){
      if ($obj->hasProperty($key) && (is_string($t) || is_int($t) || is_array($t) || is_numeric($t))){
        $this->$key = $t;
      }
    }
  }

  public function nonifyPage(string $title, string $content,
  string $alt="Vous pouvez poursuivre normalement votre navigation. Pour toute demande d'assistance, n'hésitez pas à nous contacter.") : void {
    global $c; $c = $content;
    global $a; $a = $alt;
    $this->loadView('pages/nonify', 'nonify', $title);
  }

  public static function makeGetImageLink(string $filename, bool $folder=false) : string{
    $array_temp = explode(".", $filename);
    $ext = array_pop($array_temp);
    if (!$folder)
      return LINK . "getimage/" . $ext . "/-" . "/" . implode(".", $array_temp); 
    else 
      return LINK . "getimage/" . $ext . "/" . implode(".", $array_temp);
  }

  public static function canIShowThisFile(string $path, $user=null) : bool {
    $path_array = explode("/", $path);
    if (sizeof($path_array) < 2)
      return true;

    $working_array = array_filter($path_array);
    $target = array_pop($working_array);
    $previous_path = (sizeof($working_array) > 0) ? implode("/",  $working_array) : "";
    if ($previous_path[strlen($previous_path)-1] != '/')
        $previous_path .= "/";

    if (!file_exists($previous_path . "locked_files.dfiles") && file_exists("/" . $previous_path . "locked_files.dfiles"))
      $previous_path = "/" . $previous_path;
      
    if (file_exists($previous_path . "locked_files.dfiles")){
        $level_min = 1;
        $conf = json_decode(file_get_contents($previous_path . "locked_files.dfiles"), true);
        if (is_array($conf) && array_key_exists("__GLOBAL-FOLDER-DIAMONDCONF__", $conf) && is_array($conf["__GLOBAL-FOLDER-DIAMONDCONF__"]) && array_key_exists("access_level", $conf["__GLOBAL-FOLDER-DIAMONDCONF__"]) && is_numeric($conf["__GLOBAL-FOLDER-DIAMONDCONF__"]["access_level"]))
            $level_min = max($level_min, intval($conf["__GLOBAL-FOLDER-DIAMONDCONF__"]["access_level"]));
        
        if (is_array($conf) && array_key_exists($target, $conf) && is_array($conf[$target]) &&
            array_key_exists("access_level", $conf[$target]) && is_numeric($conf[$target]["access_level"]) 
            && intval($conf[$target]["access_level"]) > 1){
                if ((array_key_exists("locked", $conf[$target]) and $conf[$target]["locked"]) or (array_key_exists("protected", $conf[$target]) and $conf[$target]["protected"]))
                    $level_min = intval($conf[$target]["access_level"]);
                else
                    $level_min = max($level_min, intval($conf[$target]["access_level"]));
        }
        if ($level_min > 1 AND 
        (!isset($user) or is_null($user) or !($user instanceof User) or (isset($user) and $user instanceof User and $user->getlevel() < $level_min)))
            return false;
    }
    return true;
  }

  public static function listAvailableFiles(string $path, int $level=1){
    if (($dir = opendir($path)) === false)
      return array();
    if (strlen($path) > 1 && $path[strlen($path)-1] != '/')
        $path .= "/";
    $conf = null;
    $level_min = 1;
    if (file_exists($path . "locked_files.dfiles")){
      $conf = json_decode(file_get_contents($path . "locked_files.dfiles"), true);
      if (is_array($conf) && array_key_exists("__GLOBAL-FOLDER-DIAMONDCONF__", $conf) && is_array($conf["__GLOBAL-FOLDER-DIAMONDCONF__"]) && array_key_exists("access_level", $conf["__GLOBAL-FOLDER-DIAMONDCONF__"]) && is_numeric($conf["__GLOBAL-FOLDER-DIAMONDCONF__"]["access_level"]))
        $level_min = max($level_min, intval($conf["__GLOBAL-FOLDER-DIAMONDCONF__"]["access_level"]));
    }
    if ($level_min > $level)
      return array();

    $to_return = array();
    while(false !== ($fichier = readdir($dir))){
      $level_min = 1;
      if($fichier != '.' && $fichier != '..' && !is_dir($path . $fichier)){
        if (is_array($conf) && array_key_exists($fichier, $conf) && is_array($conf[$fichier]) &&
        array_key_exists("access_level", $conf[$fichier]) && is_numeric($conf[$fichier]["access_level"]) 
        && intval($conf[$fichier]["access_level"]) > 1){
            if ((array_key_exists("locked", $conf[$fichier]) and $conf[$fichier]["locked"]) or (array_key_exists("protected", $conf[$fichier]) and $conf[$fichier]["protected"]))
                $level_min = intval($conf[$fichier]["access_level"]);
            else
                $level_min = max($level_min, intval($conf[$fichier]["access_level"]));
        }
        if ($level_min <= $level)
          array_push($to_return, $fichier);
      }
    }
    return $to_return;
  }
}
