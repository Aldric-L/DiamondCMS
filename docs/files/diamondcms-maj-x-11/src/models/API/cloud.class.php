<?php

class cloud extends DiamondAPI {

    public function __construct($paths, $pdo, $controleur, $level){
        parent::__construct($paths, $pdo, $controleur, $level);
        $this->params_needed = array(
            "set_deleteFile" => array("path"),
            "set_renameFile" => array("path", "filename", "newfilename"),
            "set_editAccessProperties" => array("path"),
            "set_uploadFile" => array("path"),
            "set_createFolder" => array("path", "folder_name"),
            "set_mooveFile" => array("pathfrom", "pathto", "itemname"),
        );

        if (!isset($_SESSION['user']) OR !($_SESSION['user'] instanceof User))
            throw new DiamondException("User needed", 701);
        
    }

    /** 
     * set_deleteFile - Fonction pour supprimer un fichier ou récursivement un dossier
     * Réservée au level 4 ou supérieur pour les fichiers normaux, ou un level 5 pour les fichiers protégés (avec un access_level > 1)
     * 
     * @throws 541 : si le fichier n'est pas trouvé, ou qu'on trouve pas le dossier parent
     * @throws 540 : erreur avec une fonction de suppression
     * @param string path : chemin complet (avec ou sans ROOT)
     * @access public 
     * @author Aldric L.
     * @copyright 2023
     */
    public function set_deleteFile(){
        if (!file_exists($this->args["path"])){
            if (!file_exists(ROOT . $this->args["path"]))
                throw new DiamondException("Unable to find requested file", 541);
            else
                $this->args["path"] = ROOT . $this->args["path"];
        }

        $working_array = $path_array = explode("/", $this->args["path"]);
        $target = array_pop($working_array);
        $previous_path = implode("/", $working_array);
        if (!file_exists($previous_path))
            throw new DiamondException("Unable to find parent folder", 541);
        
        if ($previous_path[strlen($previous_path)-1] != '/')
            $previous_path .= "/";

        $level_min = 4;

        if (file_exists($previous_path . "locked_files.dfiles")){
            $conf = json_decode(file_get_contents($previous_path . "locked_files.dfiles"), true);
            if (is_array($conf) && array_key_exists($target, $conf) && is_array($conf[$target])){
                if (array_key_exists("protected", $conf[$target]) && $conf[$target]["protected"])
                    throw new DiamondException("Item requested to be deleted is protected", 542);

                if (array_key_exists("locked", $conf[$target]) && $conf[$target]["locked"])
                    throw new DiamondException("Item requested to be deleted is locked", 542);

                if (array_key_exists("access_level", $conf[$target]) && is_numeric($conf[$target]["access_level"]) && intval($conf[$target]["access_level"]) > 3)
                    $level_min = 5;                
            }
            if (is_array($conf) && array_key_exists("__GLOBAL-FOLDER-DIAMONDCONF__", $conf) && is_array($conf["__GLOBAL-FOLDER-DIAMONDCONF__"])){
                if (array_key_exists("protected", $conf["__GLOBAL-FOLDER-DIAMONDCONF__"]) && $conf["__GLOBAL-FOLDER-DIAMONDCONF__"]["protected"])
                    throw new DiamondException("Item requested to be edited is protected (inheritance)", 542);

                if (array_key_exists("locked", $conf["__GLOBAL-FOLDER-DIAMONDCONF__"]) && $conf["__GLOBAL-FOLDER-DIAMONDCONF__"]["locked"])
                    throw new DiamondException("Item requested to be edited is locked", 542);

                if (array_key_exists("access_level", $conf["__GLOBAL-FOLDER-DIAMONDCONF__"]) && is_numeric($conf["__GLOBAL-FOLDER-DIAMONDCONF__"]["access_level"])){
                    if ((array_key_exists("inherited_forced", $conf["__GLOBAL-FOLDER-DIAMONDCONF__"]) and $conf["__GLOBAL-FOLDER-DIAMONDCONF__"]["inherited_forced"]) OR intval($conf["__GLOBAL-FOLDER-DIAMONDCONF__"]["access_level"]) > 1 )
                        $level_min = max($level_min, 5);          
                }        
            }
        }
        if ($this->level < $level_min)
            throw new DiamondException("You are not allowed to perform this action.", 706);
        
        try {
            if (is_dir($this->args["path"])){
                rrmdir($this->args["path"]);
            }else {
                if (unlink($this->args["path"]) != true)
                    throw new DiamondException("Unlink failed.", 540);
            }
        }catch (DiamondException $e){ throw $e; }
        catch (Throwable $e){
            throw new DiamondException("Unable to delete, an error has been raised.", 540);
        }

        if (isset($conf) && is_array($conf) && array_key_exists($target, $conf) && is_array($conf[$target])){
            unset($conf[$target]);
            file_put_contents($previous_path . "locked_files.dfiles", json_encode($conf));
        }

        return $this->formatedReturn(1);
    }

