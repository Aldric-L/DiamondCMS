<?php 

/**
 * Forum - API qui gère l'utilisation courante du forum par les membres (edition des posts, commentaires etc...)
 *  
 * @author Aldric L.
 * @copyright 2023
 */
class forum extends DiamondAPI {
    public function __construct($paths, $pdo, $controleur, $level){
        parent::__construct($paths, $pdo, $controleur, $level);
        $this->params_needed = array(
            "get_createPost" => array("title", "content", "scat"),
            "get_createComment" => array("content", "post"),
            "set_deleteComment" => array("id"),
            "set_editComment" => array("id", "content"),
            "set_editPost" => array("id", "content"),
            "set_solved" => array("id"),
            "set_deletePost" => array("id"),
            "set_moovePost" => array("id", "new_cat"),
            "set_enforum" => array(),
            "set_extforum" => array("other_forum", "link_forum"),
            "set_newcat"=> array("titre_cat"),
            "set_newscat"=> array("titre_scat", "cat_id"),
            "set_delcat"=> array("id"),
            "set_delscat"=> array("id"),
            "set_moovescat"=> array("scat_id", "cat_id"),
        );
        $this->registerAntiSpam(array(
            "get_createPost" => array(2, 5, 100),
            "get_createComment" => array(2, 5, 100),
            "set_editComment" => array(5, 50, 100),
            "set_editPost" => array(5, 50, 100),
        ));
    }

    public function get_createPost(){
        if (!is_numeric($this->args['scat']))
            throw new DiamondException("An int is an int (scat)", 701);

        if (!isset($_SESSION['user']) || !($_SESSION['user'] instanceof User))
            throw new DiamondException("A user need to be connected", 701);

        $this->args['scat'] = intval($this->args['scat']);

        $this->cleanArg($this->args['content']);

        $this->args['content'] = DiamondShortcuts\utf8_encode(htmlspecialchars($this->args['content']));
        $this->args['title'] = htmlspecialchars($this->args['title']);

        if ( !simplifySQL\insert( $this->getPDO(), "d_forum", 
            array("titre_post", "user", "resolu", "content_post", "date_post", "id_scat"), 
            array($this->args['title'], $_SESSION['user']->getId(), 0, $this->args['content'], date("Y-m-d H:i:s"), $this->args['scat']) ) ){
            throw new DiamondException("Error while inserting", "342c");
        }

        $nb = (is_array($req= simplifySQL\select($this->getPDO(), false, "d_forum", array("id"), array(array("id_scat", "=", $this->args['scat'])), "id", true))) ? sizeof($req) : 0;

        try{
            if (!simplifySQL\update($this->getPDO(), "d_forum_sous_cat", array(array("nb_sujets", "=", $nb)), array(array("id", "=", $this->args['scat']))))
                throw new DiamondException("SQL UPDATE error", "342c");
        }catch(DiamondException $e ){ throw $e; }catch (Exception|Error $e){
            throw new DiamondException($e->getMessage(), "342c");
        }

        return $this->formatedReturn(1);
    }

    public function get_createComment(){
        if (!is_numeric($this->args['post']))
            throw new DiamondException("An int is an int (post)", 701);

        if (!isset($_SESSION['user']) || !($_SESSION['user'] instanceof User))
            throw new DiamondException("A user need to be connected", 701);

        $this->args['post'] = intval($this->args['post']);
        $this->cleanArg($this->args['content']);
        $this->args['content'] = DiamondShortcuts\utf8_encode(htmlspecialchars($this->args['content']));

        if ( !simplifySQL\insert( $this->getPDO(), "d_forum_com", 
            array("id_post", "content_com", "user", "date_comment"), 
            array($this->args['post'], $this->args['content'], $_SESSION['user']->getId(), date("Y-m-d H:i:s")) ) ){
            throw new DiamondException("Error while inserting", "342c");
        }

        $nb = (is_array($parent= simplifySQL\select($this->getPDO(), true, "d_forum", array("id", "titre_post", "nb_rep", "user", "id_scat"), array(array("id", "=", $this->args['post'])), "id", true)) && array_key_exists("nb_rep", $parent)) ? intval($parent["nb_rep"]) : 0;

        try{
            if (!simplifySQL\update($this->getPDO(), "d_forum", array(array("nb_rep", "=", $nb+1), array("last_activity", "=", date("Y-m-d H:i:s"))), array(array("id", "=", $this->args['post']))))
                throw new DiamondException("SQL UPDATE error", "342c");
        }catch(DiamondException $e ){ throw $e; }catch (Exception|Error $e){
            throw new DiamondException($e->getMessage(), "342c");
        }
        
        if (is_array($parent) && array_key_exists("user", $parent) && intval($parent['user']) != $_SESSION['user']->getId())
            $this->getControleur()->notify('Une nouvelle réponse a été envoyée sur votre sujet "'. $parent['titre_post'] . '"', $parent['user'], 4, "Nouvelle Activité", LINK . "forum/com/" . $parent['id']);
        
        return $this->formatedReturn(1);
    }

