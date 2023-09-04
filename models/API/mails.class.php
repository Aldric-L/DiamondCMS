<?php

class mails extends DiamondAPI{
    public function __construct(array $paths, PDO $pdo, Controleur $controleur, int $level){
        parent::__construct($paths, $pdo, $controleur, $level);
        $this->params_needed = array(
            "set_config" => array(),
            "set_testConfig" => array(),
            "set_newDraft" => array("mail_from", "mail_to", "mail_subject", "mail_content"),
            "set_editDraft" => array("draft_id", "mail_from", "mail_to", "mail_subject", "mail_content"),
            "set_sendDraft" => array("draft_id"),
            "set_sendMail" => array("mail_from", "mail_to", "mail_subject", "mail_content"),
            "set_editRecoveryPswd" => array("mail_content"),
        );
    }

    /** 
     * set_config - Fonction permettant de modifier la config des mails (fichier config/mails.ini)
     * 
     * @param bool en_mail_passwordrecovery (optionnal) : activer les mails en password recovery
     * @param string host (optionnal) : pour la config du serveur SMTP
     * @param string adress (optionnal) : pour la config du serveur SMTP
     * @param string username (optionnal) : pour la config du serveur SMTP
     * @param string password (optionnal) : pour la config du serveur SMTP
     * @param int port (optionnal) : pour la config du serveur SMTP
     * @param string encryption (optionnal) : type de sécurité des échanges (valeurs autorisées : ENCRYPTION_STARTTLS, ENCRYPTION_SMTPS, NONE)
     * @access public 
     * @author Aldric L.
     * @copyright 2023
     */
    public function set_config(){
        if ($this->level <= 4)
            throw new Exception("Forbidden access", 706);

        if (empty($this->args))
            throw new DiamondException("No argument provided", 701);

        $this->registerNewConfig();
        
        return $this->formatedReturn(1);
    }

    /** 
     * set_testConfig - Fonction permettant de tester la config du SMTP
     * Attention, si une config est passée en argument, elle sera dabord traitée comme une enregistrement de config normal
     * 
     * @access public 
     * @author Aldric L.
     * @copyright 2023
     */
    public function set_testConfig(){
        if (!empty($this->args))
            $this->registerNewConfig();

        require_once $this->paths['models'] .  'libs/phpmailer/Exception.php';
        require_once $this->paths['models'] .  'libs/phpmailer/PHPMailer.php';
        require_once $this->paths['models'] .  'libs/phpmailer/SMTP.php';

        $rtrn_str = "Installation complète et fonctionnelle.";
        $config = $this->getIniConfig(ROOT . "config/mails.ini", true, false);

        //Create an instance; passing `true` enables exceptions
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);

