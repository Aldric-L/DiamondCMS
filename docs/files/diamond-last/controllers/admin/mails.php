<?php 

/**
 * Page Admin/mails
 * 
 * Sur cette page on configure le serveur mail SMTP et on permet d'envoyer quelques mails
 * Cette page est réalisée en pageBuilder, il n'y a donc pas de vue.
 * Elle est donc à jour des bonnes pratiques en 2022.
 * 
 * @author Aldric L.
 * @copyright 2023
 * 
 */
$controleur_def->loadModel('admin/accueil');

$tb = new PageBuilders\ThemeBuilder($Serveur_Config['theme']);

$mails_config = cleanIniTypes(parse_ini_file(ROOT . 'config/mails.ini', true));

// On construit la page, avec le nom et la description
$adminBuilder = $tb->AdminBuilder("Utilitaire de mails", "Sur cette page, vous pouvez configurer DiamondCMS avec votre serveur SMTP mail pour activer la récupération de mots de passe automatique, et envoyer des mails à votre communauté.");

$encryption_options = array(
    array("disp" => "Start TLS", "val" => "ENCRYPTION_STARTTLS"),
    array("disp" => "SMTPS (SSL)", "val" => "ENCRYPTION_SMTPS"),
    array("disp" => "Aucun", "val" => "NONE")  
);

$config_encryption_options = $encryption_options;
if (is_string($mails_config["SMTP"]["encryption"])){
    foreach ($config_encryption_options as &$s){
        if ($s['val'] == $mails_config["SMTP"]["encryption"]){
            $s["selected"] = true;
            break;
        }
    }
}

$config_form = $tb->AdminForm("configform", true);
$config_form->addCheckField("en_mail_passwordrecovery", "Activer la fonction 'Mot de passe oublié' par mail", $mails_config["en_mail_passwordrecovery"])
->addCustom($tb->UIString("<hr>"))
->addTextField("host", "Host du serveur mail SMTP", (is_string($mails_config["SMTP"]["host"]) ? $mails_config["SMTP"]["host"] : ""), true)
->addTextField("username", "Username du serveur mail SMTP", (is_string($mails_config["SMTP"]["username"]) ? $mails_config["SMTP"]["username"] : ""), true, false,
"Il s'agit fréquemment de votre adresse mail.")
->addTextField("adress", "Adresse mail du serveur mail SMTP", (is_string($mails_config["SMTP"]["adress"]) ? $mails_config["SMTP"]["adress"] : ""), true)
->addMpField("password", "Mot de passe du serveur mail SMTP", (is_string($mails_config["SMTP"]["password"]) ? $mails_config["SMTP"]["password"] : ""), true)
->addSelectField("encryption", "Mode de communication sécurisée", $config_encryption_options, true, false)
->addNumberField("port", "Port du serveur mail SMTP", (is_numeric($mails_config["SMTP"]["port"]) ? $mails_config["SMTP"]["port"] : ""), true, false,
"Attention, ce port dépend fortement de votre réglage du mode de communication sécurisée (usuellement : 25 ou 2525 sans sécurité, 465 avec SMTPS et 587 avec TLS).")
->addAPIButton($tb->AdminAPIButton("Tester la configuration", "btn btn-warning", LINK . "api/", "mails", "set", "testConfig", $config_form , "", true, true ))
->addAPIButton($tb->AdminAPIButton("Sauvegarder", "btn btn-custom", LINK . "api/", "mails", "set", "config", $config_form , "", true ))
->setButtonsLine('class="text-right"');

$config_panel = $tb->AdminPanel("Configuration", "fa-cogs", $config_form, "lg-4");


$left_column = $tb->UIColumn("lg-4", $config_panel);
$adminBuilder->addColumn($left_column);

$controleur_def->loadModel("hydratation/roleHydrate.class");
$permissions = simplifySQL\select($controleur_def->bddConnexion(), false, "d_roles", "*", false, "level");
$select_to_array = array();
array_push($select_to_array, array("val" =>"all", "disp" => "Tous les utilisateurs"));
foreach ($permissions as $k => $p){
    $permissions[$k] = new RoleHydrate($controleur_def->bddConnexion(), false, $p);
    array_push($select_to_array, array("val" =>$permissions[$k]->getId(), "disp" => "Utilisateurs " . $permissions[$k]->getName()));
}
array_push($select_to_array, array("val" =>"custom", "disp" => "Liste personnalisée"));

