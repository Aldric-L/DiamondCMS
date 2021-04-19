<?php
/**
 * BDD - Class pour se connecter à une base de donnée via PDO
 * @author Aldric.L
 * @copyright Copyright 2016-2017-2020 Aldric L.
 * @access public
 */
//Cette class permet de créer une connexion en PDO
  class BDD extends DB {
    private $forceinstall = false;
    private $noerr = false;

    //On récupère les identifiants pour la BDD (venant d'un fichier)
    function __construct($bdd_file, $noerr=false, $forceinstall=false){
        parent::__construct($bdd_file);
        $this->forceinstall = $forceinstall;
        $this->noerr = $noerr;
    }

   /**
    * changeConfig - Fonction pour modifier la config du serveur (en ce qui concerne le fichier bdd.ini)
    * Attention, cette fonction peut lever des Exceptions. Il faut l'utiliser dans une structure try catch
    *
    * @author Aldric.L, 
    * @copyright 2020 Aldric L.
    * @access public
    * @return bool
    */
    public function changeConfig($host, $db, $usr, $pwd, $port=3306){
        $temp_conf = $this->bdd_Config;
        //On modifie l'array temporaire
        $temp_conf['host'] = $host;
        $temp_conf['db'] = $db;
        $temp_conf['usr'] = $usr;
        $temp_conf['pwd'] = $pwd;
        $temp_conf['port'] = $port;
        $ini = new ini (ROOT . "config/bdd.ini", ';Configuration DiamondCMS - Base de donnees');
        //On lui passe l'array modifié
        $ini->ajouter_array($temp_conf);
        //On écrit en lui demmandant de conserver les groupes
        return $ini->ecrire(true);
    }


  /**
   * getPDO - Fonction pour faire une connexion PDO plus stable
   * Attention : Cette méthode remplace celle de la classe mère (DB)
   * @author Aldric.L 
   * @copyright Copyright 2016-2017 Aldric L.
   * @access public
   * @return PDO
   */
    public function getPDO() {
       static $pdo = null;
       if(is_null($pdo)) {
          $host = $this->bdd_Config['host'];
          $db = $this->bdd_Config['db'];
          $user = $this->bdd_Config['usr'];
          $pwd = $this->bdd_Config['pwd'];
          $port = intval($this->bdd_Config['port']);
          $charset = 'utf8';
          $options = [
             PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // activation des erreurs par exceptions
             PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
             PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
          ];
          if ($this->forceinstall){
            $dsn = sprintf('mysql:host=%s;port=%s;charset=%s', $host, $port, $charset);
            $pdo = new PDO($dsn, $user, $pwd, $options);
          }else {
            $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=%s', $host, $port, $db, $charset);
            try {
              $pdo = new PDO($dsn, $user, $pwd, $options);
            }catch (PDOException $e) {
               if (!$this->noerr){
                  define("EXCEPTION", $e->getMessage());
                  require_once(ROOT . "installation/bdd_urgence.php");
                  die;
               }
            }
          }
       }
       return $pdo;
    }

  /**
   * testPDO - Fonction pour tester la connexion à la base de données
   * @author Aldric.L 
   * @copyright Copyright 2020 Aldric L.
   * @return PDO
   * @access public
   */
  public function testPDO() {
   static $pdo = null;
   if(is_null($pdo)) {
      $host = $this->bdd_Config['host'];
      $db = $this->bdd_Config['db'];
      $user = $this->bdd_Config['usr'];
      $pwd = $this->bdd_Config['pwd'];
      $port = intval($this->bdd_Config['port']);
      $charset = 'utf8';
      $options = [
         PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // activation des erreurs par exceptions
         PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
         PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
      ];
      $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=%s', $host, $port, $db, $charset);
      $pdo = new PDO($dsn, $user, $pwd, $options);
      return $pdo;
   }
}

  }
