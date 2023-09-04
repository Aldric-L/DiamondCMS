<?php
/**
 * DB - Class est une base pour se connecter à une base de données avec PDO
 * @author Aldric.L
 * @copyright Copyright 2016-2017-2020 Aldric L.
 * @access public
 */
class DB {
    protected $bdd_Config = array();

    /**
    * __construct - Constructeur qui veu récuperer les identifiants de la base de données
    * @author Aldric.L, 
    * @copyright 2020 Aldric L.
    * @access public
    * @param array $bdd_infos : array("host" => host, "db" => db, "usr" => Utilisateur, "pwd" => mot de passe, "port" => 3306)
    * @return array
    */
    function __construct($bdd_infos){
        $this->bdd_Config = $bdd_infos;
    }

    /**
    * getConfig - Fonction pour récuperer la config de la BDD
    * @author Aldric.L, 
    * @copyright 2020 Aldric L.
    * @access public
    * @return array
    */
    public function getConfig(){
        return $this->bdd_Config;
    }


  /**
   * getPDO - Fonction pour faire une connexion PDO plus stable
   * Attention, il fortement conseillé d'utiliser cette méthode dans une structure try/catch puisqu'elle crée l'objet PDO.
   * @author Aldric.L 
   * @copyright Copyright 2016-2017-2020 Aldric L.
   * @access public
   * @return PDO|PDOException 
   */
    public function getPDO() {
       static $pdo = null;
       if(is_null($pdo)) {
          $host = $this->bdd_Config['host'];
          $db = $this->bdd_Config['db'];
          $user = $this->bdd_Config['usr'];
          $pwd = $this->bdd_Config['pwd'];
          $port = intval($this->bdd_Config['port']);
          $charset = 'utf8mb4';
          $options = [
             PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // activation des erreurs par exceptions
             PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
          ];
          $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=%s', $host, $port, $db, $charset);
          $pdo = new PDO($dsn, $user, $pwd, $options);
       }
       return $pdo;
    }

}
