<?php

/**
 * admin - API Admin regroupant des fonctions utiles un peu partout ou dans la page d'accueil
 *  
 * @author Aldric L.
 * @copyright 2022
 */
class admin extends DiamondAPI {
    public function __construct(array $paths, PDO $pdo, Controleur $controleur, int $level){
        parent::__construct($paths, $pdo, $controleur, $level);
        $this->params_needed = array(
            "get_maj" => array(),
            "set_mtnc" => array(),
            "set_theme" => array("theme"),
            "set_addonstate" => array("addon"),
            "set_delContact" => array("id"),
            "set_addNews" => array("name", "message"),
            "set_delNews" => array("id"),
            "get_contactSeen" => array("id"),
            "get_uploadedImgs" => array(),
            "get_lastApiCalls" => array(),
        );
    }

    /**
     * get_maj - Fonction pour vérifier que le CMS est à jour
     * La fonction retourne le résultat de façon formatée, près à l'emploi dans la vue
     * 
     * @access public 
     * @author Aldric L.
     * @copyright 2022
     */
    public function get_maj(){
        if ($this->level < 4)
            throw new DiamondException("Forbidden access", 706);
        //Vérification de la version du CMS :
        $version = @file_get_contents('https://aldric-l.github.io/DiamondCMS/version.txt');
        if (!empty($version)){
            $version = intval($version);
            if (DCMS_INT_VERSION < $version){
                $file = fopen(ROOT . 'config/outdated.dcms', 'w+');
                fwrite($file, 'version_int="'. $version . '"');
                return $this->formatedReturn('<strong><span style="color: red;">Attention !</span> DiamondCMS n\'est plus à jour.</strong> <a href="https://aldric-l.github.io/DiamondCMS/files/diamondcms-maj-' .DCMS_INT_VERSION . '-' . $version .'.zip">Télécharger l\'archive de la Mise à jour.</a>');
            }else {
                if (file_exists(ROOT . 'config/outdated.dcms')){
                    unlink(ROOT . 'config/outdated.dcms');
                }
            }
        }
        return $this->formatedReturn('DiamondCMS est à jour. Vérification effectuée le ' . date('d.m.Y à H:i:s'));
    }


    /**
     * set_mtnc - Fonction pour enclencher/annuler une maintenance
     * 
     * @access public 
     * @author Aldric L.
     * @copyright 2022
     */
    public function set_mtnc(){
        if ($this->level < 5)
            throw new DiamondException("Forbidden access", 706);

        $conf = $this->getIniConfig(ROOT."config/config.ini", true);
        $args = array();
        if ($conf['mtnc']){
            $args['mtnc'] = false;
        }else {
            $args['mtnc'] = true;
        }
        $this->setConfig(ROOT."config/config.ini", $args);
        return $this->formatedReturn(1);
    }

    /**
     * set_theme - Fonction pour changer de thème
     * 
     * @param string theme : nom du thème tel qu'il est à la racine
     * @access public 
     * @author Aldric L.
     * @copyright 2022
     */
    public function set_theme(){
        if ($this->level < 5)
            throw new DiamondException("Forbidden access", 706);

        if (!file_exists(ROOT . 'views/themes/' . $this->args['theme'] . '/theme.ini'))
            throw new DiamondException("File unreachable", 550);
        $args = array("theme" => $this->args['theme']);
        $this->setConfig(ROOT."config/config.ini", $args);
        return $this->formatedReturn(1);
    }

    /**
     * set_addonstate - Fonction pour désactiver/activer un addon
     * 
     * @param string addon : nom de l'addon
     * @access public 
     * @author Aldric L.
     * @copyright 2022
     */
    public function set_addonstate(){
        if ($this->level < 5)
            throw new DiamondException("Forbidden access", 706);

        if (file_exists(ROOT . 'addons/' . $this->args['addon'] . '/init.php')){
            //SI on est sur un addon pré v2
            if (!file_exists(ROOT . 'addons/' . $this->args['addon'] . '/addon.ini')){
                if (file_exists(ROOT . 'addons/' . $this->args['addon'] . '/disabled.dcms')){
                    unlink(ROOT . 'addons/' . $this->args['addon'] . '/disabled.dcms');
                }else {
                    $file = fopen(ROOT . 'addons/' . $this->args['addon'] . '/disabled.dcms', 'x');
                    fclose($file);
                }
            }else {
                $aconf = cleanIniTypes(parse_ini_file(ROOT . 'addons/' . $this->args['addon'] . '/addon.ini'));
                $aconf['enabled'] = (!$aconf['enabled']) ? true : false;
                $this->setConfig(ROOT . 'addons/' . $this->args['addon'] . '/addon.ini', $aconf);

                if ($aconf['enabled'] && file_exists(ROOT . 'addons/' . $this->args['addon'] . '/disabled.dcms')){
                    unlink(ROOT . 'addons/' . $this->args['addon'] . '/disabled.dcms');
                }
            }
            
        }
        return $this->formatedReturn(1);
    }

