<?php 
final class Updater {
    protected $instance;
    protected $path_to_files;
    protected $cur_version;
    protected $cur_maj;
    protected $available_majs = array();

    public function __construct($path_to_files, $cur_version){
        $this->path_to_files = $path_to_files;
        $this->cur_version = $cur_version;
    }

    public function getAvailableMajs(){
        if (empty($available_majs)){
            return $this->isNewMaj();
        }
        return $this->available_majs;
    }

    public function isNewMaj(){
        $majs = array();
        //Chargement des addons
        if ($dir = opendir($this->path_to_files . 'installation/updates/')) {
            while($file = readdir($dir)) {
                //On ouvre les sous-dossiers
                if(is_dir($this->path_to_files . 'installation/updates/' . $file) && !in_array($file, array(".",".."))) {
                    if ($d = opendir($this->path_to_files . 'installation/updates/' . $file)) {
                        while($f = readdir($d)) {
                            //Dans ces sous-dossiers, on charge les fichiers nommés maj.ini
                            if ($f == "maj.ini"){
                                $maj = @parse_ini_file($this->path_to_files . 'installation/updates/' . $file . '/' . $f, true);
                                //Si la mise à jour n'a pas déjà été installée
                                if ($maj['id'] > $this->cur_version){
                                    array_push($majs, array("maj" => $maj, "path" => $this->path_to_files . 'installation/updates/' . $file . '/'));
                                }
                            }
                        }
                        closedir($d);
                    }
                }
            }
            closedir($dir);
        }
        if (empty($majs)){
            return false;
        }else {
            $this->available_majs = $majs;
            return $majs;
        }
    }

    public function instance($path){
        foreach ($this->available_majs as $m){
            if ($m['path'] == $path){
                $this->cur_maj = $m;
            }
        }
        return $this->instance = $path;
    }


    public function installBDD($BDD){
        if ($this->instance != null){
            return $BDD->getPDO()->exec(file_get_contents($this->instance . "db/exec.sql"));
        }
    }

    public function checkFiles(){
        if ($this->instance != null && !empty($this->cur_maj)){
            foreach ($this->cur_maj['maj']['filechecker'] as $folder =>$file){
                if (!@file_exists($this->path_to_files . $folder . '/'. $file)){
                    return false;
                }
            }
            return true;
        }else {
            return false;
        }
    }
}