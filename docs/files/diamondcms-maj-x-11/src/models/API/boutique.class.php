<?php

class boutique extends DiamondAPI {

    public function __construct(array $paths, PDO $pdo, Controleur $controleur, int $level){
        parent::__construct($paths, $pdo, $controleur, $level);
        $this->params_needed = array(
            "set_delCategory" => array("id"),
            "set_genConfig" => array("money_name", "money_sym", "money"),
            "set_addCategory" => array("cat_name"),
            "set_enBoutique" => array("en_boutique"),
            "set_addPayPalOffer" => array("name", "price", "tokens"),
            "set_delPayPalOffer" => array("id"),
            "set_PayPalConfig" => array("en_paypal"),
            "set_DDPConfig" => array("en_ddp"),
            "set_delArticle" => array("id_article"),
            "set_addArticle" => array("name", "description", "cat", "prix"),
            "set_modArticle" => array("id_article"),
            "set_delTask" => array("id_task"),
            "set_stopTask" => array("id_task"),
            "set_completeTask" => array("id_task"),
            "get_imgAvailable" => array()
        );
    }

    public function set_delCategory(){
        if ($this->level <= 4)
            throw new Exception("Forbidden access", 706);

        if (!is_numeric($this->args['id']))
            throw new DiamondException("An id is an integer", 701);

        if (simplifySQL\delete($this->getPDO(), "d_boutique_cat", array(array("id", "=", $this->args['id']))) != true)
            throw new DiamondException("Unable to delete requested category", "341b");

        //On met en archive tous les articles de la catégorie
        @simplifySQL\update($this->getPDO(), "d_boutique_articles", array(array("archive", "=", 1)), array(array("cat", "=", $this->args['id'])));
        
        return $this->formatedReturn(1);
    }

    public function set_genConfig(){
        if ($this->level <= 4)
            throw new Exception("Forbidden access", 706);

        $args = array("money" => htmlspecialchars($this->args['money_sym']),
                      "money_name" => htmlspecialchars($this->args['money_name']),
                      "Serveur_money" => htmlspecialchars($this->args['money']));
        $this->setConfig(ROOT."config/config.ini", $args);
        return $this->formatedReturn(1);
    }

    public function set_addCategory(){
        if ($this->level <= 4)
            throw new Exception("Forbidden access", 706);

        if (is_array(simplifySQL\select($this->getPDO(), true, "d_boutique_cat", "*", array(array("name", "=", htmlspecialchars($this->args['cat_name']))))))
            throw new DiamondException("Unable to add requested category", "360");  

        if (simplifySQL\insert($this->getPDO(), "d_boutique_cat", array("name"), array(htmlspecialchars($this->args['cat_name']))) != true)
            throw new DiamondException("Unable to add requested category", "342c");    

        return $this->formatedReturn(1);
    }

    public function set_enBoutique(){
        if ($this->level <= 4)
            throw new Exception("Forbidden access", 706);

        $this->args = cleanIniTypes($this->args);
        $args = array("en_boutique" => $this->args['en_boutique']);
        if (isset($this->args['boutique_ext']) && is_bool($this->args['boutique_ext'])){
            if ($this->args['boutique_ext'] && (!isset($this->args['link_boutique_externe']) || empty($this->args['link_boutique_externe'])))
                throw new DiamondException("No outside shop without link", "701");  
            else if (!$this->args['boutique_ext'])
                $args["boutique_ext"] = array("en_boutique_externe" => $this->args['boutique_ext'], "link_boutique_externe" => (isset($this->getIniConfig(ROOT . "config/config.ini")['boutique_ext']['link_boutique_externe'])) ? $this->getIniConfig(ROOT . "config/config.ini")['boutique_ext']['link_boutique_externe'] : "null");
            else if ($this->args['boutique_ext'] && $this->args['en_boutique'])
                throw new DiamondException("Cannot enable both default and outside shops", "361");  
            else if (isset($this->args['link_boutique_externe']) && $this->args['boutique_ext'] && !$this->args['en_boutique'])
                $args["boutique_ext"] = array("en_boutique_externe" => $this->args['boutique_ext'], "link_boutique_externe" => $this->args['link_boutique_externe']);
        }
        $this->setConfig(ROOT."config/config.ini", $args);
        return $this->formatedReturn(1);
    }