    /**
     * set_delContact - Fonction pour supprimer une entrée de contact dans la bdd
     * 
     * @param int id : id dans la BDD du contact
     * @access public 
     * @author Aldric L.
     * @copyright 2022
     */
    public function set_delContact(){
        if ($this->level < 4)
            throw new DiamondException("Forbidden access", 706);
        if (simplifySQL\delete($this->getPDO(), "d_contact", array(array("id", "=", $this->args['id']))) != true)
            throw new DiamondException("Error with simplifySQL\delete", 713);
        return $this->formatedReturn(1);
    }

    /**
     * set_delNews - Fonction pour supprimer une news
     * 
     * @param int id : id de la news
     * @access public 
     * @author Aldric L.
     * @copyright 2022
     */
    public function set_delNews(){
        if ($this->level < 4)
            throw new DiamondException("Forbidden access", 706);
        
        $news = simplifySQL\select($this->getPDO(), true, "d_news", "id, img", array(array("id", "=", $this->args['id'])));
        if ((is_bool($news) && $news == false) || (is_array($news) && empty($news)))
            throw new Exception("Unable to find requested news", 717);
        
        $news['img'] = str_replace("news/", "", $news['img']);
        
        if (simplifySQL\delete($this->getPDO(), "d_news", array(array("id", "=", $this->args['id']))) != true)
            throw new DiamondException("Error with simplifySQL\delete", 713);

        unlock_file(ROOT . "views/uploads/img/news/", $news['img']);
        return $this->formatedReturn(1);
    }

     /**
     * get_contactSeen - Fonction pour mettre une demande de contact en vu
     * 
     * @param int id : id de la demande de contact
     * @access public 
     * @author Aldric L.
     * @copyright 2023
     */
    public function get_contactSeen(){
        if ($this->level < 4)
            throw new DiamondException("Forbidden access", 706);
        
        $contact = simplifySQL\select($this->getPDO(), true, "d_contact", "id", array(array("id", "=", $this->args['id'])));
        if ((is_bool($contact) && $contact == false) || (is_array($contact) && empty($contact)))
            throw new Exception("Unable to find requested news", 717);
                
        try{
            if (!simplifySQL\update($this->getPDO(), "d_contact",
                array("seen" => true),
                array(array("id","=", $this->args['id']))))
                    
            throw new DiamondException("Unable to update contact status.", "342a");   
        }catch (DiamondException $e){ throw $e; }
        catch (Throwable $e){
            throw new DiamondException("Unable to update contact status.", "342a");   
        }

        return $this->formatedReturn(1);
    }

    /**
     * set_addNews - Fonction pour ajouter une nouvelle news
     * 
     * @param string name : nom de la news
     * @param string message : contenu à écrire en news
     * @param image img : optionnal image à associer (en upload)
     * @access public 
     * @author Aldric L.
     * @copyright 2022
     */
    public function set_addNews(){
        if ($this->level < 4)
            throw new DiamondException("Forbidden access", 706);

        if (isset($_FILES['img']) && $_FILES['img']['size'] != 0){
            if (strrpos($_FILES['img']['type'], "image/") === false){
                throw new DiamondException("Bad ext", 524);
            }else {
                $upload = uploadFile('img', "news", true, ROOT . "views/uploads/img/", array("access_level" => 1, "protected" => false, "locked" => true, "protected_name" => true));
                if (is_int($upload)){
                    throw new DiamondException("Error while uploading", 500 + intval($upload));
                }else {
                  $filename = $upload;
                }
            }    
          }else {
              $filename = "noimg";
          }

        if (simplifySQL\insert($this->getPDO(), "d_news", array("name", "content_new", "date", "img", "user"), array($this->args['name'], $this->args['message'], date("Y-m-d h:i:s"), $filename, $_SESSION['user']->getId())) != true)
            throw new DiamondException("Error with simplifySQL\insert", 714);
            
        return $this->formatedReturn(1);
    }

