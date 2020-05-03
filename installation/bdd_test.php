<?php
//Ce controleur, associé à l'étape 2, permet, via AJAX de tester des identifiants de connexion SQL et de les définir si besoin
define('ROOT', str_replace('installation/bdd_test.php','', $_SERVER['SCRIPT_FILENAME']));
require_once(ROOT . 'models/DiamondCore/bdd_connexion.php');
$bddtest = new BDD(array("host" => $_POST['host'], "db" => $_POST['db'], "usr" => $_POST['usr'], "pwd" => $_POST['psw'], "port" => $_POST['port']));
try{
    $test = $bddtest->testPDO();
}catch (Exception $e){
    echo $e->getMessage();
    die;
}
if (isset($_POST['type']) && $_POST['type'] == "def"){
    require_once(ROOT . 'models/ini.php');
    $bddtest->changeConfig($_POST['host'], $_POST['db'], $_POST['usr'], $_POST['psw'], $_POST['port']);
    die ('Success');
}else if (isset($_POST['type']) && $_POST['type'] == "install"){
    $error = false;
    $msg = "";
    try {
        $bddtest->getPDO()->exec(file_get_contents(ROOT . 'installation/core/DiamondBDD.sql'));
    }
    catch (Exception $e){
        $error = true;
        $msg = $e->getMessage();
    }
    if ($error == false){
        die('Success');
    }else{
        die('Erreur : ' . $msg);
    }

}