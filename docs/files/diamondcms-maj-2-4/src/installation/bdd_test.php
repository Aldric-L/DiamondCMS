<?php
//Ce controleur, associé à l'étape 2, permet, via AJAX de tester des identifiants de connexion SQL et de les définir si besoin
define('ROOT', str_replace('installation/bdd_test.php','', $_SERVER['SCRIPT_FILENAME']));

//On gère les erreurs et exceptions (code issu de l'index.php)
define('FORCE_INLINE_ERR', true);
require_once(ROOT . "models/errorhandler.php");
set_error_handler("diamondInstallerErrorHandler", E_ALL);
set_exception_handler('diamondInstallerExceptionHandler');
register_shutdown_function("installerShut");

require_once(ROOT . 'models/DiamondCore/db.class.php');
require_once(ROOT . 'models/bdd_connexion.php');
$bddtest = new BDD(array("host" => $_POST['host'], "db" => $_POST['db'], "usr" => $_POST['usr'], "pwd" => $_POST['psw'], "port" => $_POST['port']), true);
try{
    $test = $bddtest->testPDO();
}catch (Exception $e){
    if ($e->getCode() == 1049){
        if (!isset($_POST['type']) || $_POST['type'] != "install"){
            echo "notable"; die;
        }  
    }else {
        echo $e->getMessage();
        die;
    }
}
if (isset($_POST['type']) && $_POST['type'] == "def"){
    require_once(ROOT . 'models/ini.php');
    $error = "";
    try {
        $bddtest->changeConfig($_POST['host'], $_POST['db'], $_POST['usr'], $_POST['psw'], $_POST['port']);
    }catch (Exception $e){
        $error = "Code " . $e->getCode() . " - " . $e->getMessage();
    }
    
    if ($error == ""){
        die('Success');
    }else {
        die('Error: ' . $error);
    }
}else if (isset($_POST['type']) && $_POST['type'] == "install"){
    $error = false;
    $msg = "";
    if ($bddtest->getPDO() != null){
        try {  
            $bddtest->getPDO()->exec("
            SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";
            SET time_zone = \"+00:00\";
            
            
            /*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
            /*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
            /*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
            /*!40101 SET NAMES utf8mb4 */;
            " 
            . file_get_contents(ROOT . 'installation/core/DiamondBDD-fromscratch.sql'));
        }
        catch (Exception $e){
            if ($e->getCode() == 1049){  
                $bddtest = new BDD(array("host" => $_POST['host'], "db" => $_POST['db'], "usr" => $_POST['usr'], "pwd" => $_POST['psw'], "port" => $_POST['port']), true);
                try {
                    $bddtest->getPDO()->exec("SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";
                    SET time_zone = \"+00:00\";
                    
                    
                    /*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
                    /*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
                    /*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
                    /*!40101 SET NAMES utf8mb4 */;
                    
                    CREATE DATABASE IF NOT EXISTS `" . $_POST['db'] ."` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
                    USE `". $_POST['db'] . "`;" . 
                    file_get_contents(ROOT . 'installation/core/DiamondBDD-fromscratch.sql'));
                }
                catch (Exception $ec){
                    $error = true;
                    $msg = $ec->getMessage();
                }
            }else {
                $error = true;
                $msg = $e->getMessage();
            }
        }
    }else {
        $bddtest = new BDD(array("host" => $_POST['host'], "db" => $_POST['db'], "usr" => $_POST['usr'], "pwd" => $_POST['psw'], "port" => $_POST['port']), true, true);
        try {
            $bddtest->getPDO()->exec("SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";
                    SET time_zone = \"+00:00\";
                    
                    
                    /*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
                    /*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
                    /*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
                    /*!40101 SET NAMES utf8mb4 */;
                    
                    CREATE DATABASE IF NOT EXISTS `" . $_POST['db'] ."` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
                    USE `". $_POST['db'] . "`;" . 
            file_get_contents(ROOT . 'installation/core/DiamondBDD-fromscratch.sql'));
        }
        catch (Exception $ec){
            $error = true;
            $msg = $ec->getMessage();
        }
    }
    
    if ($error == false){
        die('Success');
    }else{
        die('Erreur : ' . $msg);
    }

}