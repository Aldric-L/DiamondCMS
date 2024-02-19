<?php

/**
 * DiamondAPI - Classe abstraite qui sert tant de mère aux classes des modules API, que de boite à outil pour exécuter l'API en PHP
 * Il est défendu tant de créer un module API qui n'hériterait pas de cette classe, que d'essayer de traiter une requête API sans utiliser DiamondAPI::execute.
 * Pour ce dernier point, les sous-méthodes de vérification et validation sont désormais private.
 * 
 * @version 1.0
 * @author Aldric L. 2023
 * 
 */
abstract class DiamondAPI extends Errors {

    /**
     * $args : sorte de reprise du tableau $_POST, permettant de le filtrer si besoin
     * $args = array("arg1" => valarg1, ..)
     */
    protected $args;
    protected $errors_manager;
    protected $paths;
    protected $level;
    protected $params_needed;

    protected $output_buffer = null;

    protected array $cache_instances = array();
    const CACHE_DYN = 1;
    const CACHE_SEMISTATIC = 5;
    const CACHE_STATIC = 1440;
    const CACHE_NAMES = array(self::CACHE_DYN => "CACHE_DYN", self::CACHE_SEMISTATIC => "CACHE_SEMISTATIC", self::CACHE_STATIC => "CACHE_STATIC");

    const MAXLEVEL = 5;
    
    private $ini_cache = array();
    private $pdo;
    private $controleur;

    private array $antispam = array();

    /**
     * __construct : Constructeur de tout module API
     * 
     * @param array $paths : issu de Controleur->getPaths
     * @param \PDO $pdo : instance BDD
     * @param \Manager $controleur : instance de la classe Manager
     * @param int $level : niveau d'autorisation de l'utilisateur
     */
    public function __construct(array $paths, \PDO $pdo, \Manager &$controleur, int $level=-1){
        $this->paths = $paths;
        $this->errors_manager = $controleur->getErrorHandler();
        $this->level = $level;
        $this->pdo = $pdo;
        $this->controleur = $controleur;
    }

    /**
     * getCacheInstance - Méthode retournant l'instance de cache adaptée à la durée de vie fournie
     * Elle économise des ressources puisqu'elle évite de recréer une instance à chaque utilisation du cache
     * 
     * @param int $CACHE_DURATION : utiliser une constante de durée (parmi les publiques de la classe : CACHE_DYN, CACHE_SEMISTATIC, CACHE_STATIC)
     * @return \DiamondCache : Instance de cache
     */
    protected function getCacheInstance(int $CACHE_DURATION) : \DiamondCache{
        if (isset(self::CACHE_NAMES[$CACHE_DURATION]) && isset($this->cache_instances[self::CACHE_NAMES[$CACHE_DURATION]]))
            return $this->cache_instances[self::CACHE_NAMES[$CACHE_DURATION]];
        else if (isset(self::CACHE_NAMES[$CACHE_DURATION]))
            return $this->cache_instances[self::CACHE_NAMES[$CACHE_DURATION]] = new \DiamondCache($this->paths["cache"] . "API/" .self::CACHE_NAMES[$CACHE_DURATION] . "/", $CACHE_DURATION);
        else 
            return new \DiamondCache($this->paths["cache"] . "API/custom_" . (string)($CACHE_DURATION) . "/", $CACHE_DURATION);
    }

    protected function getPDO(){
        return $this->pdo;
    }

    protected function getControleur(){
        return $this->controleur;
    }

    public function setArgs($args_t){
        if (!is_array($args_t))
            throw new DiamondException("Missing arguments", 701);
        $this->args = $args_t;
    }

