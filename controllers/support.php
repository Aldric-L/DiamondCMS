<?php
if ($Serveur_Config['en_support'] && isset($_SESSION['pseudo']) && !empty($_SESSION['pseudo'])){
  $controleur_def->loadModel('support');
  if (isset($param[1]) && isset($param[2]) && isset($param[3]) && $param[1] == "a"){
    if ($param[2] == "d_t" && intval($param[3])){
      if (isset($_SESSION['admin']) && $_SESSION['admin']){
        $close = CloseTicket($controleur_def->bddConnexion(), $param[3]);
      }else {
        $close = CloseTicket($controleur_def->bddConnexion(), $param[3], $_SESSION['pseudo']);
      }
      if ($close){
        exit("Success");
      }
      exit("Error");
    }
    if ($param[2] == "d_r" && intval($param[3])){
      if (isset($_SESSION['admin']) && $_SESSION['admin']){
        $del = delReponse($controleur_def->bddConnexion(), $param[3]);
      }else {
        $del = delReponse($controleur_def->bddConnexion(), $param[3], $_SESSION['pseudo']);
      }
      if ($del){
        exit("Success");
      }
      exit("Error");
    }
    if ($param[2] == "a_r" && intval($param[3]) && !empty($_POST['content'])){
      $content = $_POST['content'];
      $new = addResponse($controleur_def->bddConnexion(), $content, $_SESSION['pseudo'], intval($param[3]), $_SESSION['user']->getRole());
      $ticketfornotif = getTicketById($controleur_def->bddConnexion(), intval($param[3]));
      if ($ticketfornotif['pseudo'] != $_SESSION['pseudo']){
        $controleur_def->notify('Une nouvelle réponse sur votre ticket.', $ticketfornotif['pseudo'], 2, "Activité sur votre ticket", $Serveur_Config['protocol'] . "://" . $_SERVER['HTTP_HOST'] . WEBROOT . "support/");    
      }else if ($_SESSION['user']->getLevel() < 2) {
        $controleur_def->notify('Une nouvelle réponse sur le ticket de '. $_SESSION['pseudo'], "admin", 2, "Activité sur un Ticket", $Serveur_Config['protocol'] . "://" . $_SERVER['HTTP_HOST'] . WEBROOT . "support/");
      }
      exit("Success");
    }
    if ($param[2] == "a_t" && !empty($_POST['content']) && !empty($_POST['title'])){
      $content = $_POST['content'];
      $new = createTicket($controleur_def->bddConnexion(), $_SESSION['pseudo'], $_POST['title'], $content, $_SESSION['user']->getRole());
      $controleur_def->notify('Un ticket a été créé par l\'utilisateur '. $_SESSION['pseudo'], "admin", 2, "Nouveau Ticket", $Serveur_Config['protocol'] . "://" . $_SERVER['HTTP_HOST'] . WEBROOT . "support/");
      exit("Success");
    }
  }
  if ($_SESSION['user']->getRoleLevel($controleur_def->bddConnexion()) >= 2){
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
      $tickets = getTicketsByName($controleur_def->bddConnexion(), $_SESSION['pseudo'], $min, 10);
    }else {
      $tickets = getTicketsByName($controleur_def->bddConnexion(), $_SESSION['pseudo'], 0, 10);
    }
    $ntickets = getNumberTicketsByName($controleur_def->bddConnexion(), $_SESSION['pseudo']);
  }
   
  foreach ($tickets as $key => $ticket) {
    $tickets[$key]['rep'] = getTicketsReponses($controleur_def->bddConnexion(), $tickets[$key]['id']);
    
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
  header('Location: '. $Serveur_Config['protocol'] . '://' .$_SERVER['HTTP_HOST'] . WEBROOT);
}