    public function set_addPayPalOffer(){
        if ($this->level <= 4)
            throw new Exception("Forbidden access", 706);

        if (!is_numeric($this->args['price']) || $this->args['price'] <= 0  || !is_numeric($this->args['tokens']) || $this->args['tokens'] < 1)
            throw new DiamondException("A price is an integer", 701);
        
        if (simplifySQL\insert($this->getPDO(), "d_boutique_paypal_offres", 
            array("name", "price", "tokens", "uuid"), 
            array($this->args['name'], $this->args['price'], $this->args['tokens'], uniqid())) != true){
                throw new DiamondException("SQL INSERT error", "342c");
        }
        return $this->formatedReturn(1);   
    }

    public function set_delPayPalOffer(){
        if ($this->level <= 4)
            throw new Exception("Forbidden access", 706);

        if (!is_numeric($this->args['id']))
            throw new DiamondException("An id is an integer", 701);
        
        if (simplifySQL\delete($this->getPDO(), "d_boutique_paypal_offres", array(array("id", "=", $this->args['id']))) != true)
            throw new DiamondException("SQL DELETE error", "341b");

        return $this->formatedReturn(1);   
    }

    
    public function set_PayPalConfig(){
        if ($this->level <= 4)
            throw new Exception("Forbidden access", 706);

        $this->args = cleanIniTypes($this->args);
        if (!is_bool($this->args['en_paypal']))
            throw new DiamondException("A bool is a bool (en_paypal)", 701);
        
        if ($this->args['en_paypal'] && !(array_key_exists("sandbox", $this->args) && array_key_exists("money", $this->args) && array_key_exists("id", $this->args) && array_key_exists("secret", $this->args) && !empty($this->args['id']) && !empty($this->args['secret'])))
            throw new DiamondException("Missing arguments to enable Paypal payments", 701);
        
        $tmp_boutique['PayPal']['en_paypal'] = $this->args['en_paypal'];
        if (isset($this->args['sandbox']) && is_bool($this->args['en_paypal'])){
            $tmp_boutique['PayPal']['sandbox'] = $this->args['sandbox'];
        }        
        if (isset($this->args['money']) && is_numeric($this->args['money'])){
            $tmp_boutique['PayPal']['money'] = $this->args['money'];
        }    
        if (array_key_exists('id', $this->args)){
            $tmp_boutique['PayPal']['id'] = $this->args['id'];
        }   
        if (array_key_exists('secret', $this->args)){
            $tmp_boutique['PayPal']['secret'] = $this->args['secret'];
        }       
        $this->setConfig(ROOT."config/boutique.ini", $tmp_boutique);
        return $this->formatedReturn(1);
    }

    public function set_DDPConfig(){
        if ($this->level <= 4)
            throw new Exception("Forbidden access", 706);

        $this->args = cleanIniTypes($this->args);
        if (!is_bool($this->args['en_ddp']))
            throw new DiamondException("A bool is a bool (en_ddp)", 701);
        
        if ($this->args['en_ddp'] && !(array_key_exists("public_key", $this->args) && array_key_exists("private_key", $this->args) && !empty($this->args['public_key']) && !empty($this->args['private_key'])))
            throw new DiamondException("Missing arguments to enable DDP payments", 701);
        
        $tmp_boutique['DediPass']['en_ddp'] = $this->args['en_ddp'];
        if (array_key_exists('public_key', $this->args)){
            $tmp_boutique['DediPass']['public_key'] = $this->args['public_key'];
        }   
        if (array_key_exists('private_key', $this->args)){
            $tmp_boutique['DediPass']['private_key'] = $this->args['private_key'];
        }       
        $this->setConfig(ROOT."config/boutique.ini", $tmp_boutique);
        return $this->formatedReturn(1);
    }

