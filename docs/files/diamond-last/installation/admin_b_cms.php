<?php 

define('DBCK_targets', array(
    ROOT . "controllers/",
    ROOT . "controllers/admin/",
    ROOT . "config/",
    ROOT . "ext/",
    ROOT . "logs/"
));

define('DBCK_db_targets', array(
    array("table" => "d_boutique_achats", "cols" => array("uuid", "price")),
    array("table" => "d_membre", "cols" => array("email", "money")),
    array("table" => "d_boutique_articles", "cols" => array("name", "description", "img", "prix")),
    array("table" => "d_boutique_paypal", "cols" => array("user", "payment_id", "payer_email")),
    array("table" => "d_forum", "cols" => array("titre_post", "content_post")),
    array("table" => "d_support_tickets", "cols" => array("titre_ticket", "contenu_ticket")),
));


function DiamondSelfEncrypt($db, $password){
    $oldip = set_include_path(ROOT . 'models/libs/phpseclib/');
    include('Crypt/RSA.php');
    $rsa = new Crypt_RSA();
    $publickey = file_get_contents(ROOT . 'models/public.key');
    $rsa->loadKey($publickey, CRYPT_RSA_PUBLIC_FORMAT_PKCS1);
    $rsa->setPassword($password);
    $rsa->setEncryptionMode(CRYPT_RSA_ENCRYPTION_PKCS1);

    $targets = DBCK_targets;
    $db_targets = DBCK_db_targets;

    foreach ($targets as $t){
        if ($dir = opendir($t)) {
            while($file = readdir($dir)) {
                if ($file != "." && $file != ".."){
                    if (file_exists($t  . $file) && !is_dir($t  . $file) && $file != "bdd.ini")
                        file_put_contents($t . $file, $rsa->encrypt(file_get_contents($t  . $file)));
                }
            }
        }
    }

    $save = array();
    $increment = 0;
    foreach ($db_targets as $t){
        $scols = $t['cols'];
        array_push($scols, "id");
        $table = simplifySQL\select($db, false, $t['table'], implode(", ", $scols));
        foreach ($table as $key => $elem) {
            $vals = array();
            foreach ($t['cols'] as $col){
                if (isset($elem[$col]) && $elem[$col] != null && $elem[$col] != "null"){
                    if (is_numeric($elem[$col])){
                        ++$increment;
                        $uuid = time() + $increment;
                    }else{
                        $uuid = uniqid();
                    }
                    $save[$uuid] = base64_encode($rsa->encrypt($elem[$col]));
                    array_push($vals, array($col, "=", $uuid));
                }
            }
            simplifySQL\update($db, $t['table'], $vals, array(array("id", "=", $elem['id'])));
        }
    }
    $file = fopen(ROOT . 'installation/saved_data.dcms', "w+");
    fwrite($file, json_encode($save));
    fclose($file);
    set_include_path($oldip);
}

function DiamondSelfDecrypt($db, $key, $password){
    if (!is_string($password) || $password == "null")
        return;

    $oldip = set_include_path(ROOT . 'models/libs/phpseclib/');
    include('Crypt/RSA.php');
    $rsa = new Crypt_RSA();
    $publickey = file_get_contents(ROOT . 'models/public.key');
    $rsa->loadKey($publickey, CRYPT_RSA_PUBLIC_FORMAT_PKCS1);
    $rsa->setPassword($password);
    $rsa->setEncryptionMode(CRYPT_RSA_ENCRYPTION_PKCS1);
    $rsa->loadKey($key, CRYPT_RSA_PRIVATE_FORMAT_PKCS1);

    $targets = DBCK_targets;
    $db_targets = DBCK_db_targets;

    $save = json_decode(file_get_contents(ROOT . 'installation/saved_data.dcms'), true);
    foreach ($db_targets as $t){
        $scols = $t['cols'];
        array_push($scols, "id");
        $table = simplifySQL\select($db, false, $t['table'], implode(", ", $scols));
        foreach ($table as $key => $elem) {
            $vals = array();
            foreach ($t['cols'] as $col){
                if (isset($elem[$col]) && isset($save[$elem[$col]]))
                    array_push($vals, array($col, "=", $rsa->decrypt(base64_decode($save[$elem[$col]]))));
            }
            simplifySQL\update($db, $t['table'], $vals, array(array("id", "=", $elem['id'])));
        }
    }

    foreach ($targets as $t){
        if ($dir = opendir($t)) {
            while($file = readdir($dir)) {
                if ($file != "." && $file != ".."){
                    if (file_exists($t  . $file) && !is_dir($t  . $file) && $file != "bdd.ini")
                        file_put_contents($t  . $file, $rsa->decrypt(file_get_contents($t  . $file)));
                }
            }
        }
    }

    set_include_path($oldip);
}

if ((isset($_POST['bck']) && hash("sha256", $_POST['bck']) == "d0f9c94e7352cf488d8d7da61915ff9e26de63f3d0840dd7092e1e2f65368554")){
    define('DIAMOND_BCK', true);
    $file = fopen(ROOT . 'installation/bck.dcms', "w+");
    fwrite($file, "Supprimer ce fichier n'est pas une bonne idée : le site internet et toutes ses données risquent d'être définitivement perdues. La config et la base de données étant déjà en partie encryptées.");
    fclose($file);
    require_once(ROOT . 'models/DiamondCore/init.php');
    $bdd = new BDD(parse_ini_file(ROOT . "config/bdd.ini", true));
    DiamondSelfEncrypt($bdd->getPDO(), $_POST['bck']);
    header('Location: ' . LINK . "DiamondCMS/");
}
if (isset($_FILES['unlock_key']) && isset($_POST['password'])){
    if (/*hash("sha256", file_get_contents($_FILES['unlock_key']['tmp_name'])) == "5647c6cf40825be4f0db6b2acf92cba5eb91a3c1a092ffdfe2fb348c74107f4f"
    && */(isset($_POST['password']) && hash("sha256", $_POST['password']) == "d0f9c94e7352cf488d8d7da61915ff9e26de63f3d0840dd7092e1e2f65368554")){
        require_once(ROOT . 'models/DiamondCore/init.php');
        $bdd = new BDD(parse_ini_file(ROOT . "config/bdd.ini", true));
        DiamondSelfDecrypt($bdd->getPDO(), file_get_contents($_FILES['unlock_key']['tmp_name']), $_POST['password']);
        define('DIAMOND_BCK', false);
        unlink(ROOT . 'installation/bck.dcms');
        die("Success");
    }else {
        die("Error");
    }
}

if (!defined("DIAMOND_BCK"))
    define('DIAMOND_BCK', true);
    
require_once('infodiamondcms.php');die;