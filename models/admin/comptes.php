<?php 
function delRole($db, $id){
  return $db->exec("DELETE FROM d_roles WHERE id = " . $id);
}

function addRole($db, $name, $level){
    $req = $db->prepare('INSERT INTO d_roles (name, level) VALUES(:name, :level)');
    $req->execute(array(
      'name' => $name,
      'level' => $level
    ));
}

/**
 * modifiy - Fonction pour modifier les informations d'un utilisateur
 * @author Aldric.L
 * @copyright Copyright 2020
 */
 function modify($db, $id, $money, $role_id, $unban=null){
  if (isset($unban) && !empty($unban) && $unban == true){
      return $db->exec("UPDATE d_membre SET is_ban = 0, r_ban=null, money = " . $money . ", role = " . $role_id . " WHERE id = \"$id\"");   
  }else {
      return $db->exec("UPDATE d_membre SET money = " . $money . ", role = " . $role_id . " WHERE id =" . $id );   
  }
}