<<<<<<< HEAD
<div id="fh5co-page-title" style="background-image: url(<?php echo $Serveur_Config['protocol']; ?>://<?= $_SERVER['HTTP_HOST']; ?><?=WEBROOT; ?>views/uploads/img/<?php echo $Serveur_Config['bg']; ?>)">
  <div class="overlay"></div>
  <div class="text">
    <h1>Forum</h1>
  </div>
</div>
<div id="explicforum">
  <h2>Le serveur <?php echo $Serveur_Config['Serveur_name']; ?> met à votre disposition un forum.</h2>
  <p class="explicp">Ce forum vous permettra, de "report" des bugs trouvés sur le serveur, demander de l'aide à d'autres joueurs plus expérimentés, de contacter le staff, et bien d'autre.
    N'hesitez pas à nous rejoindre, et à partager votre expérience avec d'autres joueurs</p>
  <p id="red" class="text-danger"><strong><i class="fa fa-exclamation-triangle" aria-hidden="true"></i>  Attention, tout comportement inaproprié pourra entrainer des sonctions.  <i class="fa fa-exclamation-triangle" aria-hidden="true"></i></strong></p>
</div>
<div class="content-container-forum">
        <?php
          global $cats;
          if (!empty($cats)){
            foreach ($cats as $cat) {
              echo '<div id="f_cat"><h3>' . $cat['titre'] . "</h3></div>";
              if (!empty($cat['sous_cat'])){
                //var_dump($cat['sous_cat']);
                foreach ($cat['sous_cat'] as $scat['sous_cat']){
                  echo '<a href="' . $Serveur_Config['protocol'] . '://' . $_SERVER['HTTP_HOST'] . WEBROOT . 'forum/' . str_replace(' ', '-', $scat['sous_cat']['titre']) . '/" class="forum-a"><p id="f_sous_cat" style="float: left;"><i class="fa fa-comments-o fa-2x" aria-hidden="true"></i> | ' . $scat['sous_cat']['titre'] . '</p><p id="f_sous_cat" style="padding-top: 16px;float: right;">Nombre de sujets : '. $scat['sous_cat']['nb_sujets'] . '</p><hr style="width: 94%;"></a>';
                }
              }else {
                  echo '<p id="f_sous_cat">Aucune sous-catégorie n\'a été trouvée !</p>';
              }
            }
          }else {
            echo '<p class="text-center">Aucune catégorie n\'a été trouvée !</p>';
          }
         ?>
  <br />
</div>
=======
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
>>>>>>> f73348d50b56501cae02d84fa1249082fe8b0232
