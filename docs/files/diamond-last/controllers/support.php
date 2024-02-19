<?php
if ($Serveur_Config['en_support'] && isset($_SESSION['pseudo']) && !empty($_SESSION['pseudo'])){
  $alltickets = $tickets = simplifySQL\select($controleur_def->bddConnexion(), false, "d_support_tickets", array("id, status, contenu_ticket, titre_ticket, pseudo", array("date_ticket", "%d/%m/%Y", "date_t")),
  ($_SESSION['user']->getLevel() >= 2) ? false : array(array("pseudo", "=", $_SESSION['user']->getId())), 
  "id", true);
  $ntickets = sizeof($alltickets);
  $min = (isset($param[1]) && !empty($param[1]) && is_numeric($param[1])) ? intval($param[1])*10 : 0;
  $tickets = ($ntickets > 10) ? array_slice($alltickets, $min, 10, true) : $alltickets;

  foreach ($tickets as $key => $ticket) {
    $tickets[$key]['rep'] = simplifySQL\select($controleur_def->bddConnexion(), false, "d_support_rep", array("id, contenu_reponse, role, pseudo, id_ticket", array("date_reponse", "%d/%m/%Y à %h:%i", "date_rep")),
    array(array("id_ticket", "=", $tickets[$key]['id'])), 
    "id", false);
    //$tickets[$key]['rep'] = getTicketsReponses($controleur_def->bddConnexion(), $tickets[$key]['id']);
    foreach($tickets[$key]['rep'] as $k => $rep){
      $membre = simplifySQL\select($controleur_def->bddconnexion(), true, "d_membre", 'pseudo, profile_img', array(array("id", "=", $tickets[$key]['rep'][$k]['pseudo'])));
      $pseudo = $membre['pseudo'];
      if (empty($pseudo)){
        $tickets[$key]['rep'][$k]['pseudo'] = "Utilisateur inconnu";
        $tickets[$key]['rep'][$k]['profile_img'] = "no_profile.png";
      }else {
        $tickets[$key]['rep'][$k]['pseudo'] = $pseudo;
        $tickets[$key]['rep'][$k]['profile_img'] = $membre['profile_img'];
      }
    }

    $membre = simplifySQL\select($controleur_def->bddconnexion(), true, "d_membre", 'pseudo, profile_img', array(array("id", "=", $tickets[$key]['pseudo'])));
    $pseudo = $membre['pseudo'];
    if (empty($pseudo)){
      $tickets[$key]['pseudo'] = "Utilisateur inconnu";
      $tickets[$key]['profile_img'] = "no_profile.png";
    }else {
      $tickets[$key]['pseudo'] = $pseudo;
      $tickets[$key]['profile_img'] = $membre['profile_img'];
    }
    
    switch ($tickets[$key]['status']) {
      case '2':
        $tickets[$key]['status_l'] = "Fermé";
        break;

      case '1':
        $tickets[$key]['status_l'] = "Ouvert";
        break;

      default:
        $tickets[$key]['status_l'] = "Ouvert, en attente d'une réponse";
        break;
    }
  }
  $controleur_def->loadView('pages/support', 'support', 'Support');
}else {
  $controleur_def->nonifyPage("Impossible de poursuivre", "Support inaccessible ou désactivé : Êtes-vous connecté à votre compte utilisateur ?");
}
