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
  $db->exec('UPDATE d_membre SET date_last_vote ="' . $datetime . '", votes = votes + 1 WHERE pseudo = "' . $pseudo . '"');
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
    $datetime = strtotime(date("Y-m-d H:i:s"));
    $date2 = strtotime($rep['date_last_vote']);
    $result = dateDiff($datetime, $date2);
    if ($result['hour'] <= 24){
      return true;
    }else {
      return false;
    }
  }else {
    return false;
  }
}

/**
 * dateDiff - Fonction pour avoir la difference entre 2 date
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