        try {
            //$mail->SMTPDebug = PHPMailer\PHPMailer\SMTP::DEBUG_SERVER; 
            $mail->isSMTP();      
            $mail->Host       = $config["SMTP"]["host"];    
            $mail->SMTPAuth   = true; 
            $mail->Username   = $config["SMTP"]["username"];
            $mail->Password   = $config["SMTP"]["password"];
            switch ($config["SMTP"]["encryption"]) {
                case 'ENCRYPTION_STARTTLS':
                    $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;      
                    break;
                case 'ENCRYPTION_SMTPS':
                    $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;      
                    break;
                
                default:
                    //$mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;      
                    break;
            }  
            $mail->Port = $config["SMTP"]["port"];   
            $mail->setFrom($config["SMTP"]["adress"], 'Mailer');            
            if ($mail->SmtpConnect() == false)
                $rtrn_str = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        } catch (Exception $e) {
            $rtrn_str = "Message could not be sent. Mailer Error: {$e->getMessage()}";
        }
        return $this->formatedReturn($rtrn_str);
    }

    /** 
     * registerNewConfig - Fonction permettant d'enregistrer les modifications de la config des mails
     * Cette fonction existe pour éviter les recopies de code
     * 
     * @access privzte 
     * @author Aldric L.
     * @copyright 2023
     */
    private function registerNewConfig() : void{
        $to_conf = $this->getIniConfig(ROOT . "config/mails.ini");
        $this->args = cleanIniTypes($this->args);
        if (array_key_exists("host", $this->args) && is_string($this->args["host"]) && !empty($this->args["host"])){
            $to_conf["SMTP"]["host"]= $this->args["host"];
            unset($this->args["host"]);
        }
        if (array_key_exists("username", $this->args) && is_string($this->args["username"]) && !empty($this->args["username"])){
            $to_conf["SMTP"]["username"]= $this->args["username"];
            unset($this->args["username"]);
        }
        if (array_key_exists("adress", $this->args) && is_string($this->args["adress"]) && !empty($this->args["adress"])){
            $to_conf["SMTP"]["adress"]= $this->args["adress"];
            unset($this->args["adress"]);
        }
        if (array_key_exists("password", $this->args) && is_string($this->args["password"]) && !empty($this->args["password"])){
            $to_conf["SMTP"]["password"]= $this->args["password"];
            unset($this->args["password"]);
        }
        if (array_key_exists("port", $this->args) && is_numeric($this->args["port"]) ){
            $to_conf["SMTP"]["port"]= $this->args["port"];
            unset($this->args["port"]);
        }
        if (array_key_exists("encryption", $this->args) && is_string($this->args["encryption"]) && !empty($this->args["encryption"])){
            if (!in_array($this->args["encryption"], array("ENCRYPTION_STARTTLS","ENCRYPTION_SMTPS","NONE")))
                throw new DiamondException("Illegal value for encryption", 701);
            $to_conf["SMTP"]["encryption"]= $this->args["encryption"];
            unset($this->args["encryption"]);
        }
        if (array_key_exists("en_mail_passwordrecovery", $this->args) && is_bool($this->args["en_mail_passwordrecovery"])){
            if ($this->args["en_mail_passwordrecovery"] && 
            (empty($to_conf["SMTP"]["host"]) OR empty($to_conf["SMTP"]["adress"]) OR
            empty($to_conf["SMTP"]["username"]) OR empty($to_conf["SMTP"]["password"]) OR
            empty($to_conf["SMTP"]["port"]) OR empty($to_conf["SMTP"]["encryption"]) ))
                throw new DiamondException("Unable to enable mail password recovery because SMTP server si badly configured.", 701);
            $to_conf["en_mail_passwordrecovery"]= $this->args["en_mail_passwordrecovery"];
            unset($this->args["en_mail_passwordrecovery"]);
        }
        if (!empty($to_conf))
            $this->setConfig(ROOT."config/mails.ini", $to_conf);
    }

    /** 
     * set_newDraft - Fonction permettant de créer un brouillon de mail
     * Note : Cette fonction n'a pas besoin d'être appelée lors d'un envoi direct (pas besoin de créer un brouillon préalable)
     * Réservée au level 4 ou supérieur
     * 
     * @param string mail_from : adresse d'envoi, qui doit être celle de la config du serveur SMTP
     * @param string/int mail_to : si int: envoyer le mail à tout le role, si string == "custom", lire le champ mail_to_custom
     * @param string mail_to_custom (optionnal) : string des destinataires au format : "Nom d'utilisateur 1";"Nom d'utilisateur 2";r="Role utilisateur";...
     * @param string mail_subject : sujet du mail
     * @param string mail_content : contenu du mail
     * @author Aldric L.
     * @copyright 2023
     */
    public function set_newDraft(){
        if ($this->level < 4)
            throw new Exception("Forbidden access", 706);

        if (is_numeric($this->args["mail_to"]) or $this->args["mail_to"] == "all")
            $this->args["mail_to_custom"] = $this->args["mail_to"];
        else if (!array_key_exists("mail_to_custom", $this->args))
            throw new DiamondException("No valid recipient provided.", 701);

        $to_conf = $this->getIniConfig(ROOT . "config/mails.ini");
        if ($to_conf["SMTP"]["adress"] != $this->args["mail_from"])
            throw new DiamondException("Invalid sender.", 701);
        
        try{
            if (!simplifySQL\insert($this->getPDO(), "d_mails",
                array("from_adress", "to_list", "content", "subject"),
                array($this->args['mail_from'], $this->args['mail_to_custom'], $this->args['mail_content'], $this->args['mail_subject'])))
                    
            throw new DiamondException("Unable to create draft", "342c");   
        }catch (DiamondException $e){ throw $e; }
        catch (Throwable $e){
            throw new DiamondException("Unable to create draft (2)", "342c");   
        }

        return $this->formatedReturn(1);
    }


    /** 
     * set_editDraft - Fonction permettant de modifier un brouillon de mail
     * Réservée au level 4 ou supérieur 
     * 
     * @param int draft_id : id du draft
     * @param string mail_from : adresse d'envoi, qui doit être celle de la config du serveur SMTP
     * @param string/int mail_to : si int: envoyer le mail à tout le role, si string == "custom", lire le champ mail_to_custom
     * @param string mail_to_custom (optionnal) : string des destinataires au format : "Nom d'utilisateur 1";"Nom d'utilisateur 2";r="Role utilisateur";...
     * @param string mail_subject : sujet du mail
     * @param string mail_content : contenu du mail
     * @author Aldric L.
     * @copyright 2023
     */
    public function set_editDraft(){
        if ($this->level < 4)
            throw new Exception("Forbidden access", 706);

        if (!is_numeric($this->args['draft_id']))
            throw new DiamondException("An int is an int (id)", 701);
        $this->args['draft_id'] = intval($this->args['draft_id']);

        if (is_numeric($this->args["mail_to"]) or $this->args["mail_to"] == "all")
            $this->args["mail_to_custom"] = $this->args["mail_to"];
        else if (!array_key_exists("mail_to_custom", $this->args))
            throw new DiamondException("No valid recipient provided.", 701);

        $to_conf = $this->getIniConfig(ROOT . "config/mails.ini");
        if ($to_conf["SMTP"]["adress"] != $this->args["mail_from"])
            throw new DiamondException("Invalid sender.", 701);
        
        try{
            if (!simplifySQL\update($this->getPDO(), "d_mails",
                array("from_adress" => $this->args['mail_from'], 
                "to_list" => $this->args['mail_to_custom'], 
                "content" => $this->args['mail_content'], 
                "subject" => $this->args['mail_subject']),
                array(array("id","=", $this->args['draft_id']))))
                    
            throw new DiamondException("Unable to update draft", "342a");   
        }catch (DiamondException $e){ throw $e; }
        catch (Throwable $e){
            throw new DiamondException("Unable to update draft (2)", "342a");   
        }

        return $this->formatedReturn(1);
    }

    /** 
     * set_sendDraft - Fonction permettant de modifier un brouillon de mail et de l'envoyer
     * Réservée au level 4 ou supérieur 
     * 
     * @param int draft_id : id du draft
     * @author Aldric L.
     * @copyright 2023
     */
    public function set_sendDraft(){
        if ($this->level < 4)
            throw new Exception("Forbidden access", 706);

        if (!is_numeric($this->args['draft_id']))
            throw new DiamondException("An int is an int (id)", 701);
        $this->args['draft_id'] = intval($this->args['draft_id']);

        try{
            $draft = simplifySQL\select($this->getPDO(), true, "d_mails", "*", array(array("id", "=", $this->args['draft_id'])));
            if (!is_array($draft) || empty($draft) || $draft == null || $draft == false)
                throw new DiamondException("Unable to locate draft", "718");   
        }catch (DiamondException $e){ throw $e; }
        catch (Throwable $e){
            throw new DiamondException("Unable to find draft", "343a");   
        }

        if (array_key_exists("mail_to", $this->args) && array_key_exists("mail_content", $this->args) && array_key_exists("mail_subject", $this->args)){
            if (is_numeric($this->args["mail_to"]) or $this->args["mail_to"] == "all")
                $this->args["mail_to_custom"] = $this->args["mail_to"];
            else if (!array_key_exists("mail_to_custom", $this->args))
                throw new DiamondException("No valid recipient provided.", 701);

            $draft["to_list"] = $this->args["mail_to_custom"];
            $draft["from_adress"] = $this->args["mail_from"];
            $draft["content"] = $this->args["mail_content"];
            $draft["subject"] = $this->args["mail_subject"];
        
        }

        if (!array_key_exists("from_adress", $draft) OR empty($draft["from_adress"]) OR
        !array_key_exists("to_list", $draft) OR empty($draft["to_list"]) OR
        !array_key_exists("content", $draft) OR empty($draft["content"]) OR
        !array_key_exists("subject", $draft) OR empty($draft["subject"]))
            throw new DiamondException("Draft uncomplete. Unable to send it.", "native$810");

        $to_conf = $this->getIniConfig(ROOT . "config/mails.ini");
        if ($to_conf["SMTP"]["adress"] != $draft["from_adress"])
            throw new DiamondException("Invalid sender.", 701);

        $this->sendMail($to_conf["SMTP"], $draft["subject"], $draft["content"], $draft["to_list"]);
        
        try{
            if (!simplifySQL\update($this->getPDO(), "d_mails",
                array("from_adress" => $draft['from_adress'], 
                "to_list" => $draft['to_list'], 
                "content" => $draft['content'], 
                "subject" => $draft['subject'],
                "date_send" => date("Y-m-d h:i:s"),
                "author" => (isset($_SESSION['user']) && $_SESSION['user'] instanceof User) ? $_SESSION['user']->getId() : NULL,
                ),
                array(array("id","=", $this->args['draft_id']))))
                    
            throw new DiamondException("Unable to update draft status, but mail has been successfully sent", "342a");   
        }catch (DiamondException $e){ throw $e; }
        catch (Throwable $e){
            throw new DiamondException("Unable to update draft status, but mail has been successfully sent", "342a");   
        }
        
        return $this->formatedReturn(1);
    }

    /** 
     * set_sendMail - Fonction permettant d'envoyer un mail sans passer par l'écriture d'un brouillon
     * Réservée au level 4 ou supérieur 
     * 
     * @param string mail_from : adresse d'envoi, qui doit être celle de la config du serveur SMTP
     * @param string/int mail_to : si int: envoyer le mail à tout le role, si string == "custom", lire le champ mail_to_custom
     * @param string mail_to_custom (optionnal) : string des destinataires au format : "Nom d'utilisateur 1";"Nom d'utilisateur 2";r="Role utilisateur";...
     * @param string mail_subject : sujet du mail
     * @param string mail_content : contenu du mail
     * @author Aldric L.
     * @copyright 2023
     */
    public function set_sendMail(){
        if ($this->level < 4)
            throw new Exception("Forbidden access", 706);

        if (is_numeric($this->args["mail_to"]) or $this->args["mail_to"] == "all")
            $this->args["mail_to_custom"] = $this->args["mail_to"];
        else if (!array_key_exists("mail_to_custom", $this->args))
            throw new DiamondException("No valid recipient provided.", 701);

        $to_conf = $this->getIniConfig(ROOT . "config/mails.ini");
        if ($to_conf["SMTP"]["adress"] != $this->args["mail_from"])
            throw new DiamondException("Invalid sender.", 701);
        
        $this->sendMail($to_conf["SMTP"], $this->args["mail_subject"], $this->args["mail_content"], $this->args["mail_to_custom"]);

        try{
            if (!simplifySQL\insert($this->getPDO(), "d_mails",
                array("from_adress", "to_list", "content", "subject", "date_send", "author"),
                array($this->args['mail_from'], $this->args['mail_to_custom'], $this->args['mail_content'], $this->args['mail_subject'], date("Y-m-d h:i:s"), (isset($_SESSION['user']) && $_SESSION['user'] instanceof User) ? $_SESSION['user']->getId() : NULL)))
                    
            throw new DiamondException("Unable to create draft", "342c");   
        }catch (DiamondException $e){ throw $e; }
        catch (Throwable $e){
            throw new DiamondException("Unable to create draft (2)", "342c");   
        }

        return $this->formatedReturn(1);
    }

    /** 
     * sendMail - Méthode interne permettant d'envoyer un mail 
     * La méthode s'occupe notamment du parsing de to_list
     * 
     * @param array config_smtp : partie SMTP de la config mails
     * @param string/int subject : objet du mail
     * @param string content : contenu brut html
     * @param mixed to_list : liste de destinataires à parser
     * @author Aldric L.
     * @copyright 2023
     */
    private function sendMail(array $config_smtp, string $subject, string $content, $to_list) : void{
        $this->getControleur()->loadModel("hydratation/roleHydrate.class");
        $this->getControleur()->loadModel("hydratation/userHydrate.class");
        $recipients = array();
        if (is_numeric($to_list)){
            $role = new RoleHydrate($this->getPDO(), intval($to_list));
            foreach ($role->getLinkedAccounts($this->getPDO()) as $a){
                $user = new UserHydrate($a['pseudo'], $this->getPDO(), $a);
                if ($user->isOkToGetMails() && !$user->isBanned())
                    array_push($recipients, $user->getEmail());
            }
        }else if ($to_list == "all") {
            $users = simplifySQL\select($this->getPDO(), false, "d_membre", User::getSelectFetchInGetInfos(), 
            array(array("news", "=", true), "AND", array("is_ban", "=", false)));
            foreach ($users as $a){
                array_push($recipients, $a["email"]);
            }
        }
        else {
            $recipients_raw = explode(";", $to_list);
            foreach ($recipients_raw as $r){
                if (substr($r, 0, 3) == 'r="' && substr($r, -1, 1) == '"'){
                    $role_name = substr($r, 3, strlen($r)-4);
                    $role = new RoleHydrate($this->getPDO(), $role_name);
                    foreach ($role->getLinkedAccounts($this->getPDO()) as $a){
                        $user = new UserHydrate($a['pseudo'], $this->getPDO(), $a);
                        if ($user->isOkToGetMails() && !$user->isBanned() && !in_array($user->getEmail(), $recipients))
                            array_push($recipients, $user->getEmail());
                    }
                }else if(substr($r, 0, 1) == '"' && substr($r, -1, 1) == '"'){
                    $user = new UserHydrate(substr($r, 1, strlen($r)-2), $this->getPDO());
                    if ($user->isOkToGetMails() && !$user->isBanned() && !in_array($user->getEmail(), $recipients))
                        array_push($recipients, $user->getEmail());
                }
            }
        }
        
        try{
            $this->getControleur()->loadModel("mail");
            $rtrn_mail = sendMail($config_smtp, $recipients, $subject, $content);
            if (is_array($rtrn_mail) && $rtrn_mail["success"] == true)
                $this->formatedReturn("Mail envoyé avec succès");
            else if (is_array($rtrn_mail) && $rtrn_mail["success"] == false)
                throw new DiamondException($rtrn_mail["error"], "811");   
            else
                throw new DiamondException("Unable to send mail", "811");   
        }catch (DiamondException $e){ throw $e; }
        catch (PHPMailer\PHPMailer\Exception $e){
            throw new DiamondException("Unable to send mail : " . $e->getMessage(), "811");   
        }
        catch (Throwable $e){
            throw new DiamondException("Unable to send mail (fatal error)", "811");   
        }
    }

    /** 
     * set_editRecoveryPswd - Fonction permettant de modifier le mail envoyé aux utilisateurs réinitialisant leur mot de passe
     * Réservée au level 5 
     * 
     * @param string mail_content : contenu du mail
     * @author Aldric L.
     * @copyright 2023
     */
    public function set_editRecoveryPswd(){
        if ($this->level <= 4)
            throw new Exception("Forbidden access", 706);

        file_put_contents($this->paths["config"] . "reinitpswd_mailtemplate.ftxt", $this->args["mail_content"]);

        return $this->formatedReturn(1);
    }
}