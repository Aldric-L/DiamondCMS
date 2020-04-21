<?php
if ($Serveur_Config['en_support'] && isset($_SESSION['pseudo']) && !empty($_SESSION['pseudo'])){
  $controleur_def->loadModel('support');
  if (isset($param[1]) && isset($param[2]) && isset($param[3]) && $param[1] == "a"){
    if ($param[2] == "d_t" && intval($param[3])){
      if (isset($_SESSION['user']) && $_SESSION['user']->getLevel() >= 2){
        $close = CloseTicket($controleur_def->bddConnexion(), $param[3]);
      }else {
        $close = CloseTicket($controleur_def->bddConnexion(), $param[3], $_SESSION['user']->getId());
      }
      if ($close){
        exit("Success");
      }
      exit("Error (1)");
    }
    if ($param[2] == "d_r" && intval($param[3])){
      if (isset($_SESSION['user']) && $_SESSION['user']->getLevel() >=2){
        $del = delReponse($controleur_def->bddConnexion(), $param[3]);
      }else {
        $del = delReponse($controleur_def->bddConnexion(), $param[3], $_SESSION['user']->getId());
      }
      if ($del){
        exit("Success");
      }
      exit("Error (2)");
    }
    if ($param[2] == "a_r" && intval($param[3]) && !empty($_POST['content'])){
      $content = $_POST['content'];
      $new = addResponse($controleur_def->bddConnexion(), $content, $_SESSION['user']->getId(), intval($param[3]), $_SESSION['user']->getRole());
      $ticketfornotif = getTicketById($controleur_def->bddConnexion(), intval($param[3]));
      if ($ticketfornotif['pseudo'] != $_SESSION['pseudo']){
        $controleur_def->notify('Une nouvelle réponse sur votre ticket.', $ticketfornotif['pseudo'], 2, "Activité sur votre ticket", $Serveur_Config['protocol'] . "://" . $_SERVER['HTTP_HOST'] . WEBROOT . "support/");    
      }else if ($_SESSION['user']->getLevel() < 2) {
        $controleur_def->notify('Une nouvelle réponse sur le ticket de '. $_SESSION['pseudo'], "admin", 2, "Activité sur un Ticket", $Serveur_Config['protocol'] . "://" . $_SERVER['HTTP_HOST'] . WEBROOT . "support/");
      }
      exit("Success");
    }else {
      exit('Error (3)');
    }
    if ($param[2] == "a_t" && !empty($_POST['content']) && !empty($_POST['title'])){
      $content = $_POST['content'];
      $new = createTicket($controleur_def->bddConnexion(), $_SESSION['user']->getId(), $_POST['title'], $content, $_SESSION['user']->getRole());
      $controleur_def->notify('Un ticket a été créé par l\'utilisateur '. $_SESSION['pseudo'], "admin", 2, "Nouveau Ticket", $Serveur_Config['protocol'] . "://" . $_SERVER['HTTP_HOST'] . WEBROOT . "support/");
      exit("Success");
    }else {
      exit('Error (4)');
    }
  }
  if ($_SESSION['user']->getLevel() >= 2){
    if (isset($param[1]) && !empty($param[1]) && intval($param[1])){
      $min = $param[1]*10;
      $tickets = getTickets($controleur_def->bddConnexion(), $min, 10);
    }else {
      $tickets = getTickets($controleur_def->bddConnexion(), 0, 10);
    }
    $ntickets = getNumberTickets($controleur_def->bddConnexion());
  }else {
    if (isset($param[1]) && !empty($param[1]) && intval($param[1])){
      $min = $param[1]*10;
      $tickets = getTicketsByName($controleur_def->bddConnexion(), $_SESSION['user']->getId(), $min, 10);
    }else {
      $tickets = getTicketsByName($controleur_def->bddConnexion(), $_SESSION['user']->getId(), 0, 10);
    }
    $ntickets = getNumberTicketsByName($controleur_def->bddConnexion(), $_SESSION['user']->getId());
  }
   
  foreach ($tickets as $key => $ticket) {
    $tickets[$key]['rep'] = getTicketsReponses($controleur_def->bddConnexion(), $tickets[$key]['id']);
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
  $controleur_def->loadJS('support');
  $controleur_def->loadView('pages/support', 'support', 'Support');
}else {
  header('Location: '. $Serveur_Config['protocol'] . '://' .$_SERVER['HTTP_HOST'] . WEBROOT);
}