    public function set_delArticle(){
        if ($this->level <= 4)
            throw new Exception("Forbidden access", 706);

        $this->args = cleanIniTypes($this->args);
        if (!is_numeric($this->args['id_article']))
            throw new DiamondException("An int is an int (id_article)", 701);
            
        $id_cat = simplifySQL\select($this->getPDO(), true, "d_boutique_articles", "id, cat, img", array(array("id", "=", $this->args['id_article'])));
        if ((is_array($id_cat) && empty($id_cat)) || (is_bool($id_cat) && !$id_cat) || !array_key_exists('cat', $id_cat) || !is_numeric($id_cat['cat']))
            throw new DiamondException("Article unknown", 370);

        $nb_cat = simplifySQL\select($this->getPDO(), true, "d_boutique_cat", "nb_articles", array(array("id", "=", intval($id_cat['cat']))));                    
        if ((is_array($nb_cat) && empty($nb_cat)) || (is_bool($nb_cat) && !$nb_cat))
            throw new DiamondException("Categroy unknown", 371);

        @unlink(ROOT . 'views/uploads/img/' . $id_cat['img']);

        //On vérifie si cet article a déjà été acheté
        $commandes = simplifySQL\select($this->getPDO(), false, "d_boutique_achats", "*", array(array("id_article", "=", $this->args['id_article'])));
        if ($commandes !=  false && !empty($commandes)){
            //On archive alors l'article
            if (!simplifySQL\update($this->getPDO(), "d_boutique_articles", array(array("archive", "=", 1)), array(array("id", "=", $this->args['id_article']))))
                throw new DiamondException("SQL UPDATE error", "342c");
        //Si l'article n'a jamais été acheté
        }else {
            //On le supprime
            if (simplifySQL\delete($this->getPDO(), "d_boutique_articles", array(array("id", "=", $this->args['id_article']))) != true)
                throw new DiamondException("SQL DELETE error", "341b");
        } 

        //Dans tous les cas, on modifie le nb d'article dans la catégorie, et on supprime toutes les taches associées/
        if (!simplifySQL\update($this->getPDO(), "d_boutique_cat", 
            array(array("nb_articles", "=", intval($nb_cat['nb_articles'])-1)), 
            array( array( "id", "=", intval($id_cat['cat'])) ) ) )
            throw new DiamondException("Error while updating category", "342a");
        
        if (simplifySQL\delete($this->getPDO(), "d_boutique_cmd", array(array("id_article", "=", $id_cat['id']))) != true)
            throw new DiamondException("Error while deleting tasks", "341b");
            
        return $this->formatedReturn(1);
    }

