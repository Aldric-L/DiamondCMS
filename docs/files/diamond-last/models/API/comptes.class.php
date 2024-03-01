<?php 

/**
 * comptes - API Admin permettant de gérer les comptes utilisateurs
 *  
 * @author Aldric L.
 * @copyright 2022
 */
class comptes extends DiamondAPI {

    private $cache_user;

    public function __construct(array $paths, PDO $pdo, Controleur $controleur, int $level){
        parent::__construct($paths, $pdo, $controleur, $level);
        $this->params_needed = array(
            "set_resetPDP" => array("user_id"),
            "set_ban" => array("user_id", "r_ban"),
            "set_deban" => array("user_id"),
            "get_modifAccount" => array("user_id"),
            "set_modifRole" => array("role_id"),
            "set_defRole" => array("role_id"),
            "set_addRole" => array("name", "level", "def"),
            "get_connectUser" => array("pseudo_connexion", "mp_connexion"),
            "get_isAccount" => array("pseudo_connexion", "mp_connexion"),
            "get_addUser" => array("pseudo_inscription", "email_inscription", "mp_inscription", "mp2_inscription"),
            "get_disconnect" => array(),
            "set_deleteLastActions" => array("user_id"),
            "set_addVote" => array("user_id"),
            "set_startReinitPassword" => array("email"),
            "get_endReinitPassword" => array("code_reinit", "newmp_reinit"),
            "get_emailUnsubscribe" => array("email"),
        );
        $this->cache_user = null;
        $this->registerAntiSpam(array(
            "get_isAccount" => array(3, 3, 50),
            "get_addUser" => array(1, 3, 50),
            "set_modifAccount" => array(5, 10, 100),
        ));
        $controleur->loadModel("hydratation/userHydrate.class");
        $controleur->loadModel("hydratation/roleHydrate.class");
    }

    public function set_resetPDP(){
        try {
            $user = $this->findUser($this->args['user_id']);
        }catch (Exception $e){
            throw new DiamondException("Unable to find requested user.", 335);
        }

        if ($this->level < 4 || !$user->can_edit())
            throw new DiamondException("Forbidden access", 706);
            
        if ($user->setPDP($this->getPDO()) != false){
            return $this->formatedReturn(1);
        }
    }

    public function set_ban(){
        try {
            $user = $this->findUser($this->args['user_id']);
        }catch (Exception $e){
            throw new DiamondException("Unable to find requested user.", 335);
        }

        if (!$user->can_ban())
            throw new DiamondException("Forbidden access", 706);

        if ($user->ban($this->getPDO(), $_SESSION['user'], $this->args['r_ban']) != false){
            return $this->formatedReturn(1);
        }
    }

    public function set_deban(){
        try {
            $user = $this->findUser($this->args['user_id']);
        }catch (Exception $e){
            throw new DiamondException("Unable to find requested user.", 335);
        }

        if (!$user->can_deban() and !($this->level === 5 && $user->getPseudo() == "diamond_support"))
            throw new DiamondException("Forbidden access", 706);

        if ($user->deban($this->getPDO()) != false){
            return $this->formatedReturn(1);
        }
    }

