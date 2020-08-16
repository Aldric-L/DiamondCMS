<?php global $sous_cat, $cat, $posts; //var_dump($posts); ?>
<div id="fh5co-page-title" style="background-image: url(<?= LINK; ?>views/uploads/img/<?php echo $Serveur_Config['bg']; ?>)">
  <div class="overlay"></div>
  <div class="text">
    <h1>Forum -> <a href="<?php echo LINK . 'forum/' ?>"><?php echo $cat['titre']; ?></a> -> <?php echo $sous_cat['titre']; ?></h1>
  </div>
</div>
<div class="content-container-forum">
  <div id="f_cat"><h3 class="green"><?php echo $cat['titre']; ?> -> <?php echo $sous_cat['titre']; ?></h3></div>
  <?php if (empty($posts)){ ?>
    <br><p>Aucun sujet a été trouvé dans cette catégorie ! Créez-en un !</p>
  <?php } ?>
  <table class="table table-forum">
    <?php
    
    foreach ($posts as $post) {
      echo '<tr><a class="forum-a" href="' . LINK . 'forum/com/'. $post['id'] .'"><td><img width=54 height=54 src="' . LINK .'getprofileimg/' . $post['user'] . '/64"></a></td>';
      echo '<td><a class="forum-a" href="' . LINK . 'forum/com/'. $post['id'] .'"><span style="font-weight: bold;">'. $post['titre_post'] . '</span><br />Par <strong>' . $controleur_def->echoRoleName($controleur_def->bddConnexion(), $post['user']) . $post['user'] . '</strong> le ' . $post['date_post'];
      echo '</td>'; 
      echo '<td>' . $post['nb_rep'] . ' réponses</td>';
      echo '</a></tr>';
    }    
    ?>
  </table>
<?php global $pages, $cur_page; if ($pages != 1 && $pages > 0 && $pages != 0){
  echo '<h4 class="text-right">Pages : ';
    for ($i = 1; $i <= $pages; $i++){
      if ($i == $cur_page){
        echo '<a href="#"><button class="btn btn-success disabled">' . $i .'</button></a>   ';
      }else {
        echo '<a href="'. LINK . 'forum/' . str_replace(' ', '-', $sous_cat['titre']) . '/' . ($i-1) . '"><button class="btn btn-success">' . $i .'</button></a>  ';
      }
    }
    echo '</h4></div>';
 } ?>
 </div>
 <?php 
if (isset($_SESSION['pseudo']) && !empty($_SESSION['pseudo'])){?>
  <br /><br />
  <div class="center">
  <?php global $erreur_newpost; if (!empty($erreur_newpost)){?><h3 class="text-danger"><?= $erreur_newpost; ?></h3><?php } ?>
    <form method="post" action="">
    <input type="hidden" name="scat" value="<?php echo $sous_cat['id']; ?>">
    <label for="form-control col-sm-2">Titre du sujet :</label>
    <input class="form-control" type="text" name="titre_post">
    <br />
    <label for="form-control col-sm-2">Contenu du nouveau sujet :</label>
    <textarea class="form-control" cols="25" rows="6" name="content_post"></textarea><br />
    <button type="submit" class="btn pull-right btn-danger sub acc">Valider</button>
  </form></div>
  <br /></br />
<?php }else {?>
<p class="text-center"><em>Une question ? Pour créer un sujet <a class="green" href="<?php echo LINK . 'connexion'; ?>"> <i class="fa fa-key" aria-hidden="true"></i> Connectez-vous !</a></em><p>
<br /><br />
<?php } ?>