    public function set_addArticle(){
        if ($this->level <= 4)
            throw new Exception("Forbidden access", 706);
            
        $img = $this->processImgFromDIC("boutique");
        $this->args = cleanIniTypes($this->args);

        if (!is_numeric($this->args['prix']) || !(intval($this->args['prix']) > 0)){
            throw new Exception("A price is an int", 701);
        }  
        if (!is_numeric($this->args['cat']) ){
            throw new Exception("A category id is an int", 701);
        }else {
            $this->args['cat'] = intval($this->args['cat']);
            $cat_nb_articles = simplifySQL\select($this->getPDO(), true, "d_boutique_cat", "id, nb_articles", array(array("id", "=", $this->args['cat'])));
            if ((is_bool($cat_nb_articles) && $cat_nb_articles == false) || (is_array($cat_nb_articles) && empty($cat_nb_articles)))
                throw new Exception("Category requested is unreachable", 152);
        }      
        
        $this->cleanArg($this->args['description']);

        if ( !simplifySQL\insert( $this->getPDO(), "d_boutique_articles", 
            array('name', 'description', 'img', 'prix', 'date_ajout', 'cat'), 
            array(htmlspecialchars($this->args['name']), htmlspecialchars($this->args['description']), $img, $this->args['prix'], date("Y-m-d"), $this->args['cat']) ) ){
            throw new DiamondException("Error while inserting", "342c");
        }
        
        if (!simplifySQL\update($this->getPDO(), "d_boutique_cat", array(array("nb_articles", "=", intval($cat_nb_articles['nb_articles'])+1)), array( array( "id", "=", intval($this->args['cat']) ) ) ))
            throw new DiamondException("Error while updating category", "342a");
        
        $id = simplifySQL\select($this->getPDO(), true, "d_boutique_articles", "*", array(array("id", ">=", "all"), "AND", array("name", "=", htmlspecialchars($this->args['name']))), "id", true);
        if ((is_bool($id) && $id == false) || (is_array($id) && empty($id)))
            throw new Exception("Unable to find back the article", 121);

        $this->processWithNewTasks(intval($id['id']), 
                                (isset($this->args['nb_man_tasks']) && is_numeric($this->args['nb_man_tasks'])) ? intval($this->args['nb_man_tasks']) : 0,
                                (isset($this->args['nb_auto_tasks']) && is_numeric($this->args['nb_auto_tasks'])) ? intval($this->args['nb_auto_tasks']) : 0);
        
        return $this->formatedReturn(1);
    }

    public function set_modArticle(){
        if ($this->level <= 4)
            throw new Exception("Forbidden access", 706);
            
        if (!is_numeric($this->args['id_article']) || !(intval($this->args['id_article']) > 0))
            throw new Exception("An id is an int", 701);
    
        $this->args = cleanIniTypes($this->args);
        $available_fields = array("name" => "string", "description" => "string", "prix" => "int", "cat" => "int");
        $setter = array();

        foreach ($available_fields as $key => $type) {
            if (isset($this->args[$key]) 
            && (($type == "string" && is_string($this->args[$key]))
            || ($type == "int" && is_numeric($this->args[$key]))
            || ($type == "bool" && is_bool($this->args[$key]))))
                array_push($setter, array($key, "=", $this->args[$key]));
        }

        if (isset($setter['description']))
            $this->cleanArg($setter['description']);

        if (!((is_array($setter) && empty($setter)) || (is_bool($setter) && !$setter))){
            try{
                if (!simplifySQL\update($this->getPDO(), "d_boutique_articles", $setter, 
                    array(array("id", "=", intval($this->args['id_article'])))))
                    throw new DiamondException("Error while updating article", "342a");
            }catch (Exception $e){
                throw new DiamondException("Error while updating article (" . $e->getMessage() . ")", "342a");
            }
        }
        
    
        if (!isset($this->args['nb_man_tasks']) || !is_numeric($this->args['nb_man_tasks']) 
        || !isset($this->args['nb_auto_tasks']) || !is_numeric($this->args['nb_auto_tasks'])
        || (!isset($this->args['man_cmd']) && !isset($this->args['cmd'])))
            return $this->formatedReturn(1);
        
        $this->processWithNewTasks(intval($this->args['id_article']), 
                                (isset($this->args['nb_man_tasks']) && is_numeric($this->args['nb_man_tasks'])) ? intval($this->args['nb_man_tasks']) : 0,
                                (isset($this->args['nb_auto_tasks']) && is_numeric($this->args['nb_auto_tasks'])) ? intval($this->args['nb_auto_tasks']) : 0);
        
        return $this->formatedReturn(1);
    }

