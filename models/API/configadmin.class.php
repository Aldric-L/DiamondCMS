<?php

/**
 * configadmin - API Admin dont l'objectif est de modifier la config du CMS notamment de la BDD ou du forum
 *  
 * @author Aldric L.
 * @copyright 2022
 */
class configadmin extends DiamondAPI {

    public function __construct($paths, $pdo, $controleur, $level){
        parent::__construct($paths, $pdo, $controleur, $level);
        $this->params_needed = array(
            "set_genconfig" => array(),
            "set_bddconfig" => array(),
            "set_tinymceconfig" => array("enable", "key"),
            "set_accueilconfig" => array("desc"),
            "set_serveursconfig" => array(),
            "get_testDBConnection" => array(),
            "set_enjouermodal" => array(),
            "set_jouermodal" => array("text_jouer_menu"),
            "set_enfac" => array(),
            "set_facquestion" => array("question", "reponse"),
            "set_delfacquestion" => array("id")
        );
    }

    /**
     * set_genconfig - Fonction pour éditer la config
     * 
     * ATTENTION : cette fonction présente un danger important parce qu'elle enregistre en config
     * tout l'array $_POST sans distinction (excepté pour les entrées qui ne préexistent pas dans la conf)
     * 
     * @throws 706 : en cas de manquement de diamond_master
     * @access public 
     * @author Aldric L.
     * @copyright 2022
     */
    public function set_genconfig(){
        if ($this->level < 5)
            throw new Exception("Forbidden access", 706);

        $social = array();
        if (array_key_exists("social_yt", $this->args)){
            $social["yt"]= $this->args["social_yt"];
            unset($this->args["social_yt"]);
        }
        if (array_key_exists("social_tw", $this->args)){
            $social["tw"]= $this->args["social_tw"];
            unset($this->args["social_tw"]);
        }
        if (array_key_exists("social_fb", $this->args)){
            $social["fb"]= $this->args["social_fb"];
            unset($this->args["social_fb"]);
        }
        if (array_key_exists("social_gl", $this->args)){
            $social["gl"]= $this->args["social_gl"];
            unset($this->args["social_gl"]);
        }
        if (array_key_exists("social_discord", $this->args)){
            $social["discord"]= $this->args["social_discord"];
            unset($this->args["social_discord"]);
        }
        if (!empty($social)){
            $this->args["Social"]= $social;
        }

        if (array_key_exists("text_jouer_menu", $this->args)){
            $this->args["text_jouer_menu"] = htmlspecialchars($this->args["text_jouer_menu"]);
        }
        if (array_key_exists("text_popup_accueil", $this->args)){
            $this->args["text_popup_accueil"] = htmlspecialchars($this->args["text_popup_accueil"]);
        }
        
        return $this->saveconf("config.ini");
    }

    /**
     * set_bddconfig - Fonction pour modifier la config de la bdd
     * Cette fonction permet de ne modifier qu'un champ ou plusieurs dans la config
     * Toute modification fait l'objet d'un test en testDBConnection
     * 
     * @throws 705 : En cas d'erreur de testDBConnection
     * @throws 706 : en cas de manquement de diamond_master
     * @access public 
     * @author Aldric L.
     * @copyright 2022
     */
    public function set_bddconfig(){
        if ($this->level < 5)
            throw new Exception("Forbidden access", 706);
        
        try {
            $this->testDBConnection();
        }catch (Exception $e){
            throw new Exception($e->getMessage(), 705);
        }
    
        return $this->saveconf("bdd.ini");
    }

    /**
     * set_tinymceconfig - Fonction pour modifier la config de tinymce
     * 
     * @throws 706 : en cas de manquement de diamond_master
     * @param bool enable : activer ou non tiny
     * @param string key : clée unique pour faire fonctionner l'API
     * @access public 
     * @author Aldric L.
     * @copyright 2022
     */
    public function set_tinymceconfig(){
        if ($this->level < 5)
            throw new Exception("Forbidden access", 706);
        if (file_exists(ROOT."config/tinymce.ini")){
            $old_conf = $this->getIniConfig(ROOT."config/tinymce.ini");
        }

        $a = $this->args;
        unset($this->args);
        $this->args['editor'] = $a;
        if (file_exists(ROOT."config/tinymce.ini")){
            $this->args['editor']['def_key'] = $old_conf['editor']['def_key']; 
        }
        $this->setConfig(ROOT."config/tinymce.ini", $this->args, true);
        return $this->formatedReturn(1);
    }

    /**
     * set_accueilconfig - Fonction pour modifier la config de la page accueil
     * 
     * @throws 706 : en cas de manquement de diamond_master
     * @param DIC : un retour de DIC
     * @param string desc : description du serveur
     * @access public 
     * @author Aldric L.
     * @copyright 2022
     */
    public function set_accueilconfig(){
        if ($this->level < 5)
            throw new Exception("Forbidden access", 706);
        if (file_exists(ROOT."config/config.ini")){
            $old_conf = $this->getIniConfig(ROOT."config/config.ini");
        }

        $a['desc'] = $this->args['desc'];
        $a['bg'] = $this->processImgFromDIC();

        $this->setConfig(ROOT."config/config.ini", $a, false);
        return $this->formatedReturn(1);
    }