    public function get_modifAccount(){
        $this->args = cleanIniTypes($this->args);
        try {
            $user = $this->findUser($this->args['user_id']);
        }catch (Exception $e){
            throw new DiamondException("Unable to find requested user.", 335);
        }

        if (!$user->can_edit())
            throw new DiamondException("Forbidden access", 706);

        if (isset($this->args['pseudo']) && $this->args['pseudo'] != $user->getPseudo()){
            if ($user->setPseudo($this->getPDO(), $this->args['pseudo']) === false)
                throw new DiamondException("Unable to update user's pseudo.", 336);
            
            if (!isset($this->args['notify']) || boolval($this->args['notify']) != false)
                $this->getControleur()->notify('Votre nom d\'utilisateur a été modifié !', $user->getId(), 3, "Modification utilisateur", LINK . "comptes/" . $user->getId());
        }
        if (isset($this->args['email']) && $this->args['email'] != $user->getEmail()){
            if ($user->setEmail($this->getPDO(), $this->args['email']) === false)
                throw new DiamondException("Unable to update user's email.", 336);
            
            if (!isset($this->args['notify']) || boolval($this->args['notify']) != false)
                $this->getControleur()->notify('Votre email utilisateur a été modifié !', $user->getId(), 3, "Modification utilisateur", LINK . "comptes/" . $user->getId());
        }
        if (isset($this->args['news']) && $this->args['news'] != $user->isOkToGetMails()){
            if ($user->setMailPreferences($this->getPDO(), $this->args['news']) === false)
                throw new DiamondException("Unable to update user's mail preferences.", 336);
        }
        if (isset($this->args['money']) && $this->args['money'] != $user->getMoney() && $this->level >= 4){
            if ($user->setMoney($this->getPDO(), intval($this->args['money'])) === false)
                throw new DiamondException("Unable to update user's money account.", 336);
            if (!isset($this->args['notify']) || boolval($this->args['notify']) != false)
                $this->getControleur()->notify('Votre compte de monnaie virtuel a été modifié !', $user->getId(), 5, "Modification utilisateur", LINK . "comptes/" . $user->getId());
        }else if (isset($this->args['money']) && $this->args['money'] != $user->getMoney()){
            throw new DiamondException("Forbidden access", 706);
        }
        if (isset($this->args['role']) && $this->args['role'] != $user->getRole() && $this->level > 4){
            if ($user->setRole($this->getPDO(), intval($this->args['role'])) === false)
                throw new DiamondException("Unable to update user's role.", 336);
            if (!isset($this->args['notify']) || boolval($this->args['notify']) != false)
                $this->getControleur()->notify('Votre rang utilisateur a été modifié !', $user->getId(), 5, "Modification utilisateur", LINK . "comptes/" . $user->getId());
        }else if (isset($this->args['role']) && $this->args['role'] != $user->getRole()){
            throw new DiamondException("Forbidden access", 706);
        }
        if (isset($this->args['r_ban']) && $user->can_ban()){
            if ($user->setRBan($this->getPDO(), $this->args['r_ban']) === false)
                throw new DiamondException("Unable to update user's ban reason.", 336);
        }else if (isset($this->args['r_ban'])){
            throw new DiamondException("Forbidden access", 706);
        }
        if (isset($this->args['signature']) && $this->args['signature'] != $user->getForumSignature()){
            $this->cleanArg($this->args['signature']);

            if ($user->setSignature($this->getPDO(), $this->args['signature']) === false)
                throw new DiamondException("Unable to update user's email.", 336);

            if (!isset($this->args['notify']) || boolval($this->args['notify']) != false)
                $this->getControleur()->notify('Votre signature forum a été modifiée !', $user->getId(), 4, "Modification utilisateur", LINK . "comptes/" . $user->getId());
        }
        if(isset($_FILES['pdp'])){
            $upload = uploadFile('pdp', 'profiles', true, ROOT . "views/uploads/img/", array("png", "jpg", "jpeg", "bmp"));
            if (is_int($upload))
                throw new DiamondException("Unable to upload profile img", 500 + intval($upload));
            else 
              $filename = $upload;
            
            $user->setPDP($this->getPDO(), $upload);

            if (!isset($this->args['notify']) || boolval($this->args['notify']) != false)
                $this->getControleur()->notify('Votre photo de profil a été modifiée !', $user->getId(), 3, "Modification utilisateur", LINK . "comptes/" . $user->getId());
        }
        if (isset($this->args['password']) && isset($_SESSION['user']) && $_SESSION['user'] instanceof User && $_SESSION['user']->getId() == $this->args['user_id']){
            if ($user->changeOnesPassword($this->getPDO(), $_SESSION['user']->getId(), $this->args['password'], true) === false)
                throw new DiamondException("Unable to update user's password.", 336);

            if (!isset($this->args['notify']) || boolval($this->args['notify']) != false)
                $this->getControleur()->notify('Votre mot de passe a bien été modifié !', $user->getId(), 3, "Modification utilisateur", LINK . "comptes/" . $user->getId());
        }else if (isset($this->args['password'])){
            throw new DiamondException("Unable to update user's password, admins are not allowed to edit user's password directly. Please use the password recovery process.", 706);
        }
        return $this->formatedReturn(1);
    }

