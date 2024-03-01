<?php 
/**
 * diamondInstallerErrorHandler - Fonction qui gère les erreurs levées AVANT l'initialisation de DiamondCORE, et dans la partie installation
 * Elle log les erreurs, et les traite en fonction des constantes définies (FORCE_NO_ERR, FORCE_INLINE_ERR, DEV_MODE)
 * 
 * @author Aldric L. (inspiré de la PHP doc)
 * @copyright 2020
 * 
 * @param int $errno : Type d'erreur
 * @param string $errstr : Message d'erreur
 * @param string $errfile : Fichier où se situe l'erreur
 * @param int $errline : Ligne où se situe l'erreur
 * 
 * @return true|void
 */
function diamondInstallerErrorHandler($errno, $errstr, $errfile, $errline)
{
   if (!(error_reporting() & $errno)) {
        // Ce code d'erreur n'est pas inclu dans error_reporting(), donc il continue
        // jusqu'au gestionaire d'erreur standard de PHP
        return;
    }

    if (defined("FORCE_NO_ERR") && FORCE_NO_ERR){
        @file_put_contents(ROOT . 'logs/dev_errors.log', 'Type: ' . $errno . ' - ' . date("j/m/y à H:i:s") . " - " . $errstr . ". Affichée : Non (Mode sans erreur) - Levée dans " . $errfile . " à la ligne " . $errline . "." . " \r\n".@file_get_contents(ROOT . "logs/dev_errors.log"));
        return true;
    }

    switch ($errno) {
        case E_USER_ERROR:
        case E_ERROR:
            @file_put_contents(ROOT . 'logs/dev_errors.log', 'Type: Fatale - ' . date("j/m/y à H:i:s") . " - " . $errstr . ". Affichée : Oui - Levée dans " . $errfile . " à la ligne " . $errline . "." . " \r\n".@file_get_contents(ROOT . "logs/dev_errors.log"));
            if (defined("FORCE_INLINE_ERR") && FORCE_INLINE_ERR){
                die("Erreur fatale: " . $errstr . ". Levée dans " . $errfile . " à la ligne " . $errline . ".");
            }
            $type = "Erreur fatale";
            require_once(ROOT . 'installation/exceptions.php');
            die;
            break;

        case E_USER_WARNING:
        case E_WARNING:
            if (!defined("DEV_MODE") || !DEV_MODE){
                @file_put_contents(ROOT . 'logs/dev_errors.log', 'Type: Alerte - ' . date("j/m/y à H:i:s") . " - " . $errstr . ". Affichée : Non - Levée dans " . $errfile . " à la ligne " . $errline . "." . " \r\n".@file_get_contents(ROOT . "logs/dev_errors.log"));
                return true;
            }
            @file_put_contents(ROOT . 'logs/dev_errors.log', 'Type: Alerte - ' . date("j/m/y à H:i:s") . " - " . $errstr . ". Affichée : Oui - Levée dans " . $errfile . " à la ligne " . $errline . "." . " \r\n".@file_get_contents(ROOT . "logs/dev_errors.log"));
            if (defined("FORCE_INLINE_ERR") && FORCE_INLINE_ERR){
                echo "Alerte : " . $errstr . ". Levée dans " . $errfile . " à la ligne " . $errline . ".";
            }else {
                $type = "Alerte";
                require_once(ROOT . 'installation/exceptions.php');
                die;
                break;
            } 
        break;
        
        case E_USER_NOTICE:
        case E_NOTICE: 
            if (!defined("DEV_MODE") || !DEV_MODE){
                @file_put_contents(ROOT . 'logs/dev_errors.log', 'Type: Notice - ' . date("j/m/y à H:i:s") . " - " . $errstr . ". Affichée : Non - Levée dans " . $errfile . " à la ligne " . $errline . "." . " \r\n".@file_get_contents(ROOT . "logs/dev_errors.log"));
                return true;
            }
            @file_put_contents(ROOT . 'logs/dev_errors.log', 'Type: Notice - ' . date("j/m/y à H:i:s") . " - " . $errstr . ". Affichée : Oui - Levée dans " . $errfile . " à la ligne " . $errline . "." . " \r\n".@file_get_contents(ROOT . "logs/dev_errors.log"));
            if (defined("FORCE_INLINE_ERR") && FORCE_INLINE_ERR){
                echo "Notice : " . $errstr . ". Levée dans " . $errfile . " à la ligne " . $errline . ".";
            }else {
                $type = "Avertissement";
                require_once(ROOT . 'installation/exceptions.php');
                die;
                break;
            } 
        break;

        case E_PARSE: 
            if (!defined("DEV_MODE") || !DEV_MODE){
                @file_put_contents(ROOT . 'logs/dev_errors.log', 'Type: Syntaxe - ' . date("j/m/y à H:i:s") . " - " . $errstr . ". Affichée : Non - Levée dans " . $errfile . " à la ligne " . $errline . "." . " \r\n".@file_get_contents(ROOT . "logs/dev_errors.log"));
                return true;
            }
            @file_put_contents(ROOT . 'logs/dev_errors.log', 'Type: Syntaxe - ' . date("j/m/y à H:i:s") . " - " . $errstr . ". Affichée : Oui - Levée dans " . $errfile . " à la ligne " . $errline . "." . " \r\n".@file_get_contents(ROOT . "logs/dev_errors.log"));
            if (defined("FORCE_INLINE_ERR") && FORCE_INLINE_ERR){
                echo "Syntaxe : " . $errstr . ". Levée dans " . $errfile . " à la ligne " . $errline . ".";
            }else {
                $type = "Syntaxe";
                require_once(ROOT . 'installation/exceptions.php');
                die;
                break;
            } 
        break;

        default:
            if (!defined("DEV_MODE") || !DEV_MODE){
                @file_put_contents(ROOT . 'logs/dev_errors.log', 'Type: Inconnu - ' . date("j/m/y à H:i:s") . " - " . $errstr . ". Affichée : Non - Levée dans " . $errfile . " à la ligne " . $errline . "." . " \r\n".@file_get_contents(ROOT . "logs/dev_errors.log"));
                return true;
            }
            @file_put_contents(ROOT . 'logs/dev_errors.log', 'Type: Inconnu - ' . date("j/m/y à H:i:s") . " - " . $errstr . ". Affichée : Oui - Levée dans " . $errfile . " à la ligne " . $errline . "." . " \r\n".@file_get_contents(ROOT . "logs/dev_errors.log"));

            if (defined("FORCE_INLINE_ERR") && FORCE_INLINE_ERR){
                echo "Alerte : " . $errstr . ". Levée dans " . $errfile . " à la ligne " . $errline . ".";
            }else {
                $type = "Inconnu";
                require_once(ROOT . 'installation/exceptions.php');
                die;
            } 
        break;
            
    }
    return true;
}

