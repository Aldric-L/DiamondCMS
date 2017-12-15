<?php

function addResponse($db, $content, $pseudo, $id_ticket, $role){
  $req = $db->prepare('INSERT INTO d_support_rep (contenu_reponse, id_ticket, pseudo, date_reponse, role) VALUES(:contenu_reponse, :id_ticket, :pseudo, :date_reponse, :role)');
  $s = $req->execute(array(
    'contenu_reponse' => $content,
    'id_ticket' => $id_ticket,
    'pseudo' => $pseudo,
    'date_reponse' => date('Y-m-d H:i:s'),
    'role' => intval($role)
  ));

   $req = $db->prepare('UPDATE d_support_tickets SET status = 1 WHERE id = :id');
    $del = $req->execute(array(
      'id' => intval($id_ticket)
    ));

  return $s;
}

function createTicket($db, $pseudo, $titre, $content, $role=0){
  $req = $db->prepare('INSERT INTO d_support_tickets (contenu_ticket, titre_ticket, pseudo, date_ticket, role) VALUES(:contenu_ticket, :titre_ticket, :pseudo, :date_ticket, :role)');
  $s = $req->execute(array(
    'contenu_ticket' => $content,
    'titre_ticket' => $titre,
    'pseudo' => $pseudo,
    'date_ticket' => date('Y-m-d H:i:s'),
    'role' => intval($role)
  ));
}

function getTickets($db, $min, $limite){
  $req2 = $db->prepare('SELECT id, status, contenu_ticket, titre_ticket, pseudo, DATE_FORMAT(date_ticket, \'%d/%m/%Y\ à %Hh:%imin\') AS date_ticket FROM d_support_tickets ORDER BY date_ticket DESC LIMIT :min, :limite ');

  //On passe les paramètres
  $req2->bindParam(':min', $min, PDO::PARAM_INT);
  $req2->bindParam(':limite', $limite, PDO::PARAM_INT);

  //On execute la requete
  $req2->execute();
  //On récupère tout
  $tickets = $req2->fetchAll();
  //On ferme la requete
  $req2->closeCursor();

  return $tickets;
}

function getTicketById($db, $id){
  $req2 = $db->prepare('SELECT id, status, contenu_ticket, titre_ticket, pseudo, DATE_FORMAT(date_ticket, \'%d/%m/%Y\ à %Hh:%imin\') AS date_ticket FROM d_support_tickets WHERE id = :id ORDER BY date_ticket DESC');

  //On passe les paramètres
  $req2->bindParam(':id', $id, PDO::PARAM_INT);

  //On execute la requete
  $req2->execute();
  //On récupère tout
  $ticket = $req2->fetch();
  //On ferme la requete
  $req2->closeCursor();

  return $ticket;
}

function getNumberTickets($db){
  $req2 = $db->prepare('SELECT id FROM d_support_tickets');

  //On execute la requete
  $req2->execute();
  //On récupère tout
  $tickets = $req2->fetchAll();
  //On ferme la requete
  $req2->closeCursor();

  return $tickets;
}

function getNumberTicketsByName($db, $pseudo){
  $req2 = $db->prepare('SELECT id FROM d_support_tickets WHERE pseudo = "' . $pseudo . '"');

  //On execute la requete
  $req2->execute();
  //On récupère tout
  $tickets = $req2->fetchAll();
  //On ferme la requete
  $req2->closeCursor();

  return $tickets;
}

function getTicketsByName($db, $pseudo, $min, $limite){
  $req2 = $db->prepare('SELECT id, status, contenu_ticket, titre_ticket, pseudo, DATE_FORMAT(date_ticket, \'%d/%m/%Y\ à %Hh:%imin\') AS date_ticket FROM d_support_tickets WHERE pseudo = :pseudo ORDER BY date_ticket DESC LIMIT :min, :limite ');

  //On passe les paramètres
  $req2->bindParam(':pseudo', $pseudo, PDO::PARAM_STR);
  $req2->bindParam(':min', $min, PDO::PARAM_INT);
  $req2->bindParam(':limite', $limite, PDO::PARAM_INT);

  //On execute la requete
  $req2->execute();
  //On récupère tout
  $tickets = $req2->fetchAll();
  //On ferme la requete
  $req2->closeCursor();

  return $tickets;
}