    private function processWithNewTasks($id_article, $nb_man_tasks, $nb_auto_tasks){
        if ($nb_man_tasks > 0 && isset($this->args['man_cmd'])){
            for ($i = 0; $i < $nb_man_tasks; $i++){
                if (isset($this->args['man_cmd'][$i]) && !empty($this->args['man_cmd'][$i])){
                    try{
                        if (!simplifySQL\insert($this->getPDO(), "d_boutique_cmd",
                            array("cmd", "connexion_needed", "id_article", "is_manual"),
                            array($this->args['man_cmd'][$i], false, $id_article, 1)))
                            
                        throw new DiamondException("Unable to add manual task", "342c");   
                    }catch (Exception $e){
                        throw new DiamondException("Unable to add manual task (2)", "342c");   
                    }
                                  
                }
            }
        }

        if ($nb_auto_tasks > 0 && isset($this->args['cmd'])){
            if (defined("DServerLink") && DServerLink == true){
                $cm = new \DServerLink\ConfigManager();
                $serveurs = $cm->getConfig();
            }
            for ($i = 0; $i < $nb_auto_tasks; $i++){
                if (isset($this->args['cmd'][$i]) && !empty($this->args['cmd'][$i]) &&
                isset($this->args['server'][$i]) && !empty($this->args['server'][$i])){
                    if (!($this->args['server'][$i] == "-1" || $this->args['server'][$i] == -1) && !(defined("DServerLink") && DServerLink == true))
                        throw new DiamondException("Unable to find DSL", "native$707");  

                    //On commence par vérifier que le serveur demandé existe et est bien activé
                    if (($_POST['server'][$i] == "-1" || $_POST['server'][$i] == -1) || (isset($serveurs[intval($_POST['server'][$i])]) && $serveurs[intval($_POST['server'][$i])]['enabled'])){
                        //Si c'est le cas, on regarde si on doit obliger le joueur à être connecté pour recevoir son dû
                        $mustbeconnected = false;
                        if (is_array($this->args['mustbe_connected']) && isset($this->args['mustbe_connected'][$i]) && !empty($this->args['mustbe_connected'][$i]) && $this->args['mustbe_connected'][$i]){
                            $mustbeconnected = true;
                        }

                        try{
                            //On enregistre donc bien la commande
                            if (!simplifySQL\insert($this->getPDO(), "d_boutique_cmd",
                                array("cmd", "connexion_needed", "server", "id_article", "is_manual"),
                                array($this->args['cmd'][$i], $mustbeconnected, $this->args['server'][$i], $id_article, 0))){
                                    throw new DiamondException("Unable to add auto task", "342c");                 
                            }  
                        }catch (Exception $e){
                            throw new DiamondException("Unable to add auto task (2)", "342c");                 
                        }
                        
                    }else {
                        throw new DiamondException("Requested server must be enabled", 480);                 
                    }
                }
            }
        }
    }

