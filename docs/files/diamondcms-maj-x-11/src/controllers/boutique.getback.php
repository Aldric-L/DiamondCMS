<?php 
global $cm;
//Ce controlleur a pour fonction d'éxecuter les commandes pour permettre à l'utilisateur de recevoir son dû après un payement sur la boutique
//Il doit toujours être appelé avec un identifiant unique, qui permet de retrouver la commande (l'uuid est évidemment contrôlé)
if (!isset($param[1]) || empty($param[1]) || !isset($param[2]) || empty($param[2])){
    require_once (ROOT . "controllers/accueil.php");
    die;
}
$uuid = $param[2];
//On essaye de charger la commande 
$commande = cleanIniTypes(simplifySQL\select($controleur_def->bddConnexion(), true, "d_boutique_achats", "*", array(array("uuid", "=", $uuid))));
//Pour les erreurs, on utilise pas l'utilitaire d'erreur de controleur_def puisqu'il sous-entend charger la vue en entier pour afficher un modal, or on ne peut pas prendre le risque de charger la page, donc on lève une erreur qui va interrompre l'éxecution de la page, et rediriger l'utilisateur.
if (empty($commande)){
    $erreur = "Impossible de charger la commande demandée : êtes-vous vraiment un acheteur ? Sachez que toute tentative de piratage peut faire l'objet de poursuites.<br> Numéro de commande transmis : " . $uuid;
    $controleur_def->nonifyPage("Impossible de poursuivre", "Une erreur interne grave est survenue", $erreur);
    //$controleur_def->loadView('pages/boutique/getback', 'emptyServer', 'Récupération de l\'achat');
    die;
}
if (!isset($_SESSION['user']) || empty($_SESSION['user'])){
    $erreur = "Impossible de charger votre compte client : êtes-vous vraiment un acheteur ? Sachez que toute tentative de piratage peut faire l'objet de poursuites.<br> Numéro de commande transmis : " . $uuid;
    $controleur_def->nonifyPage("Impossible de poursuivre", "Une erreur interne grave est survenue", $erreur);
    //$controleur_def->loadView('pages/boutique/getback', 'emptyServer', 'Récupération de l\'achat');
    die;
}
if ($_SESSION['user']->getId() != $commande['id_user'] && !(isset($param[3]) && !empty($param[3]) && $param[3] == "ADMIN-IFRAME" && isset($_SESSION['user']) && $_SESSION['user']->isAdmin())){
    $erreur = "Impossible de trouver la commande demandée : êtes-vous vraiment l'acheteur de cette dernière ? Sachez que toute tentative de piratage peut faire l'objet de poursuites.<br> Numéro de commande transmis : " . $uuid;
    $controleur_def->nonifyPage("Impossible de poursuivre", "Une erreur interne grave est survenue", $erreur);
    //$controleur_def->loadView('pages/boutique/getback', 'emptyServer', 'Récupération de l\'achat');
    die;
}
//On récupère aussi l'article 
$article = cleanIniTypes(simplifySQL\select($controleur_def->bddConnexion(), true, "d_boutique_articles", "*", array(array("id", "=", $commande['id_article']))));