    private function findUser(int $user_id){
        if (!isset($_SESSION['user']))
            throw new DiamondException("Forbidden access (No user in session).", 706);

        if ($this->cache_user != null && $this->cache_user->getId() == $user_id)
            return $this->cache_user;

        try {
            return $this->cache_user = new UserHydrate(User::getPseudoById($this->getPDO(), $user_id), $this->getPDO(), $_SESSION['user']);
        }catch(Exception $e){
            throw new DiamondException("Unable to find user");
        }
    }

    public function set_modifRole(){
        try {
            $role = new RoleHydrate($this->getPDO(), $this->args['role_id']);
        }catch (Exception $e){
            throw new DiamondException("Unable to find requested rôle.", 337);
        }

        if ($this->level < 4 || !$role->canBeEdited())
            throw new DiamondException("Forbidden access", 706);

        if (isset($this->args['name']) && $this->args['name'] != $role->getName()){
            if ($role->setName($this->getPDO(), $this->args['name']) === false)
                throw new DiamondException("Unable to update role's name.", 338);
        }

        if (isset($this->args['level']) && $this->args['level'] != $role->getLevel()){
            if ($role->setLevel($this->getPDO(), $this->args['level']) === false)
                throw new DiamondException("Unable to update role's level.", 338);
        }
        return $this->formatedReturn(1);
    }

    public function set_defRole(){
        try {
            $role = new RoleHydrate($this->getPDO(), $this->args['role_id']);
        }catch (Exception $e){
            throw new DiamondException("Unable to find requested rôle.", 337);
        }

        if ($this->level < 4 || !$role->canBeEdited() || !$role->canBeDefault())
            throw new DiamondException("Forbidden access", 706);

        if ($role->setDefault($this->getPDO()) === false)
            throw new DiamondException("Unable to update role's status.", 338);

        return $this->formatedReturn(1);
    }

    public function set_addRole(){
        $i = simplifySQL\select($this->getPDO(), true, "d_roles", "*", array(array("name", "=", $this->args['name'])));
        if (is_array($i))
            throw new DiamondException("Role's name already taken", 339);

        if (!simplifySQL\insert($this->getPDO(), "d_roles", array("name", "level"), array($this->args['name'], $this->args['level'])))
            throw new DiamondException("Unable to create new role", 334);

        if ($this->args['def'] == "true"){
            $i = simplifySQL\select($this->getPDO(), true, "d_roles", "*", array(array("name", "=", $this->args['name'])));
            if (!is_array($i))
                throw new DiamondException("Unable to create new role (2)", 334);

            try {
                $role = new RoleHydrate($this->getPDO(), false, $i);
            }catch (Exception $e){
                throw new DiamondException("Unable to find requested rôle.", 337);
            }

            if (!$role->setDefault($this->getPDO()))
                throw new DiamondException("Unable to create new role (3).", 334);
        }
        return $this->formatedReturn(1);            
    }