function getTicketsReponses($db, $id_ticket){
  $req2 = $db->prepare('SELECT id, contenu_reponse, role, pseudo, DATE_FORMAT(date_reponse, \'%d/%m/%Y\ à %Hh:%imin\') AS date_reponse, id_ticket FROM d_support_rep WHERE id_ticket = :id_ticket ORDER BY date_reponse');

  //On passe les paramètres
  $req2->bindParam(':id_ticket', $id_ticket, PDO::PARAM_INT);

  //On execute la requete
  $req2->execute();
  //On récupère tout
  $ticketsreponses = $req2->fetchAll();
  //On ferme la requete
  $req2->closeCursor();

  return $ticketsreponses;
}

function delReponse($db, $id_rep, $pseudo=FALSE){
    //Si un pseudo a été passé en paramètre
  if ($pseudo != false){
    //On créé une variable valide qui nous permet de passer aux étapes suivantes : une fois que l'on a verfié que
    //le pseudo est bien le propriétaire du ticket on la passe à true. Sinon elle reste à false et ainsi empeche la
    //continuer normallement.
    $valide = false;

    $reps = select($db, false, "d_support_rep", "id", array(array("pseudo", "=", $pseudo)));

    //Si aucun ticket n'a été trouver, on retourne valide (false).
    if (empty($reps)){
      return $valide;
    }
    //Si on trouve des tickets, on parcours le tableau
    foreach ($reps as $key => $rep) {
      //Pour trouver le ticket que l'on cherche avec l'id
      if ($reps[$key]['id'] == $id){
        //Si on en trouve un, on considère ainsi que le pseudo est bien le propriétaire et donc
        //on passe la variable valide à true pour lui permettre de continuer.
        $valide = true;
      }
    }
  }else {
    //Aucun pseudo passer, on considère donc que la vérifiquation a été faite en hammont.
    //On valide donc en passant la variable valide à true.
    $valide = true;
  }
  //Si tout est valide ($valide == true)
  if ($valide){
    //On update le status en fermer (2)
    $req = $db->prepare('DELETE FROM d_support_rep WHERE id = :id');
    $del = $req->execute(array(
      'id' => intval($id_rep)
    ));
    //On retourne le staatus de l'update : true si aucun problème, sinon la variable contiendra l'erreur.
    return $del;
  }else {
    //Si ce n'est pas valide, on retourne donc false.
    return $valide;
  }
}

function CloseTicket($db, $id, $pseudo=FALSE){
  //Si un pseudo a été passé en paramètre
  if ($pseudo != false){
    //On créé une variable valide qui nous permet de passer aux étapes suivantes : une fois que l'on a verfié que
    //le pseudo est bien le propriétaire du ticket on la passe à true. Sinon elle reste à false et ainsi empeche la
    //continuer normallement.
    $valide = false;

    $tickets = select($db, false, "d_support_tickets", "id", array(array("pseudo", "=", $pseudo)));

    //Si aucun ticket n'a été trouver, on retourne valide (false).
    if (empty($tickets)){
      return $valide;
    }
    //Si on trouve des tickets, on parcours le tableau
    foreach ($tickets as $key => $ticket) {
      //Pour trouver le ticket que l'on cherche avec l'id
      if ($tickets[$key]['id'] == $id){
        //Si on en trouve un, on considère ainsi que le pseudo est bien le propriétaire et donc
        //on passe la variable valide à true pour lui permettre de continuer.
        $valide = true;
      }
    }
  }else {
    //Aucun pseudo passer, on considère donc que la vérifiquation a été faite en hammont.
    //On valide donc en passant la variable valide à true.
    $valide = true;
  }
  //Si tout est valide ($valide == true)
  if ($valide){
    //On update le status en fermer (2)
    $req = $db->prepare('UPDATE d_support_tickets SET status = 2 WHERE id = :id');
    $del = $req->execute(array(
      'id' => intval($id)
    ));
    //On retourne le status de l'update : true si aucun problème, sinon la variable contiendra l'erreur.
    return $del;
  }else {
    //Si ce n'est pas valide, on retourne donc false.
    return $valide;
  }
}