$badSMTP = false;
if ((empty($mails_config["SMTP"]["host"]) OR empty($mails_config["SMTP"]["adress"]) OR
    empty($mails_config["SMTP"]["username"]) OR empty($mails_config["SMTP"]["password"]) OR
    empty($mails_config["SMTP"]["port"]) OR empty($mails_config["SMTP"]["encryption"]) ))
    $badSMTP = true;

//$badSMTPAlert = $tb->UIString('<p class="text-center text-danger">Aucun mail ne pourra être envoyé tant que le serveur SMTP ne sera pas configuré correctement. Vous pouvez néanmoins dès maintenant écrire des brouillons !</p>');
$badSMTPAlert = $tb->AdminAlert("danger", "Aucun mail ne pourra être envoyé tant que le serveur SMTP ne sera pas configuré correctement.", "Vous pouvez néanmoins dès maintenant écrire des brouillons !", true);



$new_mail_form = $tb->AdminForm("newmailform", false);
$new_mail_form->addTextField("mail_from", "Adresse d'envoi", (is_string($mails_config["SMTP"]["adress"]) ? $mails_config["SMTP"]["adress"] : ""), true, true)
->addSelectField("mail_to", "Destinataires", $select_to_array, true, false, "Veuillez noter que seuls les utilisateurs ayant accepté de recevoir vos mails parmi la liste de diffusion choisie seront contactés.")
->addTextField("mail_to_custom", "Liste personnalisée", "", false, $tb->AvailableIf($new_mail_form, "mail_to", \PageBuilders\AvailableIf::EQUAL, "custom"), 
"Indiquez les pseudos des membres que vous voulez contacter entre guillemets (\") et séparez les par des points virgule (pour faire une liste de groupes par rôles, utilisez le format : r=\"Nom du role\";r=\"Nom du second role\"; ).")
->addTextField("mail_subject", "Objet du mail", "", false)
->addtextAreaField("mail_content", "Contenu du message", "", 10, true, false, "Un lien sera ajouté en fin de mail permettant à vos utilisateurs de ne plus recevoir de courriels comme l'impose la réglementation européenne.")
->addAPIButton($tb->AdminAPIButton("Enregistrer comme brouillon", "btn btn-warning", LINK . "api/", "mails", "set", "newDraft", $new_mail_form , "", true ))
->addAPIButton($tb->AdminAPIButton("Envoyer", "btn btn-custom", LINK . "api/", "mails", "set", "sendMail", $new_mail_form , "", true, true, false, $badSMTP))
->setButtonsLine('class="text-right"');

$new_mail_panel = $tb->AdminPanel("Nouveau mail", "fa-envelope", $badSMTP ? $tb->UIArray($badSMTPAlert, $new_mail_form) : $new_mail_form, "lg-8");
$adminBuilder->addPanel($new_mail_panel);

$mails = simplifySQL\select($controleur_def->bddConnexion(), false, "d_mails", "*", false, "id", true);