//Si on passe en mode XHR
// param 3 : test/get
// param 4 : id de la tache
// param 5 : pseudo à tester
if (isset($param[3]) && !empty($param[3]) && ($param[3] == "test" || $param[3] == "get") && isset($param[4]) && !empty($param[4])){
    define('FORCE_INLINE_ERR', true);
    //On récupère la tache :
    $task = cleanIniTypes(simplifySQL\select($controleur_def->bddConnexion(), true, "d_boutique_todolist", "*", array( array( "id", "=", intval($param[4]) ) )));
    if (empty($task)){
        die('Impossible de trouver la tâche (Erreur critique, contacter un administrateur)');
    }

    if ($task['done'] == true){
        die('Cette tâche a déjà été traitée.');
    }

    //On récupère la commande correspondante
    $task['cmd'] = cleanIniTypes(simplifySQL\select($controleur_def->bddConnexion(), true, "d_boutique_cmd", "*", array(array("id", "=", $task['cmd']))));        
    if (empty($task['cmd'])){
        die('Impossible de trouver la commande à éxecuter (Erreur critique, contacter un administrateur)');
    }

    if (is_array($task['cmd']) && isset($task['cmd']['server']) && ($task['cmd']['server'] == -1)){

        $cmd_aliastable = array(
            "{PLAYER}" => $_SESSION['user']->getPseudo(),
            "{USER_ID}" => $_SESSION['user']->getId(),
        );

        $cmd_raw = $task['cmd']['cmd'];
        foreach($cmd_aliastable as $key => $alias){
            $cmd_raw = str_replace($key, $alias, $cmd_raw);
        }

        $controleur_def->loadModel("api.class");
        try {
            $cmd = DiamondAPI::cmd_parser($cmd_raw);
        } catch (\Throwable $th) {
            die("Erreur interne (Exception) : Impossible de parser la commande à exécuter. Cette dernière ne pourra jamais être exécutée (" . $cmd_raw . ").");
        }

        if (!is_array($cmd) || !is_array($cmd["request"]) || sizeof($cmd["request"]) !== 3)
            die("Erreur interne (parser) : Impossible de parser la commande à exécuter. Cette dernière ne pourra jamais être exécutée (" . $cmd_raw . ").");

        //Si on est en mode test on s'arrête là
        if ($param[3] == "test"){
            die("Test OK: Tout est prêt !");
        }

        try {
            $rtrn = DiamondAPI::execute(false, $controleur_def, $controleur_def->getAvailableAddons(), $cmd['request'][0], $cmd['request'][1], $cmd['request'][2], 
                                        DiamondAPI::MAXLEVEL, $_SESSION['user'], 
                                        (is_array($cmd["arguments"]) && !empty($cmd["arguments"]) ? $cmd["arguments"] : null));
        } catch (\Throwable $th) {
            die("Erreur interne (Exception) : Impossible d'exécuter la commande. Erreur levée :" . $th->getMessage() . ".");
        }

        if (!(is_bool($rtrn["output_buffer"]) || (is_array($rtrn["output_buffer"]) && isset($rtrn["output_buffer"]["State"]) && (intval($rtrn["output_buffer"]["State"]) == 1))))
            die('Une erreur est revenue après l\'envoi de la requête : ' . $rtrn["json_result"]);

        //On termine en modifiant la tache pour indiquer qu'elle a été traitée.
        if (!simplifySQL\update($controleur_def->bddConnexion(), "d_boutique_todolist", 
        array(
            array("done", "=", 1), 
            array("date_done", "=", date("Y-m-d H:i:s"))
        ), array(array("id", "=", $task['id'])))){
            die('SQL ERROR ! Vous avez bien reçu votre lot, mais contactez un administrateur car une erreur est survenue.');
        }

        //On vérifie dans la liste des choses à faire que celle-ci n'était pas la dernière
        $tasks = simplifySQL\select($controleur_def->bddConnexion(), false, "d_boutique_todolist", "*", array(array("id_commande", "=", $task['id_commande']), "AND", array("done", "=", 0)));

        if (empty($tasks)){
            if (!simplifySQL\update($controleur_def->bddConnexion(), "d_boutique_achats", 
                array(
                    array("success", "=", 1)
                ), array(array("id", "=", $task['id_commande'])))){
                    die('SQL ERROR ! Vous avez bien reçu votre lot, mais contactez un administrateur car une erreur est survenue. (2)');
            }
        }
        die('Success !');


    }else if (isset($param[5]) && !empty($param[5])){
        $param[5] = str_replace("_", " ", $param[5]);
        if (!defined("DServerLink") || !DServerLink){
            die('Impossible de trouver l\'addon Diamond-ServerLink (Erreur critique, contacter un administrateur)');        
        }
    
        $connexion_needed = false;
        if ($task['cmd']['connexion_needed'] == '1'){
            $connexion_needed = true;
        }
    
        $pseudo = $param[5];
    
        //On utilise config Manager pour vérifier si le serveur est activé
        $conf = $cm->getConfig();
        if (!isset($conf[$task['cmd']['server']])){
            die('Impossible de trouver le serveur demandé dans la configuration du site : contactez l\'administrateur');
        }else if ($conf[$task['cmd']['server']]['enabled'] == false ){
            die('Le serveur demandé est désactivé dans la configuration du site : contactez l\'administrateur');
        }
    
        //On initie une connexion QUERY pour vérifier la présence du joueur
        $query = new DServerLink\Query($controleur_def, $cm, true);  
        //On initie la connexion avec le serveur demandé
        $query->connect($task['cmd']['server']);
        if (!empty($query->getErrors())){
            $error = "";
            foreach ($query->getErrors() as $e){
                $error .= $query->getErrors();
                $error .= ". ";
            }
            die($error);
        }
        $infos = $query->getInfos($task['cmd']['server']);
        if (!empty($query->getErrors())){
            $error = "";
            foreach ($query->getErrors() as $e){
                $error .= $controleur_def->getContentError($e);
                if ($e == 410 || $e == "410a" || $e == "410b" || $e == "410c"){
                    die('Impossible de joindre le serveur. Réessayez plus tard.');
                }
                $error .= ". ";
            }
            $query->disconnect($task['cmd']['server']);
            die($error);
        }
    
        if ($infos[$task['cmd']['server']]['results'] == false){
            $query->disconnect($task['cmd']['server']);
            die('Le serveur n\'est pas connecté, réessayez plus tard.');
        }
    
        $players = $query->getPlayers($task['cmd']['server']);
        if (!empty($query->getErrors())){
            $error = "";
            foreach ($query->getErrors() as $e){
                $error .= $controleur_def->getContentError($e);
                $error .= ". ";
            }
            $query->disconnect($task['cmd']['server']);
            die($error);
        }
        if ($connexion_needed == true && $players[$task['cmd']['server']]['results'] == false){
            die('Aucun joueur n\'est connecté, connectez vous !');
        }
    
        $game = $query->getGame($task['cmd']['server']);
        if (!empty($query->getErrors())){
            $error = "";
            foreach ($query->getErrors() as $e){
                $error .= $controleur_def->getContentError($e);
                $error .= ". ";
            }
            $query->disconnect($task['cmd']['server']);
            die($error);
        }
    
        if ($game == "Minecraft-MPCE"){
            die('Impossible d\'envoyer une commande sur le serveur : jeu non-supporté par DiamondCMS.');
        }
    
        $is_connected = false;
        if ($game == "Minecraft-Java" && $players[$task['cmd']['server']]['results'] != false){
            foreach ($players[$task['cmd']['server']]['results'] as $p){
                if ($p == $pseudo){
                    $is_connected = true;
                    break;
                }
            }
        }else if ($players[$task['cmd']['server']]['results'] != false){
            foreach ($players[$task['cmd']['server']]['results'] as $p){
                if ($p['Name'] == $pseudo){
                    $is_connected = true;
                    break;
                }
            }
        }
    
        //Si on est en mode test on s'arrête là
        if ($param[3] == "test"){
            if (!$is_connected){
                if ($connexion_needed){
                    $query->disconnect($task['cmd']['server']);
                    die('Vous n\'êtes pas connecté au serveur de jeu');
                }else {
                    $query->disconnect($task['cmd']['server']);
                    die('Test OK: (Attention: Vous n\'êtes pas connecté au serveur de jeu)');
                }
            }else {
                $query->disconnect($task['cmd']['server']);
                die('Test OK: Votre compte en-jeu a bien été trouvé.');
            }
        //Sinon on démarre le RCON !
        }
        
        if (!$is_connected){
            if ($connexion_needed){
                $query->disconnect($task['cmd']['server']);
                die('Vous n\'êtes pas connecté au serveur de jeu');
            }
        }
        
        $query->disconnect($task['cmd']['server']);
        if (!empty($query->getErrors())){
            $error = "";
            foreach ($query->getErrors() as $e){
                $error .= $controleur_def->getContentError($e);
                $error .= ". ";
            }
            die($error);
        }
    
        //On initialise la connexion RCON
        $rcon = new DServerLink\RCon($controleur_def, $cm, true);
        $rcon->connect($task['cmd']['server']);
        if (!empty($rcon->getErrors())){
            $error = "";
            foreach ($rcon->getErrors() as $e){
                $error .= $controleur_def->getContentError($e);
                $error .= ". ";
            }
            $rcon->disconnect($task['cmd']['server']);
            die($error);
        }
        //var_dump(str_replace("{PLAYER}", $pseudo, $task['cmd']['cmd']));
        $rcon->execOnServer($task['cmd']['server'], str_replace("{PLAYER}", $pseudo, $task['cmd']['cmd']));
        if (!empty($rcon->getErrors())){
            $error = "";
            foreach ($rcon->getErrors() as $e){
                $error .= $controleur_def->getContentError($e);
                $error .= ". ";
            }
            $rcon->disconnect($task['cmd']['server']);
            die($error);
        }
        $rcon->disconnect($task['cmd']['server']);
        if (!empty($rcon->getErrors())){
            $error = "";
            foreach ($rcon->getErrors() as $e){
                $error .= $controleur_def->getContentError($e);
                $error .= ". ";
            }
            die($error);
        }
    }else if (!(isset($param[5]) && !empty($param[5]))){
        die("Error: Aucun pseudo fourni.");
    }

    //On termine en modifiant la tache pour indiquer qu'elle a été traitée.
    if (!simplifySQL\update($controleur_def->bddConnexion(), "d_boutique_todolist", 
    array(
        array("done", "=", 1), 
        array("date_done", "=", date("Y-m-d H:i:s"))
    ), array(array("id", "=", $task['id'])))){
        die('SQL ERROR ! Vous avez bien reçu votre lot, mais contactez un administrateur car une erreur est survenue.');
    }

    //On vérifie dans la liste des choses à faire que celle-ci n'était pas la dernière
    $tasks = simplifySQL\select($controleur_def->bddConnexion(), false, "d_boutique_todolist", "*", array(array("id_commande", "=", $task['id_commande']), "AND", array("done", "=", 0)));

    if (empty($tasks)){
        if (!simplifySQL\update($controleur_def->bddConnexion(), "d_boutique_achats", 
            array(
                array("success", "=", 1)
            ), array(array("id", "=", $task['id_commande'])))){
                die('SQL ERROR ! Vous avez bien reçu votre lot, mais contactez un administrateur car une erreur est survenue. (2)');
        }
    }

    die('Success !');

    //var_dump($infos, $players, $game); die;
}else if (isset($param[3]) && !empty($param[3]) && $param[3] == "ADMIN-IFRAME" && isset($_SESSION['user']) && $_SESSION['user']->isAdmin()){
    define("IFRAMER", true);
}