    public function set_deleteComment(){
        if (!is_numeric($this->args['id']))
            throw new DiamondException("An int is an int (id)", 701);

        if (!isset($_SESSION['user']) || !($_SESSION['user'] instanceof User))
            throw new DiamondException("A user need to be connected", 701);

        $this->args['id'] = intval($this->args['id']);

        $com_post = simplifySQL\select($this->getPDO(), true, "d_forum_com", "*", array(array("id", "=", $this->args['id'])));
        if (!is_array($com_post) || empty($com_post) || $com_post == null || $com_post == false || !array_key_exists('user', $com_post))
            throw new DiamondException("Unable to find requested commentary", "154");

        if (!((isset($_SESSION['user']) && $this->level >= 3) || $_SESSION['user']->getId() == $com_post['user']))
            throw new Exception("Forbidden access", 706);
        
        $sujet = simplifySQL\select($this->getPDO(), true, "d_forum", "*", array(array("id", "=", $com_post["id_post"])));
        if (!is_array($sujet) || empty($sujet) || $sujet == null || $sujet == false)
            throw new DiamondException("Unable to find parent post", "153");
        
        try{
            if (simplifySQL\delete($this->getPDO(), "d_forum_com", array(array("id", "=", $this->args['id']))) != true)
                throw new DiamondException("Unable to delete requested commentary", "341b");
        }catch(DiamondException $e ){ throw $e; }catch (Exception|Error $e){
            throw new DiamondException($e->getMessage(), "341b");
        }

        try{
            if (!simplifySQL\update($this->getPDO(), "d_forum", array(array("nb_rep", "=", intval($sujet["nb_rep"])-1)), array(array("id", "=", $sujet["id"]))))
                throw new DiamondException("SQL UPDATE error", "342c");
        }catch(DiamondException $e ){ throw $e; }catch (Exception|Error $e){
            throw new DiamondException($e->getMessage(), "342c");
        }
        
        return $this->formatedReturn(1);
    }

    public function set_deletePost(){
        if (!is_numeric($this->args['id']))
            throw new DiamondException("An int is an int (id)", 701);

        if (!isset($_SESSION['user']) || !($_SESSION['user'] instanceof User))
            throw new DiamondException("A user need to be connected", 701);

        $this->args['id'] = intval($this->args['id']);

        $sujet = simplifySQL\select($this->getPDO(), true, "d_forum", "*", array(array("id", "=", $this->args['id'])));
        if (!is_array($sujet) || empty($sujet) || $sujet == null || $sujet == false)
            throw new DiamondException("Unable to find post", "153");

        if ($this->level < 3 && $sujet['user'] != $_SESSION['user']->getId())
            throw new DiamondException("Forbidden.", "706");

        $nb = (array_key_exists('id_scat', $sujet) && is_array($req= simplifySQL\select($this->getPDO(), false, "d_forum", array("id"), array(array("id_scat", "=", $sujet['id_scat'])), "id", true))) ? sizeof($req)-1 : 0;

        try{
            if (!simplifySQL\update($this->getPDO(), "d_forum_sous_cat", array(array("nb_sujets", "=", $nb)), array(array("id", "=", $sujet['id_scat']))))
                throw new DiamondException("SQL UPDATE error", "342c");
        }catch(DiamondException $e ){ throw $e; }catch (Exception|Error $e){
            throw new DiamondException($e->getMessage(), "342c");
        }
        
        try{
            if (simplifySQL\delete($this->getPDO(), "d_forum_com", array(array("id_post", "=", $this->args['id']))) != true)
                throw new DiamondException("Unable to delete related commentaries", "341b");
            if (simplifySQL\delete($this->getPDO(), "d_forum", array(array("id", "=", $this->args['id']))) != true)
                throw new DiamondException("Unable to delete requested post", "341b");
        }catch(DiamondException $e ){ throw $e; }catch (Exception|Error $e){
            throw new DiamondException($e->getMessage(), "341b");
        }

        return $this->formatedReturn(1);
    }