    public function get_connectUser(){
        //On fait une pause pour ralentir les possibles hackers
        sleep(1);
        $salt = simplifySQL\select($this->getPDO(), true, "d_membre", "salt, recovery_deadline", array(array("pseudo", "=", $this->args['pseudo_connexion'])));
        if ($salt == false)
            return $this->formatedReturn(array("isAccount" => false,"banned" => false));
        
        if (is_array($salt) && array_key_exists("recovery_deadline", $salt) && !empty($salt["recovery_deadline"]) && !is_null($salt["recovery_deadline"]) && (new DateTime("now") < new DateTime($salt["recovery_deadline"])))
            return $this->formatedReturn(array("isAccount" => true,"banned" => true, "r_ban" => "Vous n'êtes pas banni, mais votre compte est désactivé car une procédure de réinitialisation de mot de passe est en cours."));

        $ia = $this->isAccount($this->args['pseudo_connexion'], $this->args['mp_connexion'], $salt['salt']);
        if ($ia == 0){
            return $this->formatedReturn(array("isAccount" => false,"banned" => true));
        }else if (!empty($ia) && is_array($ia)){
            try {
                $user = new User ($ia['pseudo'], $this->getPDO());
            }catch (Exception $e){
                //On lève une exception ici au lieu de juste dire que le compte n'existe pas car ce n'est pas normal, vu que la requête d'avant l'avait trouvé...
                throw new DiamondException("Unable to find requested user. (" . $e->getMessage() . ")", 335);
            }

            // On enregistre la dernière connexion pour faire des stats
            // et on prend aussi l'IP pour le ban ip et l'antispam
            $mod = simplifySQL\update($this->getPDO(), "d_membre", array(
                array("date_last_connect", "=", date("Y-m-d H:i:s")),
                array("nb_connections", "=", $user->getNbConnections()+1 ),
                array("date_lc_timestamp", "=", time()),
                array("ip", "=", $_SERVER['REMOTE_ADDR'])), array(array("id", "=", $user->getId())));

            if ($user->isBanned())
                return $this->formatedReturn(array("isAccount" => true,"banned" => true, "r_ban" => $user->getRBan()));
            
            $_SESSION['user'] = $user;
            $_SESSION['pseudo'] = htmlspecialchars($this->args['pseudo_connexion']); //par soucis de rétrocompatibilité
            
            if (isset($this->args['souvenir']) && ($this->args['souvenir'] == "true"  || $this->args['souvenir'] == "on" )){
                //On place un cookie dans lequel on inscrit le salt, un underscore, et le pseudo de connexion, le tout est hashé pour protéger le système
                //Le système de cookie est modernisé en 2022 pour intégrer l'attribut SameSite bien que la nouvelle syntaxe interdise php<7.4
                $arr_cookie_options = array (
                    'expires' => time() + 15*24*3600,
                    'path' => WEBROOT,
                    'domain' => '.' . $_SERVER['HTTP_HOST'],
                    'secure' => false,    
                    'httponly' => true,    
                    'samesite' => 'Lax' // None || Lax  || Strict
                );
                if (!empty($salt['salt'])){
                    setcookie('pseudo', sha1($salt['salt'] + '_' + htmlspecialchars($this->args['pseudo_connexion'])), $arr_cookie_options);
                    //setcookie('pseudo', sha1($salt['salt'] + '_' + htmlspecialchars($this->args['pseudo_connexion'])), time() + 15*24*3600, WEBROOT, $_SERVER['HTTP_HOST'], false, true);
                }else {
                    setcookie('pseudo', sha1(htmlspecialchars($this->args['pseudo_connexion'])), $arr_cookie_options);
                    //setcookie('pseudo', sha1(htmlspecialchars($this->args['pseudo_connexion'])), time() + 15*24*3600, WEBROOT, $_SERVER['HTTP_HOST'], false, true);
                }
            }

            return $this->formatedReturn(array("isAccount" => true, "banned" => false));
        }else if ($ia == -1){
            return $this->formatedReturn(array("isAccount" => true,"banned" => true, "r_ban" => "Votre compte a eu la même ip qu'un autre compte banni. Votre compte est donc suspendu. Banni par Console."));
        }
        throw new DiamondException("Unexpected error while fetching user account.", 329);

        return $this->formatedReturn(1);
    }

