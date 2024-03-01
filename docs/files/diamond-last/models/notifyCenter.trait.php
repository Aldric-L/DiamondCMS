<?php 
/**
 * NotifyCenter - Ce trait n'en est pas vraiment un à proprement parler, il est en quelque sorte une extension de la class Manager, qui, pour améliorer la maintenabilité du code, a été déplacée ici.
 */
trait NotifyCenter {
    public function notify($content, $user, $type, $title, $link){
        //On ajoute une alerte à l'utilisateur concerné, la base de donnée insert automatiquement le timestamp
        $req = $this->bddConnexion()->prepare('INSERT INTO d_notify (content, user, title, link, type) VALUES(:content, :user, :title, :link, :type)');
        $s = $req->execute(array(
          'content' => $content,
          'user' => $user,
          'title' => $title,
          'link' => $link,
          'type' => $type
        ));
      }

      public function getnotify($user){
        $notifications = simplifySQL\select($this->bddConnexion(), false, "d_notify", "*", array(array("user", "=", $user), "AND", array("view", "!=", 1)));
        if (!empty($notifications)){
          foreach ($notifications as $not){
            $this->bddConnexion()->exec("UPDATE d_notify SET view = 1 WHERE id = " . $not['id']);
          }
        } 
        return $notifications;
      }

      public function getnotifyLog($user, $limit=10){
        $notifications = simplifySQL\select($this->bddConnexion(), false, "d_notify", "*", array(array("user", "=", $user)), "id", true, array(0, $limit));
        if (!empty($notifications)){
          foreach ($notifications as $not){
            $this->bddConnexion()->exec("UPDATE d_notify SET view = 1 WHERE id = " . $not['id']);
          }
        } 
        return $notifications;
      }

      public function getnotifyadmin(){
        $notifications = simplifySQL\select($this->bddConnexion(), false, "d_notify", "*", array(array("user", "=", "admin")), "id", true);
        if (!empty($notifications)){
          foreach ($notifications as $not){
            $this->bddConnexion()->exec("UPDATE d_notify SET view = 1 WHERE id = " . $not['id']);
          }
        } 
        return $notifications;
      }
}