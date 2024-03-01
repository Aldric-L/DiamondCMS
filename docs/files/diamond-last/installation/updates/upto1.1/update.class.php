<?php 

final class NewUpdate extends Update{
    protected static $update_human_name = "Version 1.1 Béta Build A";
    protected static $update_name = "1.1Ba";
    protected static $update_id = 4;
    protected static $update_date = "1/08/2020";
    protected static $class_name = "NewUpdate";

    protected static $path_to_files;
    protected static $path_to_update_files;

    public static final function setDB($db){
        return static::$path_to_update_files;
        var_dump(static::$path_to_update_files);
        var_dump($req_state = $db->exec(static::$path_to_update_files . "db/exec.sql"));
        return $db;
    }

    public static final function setFiles(){
        require_once (static::$path_to_files . "models/DiamondCore/files.php");
        //On commence par Backup le site
        static::$backupFiles();
        //On transfert les nouveaux fichiers
        static::$transferFiles();
    }

    private static final function backupFiles(){
        rename(static::$path_to_files . 'controllers/', static::$path_to_update_files . 'bkup/controllers/');
        rename(static::$path_to_files . 'addons/', static::$path_to_update_files . 'bkup/addons/');
        rename(static::$path_to_files . 'config/errors.ini', static::$path_to_update_files . 'bkup/config/errors.ini');
        rename(static::$path_to_files . 'js/', static::$path_to_update_files . 'bkup/js/');
        //rename(static::$path_to_files . 'logs/', static::$path_to_update_files . 'bkup/logs/');
        rename(static::$path_to_files . 'views/themes/', static::$path_to_update_files . 'bkup/views/themes/');

        //Ces deux dernières opérations doivent avoir été réalisées par l'utilisateur manuellement
        //rename(static::$path_to_files . 'models/', static::$path_to_update_files . 'bkup/models/');
        //rename(static::$path_to_files . 'index.php', static::$path_to_update_files . 'bkup/index.php');
    }

    private static function transferFiles(){
        rename(static::$path_to_update_files . 'src/controllers/', static::$path_to_files . 'controllers/' );
        rename(static::$path_to_update_files . 'src/addons/', static::$path_to_files . 'addons/' );
        rename(static::$path_to_update_files . 'src/config/errors.ini', static::$path_to_files . 'config/errors.ini' );
        rename(static::$path_to_update_files . 'src/js/', static::$path_to_files . 'js/' );
        rename(static::$path_to_update_files . 'src/logs/dev_logs.log', static::$path_to_files . 'logs/dev_logs.log' );          
        rename(static::$path_to_update_files . 'src/views/themes/', static::$path_to_files . 'views/themes/' );  

        //Ces deux dernières opérations doivent avoir été réalisées par l'utilisateur manuellement
        //rename(static::$path_to_update_files . 'src/models/', static::$path_to_files . 'models/' );
        //rename(static::$path_to_update_files . 'src/index.php', static::$path_to_files . 'index.php' );
    }

    public static function checkIfValid(){
        if(is_dir(static::$path_to_files) && !in_array($file, array(".",".."))) {
            if ($d = opendir(static::$path_to_files)) {
                while($f = readdir($d)) {
                    if (substr(sprintf('%o', fileperms($f)), -4) != "0777" && substr(sprintf('%o', fileperms($f)), -4) != "0666"){
                        return false;
                    }
                }
                closedir($d);
            }
        }
        return true;
    }
}