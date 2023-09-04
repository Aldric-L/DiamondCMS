<?php 
/**
 * editing - API Admin permettant de modifier les pages
 * Nécessite le grade admin minimum
 *  
 * @author Aldric L.
 * @copyright 2022
 */
class editing extends DiamondAPI {
    public function __construct($paths, $pdo, $controleur, $level){
        parent::__construct($paths, $pdo, $controleur, $level);
        $this->params_needed = array(
            "set_editSimplePage" => array("name", "content"),
            "set_editPage" => array("name", "content"),
            "set_startEditing" => array(),
            "set_stopEditing" => array(),
            "set_deleteModule" => array("mod_name", "mod_key", "mm"),
            "set_addModule" => array("mod_name", "mm"),
            "set_changeModulePos" => array("mod_name", "mm", "cur_pos", "new_pos"),
            "set_whyAreWeBetterConfig" => array("icon", "title", "desc", "mm"),
            "set_TextZoneConfig" => array("content", "mm"),
        );
        if ($this->level < 4)
            throw new Exception("Forbidden access", 706);
    }

    /**
     * set_editPage - Fonction pour modifier une page
     * Cette fonction permet de dispacher la demande vers la fonction appropriée en fonction de la page dont il s'agit
     * 
     * @param string name : nom de la page
     * @param string content : contenu à écrire à la place
     * @access public 
     * @author Aldric L.
     * @copyright 2022
     */
    public function set_editPage(){
        $this->cleanArg($this->args['content']);

        if ($this->args['name'] == "forum" || $this->args['name'] == "boutique" || $this->args['name'] == "getmoney" || $this->args['name'] == "accueil"){
            if (!file_exists(ROOT . 'config/' . $this->args['name'] . '.ftxt'))
                throw new Exception("Unable to find the page's config file", 131);

            $fp = fopen (ROOT . "config/" . $this->args['name'] . ".ftxt", "w");
            if (!$fp)
                throw new Exception("Unable to open the page's config file", 111);

            fseek ($fp, 0);
            fputs ($fp, $this->args['content']);
            fclose ($fp);
            return $this->formatedReturn(1);
        }
        else if ($this->args['name'] == "footer"){
            $this->setConfig(ROOT."config/config.ini", array("about_footer" => $this->args['content']));
            return $this->formatedReturn(1);
        }
        else {
            return $this->set_editSimplePage();
        }
    }

    /**
     * set_editSimplePage - Fonction pour modifier une page custom
     * Il n'est pas recommandé de l'utiliser seule car il faut être certain que la page a bien été créée par l'utilisateur et n'est pas une page système
     * 
     * @param string name : nom de la page
     * @param string content : contenu à écrire à la place
     * @access public non recommandé
     * @author Aldric L.
     * @copyright 2022
     */
    public function set_editSimplePage(){
        $this->cleanArg($this->args['content']);
        
        //On commence par chercher la page à modifier
        $result = simplifySQL\select($this->getControleur()->bddConnexion(), true, "d_pages", "*", array(array("name", "=", $this->args["name"])));
        if (is_array($result)){
            $file = $result['file_name'];
            $page_name = $this->args['name'];
            $page_raw = $result['name_raw'];
            if (!file_exists(ROOT . 'config/' . $file . '.ftxt')){
                throw new Exception("Unable to find the page's config file", 131);
            }

            $fp = fopen (ROOT . "config/" . $file . ".ftxt", "w");
            if (!$fp){
                throw new Exception("Unable to open the page's config file", 111);
            }
            fseek ($fp, 0);
            fputs ($fp, $this->args['content']);
            fclose ($fp);
            return $this->formatedReturn(1);
        }else {
            throw new Exception("Unable to find the page", 353);
        }
    }

    /**
     * set_startEditing - Fonction pour lancer le mode d'édition
     * 
     * @access public 
     * @author Aldric L.
     * @copyright 2022
     */
    public function set_startEditing(){
        $_SESSION['editing_mode'] = true;
        return $this->formatedReturn(1);
    }

    /**
     * set_startEditing - Fonction pour lancer le mode d'édition
     * 
     * @access public 
     * @author Aldric L.
     * @copyright 2022
     */
    public function set_stopEditing(){
        $_SESSION['editing_mode'] = false;
        return $this->formatedReturn(1);
    }

    /**
     * set_deleteModule - Fonction pour supprimer un module sur une page qui charge ModulesManager
     * 
     * @access public 
     * @param string mod_name : nom concatené du module 
     * @param int mod_key : array key du module 
     * @param string mm : nom concatené de la page (instance ModulesManager)
     * @author Aldric L.
     * @copyright 2023
     */
    public function set_deleteModule(){
        if (!ModulesManager\ModulesManager::isThereAModulesManager($this->args['mm']))
            throw new DiamondException("Unable to find ModulesManager", "native$802");

        if (!is_numeric($this->args['mod_key']))
            throw new DiamondException("Module key should be an integer", 701);
        
        $mm = $this->getControleur()->getOneModulesManager($this->args['mm']);
        $mm->deleteOneModule($this->args['mod_name'], intval($this->args['mod_key']));
        return $this->formatedReturn(1);
    }