    /**
     * setConfig - Méthode permettant de modifier un fichier de config ini
     * 
     * @param string $path : chemin COMPLET jusqu'au fichier de config
     * @param array $changes : modifications apportées (tableau associatif)
     * @param bool $erase_conf : true supprimerait le contenu préalable du fichier pour le remplacer par la version encodée de $changes
     * @param bool $dont_add_entries : true empêche de rajouter des items à la config et permet seulement de modifier des existants
     */
    protected function setConfig(string $path, array $changes, bool $erase_conf=false, bool $dont_add_entries=false) : void{
        if (!file_exists($path)){
            try {
                if (!fopen($path, "w+"))
                    throw new DiamondException("Unable to open or create config file.", 613);
            }catch(Throwable $e){
                throw new DiamondException("Unable to open or create config file.", 613);
            }
        }

        if (!$erase_conf){
            $temp_conf = $this->getIniConfig($path);
            foreach ($changes as $key => $c) {
                if (array_key_exists($key, $temp_conf) && is_array($temp_conf[$key])){
                    if (is_array($changes[$key])){
                        foreach ($changes[$key] as $k => $ch) {
                            if (($dont_add_entries && array_key_exists($k, $temp_conf[$key])) || !$dont_add_entries){
                                $temp_conf[$key][$k] = $ch;
                            }
                        }
                    }
                }
                else if (($dont_add_entries && array_key_exists($key, $temp_conf)) || !$dont_add_entries){
                    $temp_conf[$key] = $c;
                }
            }
        }else {
            if (array_key_exists($path, $this->ini_cache))
                $this->ini_cache[$path] = $changes;
            
            $temp_conf = $changes;
        }
        
        $ini = new ini ($path, 'Configuration DiamondCMS');
        //On lui passe l'array modifié
        $ini->ajouter_array(cleanIniTypes($temp_conf));
        //On écrit en lui demmandant de conserver les groupes
        $ini->ecrire(true);
    }

    /**
     * saveconf - Fonction pour modifier un fichier config se trouvant dans /config/
     * Cette fonction appelle setConfig en limitant l'écrasement des données et en interdissant les nouvelles entrées
     * Elle n'est pas très propre, et son usage est déconseillé (il témoigne d'une fénéantise préjudiciable...)
     * 
     * @throws 701 : il faut évidemment des arguments à écrire en $this->args...
     * @param string file : nom du fichier de config sans root, dans le dossier config/
     * @access protected 
     * @author Aldric L.
     * @copyright 2022
     */
    protected function saveconf($file){
        if ($this->args == null || empty($this->args))
            throw new Exception("Missing arguments", 701);

        /*foreach ($this->args as $key => $arg) {
            $this->setConfig(ROOT."config/" . $file, array($key => $arg));
        }*/
        $this->setConfig(ROOT."config/" . $file, $this->args, false, true);

        return $this->formatedReturn(1);
    }

    /**
     * formatedReturn - Méthode pour terminer une méthode API en renvoyant un rendu JSON propre
     * 
     * @param int|mixed $return : si int, définit le state, si mixed, entraine State=1 et constitue le retour
     * @param array|null $errs : erreurs à renvoyer
     */
    protected function formatedReturn($return, $errs=null) : string{
        if (is_int($return)){
            $this->output_buffer = false;
            return (json_encode(array("State"=> $return, "Errors" => $errs)));
        }
        $this->output_buffer = mb_convert_encoding($return, "UTF-8", "auto");
        return (json_encode(array("State"=> 1, "Return"=> $this->output_buffer, "Errors" => null)));
    }

    protected function getOutputBuffer(){
        return $this->output_buffer;
    }

    public function getParamsNeeded(){
        return $this->params_needed;
    }

    /**
     * getParamsNeededForFunc - Renvoie une liste des index devant être présents dans le tableau $args
     * 
     * @param string $func : nom de la fonction, attention lève 701 si fonction introuvable
     */
    public function getParamsNeededForFunc(string $func) : array{
        if (empty($this->params_needed))
            return $this->params_needed;
        if (!array_key_exists($func, $this->params_needed))
            return array();
            //throw new DiamondException("Bad arguments (unable to find function in function array list)", "native$701");
        return $this->params_needed[$func];
    }