    /** 
     * set_renameFile - Fonction pour renommer un fichier ou récursivement un dossier
     * Réservée au level 4 ou supérieur pour les fichiers normaux, ou un level 5 pour les fichiers protégés (avec un access_level > 1)
     * 
     * @throws 541 : si le fichier n'est pas trouvé
     * @throws 543 : si le nouveau nom est déjà utilisé
     * @throws 544 : erreur avec une fonction de renommage
     * @param string path : chemin complet (avec ou sans ROOT)
     * @param string filename : nom avec extension du fichier
     * @param string newfilename : nouveau nom avec extension du fichier
     * @param string prefix (Optionnal): préfixe du fichier de 13 caractères généré à l'upload
     * @access public 
     * @author Aldric L.
     * @copyright 2023
     */
    public function set_renameFile(){
        if ($this->args["filename"] === $this->args["newfilename"])
            return $this->formatedReturn(1);

        if ($this->args["path"][strlen($this->args["path"])-1] != '/')
            $this->args["path"] .= "/";

        if ($this->args["path"][strlen($this->args["path"])-1] == '/' && $this->args["path"][strlen($this->args["path"])-2] == '/')
            $this->args["path"] = substr($this->args["path"], 0, -1);    

        if (array_key_exists("prefix", $this->args) && strlen($this->args["prefix"]) == 13){
            $this->args["filename"] = $this->args["prefix"] . "_" . $this->args["filename"];
            $this->args["newfilename"] = $this->args["prefix"] . "_" . $this->args["newfilename"];
        }

        if (!file_exists($this->args["path"] . $this->args["filename"])){
            if (!file_exists(ROOT . $this->args["path"] . $this->args["filename"]))
                throw new DiamondException("Unable to find requested file", 541);
            else
                $this->args["path"] = ROOT . $this->args["path"];
        }

        if (file_exists($this->args["path"] . $this->args["newfilename"]))
            throw new DiamondException("A file of this name already exists", 543);

        $level_min = 4;

        if (file_exists($this->args["path"] . "locked_files.dfiles")){
            $conf = json_decode(file_get_contents($this->args["path"] . "locked_files.dfiles"), true);
            if (is_array($conf) && array_key_exists($this->args["filename"], $conf) && is_array($conf[$this->args["filename"]])){
                if (array_key_exists("protected", $conf[$this->args["filename"]]) && $conf[$this->args["filename"]]["protected"])
                    throw new DiamondException("Item requested to be renammed is protected", 542);

                if (array_key_exists("locked", $conf[$this->args["filename"]]) && $conf[$this->args["filename"]]["locked"])
                    throw new DiamondException("Item requested to be renammed is locked", 542);

                if (array_key_exists("access_level", $conf[$this->args["filename"]]) && is_numeric($conf[$this->args["filename"]]["access_level"]) && intval($conf[$this->args["filename"]]["access_level"]) > 3)
                    $level_min = 5;                
            }
            if (is_array($conf) && array_key_exists("__GLOBAL-FOLDER-DIAMONDCONF__", $conf) && is_array($conf["__GLOBAL-FOLDER-DIAMONDCONF__"])){
                if (array_key_exists("protected", $conf["__GLOBAL-FOLDER-DIAMONDCONF__"]) && $conf["__GLOBAL-FOLDER-DIAMONDCONF__"]["protected"])
                    throw new DiamondException("Item requested to be edited is protected (inheritance)", 542);

                if (array_key_exists("locked", $conf["__GLOBAL-FOLDER-DIAMONDCONF__"]) && $conf["__GLOBAL-FOLDER-DIAMONDCONF__"]["locked"])
                    throw new DiamondException("Item requested to be edited is locked", 542);

                if (array_key_exists("access_level", $conf["__GLOBAL-FOLDER-DIAMONDCONF__"]) && is_numeric($conf["__GLOBAL-FOLDER-DIAMONDCONF__"]["access_level"])){
                    if ((array_key_exists("inherited_forced", $conf["__GLOBAL-FOLDER-DIAMONDCONF__"]) and $conf["__GLOBAL-FOLDER-DIAMONDCONF__"]["inherited_forced"]) OR intval($conf["__GLOBAL-FOLDER-DIAMONDCONF__"]["access_level"]) > 1 )
                        $level_min = max($level_min, 5);          
                }        
            }
        }
        if ($this->level < $level_min)
            throw new DiamondException("You are not allowed to perform this action.", 706);

        $newfilenamearray = explode(".", $this->args["newfilename"]);
        $ext = mb_strtolower(array_pop($newfilenamearray));
        $newnamestr = implode(".", $newfilenamearray);
        $newnamestr = clearString($newnamestr, true, true);
        if ($newnamestr[strlen($newnamestr)-1] == '_')
            $newnamestr = substr($newnamestr, 0, -1); 

        $finalnewname = $newnamestr . "." . $ext;
        $dispnamestr = str_replace("_", " ", str_replace($this->args["prefix"] . "_" , "", $newnamestr)) . "." . $ext;
 
        try {
            if (rename($this->args["path"] . $this->args["filename"], $this->args["path"] . $finalnewname) != true)
                throw new DiamondException("Rename failed.", 544);
        }catch (DiamondException $e){ throw $e; }
        catch (Throwable $e){
            throw new DiamondException("Unable to rename, an error has been raised.", 544);
        }

        if (isset($conf) && is_array($conf) && array_key_exists($this->args["filename"], $conf) && is_array($conf[$this->args["filename"]])){
            $conf[$finalnewname] = $conf[$this->args["filename"]];
            unset($conf[$this->args["filename"]]);
            file_put_contents($this->args["path"] . "locked_files.dfiles", json_encode($conf));
        }

        return $this->formatedReturn(array("ext" => $ext, "prefix" => $this->args["prefix"], "newname" => $newnamestr, "finalnewname" => $finalnewname, "dispnewname" => $dispnamestr));
    }