//On suppose que si l'on en arrive ici, c'est que l'utilisateur est authentique et qu'on passe en mode AFFICHAGE
//On charge les taches automatiques à faire
$tasks = simplifySQL\select($controleur_def->bddConnexion(), false, "d_boutique_todolist", "*", array(array("id_commande", "=", $commande['id']),  "AND", array("done", "=", 0)));
$death_list = array();
foreach ($tasks as $k => $t){
    //On récupère la commande correspondante
    $tasks[$k]['cmd'] = simplifySQL\select($controleur_def->bddConnexion(), true, "d_boutique_cmd", "*", array(array("id", "=", $t['cmd'])));
    if (is_array($tasks[$k]['cmd']) && $tasks[$k]['cmd']['is_manual'] == true){
        array_push($death_list, $k);
    }
    if (is_array($tasks[$k]['cmd']) && $tasks[$k]['cmd']['is_manual'] == false && $tasks[$k]['cmd'] != false && ($tasks[$k]['cmd']['server'] == "-1" || $tasks[$k]['cmd']['server'] == -1)) {
            $tasks[$k]['cmd']['server_name'] = "Site internet";
            $tasks[$k]['cmd']['server_game'] = "API Web";
    }else if (is_array($tasks[$k]['cmd']) && $tasks[$k]['cmd']['is_manual'] == false && $tasks[$k]['cmd'] != false) {
        $tasks[$k]['cmd']['server_name'] = $cm->getConfig()[$tasks[$k]['cmd']['server']]['name'];
        $tasks[$k]['cmd']['server_game'] = $cm->getConfig()[$tasks[$k]['cmd']['server']]['game'];
    }else {
        $tasks[$k]['cmd']['server_name'] = "";
        $tasks[$k]['cmd']['server_game'] = "";
    }
    
}
foreach ($death_list as $d){
    unset($tasks[$d]);
}
sort($tasks);