    /**
     * getIniConfig - Renvoie le contenu nettoyé avec cleanIniTypes d'un fichier de config
     * Cette fonction économise des ressources puisqu'elle met en cache les fichiers de config
     * 
     * @param string $file : chemin complet vers le fichier config
     * @param string $array_flag : array_flag de parse_ini_file (conservation des sous sections)
     */
    protected function getIniConfig(string $file, $array_flag=true, $allow_cache=true) : array{
        if (!array_key_exists($file, $this->ini_cache) && $allow_cache){
            if (!file_exists($file))
                throw new DiamondException("Unable to find file.", 613);
            
            $conf = cleanIniTypes(parse_ini_file($file, $array_flag));
            $this->ini_cache[$file] = $conf;
        }else {
            $conf = $this->ini_cache[$file];
        }
        return $conf;
    }

    /**
     * registerAntiSpam - Méthode pour initialiser un filtre antispam pour certaines fonctions du module API
     * Cette méthode est destinée à être utilisée dans le constructeur fille des modules API
     * Temps de buffer : 2 minutes, les compteurs de requêtes sont donc réinitialisés toutes les 2 minutes
     * 
     * @param array $funcs : tableau de tableau du format : array( "nom_fonction" => array(nombre_requetes_identiques, nombre_user, nombre_requetes) )
     */
    protected function registerAntiSpam(array $funcs) :void{
        $this->antispam = array_merge($this->antispam, $funcs);
    }
    
    /**
     * checkIfallowToCall - Méthode éxecutée en interne pour savoir si le filtre spam ne bloque pas la requête
     * Elle initialise aussi un log des requêtes concernées par le filtre spam qui est purgé à expiration du temps de buffer (fixé à 2 minutes)
     * Il est essentiel de l'appeler à chaque requête puisque c'est elle qui fait le log antispam et qui gère le compteur !
     * 
     * @param string $funcs : nom de la fonction
     * @return void : en cas de problème ou de non-autorisation, elle renvoie une exception
     * @throws DiamondException native$715 : Requête non-autorisée par l'antispam
     */
    private function checkIfallowToCall(string $func) : void {
        if (array_key_exists($func, $this->antispam)){
            $spamlog = array();
            if (file_exists($this->paths["logs"] . "api_spam.json.log") && json_decode(@file_get_contents($this->paths["logs"] . "api_spam.json.log")) != null)
                $spamlog = json_decode(@file_get_contents($this->paths["logs"] . "api_spam.json.log"), true);
            
            $spams = array();
            $userspams = array();

            foreach($spamlog as $key => &$log){
                if (time() - intval($log['date']) > 120){
                    unset($spamlog[$key]);
                }else {
                    if ($log['func'] == $func)
                        array_push($spams, $log);
                    if ($log['func'] == $func && $log['user'] != -1 && (isset($_SESSION['user']) && $_SESSION['user'] instanceof User) && $log['user'] == $_SESSION['user']->getId())
                        array_push($userspams, $log);
                }
            }
            $i = 0;
            if (sizeof($spams) < $this->antispam[$func][2] && sizeof($userspams) < $this->antispam[$func][1]){
                foreach($spams as &$s){
                    if ($s['args'] == $this->args)
                        $i++;
                }
            }

            array_push($spamlog, $action = array(
                'date' => time(),
                'func' => $func,
                'user' => (isset($_SESSION['user']) && $_SESSION['user'] instanceof User) ? $_SESSION['user']->getId() : -1,
                'args' => $this->args
            ));

            file_put_contents($this->paths["logs"] . "api_spam.json.log", json_encode($spamlog, JSON_PRETTY_PRINT));
            
            if (($i==0 && (sizeof($spams) > $this->antispam[$func][2] || sizeof($userspams) > $this->antispam[$func][1])) || $i>$this->antispam[$func][0])
                throw new DiamondException("AntiSpam Alert : Request intercepted.", "native$715");
        }
    }