    /** 
     * set_editAccessProperties - Fonction pour éditer les métadonnées d'un fichier ou d'un dossier
     * Réservée au level 4 ou supérieur pour les fichiers normaux, ou un level 5 pour les fichiers protégés (avec un access_level > 1)
     * 
     * @throws 541 : si le fichier n'est pas trouvé
     * @param string path : chemin complet (avec ou sans ROOT)
     * @param int access_level (optionnal) : niveau d'autorisation
     * @param bool hidden (optionnal) : cacher ou non le dossier aux utilisateurs de faible rang
     * @access public 
     * @author Aldric L.
     * @copyright 2023
     */
    public function set_editAccessProperties(){
        $this->args = cleanIniTypes($this->args);
        if (!file_exists($this->args["path"])){
            if (file_exists("/" . $this->args["path"]))
                $this->args["path"] = "/" . $this->args["path"];
            else if (!file_exists(ROOT . $this->args["path"]))
                throw new DiamondException("Unable to find requested file", 541);
            else
                $this->args["path"] = ROOT . $this->args["path"];
        }

        $working_array = $path_array = explode("/", $this->args["path"]);
        $target = array_pop($working_array);
        $previous_path = implode("/", $working_array);
        if (!file_exists($previous_path))
            throw new DiamondException("Unable to find parent folder", 541);
        
        if ($previous_path[strlen($previous_path)-1] != '/')
            $previous_path .= "/";

        $level_min = 4;
        $conf = array();

        if (file_exists($previous_path . "locked_files.dfiles")){
            $conf = json_decode(file_get_contents($previous_path . "locked_files.dfiles"), true);
            if (is_array($conf) && array_key_exists($target, $conf) && is_array($conf[$target])){
                // On protège les fichiers protégés que si on tente d'en modifier les droits d'accès
                if (array_key_exists("access_level", $this->args) && is_numeric($this->args["access_level"]) && intval($this->args["access_level"]) < 6){
                    if (array_key_exists("protected", $conf[$target]) && $conf[$target]["protected"])
                        throw new DiamondException("Item requested to be edited is protected", 542);

                    if (array_key_exists("locked", $conf[$target]) && $conf[$target]["locked"])
                        throw new DiamondException("Item requested to be edited is locked", 542);
                }

                if (array_key_exists("access_level", $conf[$target]) && is_numeric($conf[$target]["access_level"]) && intval($conf[$target]["access_level"]) > 3)
                    $level_min = 5;                
            }

            if (is_array($conf) && array_key_exists("__GLOBAL-FOLDER-DIAMONDCONF__", $conf) && is_array($conf["__GLOBAL-FOLDER-DIAMONDCONF__"])){
                // On protège les fichiers protégés que si on tente d'en modifier les droits d'accès
                if (array_key_exists("access_level", $this->args) && is_numeric($this->args["access_level"]) && intval($this->args["access_level"]) < 6){
                    if (array_key_exists("protected", $conf["__GLOBAL-FOLDER-DIAMONDCONF__"]) && $conf["__GLOBAL-FOLDER-DIAMONDCONF__"]["protected"])
                        throw new DiamondException("Item requested to be edited is protected (inheritance)", 542);

                    if (array_key_exists("locked", $conf["__GLOBAL-FOLDER-DIAMONDCONF__"]) && $conf["__GLOBAL-FOLDER-DIAMONDCONF__"]["locked"])
                        throw new DiamondException("Item requested to be edited is locked", 542);
                }

                if (array_key_exists("access_level", $conf["__GLOBAL-FOLDER-DIAMONDCONF__"]) && is_numeric($conf["__GLOBAL-FOLDER-DIAMONDCONF__"]["access_level"])){
                    if (array_key_exists("access_level", $this->args) && is_numeric($this->args["access_level"]) && intval($this->args["access_level"]) < 6 &&
                        intval($conf["__GLOBAL-FOLDER-DIAMONDCONF__"]["access_level"]) > intval($this->args["access_level"]))
                        throw new DiamondException("Item requested to be edited already inherits of a higher access level.", 542);
                    if (intval($conf["__GLOBAL-FOLDER-DIAMONDCONF__"]["access_level"]) > $level_min
                    OR array_key_exists("inherited_forced", $conf["__GLOBAL-FOLDER-DIAMONDCONF__"]) && $conf["__GLOBAL-FOLDER-DIAMONDCONF__"]["inherited_forced"])
                        $level_min = intval($conf["__GLOBAL-FOLDER-DIAMONDCONF__"]["access_level"]);          
                }
                          
            }
        }
        if ($this->level < $level_min)
            throw new DiamondException("You are not allowed to perform this action.", 706);

        $prev = array();
        if (array_key_exists($target, $conf) && is_array($conf[$target]))
            $prev = $conf[$target];
        
        if (is_dir($this->args["path"]) && array_key_exists("access_level", $this->args) && is_numeric($this->args["access_level"]) && intval($this->args["access_level"]) < 6 ){
            $folderconf = array();
            if ($this->args["path"][strlen($this->args["path"])-1] != '/')
                $this->args["path"] .= "/";

            if (file_exists($this->args["path"] . "locked_files.dfiles"))
                $folderconf = json_decode(file_get_contents($this->args["path"] . "locked_files.dfiles"), true);
            
            $folderconf["__GLOBAL-FOLDER-DIAMONDCONF__"] = array("access_level" => intval($this->args["access_level"]), "inherited" => false, "inherited_from" => $this->args["path"]);
            file_put_contents($this->args["path"] . "locked_files.dfiles", json_encode($folderconf));

            function treat_sub_folders($path, $level, $from){
                if ($path[strlen($path)-1] != '/')
                    $path .= "/";
                $folderconf = array();
                if (file_exists($path . "locked_files.dfiles")){
                    $folderconf = json_decode(file_get_contents($path . "locked_files.dfiles"), true);
                    if (is_array($folderconf) && array_key_exists("__GLOBAL-FOLDER-DIAMONDCONF__", $folderconf) && is_array($folderconf["__GLOBAL-FOLDER-DIAMONDCONF__"])
                        && array_key_exists("inherited_forced", $folderconf["__GLOBAL-FOLDER-DIAMONDCONF__"]) && $folderconf["__GLOBAL-FOLDER-DIAMONDCONF__"]["inherited_forced"])
                        return;
                }
                $folderconf["__GLOBAL-FOLDER-DIAMONDCONF__"] = array("access_level" => intval($level), "inherited" => true, "inherited_from" => $from);
                file_put_contents($path . "locked_files.dfiles", json_encode($folderconf));
                foreach(scandir($path) as $f) {
                    if(!is_string($f[0]) || $f[0] == '.' || substr($f, -7, 7) == ".dfiles")
                        continue; 
                    
                    if(is_dir($path . $f))
                        treat_sub_folders($path . $f, $level, $from);
                }
            }

            foreach(scandir($this->args["path"]) as $f) {
                if(!is_string($f[0]) || $f[0] == '.' || substr($f, -7, 7) == ".dfiles")
                    continue; 
                
                if(is_dir($this->args["path"] . $f))
                    treat_sub_folders($this->args["path"] . $f, intval($this->args["access_level"]), $this->args["path"]);
            }
        }
        
        if (array_key_exists("access_level", $this->args) && is_numeric($this->args["access_level"]) && intval($this->args["access_level"]) < 6)
            $prev["access_level"] = intval($this->args["access_level"]);

        if (array_key_exists("hidden", $this->args) && is_bool($this->args["hidden"]))
            $prev["hidden"] = boolval($this->args["hidden"]);
            
        $conf[$target] = $prev;
        file_put_contents($previous_path . "locked_files.dfiles", json_encode($conf));

        return $this->formatedReturn(1);
    }

