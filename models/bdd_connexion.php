<?php
/**
 * BDD - Class pour se connecter à une base de donnée via PDO
 * @author Aldric.L
 * @copyright Copyright 2016-2017 Aldric L.
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
   * getPDO - Fonction pour faire une connexion PDO pus stable
   * @author Aldric.L, Darth d'OpenClassroom
   * @copyright Copyright 2016-2017 Aldric L.
   * @access public
   */
    //Fonction pour récupéré un PDO
    function getPDO() {
       static $pdo = null;
       if(is_null($pdo)) {
          $host = $this->bdd_Config['host'];
          $db = $this->bdd_Config['db'];
          $user = $this->bdd_Config['usr'];
          $pwd = $this->bdd_Config['pwd'];
          $charset = 'utf8';
          $options = [
             PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // activation des erreurs par exceptions
             PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
          ];
          $dsn = sprintf('mysql:host=%s;dbname=%s;charset=%s', $host, $db, $charset);
          $pdo = new PDO($dsn, $user, $pwd, $options);
       }
       return $pdo;
    }

  }