/**
 * installerShut - Fonction qui gère les erreurs fatales levées AVANT l'initialisation de DiamondCORE, et dans la partie installation
 * Elle se charge simplement d'appeler la fonction diamondInstallerErrorHandler pour qu'elle traite l'erreur fatale relevée
 * 
 * @author Aldric L. (inspiré de la PHP doc)
 * @copyright 2020
 * @return void
 */
function installerShut(){

    $error = error_get_last();
    if ($error != NULL)
        diamondInstallerErrorHandler($error['type'], $error['message'], $error['file'], $error['line']);
}

/**
 * shut - Fonction qui gère les erreurs fatales levées APRES l'initialisation de DiamondCORE
 * Elle se charge simplement d'appeler la fonction diamondErrorHandler pour qu'elle traite l'erreur fatale relevée
 * 
 * @author Aldric L. (inspiré de la PHP doc)
 * @copyright 2020
 * @return void
 */
function shut(){

    $error = error_get_last();
    if ($error != NULL)
        diamondErrorHandler($error['type'], $error['message'], $error['file'], $error['line']);

}

/**
 * diamondErrorHandler - Fonction qui gère les erreurs levées APRES l'initialisation de DiamondCORE
 * Elle log les erreurs, et les traite en fonction des constantes définies (FORCE_NO_ERR, FORCE_INLINE_ERR, DEV_MODE)
 * Elle fait le lien avec DiamondCore en transformant certaines erreurs PHP en erreurs utilisables dans le controlleur et affichables dans le modal erreurs
 * 
 * La fonction convertit maintenant au maximum les erreurs en DiamondException
 * 
 * @author Aldric L. (inspiré de la PHP doc)
 * @copyright 2020, 2023
 * 
 * @param int $errno : Type d'erreur
 * @param string $errstr : Message d'erreur
 * @param string $errfile : Fichier où se situe l'erreur
 * @param int $errline : Ligne où se situe l'erreur
 * 
 * @return true|void
 */