    /** 
     * set_uploadFile - Fonction pour uploader un fichier dans le cloud
     * Réservée au level 4 ou supérieur (ou à minima au level d'accès du dossier)
     * 
     * @param string path : chemin complet (avec ou sans ROOT)
     * @access public 
     * @author Aldric L.
     * @copyright 2023
     */
    public function set_uploadFile(){
        $this->args = cleanIniTypes($this->args);

        if (!file_exists($this->args["path"])){
            if (file_exists("/" . $this->args["path"]))
                $this->args["path"] = "/" . $this->args["path"];
            else if (!file_exists(ROOT . $this->args["path"]))
                throw new DiamondException("Unable to find requested file", 541);
            else
                $this->args["path"] = ROOT . $this->args["path"];
        }

        if ($this->level < 4)
            throw new DiamondException("Forbidden access", 706);

        
        $flags = array("access_level" => 1, "protected" => false);
        if (array_key_exists("access_level", $this->args) && is_numeric($this->args["access_level"]) && intval($this->args["access_level"]) < 6)
            $flags["access_level"] = intval($this->args["access_level"]);

        if (array_key_exists("hidden", $this->args) && is_bool($this->args["hidden"]))
            $flags["hidden"] = boolval($this->args["hidden"]);

        $upload = uploadFile("file", null, true, $this->args["path"], null, $flags, $this->level);
        if (is_int($upload))
            throw new DiamondException("Unable to upload file", 500 + intval($upload));
        else 
            return $this->formatedReturn(1);
    }