$death_list = array();

//On charge les taches manuelles à faire
$tasks_man = simplifySQL\select($controleur_def->bddConnexion(), false, "d_boutique_todolist", "*", array(array("id_commande", "=", $commande['id']), "AND", array("done", "=", 0)));
foreach ($tasks_man as $k => $t){
    //On récupère la commande correspondante
    $tasks_man[$k]['cmd'] = simplifySQL\select($controleur_def->bddConnexion(), true, "d_boutique_cmd", "*", array(array("id", "=", $t['cmd'])));
    if (is_array($tasks_man[$k]['cmd']) && $tasks_man[$k]['cmd']['is_manual'] == false){
        array_push($death_list, $k);
    }
}
foreach ($death_list as $d){
    unset($tasks_man[$d]);
}
sort($tasks_man);


$tasks_done = simplifySQL\select($controleur_def->bddConnexion(), false, "d_boutique_todolist", "*", array(array("id_commande", "=", $commande['id']), "AND", array("done", "=", 1)));
$tasks_done = cleanIniTypes($tasks_done);

foreach ($tasks_done as $k => $t){
    //On récupère la commande correspondante
    $tasks_done[$k]['cmd'] = simplifySQL\select($controleur_def->bddConnexion(), true, "d_boutique_cmd", "*", array(array("id", "=", $t['cmd']))); 
    $tasks_done[$k]['cmd'] = cleanIniTypes($tasks_done[$k]['cmd']);

    if (is_array($tasks_done[$k]['cmd'])){
        if ($tasks_done[$k]['cmd']['is_manual'] != true && $tasks_done[$k]['cmd']['server'] == -1){
            $tasks_done[$k]['cmd']['server_name'] = "Site internet";
            $tasks_done[$k]['cmd']['server_game'] = "API Web";
        }else if ($tasks_done[$k]['cmd']['is_manual'] == false && $tasks_done[$k]['cmd'] != false) {
            $tasks_done[$k]['cmd']['server_name'] = $cm->getConfig()[$tasks_done[$k]['cmd']['server']]['name'];
            $tasks_done[$k]['cmd']['server_game'] = $cm->getConfig()[$tasks_done[$k]['cmd']['server']]['game'];
        }else {
            $tasks_done[$k]['cmd']['server_name'] = "";
            $tasks_done[$k]['cmd']['server_game'] = "";
        }
    }
}
//var_dump($tasks, $commande, $tasks_done, $tasks_man);
$controleur_def->loadJS('getback');
$controleur_def->loadView('pages/boutique/getback', 'boutique', 'Récupération de l\'achat');