    /**
     * cleanArg - Méthode interne pour nettoyer un élement envoyé par l'utilisateur pouvant contenir du code HTML ou JS vicié.
     * Elle utilise pour celà la librairie HTMLPurifier. Attention : cette fonction est lourde et ne devrait être utilisée que pour les textareas.
     * 
     * @param array|string &$arg : reférence vers la variable contenant l'élement à nettoyer
     * @return void : la fonction ne renvoie rien puisqu'elle modifie directement la variable passée en référence
     * @throws DiamondException native$715 : Le nettoyage a provoqué la destruction totale du contenu (tout était du spam, on alerte)
     */
    protected function cleanArg(&$arg) : void{
        $this->getControleur()->loadModel("libs/htmlpurifier/standalone");
        $config = HTMLPurifier_Config::createDefault();
        $purifier = new HTMLPurifier($config);
        if (is_array($arg)){
            foreach ($arg as &$a){
                $a = $purifier->purify($a);
                if (empty($a) || !preg_match('/\S/', $a))
                    throw new DiamondException("AntiSpam Alert : Request intercepted. (cleanArg)", "native$715");
            }
        }else {
            $arg = $purifier->purify($arg);
            if (empty($arg) || !preg_match('/\S/', $arg))
                throw new DiamondException("AntiSpam Alert : Request intercepted. (cleanArg)", "native$715");
        }
    }

    /**
     * processImgFromDIC - Méthode interne pour traiter les données renvoyées par DiamondImageChooser.
     * La méthode nettoie le tableau $args et $_POST, s'occupe de l'upload du fichier le cas échéant, et renvoie soit le chemin d'accès au fichier soit le lien vers l'image
     * 
     * @param string $folder : sous dossier dans views/uploads/img/ dans lequel uploader l'image
     * @return string|bool : false si échec, sinon le lien/chemin vers l'image
     */
    protected function processImgFromDIC($folder = null){
        if (!isset($_POST["img_link"]) && !isset($_FILES["newimg"]) && !isset($_POST["img_choosen"]))
            throw new Exception("Bad Arguments, Missing Image", 701);
    
        if (isset($_FILES['newimg']) && $_FILES['newimg']['size'] != 0){
            if (strrpos($_FILES['newimg']['type'], "image/") === false){
                throw new Exception("Bad ext", 524);
            }else {
                $upload = uploadFile('newimg', $folder);
                if (is_int($upload))
                    throw new Exception("Error while uploading", 500 + intval($upload));
                else
                  return $upload;
            }    
        }
        
        if (isset($_POST["img_link"])){
            $l = $_POST["img_link"];
            if (array_key_exists("img_link", $this->args) && isset($this->args["img_link"]))
                unset($this->args["img_link"]);
            unset($_POST["img_link"]);
            return $l;
        }

        $root = $this->paths['global_views'] . "uploads/img/";
        
        if (isset($_POST["img_choosen"]) && strrpos($_POST["img_choosen"], $root) !== false){
            $i = str_replace($root, "", $_POST["img_choosen"]);
            if (array_key_exists("img_choosen", $this->args) && isset($this->args["img_choosen"]))
                unset($this->args["img_choosen"]);
            unset($_POST["img_choosen"]);
            return $i;
        }

        return false;
    }

