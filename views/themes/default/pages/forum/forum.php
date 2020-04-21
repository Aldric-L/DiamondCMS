<div id="fh5co-page-title">
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
