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
$commande = simplifySQL\select($controleur_def->bddConnexion(), true, "d_boutique_achats", "*", array(array("uuid", "=", $uuid)));
//Pour les erreurs, on utilise pas l'utilitaire d'erreur de controleur_def puisqu'il sous-entend charger la vue en entier pour afficher un modal, or on ne peut pas prendre le risque de charger la page, donc on lève une erreur qui va interrompre l'éxecution de la page, et rediriger l'utilisateur.
if (empty($commande)){
    $erreur = "Impossible de charger la commande demandée : êtes-vous vraiment un acheteur ? Sachez que toute tentative de piratage peut faire l'objet de poursuites.";
    $controleur_def->loadView('pages/boutique/getback', 'emptyServer', 'Récupération de l\'achat');
    die;
}
if (!isset($_SESSION['user']) || empty($_SESSION['user'])){
    $erreur = "Impossible de charger votre compte client : êtes-vous vraiment un acheteur ? Sachez que toute tentative de piratage peut faire l'objet de poursuites.";
    $controleur_def->loadView('pages/boutique/getback', 'emptyServer', 'Récupération de l\'achat');
    die;
}
if ($_SESSION['user']->getId() != $commande['id_user']){
    $erreur = "Impossible de trouver la commande demandée : êtes-vous vraiment l'acheteur de cette dernière ? Sachez que toute tentative de piratage peut faire l'objet de poursuites.";
    $controleur_def->loadView('pages/boutique/getback', 'emptyServer', 'Récupération de l\'achat');
    die;
}

//Si on passe en mode XHR
// param 3 : test/get
// param 4 : id de la tache
// param 5 : pseudo à tester
if (isset($param[3]) && !empty($param[3]) && ($param[3] == "test" || $param[3] == "get") && isset($param[4]) && !empty($param[4]) && isset($param[5]) && !empty($param[5])){
    //On récupère la tache :
    $task = simplifySQL\select($controleur_def->bddConnexion(), true, "d_boutique_todolist", "*", array( array( "id", "=", intval($param[4]) ) ));
    if (empty($task)){
        die('Impossible de trouver la tâche (Erreur critique, contacter un administrateur)');
    }

    if ($task['done'] == true || $task['done'] == '1'){
        die('Cette tâche a déjà été traitée.');
    }

    //On récupère la commande correspondante
    $task['cmd'] = simplifySQL\select($controleur_def->bddConnexion(), true, "d_boutique_cmd", "*", array(array("id", "=", $task['cmd'])));        
    if (empty($task['cmd'])){
        die('Impossible de trouver la commande à éxecuter (Erreur critique, contacter un administrateur)');
    }

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
    }else if ($conf[$task['cmd']['server']]['enabled'] == false || $conf[$task['cmd']['server']]['enabled'] == "false"){
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
}


//On suppose que si l'on en arrive ici, c'est que l'utilisateur est authentique et qu'on passe en mode AFFICHAGE
//On charge les taches à faire
$tasks = simplifySQL\select($controleur_def->bddConnexion(), false, "d_boutique_todolist", "*", array(array("id_commande", "=", $commande['id']), "AND", array("done", "=", 0)));
foreach ($tasks as $k => $t){
    //On récupère la commande correspondante
    $tasks[$k]['cmd'] = simplifySQL\select($controleur_def->bddConnexion(), true, "d_boutique_cmd", "*", array(array("id", "=", $t['cmd'])));
    if (defined("DServerLink") && DServerLink){    
        $tasks[$k]['cmd']['server_name'] = $cm->getConfig()[$tasks[$k]['cmd']['server']]['name'];
        $tasks[$k]['cmd']['server_game'] = $cm->getConfig()[$tasks[$k]['cmd']['server']]['game'];
    }else {
        $tasks[$k]['cmd']['server_name'] = "";
        $tasks[$k]['cmd']['server_game'] = "";
    }
}

$tasks_done = simplifySQL\select($controleur_def->bddConnexion(), false, "d_boutique_todolist", "*", array(array("id_commande", "=", $commande['id']), "AND", array("done", "=", 1)));

foreach ($tasks_done as $k => $t){
    //On récupère la commande correspondante
    $tasks_done[$k]['cmd'] = simplifySQL\select($controleur_def->bddConnexion(), true, "d_boutique_cmd", "*", array(array("id", "=", $t['cmd'])));    
    if (defined("DServerLink") && DServerLink){    
        $tasks[$k]['cmd']['server_name'] = $cm->getConfig()[$tasks[$k]['cmd']['server']]['name'];
        $tasks[$k]['cmd']['server_game'] = $cm->getConfig()[$tasks[$k]['cmd']['server']]['game'];
    }else {
        $tasks[$k]['cmd']['server_name'] = "";
        $tasks[$k]['cmd']['server_game'] = "";
    }
}
//var_dump($tasks, $commande, $tasks_done);
$controleur_def->loadJS('getback');
$controleur_def->loadView('pages/boutique/getback', 'boutique', 'Récupération de l\'achat');