    /**
     * execute - Méthode statique permettant l'exécution d'une requête API préalablement traduite en array PHP.
     * Cette méthode doit être l'UNIQUE moyen pour appeler un module API. ELLe est lourde, certes, mais elle s'occupe de toutes les vérifications nécessaires et surtout gère les privilièges et élévations de privilièges.
     * Elle gère aussi le log des requêtes SET.
     * 
     * @version 1.0
     * @author Aldric L.
     * @copyright 2023
     * 
     * @param bool $is_true_API : effectuez-vous un appel à la suite d'une requête AJAX ou s'agit-il d'un faux appel API réalisée en PHP (exemple de la boutique)
     * @param \Controleur &$controleur_def
     * @param array $addons_list : directement issu du Controleur
     * @param string $model : nom du fichier API à appeler
     * @param string $verb : "SET" ou "GET"
     * @param string $func : nom de la fonction dans le fichier module sans le préfixe (la fonction est appelée comme $verb_$func)
     * @param int $level : niveau d'autorisation, -1 par défaut, sinon celui de l'utilisateur, ou indiquer celui qui est désiré dans le cadre d'une élevation de priviligèges.
     * Attention, si un utilisateur est fourni, et que celui-ci dispose d'une autorisation plus élevée, son niveau d'autorisation sera appliqué. Il n'est pas possible de restreindre les permissions utilisateur par cette fonction.
     * De même, modifier temporairement le niveau d'autorisation de l'utilisateur pour lui permettre d'effectuer une action est une très très très mauvaise pratique et cette fonction est conçue justement pour ne jamais modifier le niveau de l'utilisateur en session.
     * @param \User|null $user : utilisateur en sesssion, sinon null
     * @param array|null $arguments : les arguments passés en POST généralement
     * @return array : Tout s'est bien passé : "parameters", "args", "date", "is_AJAX_API", "user_id", "json_result", "output_buffer"
     */
    public static function execute(bool $is_true_API, \Controleur &$controleur_def, array $addons_list, string $model, string $verb, string $func, int $level=-1, $user=null, $arguments=null) : array{
        function is_addon_file($file, $addons, $addon_path=ROOT . "addons/"){
            foreach ($addons as $a){
                if (file_exists($addon_path . $a . "/models/API/" . $file . '.class.php')){
                    return $a;
                }
            }
            return false;
        }

        // On cherche le module de l'API appelée
        if (!(file_exists($controleur_def->getPaths()["models"] . 'API/' . $model . '.class.php') || is_addon_file($model, $addons_list, $controleur_def->getPaths()["addons"]) !== false))
            throw new DiamondException("No model file available", "native$702");

        $class_name = str_replace("-", "", $model);
        if (!file_exists($controleur_def->getPaths()["models"] . 'API/' . $model . '.class.php') && is_addon_file($model, $addons_list, $controleur_def->getPaths()["addons"]) !== false)
            require_once ($controleur_def->getPaths()["addons"] . is_addon_file($model, $addons_list) . "/models/API/" . $model . '.class.php');
        else
            require_once ($controleur_def->getPaths()["models"] . 'API/' . $model . '.class.php');
            
        if (!defined("FORCE_EXC_ERR"))
            define('FORCE_EXC_ERR', true);

        // On prépare le log l'action
        $log = array();
        if (file_exists($controleur_def->getPaths()["logs"] . "api_set.json.log") && json_decode(@file_get_contents($controleur_def->getPaths()["logs"] . "api_set.json.log")) != null)
            $log = json_decode(@file_get_contents($controleur_def->getPaths()["logs"] . "api_set.json.log"), true);

        $action = array(
            "parameters" => array($model, $verb, $func),
            "args" => $arguments,
            "date" => date("j/m/Y h:i:s"),
            "is_AJAX_API" => $is_true_API,
            "user" => ((isset($user) && $user !== null && $user instanceof User) ? $user->getId() : null)
        );    
 
        try{
            if ($level === -1 || $level <= 0){
                $action['level'] = $level;
                $action['privilege_elevation'] = false;
                $api_class = new $class_name($controleur_def->getPaths(), $controleur_def->bddConnexion(), $controleur_def, -1);
            }else {
                if ($user !== null && $user instanceof User){
                    if ($user->getLevel() >= $level){
                        $level = $user->getLevel();
                        $action['level'] = $level;
                        $action['privilege_elevation'] = false;
                        $api_class = new $class_name($controleur_def->getPaths(), $controleur_def->bddConnexion(), $controleur_def, $user->getLevel());
                    }
                    else{
                        $action['level'] = $level;
                        $action['privilege_elevation'] = true;
                        $action['traceback'] = debug_backtrace(0,2);
                        $api_class = new $class_name($controleur_def->getPaths(), $controleur_def->bddConnexion(), $controleur_def, $level);
                    }
                }else {
                    throw new DiamondException("A user must be connected and provided to allow privilege elevation.", "native$716");
                }
            }
            /*if ($user !== null && $user instanceof User)
                $api_class = new $class_name($controleur_def->getPaths(), $controleur_def->bddConnexion(), $controleur_def, $user->getLevel());
            else 
                $api_class = new $class_name($controleur_def->getPaths(), $controleur_def->bddConnexion(), $controleur_def, -1);
            */    
            if ($arguments !== null && is_array($arguments))
                $api_class->setArgs($arguments);
        }catch (DiamondException|Exception $e){ throw $e; }catch (Throwable $e){
            throw new DiamondException("Fatal Error : " . $e->getMessage(), "native$703");
        }

        //On s'attaque au verbe de la reqête
        if (!($verb == "GET" || $verb == "SET" || $verb == "get" || $verb == "set"))
            throw new DiamondException("Illegal verb.", "native$701");

            
    

        //On test si les paramètres requis sont biens fournis
        try {
            $needed_params = $api_class->getParamsNeededForFunc(mb_strtolower($verb) . "_" . $func);
        }catch (Exception $e){
            if ($verb == "SET" || $verb == "set"){
                $action['result'] = $result = array("State"=> 0, "Errors" => array("native$701",  "Missing arguments. " . $e->getMessage()));
                array_push($log, $action);
                @file_put_contents($controleur_def->getPaths()["logs"] . "api_set.json.log", json_encode($log, JSON_PRETTY_PRINT));
            }
            throw new DiamondException("Missing arguments. " . $e->getMessage(), "native$701");
        }catch (Throwable $e){
            if ($verb == "SET" || $verb == "set"){
                @file_put_contents($controleur_def->getPaths()["logs"] . 'dev_errors.log', 'Type: Fatale - ' . date("j/m/y à H:i:s") . " - API ERROR. Affichée : Non - " . $e->getMessage() . " (l." . $e->getLine() . " in " . $e->getFile() . ") \r\n".@file_get_contents($controleur_def->getPaths()["logs"] . "dev_errors.log"));
                $action['result'] = $result = array("State"=> 0, "Errors" => array("native$703", "Unable to load model class"));
                array_push($log, $action);
                @file_put_contents($controleur_def->getPaths()["logs"] . "api_set.json.log", json_encode($log, JSON_PRETTY_PRINT));
            }
            throw new DiamondException("Missing arguments. " . $e->getMessage(), "native$701");
        }
                
        if (!empty($needed_params)){
            if (!is_array($arguments) && is_array($needed_params) && isset($needed_params[0]))
                throw new DiamondException("Missing arguments (no argument received). Expected : " . $needed_params[0], "native$701");
            else if (!is_array($arguments))
                throw new DiamondException("Missing arguments (no argument received).", "native$701");

            foreach ($needed_params as $p) {
                if (!array_key_exists($p, $arguments) || $arguments[$p] == " " || $arguments[$p] == ""){
                    if ($verb == "SET" || $verb == "set"){
                        $action['result'] = $result = array("State"=> 0, "Errors" => array("native$701",  "Missing arguments. Expected : " . $p));
                        array_push($log, $action);
                        @file_put_contents($controleur_def->getPaths()["logs"] . "api_set.json.log", json_encode($log, JSON_PRETTY_PRINT));
                    }
                    throw new DiamondException("Missing arguments. Expected : " . $p, "native$701");
                }
            }
        }
                
        try{
            $api_class->checkIfallowToCall(mb_strtolower($verb) . "_" . $func);
            $return_val = call_user_func(array($api_class, mb_strtolower($verb) . "_" . $func)) ;
        }catch (Throwable $e){
            $code = "native$704";
            if ($e instanceof Exception && ($e instanceof DiamondException)){
                $code = $e->getTrueCode();
                $error = "Fatal Error : " . $e->getVanillaMessage() . ' [DEX]';
            }else if ($e instanceof Exception && !($e instanceof DiamondException)){
                $error = "Fatal Error : " . $e->getMessage() . " (True Code: " . $e->getCode() . ' [EX])';
            }else {
                $error = "Fatal Error : " . $e->getMessage() . " (True Code: " . $e->getCode() . " l." . $e->getLine() . " in " . $e->getFile() . " [ERR])";
                @file_put_contents($controleur_def->getPaths()["logs"] . 'dev_errors.log', 'Type: Fatale - ' . date("j/m/y à H:i:s") . " - API ERROR. Affichée : Non - " . $e->getMessage() . " (l." . $e->getLine() . " in " . $e->getFile() . ") \r\n".@file_get_contents($controleur_def->getPaths()["logs"] . "dev_errors.log"));
            }

            if ($verb == "SET" || $verb == "set"){
                $action['result'] = $result = array("State"=> 0, "Errors" => array( $code, $error));
                array_push($log, $action);
                @file_put_contents($controleur_def->getPaths()["logs"] . "api_set.json.log", json_encode($log, JSON_PRETTY_PRINT));
            }

            throw new DiamondException($error, $code);
        }

        if ($verb == "SET" || $verb == "set" || $action['privilege_elevation']){
            $action['result'] = json_decode($return_val);
            array_push($log, $action);
            @file_put_contents($controleur_def->getPaths()["logs"] . "api_set.json.log", json_encode($log, JSON_PRETTY_PRINT));
        }

        return array(
            "parameters" => array($model, $verb, $func),
            "args" => $arguments,
            "date" => date("j/m/Y h:i:s"),
            "is_AJAX_API" => $is_true_API,
            "user_id" => ((isset($user) && $user !== null && $user instanceof User) ? $user->getId() : null),
            "json_result" => $return_val,
            "output_buffer" => $api_class->getOutputBuffer(),
        );
    }

