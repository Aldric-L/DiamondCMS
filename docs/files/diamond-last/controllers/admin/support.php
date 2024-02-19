<?php 

$tb = new PageBuilders\ThemeBuilder($Serveur_Config['theme']);
$adminBuilder = $tb->AdminBuilder("Support", 
"DiamondCMS est livré avec un support. Toutefois, il est possible de le désactiver et de le paramétrer. Vous retrouverez aussi ici toutes les demandes de vos utilisateurs.");

$tickets = simplifySQL\select($controleur_def->bddConnexion(), false, "d_support_tickets", 
                              array("id, status, contenu_ticket, titre_ticket, pseudo", array("date_ticket", "%d/%m/%Y", "date_t")), false, "id", true);

//
$editform = $tb->AdminForm("editpage", false);
$ifsupportenabled = new PageBuilders\AvailableIf($editform, "en_support", PageBuilders\AvailableIf::EQUAL, true);


$editform->addCheckField("en_support", "Activer le support", $Serveur_Config['en_support'])
         ->addCustom($tb->UIString("<hr><em>Pour modifier les réglages qui suivent, vous devez avoir activé le support.</em>"))
         ->addselectField("affichage_type", "Mode d'affichage", array(
            array("val" => "All", "disp" => "Tous les tickets"),
            array("val" => "Unanswered", "disp" => "Tickets ouverts sans réponse"),
            array("val" => "open", "disp" => "Tickets ouverts"),
         ), false, $ifsupportenabled);
$editpagepanel = $tb->AdminPanel("Modifier la page", "fa-pencil", $editform, "col-lg-4");
$adminBuilder->addPanel($editpagepanel);

$availableIf = array(
    "Fermé" => new PageBuilders\AvailableIf($editform, "affichage_type", PageBuilders\AvailableIf::EQUAL, "All"),
    "Ouvert" => new PageBuilders\AvailableIf($editform, "affichage_type", PageBuilders\AvailableIf::NOT_EQUAL, "Unanswered"),
    "Ouvert, en attente d'une réponse" => true,
);

if (is_array($tickets) && !empty($tickets)){
    $list = $tb->AdminList();
    foreach ($tickets as &$t){
        $t['user'] = $t['pseudo'];
        $t['pseudo'] = User::getOnePseudoById($controleur_def->bddConnexion(), intval($t['user']));

        switch ($t['status']) {
            case '2':
              $t['status_l'] = "Fermé";
              $t['status_html'] = 'Fermé';
              break;
      
            case '1':
              $t['status_l'] = "Ouvert";
              $t['status_html'] = '<span class="text-warning">Ouvert</span>';
              break;
      
            default:
              $t['status_l'] = "Ouvert, en attente d'une réponse";
              $t['status_html'] = '<span class="text-danger">En attente</span>';
              break;
          }

        $answers = simplifySQL\select($controleur_def->bddConnexion(), false, "d_support_rep", array("id, contenu_reponse, id_ticket, pseudo, role", array("date_reponse", "%d/%m/%Y", "date_r")), array(array("id_ticket", "=", $t['id'])), "id", false);
        $modalcontent = $tb->UIArray();
        $modalcontent->push($tb->UIString("<h3>" . $t['titre_ticket'] . " <small>par ". $t['pseudo'] . "</small></h3><p>". htmlspecialchars_decode(DiamondShortcuts\utf8_decode($t['contenu_ticket'])) ."</p><hr>"));

        foreach ($answers as $key => &$a){
            $a['user'] = $a['pseudo'];
            $a['pseudo'] = User::getOnePseudoById($controleur_def->bddConnexion(), intval($a['user']));
            $a['role_name'] = User::echoRoleName($controleur_def->bddConnexion(), intval($a['user']));
            \ob_start(); ?>
            <h5><strong>Réponse 
                <?php echo ($a['user'] == $t['user']) ? "utilisateur" : "support"; ?> :
                </strong>
                <small><a href="<?php echo LINK; ?>compte/<?php echo $a['user']; ?>"><?php echo $a['role_name'] . $a['pseudo']; ?></a> le  <?php echo $a['date_r']; ?>      
                <?php echo $tb->AdminAPIButton($tb::FA("fa-trash"), "", LINK . "api/", "support", "set", "deleteAnswer", "id=" . (string)$t['id'], "", true)->customRender("a", 'text-danger'); ?>
                </small>   
            </h5>
            <?php 
            $modalcontent->push($tb->UIString(\ob_get_clean()));
            $modalcontent->push($tb->UIString(htmlspecialchars_decode(DiamondShortcuts\utf8_decode($a['contenu_reponse']))));
            $modalcontent->push($tb->UIString("<hr>"));
        }

        $answer_form = $tb->AdminForm("answerform_" . $t['id'], true);
        $answer_form
        ->addCustom($tb->UIString("<h5><strong>Formulaire de réponse</strong></h5>"))
        ->addHiddenField("id_ticket", $t['id'])
        ->addTextAreaField("contenu_reponse")
        ->addAPIButton($tb->AdminAPIButton("Envoyer", "btn-sm btn btn-custom", LINK . "api/", "support", "get", "createAnswer", $answer_form, "", true))
        ->setButtonsLine('class="text-right"');
        if ($t['status'] != 2)
            $modalcontent->push($answer_form);
        else
            $modalcontent->push($tb->UIString("<em>Le ticket est fermé. Aucune réponse ne peut être ajoutée.</em>"));

        $adminBuilder->addModal(
            $modal = $tb->AdminModal("<strong> Ticket N°". $t['id'] . "</strong> - " . $t['date_t'], "ticket_" . $t['id'], 
                                    $modalcontent, "", "modal-lg"));
        $modal->addAPIButton($tb->AdminAPIButton("Fermer le ticket", "btn-warning", LINK . "api/", "support", "set", "closeTicket", "id=" . (string)$t['id'], "", "true", "true", "false", ($t['status'] == "2")));
        $list->addField($tb->UIString("<strong>" . $t['status_html'] . " - " . $t['titre_ticket'] . "</strong> par ". $t['pseudo']." "), 
        $tb->UIString("<em>le " . $t['date_t'] ."</em>"), $modal, null, "item_". (string)$t['id'], (!is_bool($availableIf[$t['status_l']])) ? clone $availableIf[$t['status_l']] : $availableIf[$t['status_l']] );
    }
}else {
    $list = $tb->UIString("<p><em>Il n'y a aucun ticket à afficher.</em><p>");
}
//La page est constituée d'un panel principal qu'on écrit
$tickets_panel = $tb->AdminPanel("Tickets enregistrés", "fa-question", $list, "lg-8");
$adminBuilder->addPanel($tickets_panel);

echo $adminBuilder->render();