    /**
     * get_uploadedImgs - Fonction pour récupérer les images enregistrées sur le serveur
     * Cette fonction est prévue pour fonctionner avec DIC
     * 
     * @access public 
     * @author Aldric L.
     * @copyright 2022
     */
    public function get_uploadedImgs(){
        if (isset($this->args['height']) && is_numeric($this->args['height'])){
            $height = intval($this->args['height']);
        }
        if (isset($this->args['width']) && is_numeric($this->args['width'])){
            $width = intval($this->args['width']);
        }

        $images = array();
        if ($dir = opendir(ROOT . 'views/uploads/img/')) {
            if (file_exists(ROOT . 'views/uploads/img/' . "/locked_files.dfiles")){
                $conf = json_decode(file_get_contents(ROOT . 'views/uploads/img/' . "locked_files.dfiles"), true);
            }
            if (!isset($conf) OR !is_array($conf))
                $conf = array();

            while($file = readdir($dir)) {
              //On n'ouvre surtout pas les sous-dossiers
              if(!is_dir(ROOT . 'views/uploads/img/' . $file) && !in_array($file, array(".","..")) && $file != "locked_files.dfiles") {
                if (is_array($conf) && array_key_exists($file, $conf) && is_array($conf[$file]) && array_key_exists("access_level", $conf[$file]) && is_numeric($conf[$file]["access_level"]) && intval($conf[$file]["access_level"]) > $this->level)
                    continue;
                
                $n = explode(".", $file);    
                $ext = strtolower($n[sizeof($n)-1]);
                if ($ext == "png" || $ext == "jpg" || $ext == "jpeg"){
                    $name_raw = "";
                    for ($i=0; $i < sizeof($n)-1; $i++){
                        $name_raw .= $n[$i];
                        if ($i !== sizeof($n)-2)
                            $name_raw .= ".";
                    }
                    $source_link = LINK . "getimage/" . $ext . str_replace(" ", "", ' /-/ ') . $name_raw ; 
                    if (isset($height))
                        $source_link .= "/" . $height; 
                    if (isset($width))
                        $source_link .= "/" . $width; 
            
                    array_push($images, 
                        array("filename" => $file, 
                            "path" => ROOT . 'views/uploads/img/' . $file, 
                            "last_modified" => filemtime(ROOT . 'views/uploads/img/' . $file), 
                            "file_human_size" => FileSizeConvert( filesize(ROOT . 'views/uploads/img/' . $file) ),
                            "file_size" => filesize(ROOT . 'views/uploads/img/' . $file),
                            "ext" => $ext,
                            "filename_without_ext" => $name_raw,
                            "source_link" => $source_link
                    ) );
                    $i = $i+1;
                }
              }
            }
            closedir($dir);
        }
        return $this->formatedReturn($images);
    }

    /**
     * get_lastApiCalls - Fonction pour récupérer les 50 derniers appels API Set
     * Fonction optimisée SQL
     * 
     * @access public 
     * @author Aldric L.
     * @copyright 2023
     */
    public function get_lastApiCalls(){
        if ($this->level < 4)
            throw new DiamondException("Forbidden access", 706);

        $log = array();
        if (file_exists(ROOT . "logs/api_set.json.log") && json_decode(@file_get_contents(ROOT . "logs/api_set.json.log")) != null)
            $log = json_decode(@file_get_contents(ROOT . "logs/api_set.json.log"), true);

        $log = array_reverse($log);
        $log = array_slice($log, 0, ((sizeof($log) > 50) ? 50 : sizeof($log)));

        //Pour réduire les appels BDD on crée une sorte de cache à pseudos:
        $pseudos = array();

        foreach ($log as $key => $l){
            if (is_array($l) && array_key_exists("user", $l) && is_numeric($l['user']) && !isset($pseudos[intval($log[$key]['user'])])){
                $pseudos[intval($log[$key]['user'])] = $log[$key]['user'] = User::getOnePseudoById($this->getPDO(), intval($log[$key]['user']));

            }else if (is_array($l) && array_key_exists("user", $l) && is_numeric($l['user']) && isset($pseudos[intval($log[$key]['user'])])){
                $log[$key]['user'] = $pseudos[intval($log[$key]['user'])];

            }else if (is_array($l)){
                $log[$key]['user'] = "Utilisateur inconnu ou non-connecté";

            }
        }
        
        return $this->formatedReturn($log);
    }

}