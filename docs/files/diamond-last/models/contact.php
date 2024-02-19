<?php 

function addContact($db, $name, $message, $email){
    $req = $db->prepare('INSERT INTO d_contact (name, email, text, date, seen) VALUES(:name, :email, :text, :date, :seen)');
    $s = $req->execute(array(
      'name' => $name,
      'email' => $email,
      'text' => $message,
      'date' => date('Y-m-d H:i:s'),
      'seen' => false
    ));
}