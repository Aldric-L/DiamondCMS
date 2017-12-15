<div id="explicforum">
  <h2>Le serveur <?= $Serveur_Config['Serveur_name']; ?> met à votre disposition un forum.</h2>
  <p class="explicp">Ce forum vous permettra, de "report" des bugs trouvés sur le serveur, demander de l'aide à d'autres joueurs plus expérimentés, de contacter le staff, et bien d'autre.
    N'hesitez pas à nous rejoindre, et à partager votre expérience avec d'autres joueurs</p>
  <p id="red" class="text-danger"><strong><i class="fa fa-exclamation-triangle" aria-hidden="true"></i>  Attention, tout comportement inaproprié pourra entrainer des sonctions graves.  <i class="fa fa-exclamation-triangle" aria-hidden="true"></i></strong></p>
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
          echo '<a href="http://'. $_SERVER['HTTP_HOST'] . WEBROOT . 'forum/com/'. $post['id'] .'"><div class="col-xs-2 col-sm-2 col-lg-2" id="bordure"><img width=64 height=64 src="http://api.diamondcms.fr/face.php?id='. $Serveur_Config['id_cms'] . '&u='. $post['user'] . '&s=64"></div>';
          echo '<div class="col-xs-4 col-sm-10 col-lg-10"><p class="taillepetit">' . $post['titre_post'] .' | Envoyé le ' .$post['date_post'] . ' par ' . $post['user'] . '</p>';
          //echo '<p class="taillepetit">. Le ' .  .'.</p>';
          echo '<p class="taillepetit">' . $post['content_post'] .'</p></div></a><br />';
        }
       ?>
    </div>
  </div>
</div>
<br />
<?php if (isset($_SESSION['pseudo']) && !empty($_SESSION['pseudo'])){?>
  <br /><br />
  <h2>Créer un nouveau sujet :</h2>
  <?php global $erreur_newpost; if (!empty($erreur_newpost)){?><h3 class="text-danger"><?= $erreur_newpost; ?></h3><?php } ?>
  <div class="center">
    <form method="post" action="">
    <label for="form-control col-sm-2">Titre du sujet :</label>
    <input class="form-control" type="text" name="titre_post">
    <br />
    <label for="form-control col-sm-2">Contenu du nouveau sujet :</label>
    <textarea class="form-control" cols="25" rows="6" name="content_post"></textarea><br />
    <button type="submit" class="btn pull-right btn-danger sub">Valider</button>
  </form></div>
  <br /></br />
<?php }else {?>
<p class="text-center"><em>Une question ? Pour créer un sujet <a href="<?php echo 'http://'. $_SERVER['HTTP_HOST'] . WEBROOT . 'connexion'; ?>"><i class="fa fa-key" aria-hidden="true"></i> Connectez-vous !</a></em><p>
<br /><br />
<?php } ?>