$list = $tb->AdminList();
foreach ($mails as $m){
    $select_to_array_private = $select_to_array;
    $sfound = false;
    if (is_numeric($m["to_list"]) OR $m["to_list"] == "all"){
        foreach ($select_to_array_private as &$s){
            if ((is_numeric($m["to_list"]) && $s['val'] == intval($m["to_list"]))
                OR ($m["to_list"] == "all" && $s['val'] == $m["to_list"])){
                $sfound = true;
                $s["selected"] = true;
            }
        }
    }
    if (!$sfound)
        end($select_to_array_private)["selected"] = true;
    
    $editdraftform = $tb->AdminForm("editdraftform_" . $m["id"], true);
    $editdraftform->addTextField("mail_from", "Adresse d'envoi", (is_string($mails_config["SMTP"]["adress"]) ? $mails_config["SMTP"]["adress"] : ""), true, true)
        ->addSelectField("mail_to", "Destinataires", $select_to_array_private, true, !(is_null($m['date_send']) OR empty($m['date_send'])), "Veuillez noter que seuls les utilisateurs ayant accepté de recevoir vos mails parmi la liste de diffusion choisie seront contactés.")
        ->addTextField("mail_to_custom", "Liste personnalisée", ($sfound) ? "" : $m["to_list"], false, $tb->AvailableIf($editdraftform, "mail_to", \PageBuilders\AvailableIf::EQUAL, "custom"), 
        "Indiquez les pseudos des membres que vous voulez contacter entre guillemets (\") et séparez les par des points virgule (pour faire une liste de groupes par rôles, utilisez le format : r=\"Nom du role\";r=\"Nom du second role\"; ).")
        ->addTextField("mail_subject", "Objet du mail", $m['subject'], false, !(is_null($m['date_send']) OR empty($m['date_send'])))
        ->addtextAreaField("mail_content", "Contenu du message", $m["content"], 10, true, !(is_null($m['date_send']) OR empty($m['date_send'])), "Un lien sera ajouté en fin de mail permettant à vos utilisateurs de ne plus recevoir de courriels comme l'impose la réglementation européenne.")
        ->addHiddenField("draft_id", $m["id"]);

    $modal = $tb->AdminModal(((is_null($m['date_send']) OR empty($m['date_send'])) ? "Edition de brouillon" : "Mail envoyé"), "mail_" . $m['id'], $editdraftform, "", "modal-lg");
    if (is_null($m['date_send']) OR empty($m['date_send'])){
        $modal->addAPIButton($tb->AdminAPIButton("Enregistrer", "btn btn-warning", LINK . "api/", "mails", "set", "editDraft", $editdraftform , "", true ))
        ->addAPIButton($tb->AdminAPIButton("Envoyer", "btn btn-custom", LINK . "api/", "mails", "set", "sendDraft", $editdraftform , "", false, true, false, $badSMTP));   
    }
    
    $adminBuilder->addModal($modal);
    //$modal->addAPIButton($tb->AdminAPIButton("Supprimer", "btn-danger", LINK . "api/", "admin", "set", "delContact", "id=" . (string)$c['id'], "", "true", "true"));
    $list->addField($tb->UIString("<strong>Objet : </strong>" . $m['subject']), 
    $tb->UIString("<strong>Status : " . ((is_null($m['date_send']) OR empty($m['date_send'])) ? "<span class=\"text-warning\">brouillon</span>" : "<span class=\"text-success\">envoyé</span>") ."</strong>"), $modal);
}

if (file_exists($controleur_def->getPaths()["config"] . "reinitpswd_mailtemplate.ftxt")){
    $pswdrecoverydraft_content = file_get_contents($controleur_def->getPaths()["config"] . "reinitpswd_mailtemplate.ftxt");
    $mtitle = "Edition de brouillon";
    $editdraftform = $tb->AdminForm("editrecoverydraftform", true);
    $editdraftform->addTextField("mail_from", "Adresse d'envoi", (is_string($mails_config["SMTP"]["adress"]) ? $mails_config["SMTP"]["adress"] : ""), true, true)
    ->addTextField("mail_subject", "Objet du mail", "Récupération de mot de passe", false, true)
    ->addtextAreaField("mail_content", "Contenu du message", $pswdrecoverydraft_content, 10, true, false, "Un lien sera ajouté en fin de mail permettant à vos utilisateurs de ne plus recevoir de courriels comme l'impose la réglementation européenne.");
    
    $modal = $tb->AdminModal($mtitle, "mail_recoverypswd", $editdraftform, "", "modal-lg");
    $modal->addAPIButton($tb->AdminAPIButton("Enregistrer", "btn btn-warning", LINK . "api/", "mails", "set", "editRecoveryPswd", $editdraftform , "", true ));
    $adminBuilder->addModal($modal);
    //$modal->addAPIButton($tb->AdminAPIButton("Supprimer", "btn-danger", LINK . "api/", "admin", "set", "delContact", "id=" . (string)$c['id'], "", "true", "true"));
    $list->addField($tb->UIString("<strong>Objet : </strong>Récupération de mot de passe"), 
    $tb->UIString("<strong>Status : <span class=\"text-warning\">brouillon</span></strong>"), $modal);
    $pswdrecoverydraft_item = true;
}

$nothingtodisplay = $tb->UIString("<em>Aucun mail ou brouillon n'a été trouvé sur le serveur.</em>");
$panellist = $tb->AdminPanel("Mails envoyés et brouillons enregistrés", "fa-list", ((empty($mails) OR is_bool($mails)) AND !isset($pswdrecoverydraft_item)) ? $nothingtodisplay : $list, "lg-12");
$adminBuilder->addPanel($panellist);

echo $adminBuilder->render();