    /** 
     * set_createFolder - Fonction pour créer un dossier vide dans le cloud
     * Réservée au level 4 ou supérieur (ou à minima au level d'accès du dossier)
     * 
     * @param string path : chemin complet (avec ou sans ROOT)
     * @param string folder_name : nom du fichier si non compris dans l'argument path
     * @access public 
     * @author Aldric L.
     * @copyright 2023
     */
    public function set_createFolder(){
        if ($this->level < 4)
            throw new DiamondException("Forbidden access", 706);

        if (!file_exists($this->args["path"])){
            if (file_exists("/" . $this->args["path"]))
                $this->args["path"] = "/" . $this->args["path"];
            else if (!file_exists(ROOT . $this->args["path"]))
                throw new DiamondException("Unable to find requested file", 541);
            else
                $this->args["path"] = ROOT . $this->args["path"];
        }

        if ($this->args["path"][strlen($this->args["path"])-1] != '/')
            $this->args["path"] .= "/";

        if (file_exists($this->args["path"] . $this->args["folder_name"]))
            throw new DiamondException("A directory of this name already exists", 543);

        try {
            if (!mkdir($this->args["path"] . $this->args["folder_name"], 0777, true))
                throw new DiamondException("Unable to create folder.", 545);
        }
        catch (DiamondException $e){ throw $e; }
        catch (Throwable $e){
            throw new DiamondException("Unable to create folder (error raised)", 545);
        }

        if (file_exists($this->args["path"] . "locked_files.dfiles")){
            $conf = json_decode(file_get_contents($this->args["path"] . "locked_files.dfiles"), true);
            if (is_array($conf) && array_key_exists("__GLOBAL-FOLDER-DIAMONDCONF__", $conf) && is_array($conf["__GLOBAL-FOLDER-DIAMONDCONF__"])){
                $global_conf_inherited = $conf["__GLOBAL-FOLDER-DIAMONDCONF__"];
                $global_conf_inherited["inherited"] = true;
                if (!array_key_exists("inherited_from", $global_conf_inherited))
                    $global_conf_inherited["inherited"] = $this->args["path"];
            }
        }

        file_put_contents($this->args["path"] . $this->args["folder_name"] . "/locked_files.dfiles", json_encode(array("__GLOBAL-FOLDER-DIAMONDCONF__" => $global_conf_inherited)));
        return $this->formatedReturn(1);
    }