    /**
     * cmd_parser - Méthode statique pour traduire une requête API telle qu'envoyée dans la console par exemple
     * Elle parse en tenant compte des strings déclarés avec " (et non ' ou `). Elle utilise aussi \ comme caractère d'échappement.
     * 
     * @param string $cmd : commande brute à parser
     * @return array : "request" => tableau de trois élements avec le module, le verbe et la fonction ; "arguments" => les arguments parsés ; "cmd" => renvoie de la variable initiale
     */
    public static function cmd_parser(string $cmd) : array{
        $request = array();
        $arguments = array();
        $word_buffer = "";
        $arg_buffer = "";
        $val_buffer = "";
        $choice_buffer = "arg_buffer";
        $ignore = false;
        $bis_ignore = false;
        for ($i=0; $i < strlen($cmd); $i++){
            if (sizeof($request) < 3){
                if ($cmd[$i] != ' '){
                    $word_buffer .= $cmd[$i];
                }else if ($word_buffer !== "") {
                    array_push($request, $word_buffer);
                    $word_buffer = "";
                }
            }else {
                if (!$ignore && !$bis_ignore && ($cmd[$i] == '=' || $cmd[$i] == ' ')){
                    if ($choice_buffer == "arg_buffer" && $arg_buffer != ""){
                      $choice_buffer = "val_buffer";
                    }
                    else {
                      $choice_buffer = "arg_buffer";
                      $arguments = array_merge($arguments, array($arg_buffer => $val_buffer));
                      $arg_buffer = $val_buffer = "";
                    }
                }
                else if ($cmd[$i] == '"'){
                    if ($bis_ignore){
                      $bis_ignore = false;
                      if ($choice_buffer == "arg_buffer")
                        $arg_buffer .= $cmd[$i];
                      else 
                          $val_buffer .= $cmd[$i];
                    }else{
                      $ignore = !$ignore;
                    }
                }
                else if ($cmd[$i] == str_replace(" ", "", '\ ')){
                  $bis_ignore = true;
                }
                else if ($cmd[$i] != '=') {
                    $bis_ignore = false;
                    if ($choice_buffer == "arg_buffer")
                        $arg_buffer .= $cmd[$i];
                    else 
                        $val_buffer .= $cmd[$i];
                }
                
            } 
        }
        if (!empty($arg_buffer) && !empty($val_buffer))
          $arguments = array_merge($arguments, array($arg_buffer => $val_buffer));
        $arguments = cleanIniTypes($arguments);
        return array(
          "request" => $request,
          "arguments" => $arguments,
          "cmd" => $cmd
        );
    }


    /**
     * get_help - Fonction API qui permet de lister les paramètres nécessaires pour chaque fonction
     * Elle est destinée à être appelée en API
     * TODO (Optionnel) : inclure une description de chaque fonction
     * 
     * @param string func optional (nom de la fonction)
     * @return array paramètres nécessaires
     */
    public function get_help(){
        if (is_array($this->args) && !is_null($this->args) && array_key_exists("func", $this->args))
            return $this->formatedReturn($this->getParamsNeededForFunc($this->args['func']));
        
        return $this->formatedReturn($this->params_needed);
    }

}