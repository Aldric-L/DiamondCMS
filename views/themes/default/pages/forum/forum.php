<div id="explicforum">
  <h2>Le serveur <?= $Serveur_Config['Serveur_name']; ?> met à votre disposition un forum.</h2>
  <p class="explicp">Ce forum vous permettra, de "report" des bugs trouvés sur le serveur, demander de l'aide à d'autre joueurs plus expérimentés, de contacter le staff, et bien d'autre.
    N'hesitez pas à nous rejoindre, et à partager votre expérience avec d'autre joueurs</p>
  <p id="red" class="text-danger"><strong><i class="fa fa-exclamation-triangle" aria-hidden="true"></i>  Attention, tout comportement inaproprié pourras entrainer des sonctions graves.  <i class="fa fa-exclamation-triangle" aria-hidden="true"></i></strong></p>
</div>
<div id="posts">
  <h2>Derniers sujets posés :</h2>
  <br />
  <div class="container">
    <div class="row">
      <?php
        global $posts;
        foreach ($posts as $post) {
          //echo '<a href="forum/com/'. $post['id'] .'">';
          echo '<a href="forum/com/'. $post['id'] .'"><div class="col-xs-2 col-sm-2 col-lg-2" id="bordure"><p>' . $post['user'] . '</p>';
          echo '<p>Le ' . $post['date_post'] .'</p></div>';
          echo '<div class="col-xs-4 col-sm-10 col-lg-10"><p class="taillepetit">' . $post['titre_post'] . ' ' . $post['last_user'] . $post['date_last_post'] .'</p>';
          echo '<p class="taillepetit">. Le ' . $post['date_post'] .'.</p>';
          echo '<p class="taillepetit">' . $post['content_post'] .'</p></div></a><br />';
        }
       ?>
    </div>
  </div>
</div>
<br /><br /><br />
