<div id="explicforum">
  <h2>Le serveur <?= $Serveur_Config['Serveur_name']; ?> met à votre disposition un forum.</h2>
  <p class="explicp">Ce forum vous permettra, de "report" des bugs trouvés sur le serveur, demander de l'aide à d'autre joueurs plus expérimentés, de contacter le staff, et bien d'autre.
    n'hesitez pas à nous rejoindre, et à partager votre expérience avec d'autre joueurs</p>
  <p id="red" class="text-danger"><strong><i class="fa fa-exclamation-triangle" aria-hidden="true"></i>  Attention, tout comportement inaproprié pourras entrainer des sonctions graves.  <i class="fa fa-exclamation-triangle" aria-hidden="true"></i></strong></p>
</div>
<div id="news">
  <h2>Derniers sujets posés :</h2>
  <br />
  <?php
  global $posts;
    echo '<table cellspacing="5">';
    foreach ($posts as $post) {
      //echo '<a href="forum/com/'. $post['id'] .'">';
      echo "<tr onclick=\"document.location='forum/com/" . $post['id'] . "'><td class=\"bodure\">";
      echo '<div id="user">' . $post['user'];
      echo '<p>Le ' . $post['date_post'] .'</p></div></td>';
      echo '<td class="contentpost"><p>' . $post['titre_post'] . ' ' . $post['last_user'] . $post['date_last_post'] .'</p>';
      echo '<p. Le ' . $post['date_post'] .'.</p>';
      echo $post['content_post'] .'</p></td></tr>';
      echo '';
    }
    echo '</table>';
   ?>
</div>