    public function set_delTask(){
        if ($this->level <= 4)
            throw new Exception("Forbidden access", 706);

        $this->args = cleanIniTypes($this->args);
        if (!is_numeric($this->args['id_task']))
            throw new DiamondException("An int is an int (id_task)", 701);

        
        //On commence par vérifier que la tâche n'est pas associée à une commande
        $tasks = simplifySQL\select($this->getPDO(), false, "d_boutique_todolist", "*", array( array( "cmd", "=", intval($this->args['id_task']) ) ));
        if (!empty($tasks)){
            foreach ($tasks as $task){
                try{
                    if (!simplifySQL\update($this->getPDO(), "d_boutique_todolist", 
                        array(
                            array("done", "=", 1), 
                            array("date_done", "=", date("Y-m-d H:i:s")),
                            array("stopped", "=", 1),
                            array("stopped_reason", "=", "La tâche a été supprimée de l'article. (" . $_SESSION['user']->getPseudo() . ").")
                        ), array(array("id", "=", $task['id'])))){
                            throw new DiamondException("Error while terminating a task", "342a");
                    }
                }catch (Exception $e){
                    throw new DiamondException("Error while terminating a task (2)", "342a");
                }
                
                //Mais, attention, si c'est la dernière tâche, on doit clore la commande
                $ts = simplifySQL\select($this->getPDO(), false, "d_boutique_todolist", "*", array(array("id_commande", "=", $task['id_commande']), "AND", array("done", "=", 0)));
                if (empty($ts)){
                    try{
                        if (!simplifySQL\update($this->getPDO(), "d_boutique_achats", 
                            array(
                                array("success", "=", 1)
                            ), array(array("id", "=", $task['id_commande'])))){
                                throw new DiamondException("Error while terminating an order", "342a");
                        }
                    }catch (Exception $e){
                        throw new DiamondException("Error while terminating an order (2)", "342a");
                    }
                }
            }
            try{
                if (!simplifySQL\update($this->getPDO(), "d_boutique_cmd", 
                    array(
                        array("archive", "=", 1)
                    ), array(array("id", "=", $this->args['id_task'] )))){
                        throw new DiamondException("Error while filing task", "342a");
                }
            }catch (Exception $e){
                throw new DiamondException("Error while filing task (2)", "342a");
            }
        }else {
            try{
                //Si la tâche est associée à aucune commande, on peut la supprimer purement et simplement.
                if (simplifySQL\delete($this->getPDO(), "d_boutique_cmd", array(array("id", "=", $this->args['id_task']))) != true)
                    throw new DiamondException("Error while deleting the task", "341b");

            }catch (Exception $e){
                throw new DiamondException("Error while deleting the task (2)", "341b");
            }
            
        }
        
        return $this->formatedReturn(1);
    }

    public function set_stopTask(){
        if ($this->level <= 4)
            throw new Exception("Forbidden access", 706);

        $this->args = cleanIniTypes($this->args);
        if (!is_numeric($this->args['id_task']))
            throw new DiamondException("An int is an int (id_task)", 701);

        $this->stopOrCompleteTask(true);

        return $this->formatedReturn(1);
    }

    public function set_completeTask(){
        if ($this->level <= 4)
            throw new Exception("Forbidden access", 706);

        $this->args = cleanIniTypes($this->args);
        if (!is_numeric($this->args['id_task']))
            throw new DiamondException("An int is an int (id_task)", 701);
        
        $this->stopOrCompleteTask(false);

        return $this->formatedReturn(1);
    }

    private function stopOrCompleteTask($force){
        if ($this->level <= 4)
            throw new Exception("Forbidden access", 706);
            
        //On commence par essayer de mieux connaitre la tache
        $task = simplifySQL\select($this->getPDO(), true, "d_boutique_todolist", "*", array( array( "id", "=", $this->args['id_task'] ) ));
        if (!is_array($task) || is_bool($task) || empty($task))
            throw new DiamondException("Error while searching the task", 372);

        try{
            if ($force){
                if (!simplifySQL\update($this->getPDO(), "d_boutique_todolist", 
                array(
                    array("done", "=", 1), 
                    array("date_done", "=", date("Y-m-d H:i:s")),
                    array("stopped", "=", 1),
                    array("stopped_reason", "=", "Tâche interrompue par un administrateur (" . $_SESSION['user']->getPseudo() . ").")
                ), array(array("id", "=", $this->args['id_task'])))){
                    throw new DiamondException("Error while stopping the task", 373);
                }
            }else {
                if (!simplifySQL\update($this->getPDO(), "d_boutique_todolist", 
                array(
                    array("done", "=", 1), 
                    array("date_done", "=", date("Y-m-d H:i:s"))
                ), array(array("id", "=", $this->args['id_task'])))){
                    throw new DiamondException("Error while stopping the task", 373);
                }
            }
        }catch (Exception $e){
            throw new DiamondException("Error while stopping the task (2)", 373);
        }

        try{
            //Mais, attention, si c'est la dernière tâche, on doit clore la commande
            $tasks = simplifySQL\select($this->getPDO(), false, "d_boutique_todolist", "*", array(array("id_commande", "=", intval($task['id_commande'])), "AND", array("done", "=", 0)));
            if (is_array($tasks) && empty($tasks) || (is_bool($tasks) && !$tasks)){
                if (!simplifySQL\update($this->getPDO(), "d_boutique_achats", 
                    array(
                        array("success", "=", 1)
                    ), array(array("id", "=", $task['id_commande'])))){
                    throw new DiamondException("Unable to complete customer's order", 374);
                }
            }
        }catch (Exception $e){
            throw new DiamondException("Unable to complete customer's order (2)", 374);
        }
    }