    public function get_isAccount(){
        //On fait une pause pour ralentir les possibles hackers
        sleep(1);
        $salt = simplifySQL\select($this->getPDO(), true, "d_membre", "salt", array(array("pseudo", "=", $this->args['pseudo_connexion'])));
        if ($salt == false)
            throw new DiamondException("Unable to find user (Salt unknown).", 332);
        
        $ia = $this->isAccount($this->args['pseudo_connexion'], $this->args['mp_connexion'], $salt['salt']);
        if ($ia == 0){
            return $this->formatedReturn(array("isAccount" => false,"banned" => false));
        }else if (!empty($ia) && is_array($ia)){
            return $this->formatedReturn(1);
        }else if ($ia == -1){
            return $this->formatedReturn(array("isAccount" => true,"banned" => true));
        }
    }


    /**
     * 
     * Attention, ne cherche pas à savoir si ban
     * @return int : 0 pas de compte, Array compte OK, -1 compte BAN
     */
    private function isAccount($pseudo, $mdp, $salt=""){
        if (empty($salt))
            $m = $mdp;
        else
            $m = $salt . $mdp;

        $password = sha1(htmlspecialchars($m));
        $pseudo_co = htmlspecialchars($pseudo);

        $rep = simplifySQL\select($this->getPDO(), true, "d_membre", "*", array(array("pseudo", "=", $pseudo_co), "AND", array("password", "=", $password)));
        
        if ($rep != null){
            $conf = $this->getIniConfig(ROOT . "config/config.ini");
            if (empty($conf) || !isset($conf['ban_ip']))
                throw new DiamondException("Unable to load config.", 550);

            if ($conf['ban_ip']){
                $rep2 = simplifySQL\select($this->getPDO(), true, "d_membre", "*", array(array("ip", "=", $_SERVER['REMOTE_ADDR']), "AND", array("is_ban", "=", true), "AND", array("date_lc_timestamp", ">", time()-60*60*24*30)));
                if ($rep2 != false && !empty($rep2)){
                    simplifySQL\update($this->getPDO(), "d_membre", 
                    array(
                        array("r_ban", "=", "Votre compte a eu la même ip qu'un autre compte banni. Votre compte est donc suspendu. Banni par Console."), 
                        array("date_ban", "=", date("Y-m-d")), 
                        array("is_ban", "=", 1)), 
                    array("id", "=", $rep['id']));
                    return -1;
                }
            }
            return $rep;
        }
        return 0;
    }

    public function get_addUser(){
        if (strpos($this->args['pseudo_inscription'], " ") != false)
            return $this->formatedReturn(array("Valid" => false,"error" => "Les espaces ne sont pas autorisés dans les pseudos."));
        
            
        $this->getControleur()->loadModel("comptes/inscription");
        $this->args = cleanIniTypes($this->args);
        $news = (isset($this->args['news']) && $this->args['news']);
        $conf = $this->getIniConfig(ROOT . "config/config.ini");
        if (empty($conf) || !isset($conf['ban_ip']))
            throw new DiamondException("Unable to load config.", 550);

        $inscription = addMembre($this->getPDO(), htmlspecialchars($this->args['pseudo_inscription']), htmlspecialchars($this->args['email_inscription']), $news, htmlspecialchars($this->args['mp_inscription']), htmlspecialchars($this->args['mp2_inscription']), false, $conf['ban_ip']);
          
        if ($inscription == -1){
            return $this->formatedReturn(array("Valid" => false,"error" => "Les deux mots de passe ne correspondent pas."));
        }elseif ($inscription == -2) {
            return $this->formatedReturn(array("Valid" => false,"error" => "Votre mot de passe doit faire plus de 6 charactères pour valider votre inscription."));
        }elseif ($inscription == -3) {
            return $this->formatedReturn(array("Valid" => false,"error" => "Votre pseudo ou votre email sont déjà utilisés dans un autre compte."));
        }elseif ($inscription == -4) {
            return $this->formatedReturn(array("Valid" => false,"error" => "Votre adresse IP est la même que celle d'un compte banni."));
        }elseif ($inscription == -5) {
            return $this->formatedReturn(array("Valid" => false,"error" => "Impossible de trouver les permissions à vous accorder."));
        }else {
            $_SESSION['pseudo'] = htmlspecialchars($this->args['pseudo_inscription']);
            try {
                $user = new User (htmlspecialchars($this->args['pseudo_inscription']), $this->getPDO());
            }catch (Exception $e){
                //On lève une exception ici au lieu de juste dire que le compte n'existe pas car ce n'est pas normal, vu que la requête d'avant l'avait trouvé...
                throw new DiamondException("Unable to find requested user. (" . $e->getMessage() . ")", 335);
            }
            return $this->formatedReturn(array("Valid" => true));
        }
               
    }

