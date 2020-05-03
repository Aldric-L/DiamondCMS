<?php
/**
 * addVote - Fonction pour enregistrer la date du vote
 * @author Aldric.L
 * @copyright Copyright 2016-2017 Aldric L.
 * @access public
 */
function addVote($db, $pseudo){
  //on récupère la date du jour
  $datetime = date("Y-m-d H:i:s");
  //On l'insert dans la bdd
<<<<<<< HEAD
  $db->exec('UPDATE d_membre SET date_last_vote ="' . $datetime . '" WHERE pseudo = "' . $pseudo . '"');
  //Comme la requete update se veut dissidente, je commence par faire un select pour récupéré le nombre de votes
  $select = $db->query('SELECT votes, money FROM d_membre WHERE pseudo="' . $pseudo . '"');
  $rep = $select->fetch();
  //Ensuite je modifie le nombre de vote avec la valeur de select à laquelle j'ajoute 1
  $db->exec('UPDATE d_membre SET votes =' . ($rep['votes']+1) .', money = '. ($rep['money']+1) . ' WHERE pseudo ="'. $pseudo . '"');
=======
  $db->exec('UPDATE d_membre SET date_last_vote ="' . $datetime . '", votes = votes + 1 WHERE pseudo = "' . $pseudo . '"');
>>>>>>> f73348d50b56501cae02d84fa1249082fe8b0232
}

/**
 * hasVote - Fonction pour savoir si le membre a déja voté aujourd'hui
 * @author Aldric.L
 * @copyright Copyright 2016-2017 Aldric L.
 * @return boolean
 * @access public
 */
function hasVote($db, $pseudo){
  //on récupère la date de la bdd
  $select = $db->query('SELECT date_last_vote FROM d_membre WHERE pseudo="' . $pseudo . '"');
  $rep = $select->fetch();

  if(!empty($rep['date_last_vote'])){
<<<<<<< HEAD
    /*if (date('Y', strtotime($rep['date_last_vote'])) != date('Y')){
      //$datetime = date("Y-m-d H:i:s", strtotime("-1 year"));
    }else {
      //$datetime = strtotime(date("Y-m-d H:i:s"));
    }*/
    $datetime = strtotime(date("Y-m-d H:i:s"));
    $date2 = strtotime($rep['date_last_vote']);
    /*if ($datetime-$date2 <= 0){
      //return false;
    }
    /*echo $datetime  . "<br />";
    echo $date2. "<br />";*/
    $diff = abs($datetime - $date2);
    /*echo $diff. "<br />";
    echo $diff/86400 . "<br />";
    exit();*/
    //$result = dateDiff($datetime, $date2);
    //if ($result['hour'] <= 24){
    if ($diff/86400 <= 1){
=======
    $datetime = strtotime(date("Y-m-d H:i:s"));
    $date2 = strtotime($rep['date_last_vote']);
    $result = dateDiff($datetime, $date2);
    if ($result['hour'] <= 24){
>>>>>>> f73348d50b56501cae02d84fa1249082fe8b0232
      return true;
    }else {
      return false;
    }
  }else {
    return false;
  }
}

/**
<<<<<<< HEAD
 * dateDiff - Fonction pour avoir la difference entre 2 dates
=======
 * dateDiff - Fonction pour avoir la difference entre 2 date
>>>>>>> f73348d50b56501cae02d84fa1249082fe8b0232
 * @author Aldric.L, finalclap.com
 * @return array
 * @access private
 */
function dateDiff($date1, $date2){
    $diff = abs($date1 - $date2); // abs pour avoir la valeur absolute, ainsi éviter d'avoir une différence négative
    $retour = array();

    $tmp = $diff;
    $retour['second'] = $tmp % 60;

    $tmp = floor( ($tmp - $retour['second']) /60 );
    $retour['minute'] = $tmp % 60;

    $tmp = floor( ($tmp - $retour['minute'])/60 );
    $retour['hour'] = $tmp % 24;

    $tmp = floor( ($tmp - $retour['hour'])  /24 );
    $retour['day'] = $tmp;

    return $retour;
}

/**
 * bestVotes - Fonction pour récupéré les meilleurs voteurs
 * @author Aldric.L
 * @return array
 * @copyright Copyright 2016-2017 Aldric L.
 */
function bestVotes($db){
  //on récupère les 3 meilleurs voteurs
<<<<<<< HEAD
  $select = $db->query('SELECT * FROM d_membre WHERE votes >= 1 ORDER BY votes DESC LIMIT 3');
=======
  $select = $db->query('SELECT * FROM d_membre ORDER BY votes DESC LIMIT 3');
>>>>>>> f73348d50b56501cae02d84fa1249082fe8b0232
  $rep = $select->fetchAll();

  return $rep;
}
