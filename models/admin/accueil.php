<?php

function getNActionsForum($db){
    $req2 = $db->prepare('SELECT id FROM d_forum_com');
    //On execute la requete
    $req2->execute();
    //On récupère tout
    $nb_coms = $req2->fetchAll();
    //On ferme la requete
    $req2->closeCursor();

    return sizeof($nb_coms);
}

function getNumberErrorLog(){
    $descFic = fopen (ROOT . 'logs/errors.log', "r");
    $errors = array();
    while ($ligne = fgets ($descFic, filesize(ROOT . 'logs/errors.log')))
    {
    array_push($errors, substr($ligne, 0, 4));
    }
    fclose ($descFic);
    return sizeof($errors);
}

function getNumberTickets($db){
  $req2 = $db->prepare('SELECT id FROM d_support_tickets');

  //On execute la requete
  $req2->execute();
  //On récupère tout
  $tickets = $req2->fetchAll();
  //On ferme la requete
  $req2->closeCursor();

  return sizeof($tickets);
}

function analiserLog($controleur_def, $limit=0){
    $descFic = fopen (ROOT . 'logs/errors.log', "r");
    $errors = array();
    $i = 0;
    while ($ligne = fgets ($descFic, filesize(ROOT . 'logs/errors.log'))){
            if (substr($ligne, 3, 1) == " "){
                array_push($errors, array(substr($ligne, 0, 4), substr($ligne, 5, 18), $controleur_def->getContentError(substr($ligne, 0,3))));  
            }else {
                array_push($errors, array(substr($ligne, 0, 4), substr($ligne, 5, 18), $controleur_def->getContentError(substr($ligne, 0, 4))));
            }
        if ($limit != 0 && $i == $limit-1){
            fclose ($descFic);
            return $errors;
        }
        $i++;
    }
    fclose ($descFic);
    return $errors;
}