function diamondErrorHandler($errno, $errstr, $errfile, $errline)
{
   if (!(error_reporting() & $errno)) {
        // Ce code d'erreur n'est pas inclu dans error_reporting(), donc il continue
        // jusqu'au gestionaire d'erreur standard de PHP
        return;
    }

    if (defined("FORCE_NO_ERR") && FORCE_NO_ERR){
        @file_put_contents(ROOT . 'logs/dev_errors.log', 'Type: ' . $errno . ' - ' . date("j/m/y à H:i:s") . " - " . $errstr . ". Affichée : Non (Mode sans erreur) - Levée dans " . $errfile . " à la ligne " . $errline . "." . " \r\n".@file_get_contents(ROOT . "logs/dev_errors.log"));
        return true;
    }

    switch ($errno) {
        case E_USER_ERROR:
        case E_ERROR:
            @file_put_contents(ROOT . 'logs/dev_errors.log', 'Type: Fatale - ' . date("j/m/y à H:i:s") . " - " . $errstr . ". Affichée : Oui - Levée dans " . $errfile . " à la ligne " . $errline . "." . " \r\n".@file_get_contents(ROOT . "logs/dev_errors.log"));
            if (defined("FORCE_INLINE_ERR") && FORCE_INLINE_ERR){
                die("Erreur fatale: " . $errstr . ". Levée dans " . $errfile . " à la ligne " . $errline . ".");
            }
            $type = "Erreur fatale";
            require_once(ROOT . 'installation/exceptions.php');
            die;
            break;

        case E_USER_WARNING:
        case E_WARNING:
            /*if (!defined("DEV_MODE") || !DEV_MODE){
                $GLOBALS['controleur_def']->addError(123);
                @file_put_contents(ROOT . 'logs/dev_errors.log', 'Type: Alerte - ' . date("j/m/y à H:i:s") . " - " . $errstr . ". Affichée : Non - Levée dans " . $errfile . " à la ligne " . $errline . "." . " \r\n".@file_get_contents(ROOT . "logs/dev_errors.log"));
                return true;
            }*/
            @file_put_contents(ROOT . 'logs/dev_errors.log', 'Type: Alerte - ' . date("j/m/y à H:i:s") . " - " . $errstr . ". Affichée : Oui - Levée dans " . $errfile . " à la ligne " . $errline . "." . " \r\n".@file_get_contents(ROOT . "logs/dev_errors.log"));
            if (defined("FORCE_INLINE_ERR") && FORCE_INLINE_ERR){
                echo "Alerte : " . $errstr . ". Levée dans " . $errfile . " à la ligne " . $errline . ".";
            }else if(defined("FORCE_EXC_ERR") && FORCE_EXC_ERR){
                throw new DiamondException("Erreur PHP E_WARNING (". $errno .") : ". $errstr . ". Ligne " .  $errline . " de " . $errfile, 123);
            }else {
                $type = "Alerte";
                require_once(ROOT . 'installation/exceptions.php');
                die;
                break;
            } 
        break;
        
        case E_USER_NOTICE:
        case E_NOTICE: 
            if (!defined("DEV_MODE") || !DEV_MODE){
                @file_put_contents(ROOT . 'logs/dev_errors.log', 'Type: Notice - ' . date("j/m/y à H:i:s") . " - " . $errstr . ". Affichée : Non - Levée dans " . $errfile . " à la ligne " . $errline . "." . " \r\n".@file_get_contents(ROOT . "logs/dev_errors.log"));
                return true;
            }
            $GLOBALS['controleur_def']->addError(125);
            @file_put_contents(ROOT . 'logs/dev_errors.log', 'Type: Notice - ' . date("j/m/y à H:i:s") . " - " . $errstr . ". Affichée : Oui - Levée dans " . $errfile . " à la ligne " . $errline . "." . " \r\n".@file_get_contents(ROOT . "logs/dev_errors.log"));
            if (defined("FORCE_INLINE_ERR") && FORCE_INLINE_ERR){
                echo "Notice : " . $errstr . ". Levée dans " . $errfile . " à la ligne " . $errline . ".";
            }else if(defined("FORCE_EXC_ERR") && FORCE_EXC_ERR){
                throw new DiamondException("Erreur PHP E_NOTICE (". $errno .") : ". $errstr . ". Ligne " .  $errline . " de " . $errfile, 123);
            }else {
                $type = "Avertissement";
                require_once(ROOT . 'installation/exceptions.php');
                die;
                break;
            } 
        break;

        case E_PARSE: 
            if (!defined("DEV_MODE") || !DEV_MODE){
                @file_put_contents(ROOT . 'logs/dev_errors.log', 'Type: Syntaxe - ' . date("j/m/y à H:i:s") . " - " . $errstr . ". Affichée : Non - Levée dans " . $errfile . " à la ligne " . $errline . "." . " \r\n".@file_get_contents(ROOT . "logs/dev_errors.log"));
                return true;
            }
            $GLOBALS['controleur_def']->addError(124);
            @file_put_contents(ROOT . 'logs/dev_errors.log', 'Type: Syntaxe - ' . date("j/m/y à H:i:s") . " - " . $errstr . ". Affichée : Oui - Levée dans " . $errfile . " à la ligne " . $errline . "." . " \r\n".@file_get_contents(ROOT . "logs/dev_errors.log"));

            if (defined("FORCE_INLINE_ERR") && FORCE_INLINE_ERR){
                echo "Syntaxe : " . $errstr . ". Levée dans " . $errfile . " à la ligne " . $errline . ".";
            }else if(defined("FORCE_EXC_ERR") && FORCE_EXC_ERR){
                throw new DiamondException("Erreur PHP E_PARSE (". $errno .") : ". $errstr . ". Ligne " .  $errline . " de " . $errfile, 123);
            }else {
                $type = "Syntaxe";
                require_once(ROOT . 'installation/exceptions.php');
                die;
                break;
            }
        break;

        case E_USER_DEPRECATED:
        case E_DEPRECATED: 
            if (!defined("DEV_MODE") || !DEV_MODE){
                @file_put_contents(ROOT . 'logs/dev_errors.log', 'Type: Deprécié - ' . date("j/m/y à H:i:s") . " - " . $errstr . ". Affichée : Non - Levée dans " . $errfile . " à la ligne " . $errline . "." . " \r\n".@file_get_contents(ROOT . "logs/dev_errors.log"));
                return true;
            }
            $GLOBALS['controleur_def']->addError(124);
            @file_put_contents(ROOT . 'logs/dev_errors.log', 'Type: Déprécié - ' . date("j/m/y à H:i:s") . " - " . $errstr . ". Affichée : Oui - Levée dans " . $errfile . " à la ligne " . $errline . "." . " \r\n".@file_get_contents(ROOT . "logs/dev_errors.log"));
            if (defined("FORCE_INLINE_ERR") && FORCE_INLINE_ERR){
                echo "Deprécié : " . $errstr . ". Levée dans " . $errfile . " à la ligne " . $errline . ".";
            }else if(defined("FORCE_EXC_ERR") && FORCE_EXC_ERR){
                throw new DiamondException("Erreur PHP E_DEPRECATED (". $errno .") : ". $errstr . ". Ligne " .  $errline . " de " . $errfile, 123);
            }else {
                $type = "Deprécié";
                require_once(ROOT . 'installation/exceptions.php');
                die;
                break;
            } 
        break;

        default:
            if (!defined("DEV_MODE") || !DEV_MODE){
                $GLOBALS['controleur_def']->addError(122);
                @file_put_contents(ROOT . 'logs/dev_errors.log', 'Type: Inconnu - ' . date("j/m/y à H:i:s") . " - " . $errstr . ". Affichée : Non - Levée dans " . $errfile . " à la ligne " . $errline . "." . " \r\n".@file_get_contents(ROOT . "logs/dev_errors.log"));
                return true;
            }
            @file_put_contents(ROOT . 'logs/dev_errors.log', 'Type: Inconnu - ' . date("j/m/y à H:i:s") . " - " . $errstr . ". Affichée : Oui - Levée dans " . $errfile . " à la ligne " . $errline . "." . " \r\n".@file_get_contents(ROOT . "logs/dev_errors.log"));

            if (defined("FORCE_INLINE_ERR") && FORCE_INLINE_ERR){
                echo "Alerte : " . $errstr . ". Levée dans " . $errfile . " à la ligne " . $errline . ".";
            }else if(defined("FORCE_EXC_ERR") && FORCE_EXC_ERR){
                throw new DiamondException("Erreur PHP inconnue (". $errno .") : ". $errstr . ". Ligne " .  $errline . " de " . $errfile, 123);
            }else {
                $type = "Inconnu";
                require_once(ROOT . 'installation/exceptions.php');
                die;
            } 
        break;
            
    }
    return true;
}