    /**
     * get_imgAvailable - Fonction pour récupérer les images enregistrées sur le serveur dans la partie "articles"
     * Cette fonction est prévue pour fonctionner avec DIC
     * 
     * @access public 
     * @author Aldric L.
     * @copyright 2023
     */
    public function get_imgAvailable(){
        $height = 0;
        $width = 0;
        if (isset($this->args['height']) && is_numeric($this->args['height'])){
            $height = intval($this->args['height']);
        }
        if (isset($this->args['width']) && is_numeric($this->args['width']) && $height === intval($this->args['width'])){
            $width = intval($this->args['width']);
        }

        $images = array();
        if (!file_exists(ROOT . 'views/uploads/img/boutique/'))
            mkdir(ROOT . 'views/uploads/img/boutique/');

        if ($dir = opendir(ROOT . 'views/uploads/img/boutique/')) {
            while($file = readdir($dir)) {
              //On n'ouvre surtout pas les sous-dossiers
              if(!is_dir(ROOT . 'views/uploads/img/boutique/' . $file) && !in_array($file, array(".","..")) && $file != "locked_files.dfiles") {
                $currentWidth = 0;
                $currentHeight = 0;

                if (substr(mime_content_type(ROOT . 'views/uploads/img/boutique/' . $file), 0, 5) === "image"){
                    try {
                        list($currentWidth, $currentHeight) = getimagesize(ROOT . 'views/uploads/img/boutique/' . $file);
                    }catch (Exception $e){}catch (Error $e){}
                

                    //On ne garde que les images carrées, ou presque c'est à dire à moins de 10% d'écart entre width et height
                    if (($currentHeight !== 0 && $currentWidth !== 0) 
                    && ($currentHeight/$currentWidth >= 0.9 && $currentHeight/$currentWidth <= 1.1)){
                        $n = explode(".", $file);    
                        $ext = $n[sizeof($n)-1];
                        if ($ext == "png" || $ext == "jpg" || $ext == "jpeg"){
                            $name_raw = "";
                            for ($i=0; $i < sizeof($n)-1; $i++){
                                $name_raw .= $n[$i];
                                if ($i !== sizeof($n)-2)
                                    $name_raw .= ".";
                            }
                            $source_link = LINK . "getimage/" . $ext . str_replace(" ", "", ' /boutique/ ') . $name_raw ; 
                            if (isset($height))
                                $source_link .= "/" . $height; 
                            if (isset($width))
                                $source_link .= "/" . $width; 
                    
                            array_push($images, 
                                array("filename" => $file, 
                                    "path" => ROOT . 'views/uploads/img/boutique/' . $file, 
                                    "last_modified" => filemtime(ROOT . 'views/uploads/img/boutique/' . $file), 
                                    "file_human_size" => FileSizeConvert( filesize(ROOT . 'views/uploads/img/boutique/' . $file) ),
                                    "file_size" => filesize(ROOT . 'views/uploads/img/boutique/' . $file),
                                    "ext" => $ext,
                                    "filename_without_ext" => $name_raw,
                                    "source_link" => $source_link
                            ) );
                            $i = $i+1;
                        }
                    }
                }
              }
            }
            closedir($dir);
        }
        return $this->formatedReturn($images);
    }
}