<?php 
//Fonction permettant de modifier la configuration des serveurs reliés à DiamondCMS
namespace DServerLink;

class configManager{

    private $configHasChanged = false;

    public function __construct(){
        if (!file_exists(ROOT . 'config/serveurs.ini')){
            if (!copy(ROOT . "addons/Diamond-ServerLink/install_files/serveurs.ini", ROOT . "config/serveurs.ini")){
                die("Erreur critique d'installation de l'addon Diamond-ServerLink : impossible d'installer les fichiers.");
            }
            $this->configHasChanged = true;
        }
    }

    public function getConfig(){
        return parse_ini_file(ROOT . "config/serveurs.ini", true);
    }

    public function editConfig($newConf){
        //Ecriture dans le fichier ini
            //On appel la class ini pour réecrire le fichier
            $ini = new \ini (ROOT . "config/serveurs.ini", 'Configuration DiamondCMS : Addon Diamond-ServerLink');
            //On lui passe l'array modifié
            $ini->ajouter_array($newConf);
            //On écrit en lui demmandant de conserver les groupes
            $ini->ecrire(true);
        //FIN Encriture ini
        $this->configHasChanged = true;
    }
}