/**
 * diamondExceptionHandler - Fonction qui gère les exceptions levées et non-attrapées APRES l'initialisation de DiamondCORE
 * Elle transforme ces exceptions en erreurs fatales qu'elle envoie à la fonction diamondErrorHandler
 * 
 * @author Aldric L. (inspiré de la PHP doc)
 * @copyright 2020
 * 
 * @param Exception $e : L'exception levée
 * @return true
 */
function diamondExceptionHandler($e){
    diamondErrorHandler(1, 'Exception non-attrapée: "' . $e->getMessage() . '"', $e->getFile(), $e->getLine());
    return true;
}

/**
 * diamondExceptionHandler - Fonction qui gère les exceptions levées et non-attrapées AVANT l'initialisation de DiamondCORE, et dans la partie installation
 * Elle transforme ces exceptions en erreurs fatales qu'elle envoie à la fonction diamondInstallerErrorHandler
 * 
 * @author Aldric L. (inspiré de la PHP doc)
 * @copyright 2020
 * 
 * @param Exception $e : L'exception levée
 * @return true
 */
function diamondInstallerExceptionHandler($e){
    diamondInstallerErrorHandler(1, 'Exception non-attrapée: "' . $e->getMessage() . '"', $e->getFile(), $e->getLine());
    return true;
}