    public function get_disconnect(){
        if (isset($_SESSION['user']))
            User::disconnect($_SESSION['user']);
        return $this->formatedReturn(1);
    }

    public function set_deleteLastActions(){
        try {
            $user = $this->findUser($this->args['user_id']);
            if (!$user->can_ban())
                throw new DiamondException("Forbidden access", 706);

            if ($user->deleteLastActions($this->getPDO()))
                return $this->formatedReturn(1);
            else
                return $this->formatedReturn(0);
        }catch (Exception $e){
            throw new DiamondException("Unable to find requested user.", 335);
        }
    }

    public function set_addVote(){
        // On ne vérifie pas qu'on a l'utilisateur car la fonction le fait elle même
        // Evitons de charger la BDD inutilement...
        $conf = $this->getIniConfig("config/config.ini", true);

        User::addOnesVote($this->getPDO(), intval($this->args['user_id']), (isset($conf['tokens_vote']) && is_numeric($conf['tokens_vote'])) ? intval($conf['tokens_vote']) : 1);
        return $this->formatedReturn(array('vote_link' => $conf['lien_vote']));
    }

    public function set_startReinitPassword(){
        $check_email = simplifySQL\select($this->getPDO(), false, "d_membre", "id, pseudo, email", array(array("email", "=", $this->args["email"])));
        if (!(is_array($check_email) AND !empty($check_email)))
            throw new DiamondException("No account found.", "native$332");
        if (is_array($check_email) && sizeof($check_email) != 1)
            throw new DiamondException("Multiple accounts are sharing the same email. Unable to pursue.", "native$333");
        
        $target_user = new UserHydrate($check_email[0]['pseudo'], $this->getPDO());
        $code = $target_user->startPasswordReinitialization($this->getPDO(), ($this->level > 4));

        function contact_admin($controleur, $PDO, $level, $email, $code){
            if ($level < 5){
                $controleur->loadModel('contact');
                addContact($PDO, "Système", "<strong>Un utilisateur a initié une réinitialisation de mot de passe,</strong> mais comme vous n'avez pas configuré de serveur SMTP valide ou qu'une erreur s'est produite lors de l'envoi du mail, il n'est pas possible de lui envoyer le code de réinitialisation automatiquement. 
                Veuillez donc lui transmettre le code <code>" . $code . "</code> valable jusqu'au <code>" . date('Y-m-d', strtotime(date('Y-m-d'). ' + 3 days')) ."</code>. Ce code est à utiliser sur le lien <code>" . 
                LINK . "endReinit/</code> <br>
                Si ce code venait à expirer, vous avez la possibilité d'en regénérer un sur votre interface administrateur, en accédant à son profil.", $email);
                $controleur->notify('URGENT : Une nouvelle demande de contact disponible sur votre interface. ', "admin", 1, "Contact", "");
            }
        }

        $mail_conf = $this->getIniConfig(ROOT . "config/mails.ini");
        if (array_key_exists("en_mail_passwordrecovery", $mail_conf) && array_key_exists("SMTP", $mail_conf) && is_bool($mail_conf["en_mail_passwordrecovery"]) && $mail_conf["en_mail_passwordrecovery"] && file_exists($this->paths["config"] . "reinitpswd_mailtemplate.ftxt")){
            $pswdrecoverydraft_content = file_get_contents($this->paths["config"] . "reinitpswd_mailtemplate.ftxt");
            try{
                $this->getControleur()->loadModel("mail");
                $rtrn_mail = sendMail($mail_conf["SMTP"], array($check_email[0]['email']), "Réinitialisation de votre mot de passe", str_replace("{CODE}", $code, $pswdrecoverydraft_content));
                if (is_array($rtrn_mail) && $rtrn_mail["success"] == true)
                    return $this->formatedReturn("Un code de réinitialisation a été créé et vous a été envoyé dans un mail. Celui-ci est valable 72h. Si vous ne parvenez pas à le récupérer, vous pouvez contacter un administrateur (ce dernier est en capacité de récupérer votre code).");
                else if (is_array($rtrn_mail) && $rtrn_mail["success"] == false)
                    throw new DiamondException($rtrn_mail["error"] . " - Le code de réinitialisation a bien été créé. Contactez un administrateur.", "811");   
                else
                    throw new DiamondException("Unable to send mail - Le code de réinitialisation a bien été créé. Contactez un administrateur.", "811");   
            }catch (DiamondException $e){ 
                contact_admin($this->getControleur(), $this->getPDO(), $this->level, $this->args['email'], $code); throw $e; 
            }
            catch (PHPMailer\PHPMailer\Exception $e){
                contact_admin($this->getControleur(), $this->getPDO(), $this->level, $this->args['email'], $code);
                throw new DiamondException("Unable to send mail : " . $e->getMessage() . " - Le code de réinitialisation a bien été créé. Contactez un administrateur.", "811");   
            }
            catch (Throwable $e){
                contact_admin($this->getControleur(), $this->getPDO(), $this->level, $this->args['email'], $code);
                throw new DiamondException("Unable to send mail (fatal error: " . $e->getMessage() . ") - Le code de réinitialisation a bien été créé. Contactez un administrateur.", "811");   
            }
        }else {
            contact_admin($this->getControleur(), $this->getPDO(), $this->level, $this->args['email'], $code);
            return $this->formatedReturn("Un code de réinitialisation a été créé. Toutefois, le serveur mail n'est pas paramétré et nous ne pouvons vous l'envoyer par mail. Dans ses 72 heures de validité, un administrateur va vous contacter au plus vite pour vous le transmettre manuellement. Veuillez nous excuser pour la gêne occasionnée. ");
        }
    }

    public function get_endReinitPassword(){
        $check_code = simplifySQL\select($this->getPDO(), true, "d_membre", "id, pseudo, recovery_deadline, recovery_code", array(array("recovery_code", "=", $this->args["code_reinit"])));
        if (!(is_array($check_code) AND !empty($check_code)))
            throw new DiamondException("No account found.", "native$332");
        
        $target_user = new UserHydrate($check_code['pseudo'], $this->getPDO());
        if (!is_bool($changePassword = $target_user->changePassword($this->getPDO(), $this->args["newmp_reinit"])) OR !$changePassword)
            throw new DiamondException("No account found.", "native$342a");
        
        return $this->formatedReturn("Mot de passe réinitialisé avec succès.");
    }

    public function get_emailUnsubscribe(){
        $check_email = simplifySQL\select($this->getPDO(), false, "d_membre", "id, pseudo, email", array(array("email", "=", $this->args["email"])));
        if (!(is_array($check_email) AND !empty($check_email)))
            throw new DiamondException("No account found.", "native$332");
        if (is_array($check_email) && sizeof($check_email) != 1)
            throw new DiamondException("Multiple accounts are sharing the same email. Unable to pursue.", "native$333");
        
        $target_user = new UserHydrate($check_email[0]['pseudo'], $this->getPDO());
        $code = $target_user->emailUnsubscribe($this->getPDO());
        return $this->formatedReturn("Vous avez bien été désabonné de nos listes de diffusion.");
    }
}