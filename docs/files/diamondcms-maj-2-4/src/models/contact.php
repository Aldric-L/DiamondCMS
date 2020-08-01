<?php 

function addContact($db, $name, $message, $email){
    $req = $db->prepare('INSERT INTO d_contact (name, email, text, date) VALUES(:name, :email, :text, :date)');
    $s = $req->execute(array(
      'name' => $name,
      'email' => $email,
      'text' => $message,
      'date' => date('Y-m-d H:i:s')
    ));
}