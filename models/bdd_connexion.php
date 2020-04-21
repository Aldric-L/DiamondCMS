<?php
/**
 * BDD - Class pour se connecter à une base de donnée via PDO
 * @author Aldric.L
 * @copyright Copyright 2016-2017 2020 Aldric L.
 * @access public
 */
//Cette class permet de créer une connexion en PDO
  class BDD {
    private $bdd_Config = array();

    //On récupère les identifiants pour la BDD (venants d'un fichier)
    function __construct($bdd_file){
        $this->bdd_Config = $bdd_file;
    }

    /**
    * getConfig - Fonction pour récuperer la config du serveur (en ce qui concerne le fichier bdd.ini)
    * @author Aldric.L, 
    * @copyright 2020 Aldric L.
    * @access public
    */
    public function getConfig(){
        return $this->bdd_Config;
    }

    /**
    * changeConfig - Fonction pour modifier la config du serveur (en ce qui concerne le fichier bdd.ini)
    * @author Aldric.L, 
    * @copyright 2020 Aldric L.
    * @access public
    */
    public function changeConfig($host, $db, $usr, $pwd){
        $temp_conf = $this->bdd_Config;
        //On modifie l'array temporaire
        $temp_conf['host'] = $host;
        $temp_conf['db'] = $db;
        $temp_conf['usr'] = $usr;
        $temp_conf['pwd'] = $pwd;
        $ini = new ini (ROOT . "config/bdd.ini", 'Configuration DiamondCMS - Base de donnees');
        //On lui passe l'array modifié
        $ini->ajouter_array($temp_conf);
        //On écrit en lui demmandant de conserver les groupes
        return $ini->ecrire(true);
    }


  /**
   * getPDO - Fonction pour faire une connexion PDO plus stable
   * @author Aldric.L, Darth d'OpenClassroom
   * @copyright Copyright 2016-2017 Aldric L.
   * @access public
   */
    public function getPDO() {
       static $pdo = null;
       if(is_null($pdo)) {
          $host = $this->bdd_Config['host'];
          $db = $this->bdd_Config['db'];
          $user = $this->bdd_Config['usr'];
          $pwd = $this->bdd_Config['pwd'];
          $charset = 'utf8';
          $options = [
             PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // activation des erreurs par exceptions
             PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
             PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
          ];
          $dsn = sprintf('mysql:host=%s;dbname=%s;charset=%s', $host, $db, $charset);
          try {
            $pdo = new PDO($dsn, $user, $pwd, $options);
          }catch (PDOException $e) {
             define("EXCEPTION", $e);
             require_once(ROOT . "installation/bdd_urgence.php");
             die;
          }
       }
       return $pdo;
    }

  }