    public function set_editComment(){
        if (!is_numeric($this->args['id']))
            throw new DiamondException("An int is an int (id)", 701);
        $this->args['id'] = intval($this->args['id']);

        if (!isset($_SESSION['user']) || !($_SESSION['user'] instanceof User))
            throw new DiamondException("A user need to be connected", 701);

        $com = simplifySQL\select($this->getPDO(), true, "d_forum_com", "*", array(array("id", "=", $this->args['id'])));
        if (!is_array($com) || empty($com) || $com == null || $com == false || !array_key_exists('user', $com))
            throw new DiamondException("Unable to find requested commentary", "154");
        
        if (!((isset($_SESSION['user']) && $this->level >= 3) || $_SESSION['user']->getId() == $com['user']))
            throw new Exception("Forbidden access", 706);
    
        try{
            if (!simplifySQL\update($this->getPDO(), "d_forum_com", array(
                array("content_com", "=", DiamondShortcuts\utf8_encode(htmlspecialchars($this->args['content']))), 
                array("last_edit", "=", date("Y-m-d H:i:s")), 
                array("last_editer", "=", $_SESSION['user']->getId()) 
            ), array(array("id", "=", $com["id"]))))
                throw new DiamondException("SQL UPDATE error", "342c");
        }catch(DiamondException $e ){ throw $e; }catch (Exception|Error $e){
            throw new DiamondException($e->getMessage(), "342c");
        }
        
        return $this->formatedReturn(1);
    }

    public function set_editPost(){
        if (!is_numeric($this->args['id']))
            throw new DiamondException("An int is an int (id)", 701);
        $this->args['id'] = intval($this->args['id']);

        if (!isset($_SESSION['user']) || !($_SESSION['user'] instanceof User))
            throw new DiamondException("A user need to be connected", 701);

        $post = simplifySQL\select($this->getPDO(), true, "d_forum", "*", array(array("id", "=", $this->args['id'])));
        if (!is_array($post) || empty($post) || $post == null || $post == false || !array_key_exists('user', $post))
            throw new DiamondException("Unable to find requested post", 153);
        
        if (!((isset($_SESSION['user']) && $this->level >= 3) || $_SESSION['user']->getId() == $post['user']))
            throw new Exception("Forbidden access", 706);
    
        try{
            if (!simplifySQL\update($this->getPDO(), "d_forum", array(
                array("content_post", "=", DiamondShortcuts\utf8_encode(htmlspecialchars($this->args['content']))), 
                array("last_edit", "=", date("Y-m-d H:i:s")), 
                array("last_editer", "=", $_SESSION['user']->getId()) 
            ), array(array("id", "=", $post["id"]))))
                throw new DiamondException("SQL UPDATE error", "342c");
        }catch(DiamondException $e ){ throw $e; }catch (Exception|Error $e){
            throw new DiamondException($e->getMessage(), "342c");
        }
        
        return $this->formatedReturn(1);
    }


    public function set_solved(){
        if (!isset($_SESSION['user']) || !($_SESSION['user'] instanceof User))
            throw new DiamondException("A user need to be connected", 701);

        if (!is_numeric($this->args['id']))
            throw new DiamondException("An int is an int (id)", 701);
        
        $this->args['id'] = intval($this->args['id']);

        if ($this->level < 3 ){
            $sujet = simplifySQL\select($this->getPDO(), true, "d_forum", "*", array(array("id", "=", $this->args['id'])));
            if (!is_array($sujet) || empty($sujet) || $sujet == null || $sujet == false)
                throw new DiamondException("Unable to find post", "153");

            if ($sujet['user'] != $_SESSION['user']->getId())
                throw new DiamondException("Forbidden.", "706");

            if ($sujet['resolu'] == 1)
                return $this->formatedReturn(1);
        }
        
        $solved = (isset($this->args['solved']) && (is_bool($this->args['solved']) || intval($this->args['solved']) == 0 || intval($this->args['solved']) == 1)) ? boolval($this->args['solved']) : true;

        try{
            if (!simplifySQL\update($this->getPDO(), "d_forum", array(
                array("resolu", "=", $solved)
            ), array(array("id", "=", $this->args['id']))))
                throw new DiamondException("SQL UPDATE error", "342c");
        }catch(DiamondException $e ){ throw $e; }catch (Exception|Error $e){
            throw new DiamondException($e->getMessage(), "342c");
        }
        
        return $this->formatedReturn(1);  
    }