    /**
     * set_enjouermodal - Fonction pour activer/désactiver le modal jouer
     * 
     * @throws 706 : en cas de manquement de diamond_master
     * @access public 
     * @author Aldric L.
     * @copyright 2022
     */
    public function set_enjouermodal(){
        if ($this->level < 5)
            throw new Exception("Forbidden access", 706);

        $conf = $this->getIniConfig(ROOT."config/config.ini", true);
        $args = array();
        if ($conf['en_jouer']){
            $args['en_jouer'] = false;
        }else {
            $args['en_jouer'] = true;
        }
        $this->setConfig(ROOT."config/config.ini", $args);
        return $this->formatedReturn(1);
    }

    /**
     * set_jouermodal - Fonction pour modifier le texte du modal jouer
     * 
     * @throws 706 : en cas de manquement de diamond_master
     * @param string text_jouer_menu : contenu non traité pour le modal
     * @access public 
     * @author Aldric L.
     * @copyright 2022
     */
    public function set_jouermodal(){
        if ($this->level < 5)
            throw new Exception("Forbidden access", 706);

        return $this->saveconf("config.ini");
    }

    public function set_serveursconfig(){
        if ($this->level < 5)
            throw new Exception("Forbidden access", 706);
        return $this->saveconf("serveurs.ini");
    }

    public function set_enfac(){
        if ($this->level < 4)
            throw new Exception("Forbidden access", 706);

        $conf = $this->getIniConfig(ROOT."config/config.ini", true);
        $args = array();
        if ($conf['en_faq']){
            $args['en_faq'] = false;
            if (!$this->getControleur()->delPage(true, "faq")){
                throw new Exception("Unable to delete a page.", 350);
            }
        }else {
            $args['en_faq'] = true;
            if (!$this->getControleur()->addPage(true, "faq", "F.A.Q.")){
                throw new Exception("Unable to create a page.", 350);
            }
        }
        $this->setConfig(ROOT."config/config.ini", $args);
        return $this->formatedReturn(1);
    }

    /**
     * set_facquestion - Fonction pour ajouter une entrée FAQ
     * 
     * @throws 706 : en cas de manquement au rang d'admin
     * @throws 342 : erreur en insert (simplifySQL)
     * @param string question : question à poser
     * @param string reponse : réponse à apporter
     * @access public 
     * @author Aldric L.
     * @copyright 2022
     */
    public function set_facquestion(){
        if ($this->level < 4)
            throw new Exception("Forbidden access", 706);

        if (simplifySQL\insert($this->getPDO(), "d_faq", array("question", "reponse"), array($this->args['question'], $this->args['reponse'])) != true){
            throw new Exception("Unable to insert", 342);
        }
        return $this->formatedReturn(1);
    }

    /**
     * set_delfacquestion - Fonction pour supprimer une entrée FAQ
     * 
     * @throws 706 : en cas de manquement au rang d'admin
     * @throws 341 : erreur en delete (simplifySQL)
     * @param int id : id de la question à supprimer en BDD
     * @access public 
     * @author Aldric L.
     * @copyright 2022
     */
    public function set_delfacquestion(){
        if ($this->level < 4)
            throw new Exception("Forbidden access", 706);

        if (simplifySQL\delete($this->getPDO(), "d_faq", array(array("id", "=", $this->args["id"]))) != true){
            throw new Exception("Unable to delete", 341);
        }

        return $this->formatedReturn(1);
    }

    /**
     * get_testDBConnection - Fonction pour tester la connexion à la BDD selon la config actuelle
     * L'objet de cette fonction est de ne pas lever d'erreur mais de les faire passer en formated return
     * 
     * @access public 
     * @author Aldric L.
     * @copyright 2022
     */
    public function get_testDBConnection(){
        try {
            $this->testDBConnection();
        }catch (Exception $e){
            return $this->formatedReturn($e->getMessage());
        }
        return $this->formatedReturn("Connexion réussie.");
    }

    /**
     * testDBConnection - Fonction pour tester la connexion de la BDD en faisant ou non 
     * un ou plusieurs changements dans la config par $this->args
     * Attention, elle doit toujours être utilisée dans un try catch
     * 
     * @access private 
     * @author Aldric L.
     * @copyright 2022
     * @return void
     */
    private function testDBConnection(){
        $data = $this->getIniConfig(ROOT."config/bdd.ini");
        
        if ($this->args != null && !empty($this->args)){
            foreach ($data as $key => $d) {
                if (array_key_exists($key, $this->args)){
                    $data[$key] = $this->args[$key];
                }
            }
        }

        $bddtest = new BDD($data, true);
        $test = $bddtest->testPDO();
    }

}