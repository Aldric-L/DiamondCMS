<?php 
// Gestionnaire d'erreurs
function diamondInstallerErrorHandler($errno, $errstr, $errfile, $errline)
{
   if (!(error_reporting() & $errno)) {
        // Ce code d'erreur n'est pas inclus dans error_reporting(), donc il continue
        // jusqu'au gestionaire d'erreur standard de PHP
        return;
    }

    var_dump($errstr);
    switch ($errno) {
        case E_USER_ERROR:
            $type = "Erreur fatale";
            require_once(ROOT . 'installation/exceptions.php');
            exit(1);
            break;

        case E_USER_WARNING:
            $type = "Alerte";
            require_once(ROOT . 'installation/exceptions.php');
            die;
            break;

        case E_USER_NOTICE:
            $type = "Avertissement";
            require_once(ROOT . 'installation/exceptions.php');
            die;
            break;

        default:
            $type = "Inconnu";
            require_once(ROOT . 'installation/exceptions.php');
            die;
            break;
    }
    /* Ne pas exécuter le gestionnaire interne de PHP */
    return true;
}