    public function set_moovePost(){
        if (!isset($_SESSION['user']) || !($_SESSION['user'] instanceof User))
            throw new DiamondException("A user need to be connected", 701);

        if (!is_numeric($this->args['id']) || !is_numeric($this->args['new_cat']))
            throw new DiamondException("An int is an int (id|new_cat)", 701);
        
        $this->args['id'] = intval($this->args['id']);
        $this->args['new_cat'] = intval($this->args['new_cat']);

        $sujet = simplifySQL\select($this->getPDO(), true, "d_forum", "*", array(array("id", "=", $this->args['id'])));
        if (!is_array($sujet) || empty($sujet) || $sujet == null || $sujet == false)
            throw new DiamondException("Unable to find post", "153");
        
        if (!((isset($_SESSION['user']) && $this->level >= 3) || $_SESSION['user']->getId() == $sujet['user']))
            throw new Exception("Forbidden access", 706);

        try{
            if (!simplifySQL\update($this->getPDO(), "d_forum", array(array("id_scat", "=", $this->args['new_cat'])), array(array("id", "=", $this->args['id']))))
                throw new DiamondException("SQL UPDATE error", "342c");
            
            // On met à jour les compteurs
            $nb_oldcat = (array_key_exists('id_scat', $sujet) && is_array($req= simplifySQL\select($this->getPDO(), false, "d_forum", array("id"), array(array("id_scat", "=", $sujet['id_scat'])), "id", true))) ? sizeof($req) : 0;
            $nb_newcat = (array_key_exists('id_scat', $sujet) && is_array($req= simplifySQL\select($this->getPDO(), false, "d_forum", array("id"), array(array("id_scat", "=", $this->args['new_cat'])), "id", true))) ? sizeof($req) : 0;

            if (!simplifySQL\update($this->getPDO(), "d_forum_sous_cat", array(array("nb_sujets", "=", $nb_oldcat)), array(array("id", "=", $sujet['id_scat']))))
                throw new DiamondException("SQL UPDATE error", "342c");
            if (!simplifySQL\update($this->getPDO(), "d_forum_sous_cat", array(array("nb_sujets", "=", $nb_newcat)), array(array("id", "=", $this->args['new_cat']))))
                throw new DiamondException("SQL UPDATE error", "342c");
        }catch(DiamondException $e ){ throw $e; }catch (Exception|Error $e){
            throw new DiamondException($e->getMessage(), "342c");
        }

        return $this->formatedReturn(1);  
    }

        /**
     * set_enforum - Fonction pour activer/désactiver le forum par défaut
     * 
     * @throws 706 : en cas de manquement au rang d'admin
     * @access public 
     * @author Aldric L.
     * @copyright 2022
     */
    public function set_enforum(){
        if ($this->level < 4)
            throw new Exception("Forbidden access", 706);

        $conf = $this->getIniConfig(ROOT."config/config.ini", true);
        $args = array();
        if ($conf['en_forum']){
            $args['en_forum'] = false;
        }else {
            $args['en_forum'] = true;
        }
        $this->setConfig(ROOT."config/config.ini", $args);
        return $this->formatedReturn(1);
    }

    /**
     * set_extforum - Fonction pour activer/désactiver un forum externe
     * 
     * @throws 706 : en cas de manquement au rang de diamond_master
     * @param bool other_forum : true/false pour activer le forum externe
     * @param string link_forum : lien du forum externe
     * @access public 
     * @author Aldric L.
     * @copyright 2022
     */
    public function set_extforum(){
        if ($this->level < 5)
            throw new Exception("Forbidden access", 706);
        
        return $this->saveconf("config.ini");
    }

    /**
     * set_f_newcat - Fonction pour ajouter une nouvelle catégorie au forum
     * 
     * @throws 706 : en cas de manquement au rang d'admin
     * @throws 342 : erreur en insert (simplifySQL)
     * @param string titre_cat : titre de la catégorie
     * @access public 
     * @author Aldric L.
     * @copyright 2022
     */
    public function set_newcat(){
        if ($this->level < 4)
            throw new Exception("Forbidden access", 706);

        if (simplifySQL\insert($this->getPDO(), "d_forum_cat", array("titre"), array($this->args['titre_cat'])) != true){
            throw new Exception("Unable to insert", 342);
        }
        return $this->formatedReturn(1);
    }