    /** 
     * set_mooveFile - Fonction pour déplacer un fichier ou récursivement un dossier
     * Réservée au level 4 ou supérieur pour les fichiers normaux
     * 
     * @throws 541 : si le fichier n'est pas trouvé
     * @throws 543 : si le nouveau nom est déjà utilisé
     * @throws 544 : erreur avec une fonction de renommage
     * @param string pathfrom : chemin complet originel (avec ou sans ROOT)
     * @param string pathto : nouveau chemin complet (avec ou sans ROOT)
     * @param string itemname : nom avec extension (et préfixe le cas échéant) du fichier
     * @access public 
     * @author Aldric L.
     * @copyright 2023
     */
    public function set_mooveFile(){
        if ($this->args["pathfrom"] === $this->args["pathto"])
            return $this->formatedReturn(1);

        if ($this->args["pathfrom"][strlen($this->args["pathfrom"])-1] != '/')
            $this->args["pathfrom"] .= "/";

        if ($this->args["pathfrom"][strlen($this->args["pathfrom"])-1] == '/' && $this->args["pathfrom"][strlen($this->args["pathfrom"])-2] == '/')
            $this->args["pathfrom"] = substr($this->args["pathfrom"], 0, -1); 
            
        if (!file_exists($this->args["pathfrom"] . $this->args["itemname"])){
            if (!file_exists(ROOT . $this->args["pathfrom"] . $this->args["itemname"]))
                throw new DiamondException("Unable to find requested file", 541);
            else
                $this->args["pathfrom"] = ROOT . $this->args["pathfrom"];
        }

        if ($this->args["pathto"][strlen($this->args["pathto"])-1] != '/')
            $this->args["pathto"] .= "/";

        if ($this->args["pathto"][strlen($this->args["pathto"])-1] == '/' && $this->args["pathto"][strlen($this->args["pathto"])-2] == '/')
            $this->args["pathto"] = substr($this->args["pathto"], 0, -1);  

        if (!file_exists($this->args["pathto"])){
            if (!file_exists(ROOT . $this->args["pathto"]))
                throw new DiamondException("Unable to find destination folder", 541);
            else
                $this->args["pathto"] = ROOT . $this->args["pathto"];
        }

        if (file_exists($this->args["pathto"] . $this->args["itemname"]))
            throw new DiamondException("A file of this name already exists", 543);

        $level_min = 4;
        $file_conf = array();

        if (file_exists($this->args["pathfrom"] . "locked_files.dfiles")){
            $conf = json_decode(file_get_contents($this->args["pathfrom"] . "locked_files.dfiles"), true);
            if (is_array($conf) && array_key_exists($this->args["itemname"], $conf) && is_array($conf[$this->args["itemname"]])){
                if (array_key_exists("protected", $conf[$this->args["itemname"]]) && $conf[$this->args["itemname"]]["protected"])
                    throw new DiamondException("Item requested to be renammed is protected", 542);

                if (array_key_exists("locked", $conf[$this->args["itemname"]]) && $conf[$this->args["itemname"]]["locked"])
                    throw new DiamondException("Item requested to be renammed is locked", 542);

                if (array_key_exists("access_level", $conf[$this->args["itemname"]]) && is_numeric($conf[$this->args["itemname"]]["access_level"]) && intval($conf[$this->args["itemname"]]["access_level"]) > 3)
                    $level_min = 5;   
                $file_conf = $conf[$this->args["itemname"]];             
            }
            if (is_array($conf) && array_key_exists("__GLOBAL-FOLDER-DIAMONDCONF__", $conf) && is_array($conf["__GLOBAL-FOLDER-DIAMONDCONF__"])){
                if (array_key_exists("protected", $conf["__GLOBAL-FOLDER-DIAMONDCONF__"]) && $conf["__GLOBAL-FOLDER-DIAMONDCONF__"]["protected"])
                    throw new DiamondException("Item requested to be edited is protected (inheritance)", 542);

                if (array_key_exists("locked", $conf["__GLOBAL-FOLDER-DIAMONDCONF__"]) && $conf["__GLOBAL-FOLDER-DIAMONDCONF__"]["locked"])
                    throw new DiamondException("Item requested to be edited is locked", 542);

                if (array_key_exists("access_level", $conf["__GLOBAL-FOLDER-DIAMONDCONF__"]) && is_numeric($conf["__GLOBAL-FOLDER-DIAMONDCONF__"]["access_level"])){
                    if ((array_key_exists("inherited_forced", $conf["__GLOBAL-FOLDER-DIAMONDCONF__"]) and $conf["__GLOBAL-FOLDER-DIAMONDCONF__"]["inherited_forced"]) OR intval($conf["__GLOBAL-FOLDER-DIAMONDCONF__"]["access_level"]) > 3 )
                        $level_min = max($level_min, 5);          
                }        
            }
        }
        $confto = array();
        if (file_exists($this->args["pathto"] . "locked_files.dfiles")){
            $confto = json_decode(file_get_contents($this->args["pathto"] . "locked_files.dfiles"), true);
            if (is_array($confto) && array_key_exists("__GLOBAL-FOLDER-DIAMONDCONF__", $confto) && is_array($confto["__GLOBAL-FOLDER-DIAMONDCONF__"])){
                if (array_key_exists("access_level", $confto["__GLOBAL-FOLDER-DIAMONDCONF__"]) && is_numeric($confto["__GLOBAL-FOLDER-DIAMONDCONF__"]["access_level"])){
                    if ((array_key_exists("inherited_forced", $confto["__GLOBAL-FOLDER-DIAMONDCONF__"]) and $confto["__GLOBAL-FOLDER-DIAMONDCONF__"]["inherited_forced"]) OR intval($confto["__GLOBAL-FOLDER-DIAMONDCONF__"]["access_level"]) > 3 )
                        $level_min = max($level_min, 5);
                    if (array_key_exists("access_level", $file_conf) && array_key_exists("access_level", $confto["__GLOBAL-FOLDER-DIAMONDCONF__"]) && is_numeric($confto["__GLOBAL-FOLDER-DIAMONDCONF__"]["access_level"]) && intval($confto["__GLOBAL-FOLDER-DIAMONDCONF__"]["access_level"]) > intval($file_conf["access_level"]))
                        unset($file_conf['access_level']);          
                }        
            }
        }
        if ($this->level < $level_min)
            throw new DiamondException("You are not allowed to perform this action.", 706);

        try {
            if (rename($this->args["pathfrom"] . $this->args["itemname"], $this->args["pathto"] . $this->args["itemname"]) != true)
                throw new DiamondException("Rename failed.", 544);
        }catch (DiamondException $e){ throw $e; }
        catch (Throwable $e){
            throw new DiamondException("Unable to rename, an error has been raised.", 544);
        }

        if (isset($conf) && is_array($conf) && array_key_exists($this->args["itemname"], $conf) && is_array($conf[$this->args["itemname"]])){
            unset($conf[$this->args["itemname"]]);
            file_put_contents($this->args["pathfrom"] . "locked_files.dfiles", json_encode($conf));
        }

        if (isset($confto) && is_array($confto)){
            $confto[$this->args["itemname"]] = $file_conf;
            file_put_contents($this->args["pathto"] . "locked_files.dfiles", json_encode($confto));
        }

        return $this->formatedReturn(1);
    }
}