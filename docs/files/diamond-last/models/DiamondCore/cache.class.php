<?php 

/**
 * DiamondCache - Class permettant de créer un cache pour éviter de refaire des traitements répétitifs
 * Attention, lorsque l'on crée avec cette classe un cache dans un dossier, on ne peut en créer un autre au même endroit.
 * Un dossier = un cache différent (ou du moins avec le même timer)
 * 
 * @author Aldric L.
 * @copyright 2022
 */
class DiamondCache {

    private $cache_path;
    private $cache_duration; // EN MINUTES !!

    public function __construct($cache_path, $cache_duration){
        $this->cache_path = $cache_path;
        $this->cache_duration = $cache_duration;

        if (defined("DIAMOND_CACHE") && !DIAMOND_CACHE)
            return false;

        // On vérifie si un cache différent ne préexiste pas et on bloque le dossier
        if (!file_exists($cache_path))
            mkdir($cache_path, 0777, true);

        if (file_exists($cache_path . "cache.ini")){
            $cache_conf = cleanIniTypes(parse_ini_file($cache_path . "cache.ini"));
            if (!isset($cache_conf['cache_duration']) || $cache_conf['cache_duration'] != $cache_duration)
                throw new Exception("An other pre-existing cache already exists and cannot be merged.", 711);
        }else {
            $ini = new ini ($cache_path . "cache.ini");
            //On lui passe l'array modifié
            $ini->ajouter_array(cleanIniTypes(array("cache_duration" => $cache_duration)));
            //On écrit en lui demmandant de conserver les groupes
            $ini->ecrire(true);
        }
    }

    /**
     * read - Fonction pour lire un fichier du cache
     * 
     * @param string filename : nom du fichier
     * @access public 
     * @author Aldric L.
     * @copyright 2022
     * @return bool|string : ===false si le fichier n'existe pas (ou que DiamondCache est désactivé), ==false si echec, valeur du cache sinon
     */
    public function read($filename){
        if (defined("DIAMOND_CACHE") && !DIAMOND_CACHE)
            return false;

        $fpath = $this->cache_path . $filename;
        if (!file_exists($fpath))
            return false;

        if (time() - filemtime($fpath) > $this->cache_duration*60){
            unlink($fpath);
            return false;
        }

        return file_get_contents($fpath);
    }

    /**
     * write - Fonction pour écrire un fichier dans le cache ou pour supprimer un fichier du cache
     * 
     * @param string filename : nom du fichier
     * @param string content : contenu à sauvegarder, si null suppression du fichier
     * @access public 
     * @author Aldric L.
     * @copyright 2022
     * @return bool
     */
    public function write($filename, $content){
        if (defined("DIAMOND_CACHE") && !DIAMOND_CACHE)
            return false;
            
        $fpath = $this->cache_path . $filename;
        if ($content === null)
            return unlink($fpath);
        return file_put_contents($fpath, $content);
    }

    /**
     * get_path - Fonction pour récupérer le chemin d'accès du dossier courant
     * 
     * @access public 
     * @author Aldric L.
     * @copyright 2022
     * @return string
     */
    public function get_path(){
        return $this->cache_path;
    }

    /**
     * clean - Fonction pour nettoyer le cache des fichiers qui ont expiré
     * 
     * @access public 
     * @param bool $purge : vide tout le cache, quelque soit la date des fichiers
     * @author Aldric L.
     * @copyright 2022
     * @return bool : true, execution reussie
     */
    public function clean(bool $purge=false){
        if ($handle = opendir($this->cache_path)){
            while(false !== ($file = readdir($handle))){
                if(file_exists($this->cache_path . $file) 
                && ($file != '.' && $file != '..')
                && ($file != "cache.ini")
                && (time() - filemtime($this->cache_path . $file) > $this->cache_conf['cache_duration']*60  || $purge)){
                    if (unlink($this->cache_path . $file) == false){
                        throw new Exception ("Unable to delete cache file.", 540);
                    }
                }
            }
        }else {
            throw new Exception ("Unable to open cache folder.", 712);
        }
        return true;
    }

    /**
     * cacheCleaner - Fonction pour nettoyer un cache non-initialisé
     * 
     * @access public 
     * @param string $cache_path : chemin d'accès du dossier à nettoyer
     * @param bool $purge : vide tout le cache, quelque soit la date des fichiers
     * @author Aldric L.
     * @copyright 2022
     * @return bool : true, execution reussie
     */
    public static function cacheCleaner($cache_path, bool $purge=false){
        if (file_exists($cache_path . "cache.ini")){
            $cache_conf = cleanIniTypes(parse_ini_file($cache_path . "cache.ini"));
            if ($handle = opendir($cache_path)){
                while(false !== ($file = readdir($handle))){
                    if(file_exists($cache_path . $file) 
                    && ($file != '.' && $file != '..')
                    && ($file != "cache.ini")
                    && (time() - filemtime($cache_path . $file) > $cache_conf['cache_duration']*60  || $purge)){
                        if (unlink($cache_path . $file) == false){
                            throw new Exception ("Unable to delete cache file.", 540);
                        }
                    }
                }
            }else {
                throw new Exception ("Unable to open cache folder.", 712);
            }
        }else {
            throw new Exception("Folder provided is not a DiamondCache folder.", 712);
        }
        return true;
    }

}