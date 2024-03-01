<?php global $sous_cat, $cat, $posts; //var_dump($posts); ?>
<div id="fh5co-page-title" style="background-image: url(<?php echo Manager::makeGetImageLink($Serveur_Config['bg']); ?>)">
  <div class="overlay"></div>
  <div class="text">
    <h1>Forum -> <a href="<?php echo LINK . 'forum/' ?>"><?php echo $cat['titre']; ?></a> -> <?php echo $sous_cat['titre']; ?></h1>
  </div>
</div>
<div class="content-container-forum">
  <div id="f_cat"><h3 class="title"><?php echo $cat['titre']; ?> -> <?php echo $sous_cat['titre']; ?></h3></div>
  <?php if (empty($posts)){ ?>
    <br><p>Aucun sujet a été trouvé dans cette catégorie ! Créez-en un !</p>
  <?php }else { ?>
  <table class="table table-forum">
    <?php
    
    foreach ($posts as $key => $post) {
      echo '<tr><a class="forum-a" href="' . LINK . 'forum/com/'. $post['id'] .'">';
      if ($key == 0)
        echo '<td style="border-top: none;">'; 
      else 
        echo '<td>';
      echo '<img width=54 height=54 src="' . LINK .'getprofileimg/' . $post['user'] . '/64"></a></td>';
      if ($key == 0)
        echo '<td style="border-top: none;">'; 
      else 
        echo '<td>';
      echo '<a class="forum-a" href="' . LINK . 'forum/com/'. $post['id'] .'">';
      if (boolval($post['resolu']))
        echo '<span class="bold"><i class="fa fa-check" aria-hidden="true"></i></span> ';
      echo '<span style="font-weight: bold;">'. $post['titre_post'] . '</span><br />Par <strong>' . $controleur_def->echoRoleName($controleur_def->bddConnexion(), $post['user']) . $post['user'] . '</strong> le ' . $post['date_p'];
      echo '</td>'; 
      if ($key == 0)
        echo '<td class="text-right" style="border-top: none;"><small>'; 
      else 
        echo '<td class="text-right"><small>';
      if (intval($post['nb_rep']) == 0)
        echo 'Aucune réponse</small></td>';
      else if (intval($post['nb_rep']) == 1)
        echo $post['nb_rep'] . ' réponse</small></td>';
      else
        echo $post['nb_rep'] . ' réponses</small></td>';
      echo '</a></tr>';
    }  
  }
    ?>
  </table>
<?php global $pages, $cur_page; if ($pages != 1 && $pages > 0 && $pages != 0){
  echo '<h4 class="text-right">Pages : ';
    for ($i = 1; $i <= $pages; $i++){
      if ($i == $cur_page){
        echo '<a href="#"><button class="btn btn-custom disabled">' . $i .'</button></a>   ';
      }else {
        echo '<a href="'. LINK . 'forum/' . str_replace(' ', '_', $sous_cat['titre']) . '/' . ($i-1) . '"><button class="btn btn-custom">' . $i .'</button></a>  ';
      }
    }
    echo '</h4></div>';
 } ?>
 </div>
 <?php 
if (isset($_SESSION['pseudo']) && !empty($_SESSION['pseudo'])){?>
  <br /><br />
  <div class="content-container-forum">
    <div class="col-lg-1"></div>
    <div class="col-lg-11">
      <form method="post" id="newpost" action="">
        <input type="hidden" name="scat" value="<?php echo $sous_cat['id']; ?>">
        <label for="form-control col-sm-2">Titre du sujet :</label>
        <input class="form-control" type="text" name="title">
        <br />
        <label for="form-control col-sm-2">Contenu du nouveau sujet :</label>
        <textarea class="form-control" cols="25" rows="6" name="content"></textarea><br />
      </form>
      <button type="submit" class="btn pull-right btn-custom ajax-simpleSend"
      data-module="forum/" data-verbe="get" data-func="createPost" data-needAll="true" data-tosend="#newpost" data-useform="true" data-reload="true">Valider</button>
    </div>
  </div>
  <br /></br />
<?php }else {?>
<p class="text-center"><em>Une question ? Pour créer un sujet <a class="title connexion"> <i class="fa fa-key" aria-hidden="true"></i> Connectez-vous !</a></em><p>
<br /><br />
<?php } ?>