    /**
     * set_f_newcat - Fonction pour ajouter une nouvelle sous catégorie au forum
     * 
     * @throws 706 : en cas de manquement au rang d'admin
     * @throws 342 : erreur en insert (simplifySQL)
     * @throws 150 : le nom est déjà utilisé
     * @throws 151 : la catégorie parente est introuvable
     * @param int cat_id : id de la catégorie parente
     * @param string titre_scat : titre de la sous catégorie
     * @access public 
     * @author Aldric L.
     * @copyright 2022
     */
    public function set_newscat(){
        if ($this->level < 4)
            throw new Exception("Forbidden access", 706);
        $titre_scat = clearString($this->args['titre_scat']);
        $titre_scat = str_replace("_", "", $titre_scat);

        if (!empty(simplifySQL\select($this->getPDO(), true, "d_forum_sous_cat", "*", array(array('titre', "=", $titre_scat))))){
            throw new Exception("Category already exists", 150);
        }else if (empty(simplifySQL\select($this->getPDO(), true, "d_forum_cat", "*", array(array('id', "=", $this->args['cat_id']))))){
            throw new Exception("Unable to find parent category", 151);
        }else if (!simplifySQL\insert($this->getPDO(), "d_forum_sous_cat", array("titre", "id_cat"), array($titre_scat, $this->args['cat_id']))){
            throw new Exception("Unable to insert", 342);
        }
        return $this->formatedReturn(1);
    }

    /**
     * set_f_delcat - Fonction pour supprimer une catégorie au forum
     * Cette fonction supprime de fait toutes les sous catégories associées si elles existent
     * 
     * @throws 706 : en cas de manquement au rang d'admin
     * @throws 341 : erreur en delete (simplifySQL)
     * @param int id : id de la catégorie à supprimer
     * @access public 
     * @author Aldric L.
     * @copyright 2022
     */
    public function set_delcat(){
        if ($this->level < 4)
            throw new Exception("Forbidden access", 706);

        if (simplifySQL\delete($this->getPDO(), "d_forum_cat", array(array("id", "=", $this->args["id"]))) != true){
            throw new Exception("Unable to delete", 341);
        }

        if (simplifySQL\delete($this->getPDO(), "d_forum_sous_cat", array(array("id_cat", "=", $this->args["id"]))) != true){
            throw new Exception("Unable to delete", 341);
        }

        return $this->formatedReturn(1);
    }

    /**
     * set_f_delscat - Fonction pour supprimer une sous catégorie au forum
     * 
     * @throws 706 : en cas de manquement au rang d'admin
     * @throws 341 : erreur en delete (simplifySQL)
     * @param int id : id de la sous catégorie à supprimer
     * @access public 
     * @author Aldric L.
     * @copyright 2022
     */
    public function set_delscat(){
        if ($this->level < 4)
            throw new Exception("Forbidden access", 706);

        if (simplifySQL\delete($this->getPDO(), "d_forum_sous_cat", array(array("id", "=", $this->args["id"]))) != true){
            throw new Exception("Unable to delete", 341);
        }

        return $this->formatedReturn(1);
    }

    /**
     * set_f_newcat - Fonction pour ajouter une nouvelle sous catégorie au forum
     * 
     * @throws 706 : en cas de manquement au rang 3
     * @throws 342 : erreur en update (simplifySQL)
     * @throws 152 : impossible de trouver la sous catégorie à déplaceer
     * @throws 151 : la catégorie parente est introuvable
     * @param int cat_id : id de la nouvelle catégorie parente
     * @param int scat_id : id de la sous catégorie 
     * @access public 
     * @author Aldric L.
     * @copyright 2022
     */
    public function set_moovescat(){
        if ($this->level < 3)
            throw new Exception("Forbidden access", 706);

        if (empty(simplifySQL\select($this->getPDO(), true, "d_forum_sous_cat", "*", array(array('id', "=", $this->args['scat_id']))))){
            throw new Exception("Unable to find child category", 152);
        }else if (empty(simplifySQL\select($this->getPDO(), true, "d_forum_cat", "*", array(array('id', "=", $this->args['cat_id']))))){
            throw new Exception("Unable to find new parent category", 151);
        }

        if (!simplifySQL\update($this->getPDO(), "d_forum_sous_cat", array(array("id_cat", "=", $this->args["cat_id"])), array(array("id", "=", $this->args["scat_id"])))){
            throw new Exception("Unable to update", 342);
        }

        return $this->formatedReturn(1);
    }

}