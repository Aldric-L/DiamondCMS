<?php 
//PATH theme =ROOT . '/views/themes/'. $this->Serveur_Config['theme'] . '/'
final class Manager extends Controleur {
    use Users, NotifyCenter;    
    public function __construct(&$Serveur_Config, $view_path){
        parent::__construct($Serveur_Config, $view_path);
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
      public function addPage($is_link=FALSE, $name_raw, $name, $fa_icon=null){
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
       * getManualTasks - Méthode pour obtenir les tâches manuelles qui n'ont pas encore été réalisées (fonction boutique)
       * 
       * @author Aldric.L
       * @copyright Copyright 2020 Aldric L.
       * @access public
       * @return array 
       */
      public function getManualTasks(){
        $tasks = simplifySQL\select($this->bddConnexion(), false, "d_boutique_todolist", "*", array(array("done", "=", 0)));
        $final = array();
        foreach ($tasks as $k => $t){
          $cmd = simplifySQL\select($this->bddConnexion(), true, "d_boutique_cmd", "*", array(array("id", "=", $t['cmd'])));
          if ($cmd != false && isset($cmd['is_manual']) && $cmd['is_manual']){
            array_push($final, $tasks[$k]);
          }
        }
        return $final;
      }
      
      /**
       * loadPage - Fonction pour charger les pages "text simple", gérées par l'admin, et auquelles correspondent toujours des fichiers ftxt
       * @author Aldric.L
       * @copyright Copyright 2020 Aldric L.
       * @access public
       * @param string $page_name, qui correspond à $param[0]
       * @return void
       */
      public function loadPage($page_name=NULL){
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
        $this->loadView('pages/404', '404', 'Erreur 404');
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
      public function rconlog($id, $cmd, $reponse) {
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
}