    /**
     * set_addModule - Fonction pour ajouter un module sur une page qui charge ModulesManager
     * 
     * @access public 
     * @param string mod_name : nom concatené du module 
     * @param string mm : nom concatené de la page (instance ModulesManager)
     * @author Aldric L.
     * @copyright 2023
     */
    public function set_addModule(){
        if (!ModulesManager\ModulesManager::isThereAModulesManager($this->args['mm']))
            throw new DiamondException("Unable to find ModulesManager", "native$802");
        
        $mm = $this->getControleur()->getOneModulesManager($this->args['mm']);
        $mm->addOneModule($this->getPDO(), $this->args['mod_name']);
        return $this->formatedReturn(1);
    }


    /**
     * set_changeModulePos - Fonction pour changer un module de place sur une page qui charge ModulesManager
     * 
     * @access public 
     * @param string mod_name : nom concatené du module 
     * @param string mm : nom concatené de la page (instance ModulesManager)
     * @param int cur_pos : array key actuelle
     * @param int new_pos : array key souhaitée
     * @author Aldric L.
     * @copyright 2023
     */
    public function set_changeModulePos(){
        if (!ModulesManager\ModulesManager::isThereAModulesManager($this->args['mm']))
            throw new DiamondException("Unable to find ModulesManager", "native$802");

        if (!is_numeric($this->args['cur_pos']) || !is_numeric($this->args['new_pos']))
            throw new DiamondException("Module key should be an integer", 701);
        
        $mm = $this->getControleur()->getOneModulesManager($this->args['mm']);
        $mm->changeModuleKeyInConfig($this->args['mod_name'], intval($this->args['cur_pos']), intval($this->args['new_pos']));
        return $this->formatedReturn(1);
    }


    /**
     * set_whyAreWeBetterConfig - Fonction pour éditer la configuration du module graphique WhyAreWeBetter destiné à être utilisé sur un ModulesManager
     * 
     * @access public 
     * @param string mm : nom concatené de la page (instance ModulesManager)
     * @param string icon : array (indexé : 1,2,3) contenant toutes les icones ou img
     * @param string title : array (indexé : 1,2,3) contenant toutes les titres
     * @param string desc : array (indexé : 1,2,3) contenant toutes les description
     * @author Aldric L.
     * @copyright 2023
     */
    public function set_whyAreWeBetterConfig(){
        if (!ModulesManager\ModulesManager::isThereAModulesManager($this->args['mm']))
            throw new DiamondException("Unable to find ModulesManager", "native$802");

        $conf = $this->getControleur()->getOneModulesManager($this->args['mm'])->getMyConfig("WhyAreWeBetter", "gen_whyarewebetter.json");
        $conf = json_decode($conf, true);
        $this->args = cleanIniTypes($this->args);

        foreach ($conf as $key => &$c){
            if (is_array($c) && substr($key, 0, 3) == "col" && strlen($key) === 5){
                $id = intval(substr($key, 4, 1));
                foreach ($c as $col_name => $val){
                    $c[$col_name] = $this->args[$col_name][$id];
                }
            }
            if ($key == "animations" && isset($this->args["animations"]) && is_bool($this->args["animations"]))
                $c = $this->args["animations"];

        }
        
        $this->getControleur()->getOneModulesManager($this->args['mm'])->setMyConfig("WhyAreWeBetter", "gen_whyarewebetter.json", json_encode($conf, JSON_PRETTY_PRINT));
        $this->getControleur()->getOneModulesManager($this->args['mm'])->deleteOnesModuleCache("WhyAreWeBetter");
        return $this->formatedReturn(1);
    }

    /**
     * set_TextZoneConfig - Fonction pour éditer la configuration du module graphique textZone destiné à être utilisé sur un ModulesManager
     * 
     * @access public 
     * @param string mm : nom concatené de la page (instance ModulesManager)
     * @param string content : le contenu de la zone de texte
     * @author Aldric L.
     * @copyright 2023
     */
    public function set_TextZoneConfig(){
        if (!ModulesManager\ModulesManager::isThereAModulesManager($this->args['mm']))
            throw new DiamondException("Unable to find ModulesManager", "native$802");

        $conf = $this->getControleur()->getOneModulesManager($this->args['mm'])->getMyConfig("TextZone", "textzone.ftxt");
        if ($conf != $this->args['content']){
            $this->getControleur()->getOneModulesManager($this->args['mm'])->setMyConfig("TextZone", "textzone.ftxt", $this->args['content']);
            $this->getControleur()->getOneModulesManager($this->args['mm'])->deleteOnesModuleCache("TextZone");
        }
         
        return $this->formatedReturn(1);
    }

}