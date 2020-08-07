<?php global $post, $posts, $scats, $coms, $sous_cat, $resolu, $cat, $Serveur_Config, $controleur_def; ?>

<style>
@media (min-width: 1400px){
  .content-container-forum {
    margin-left: 18%;
    margin-right: 18%;
    width: 64%;
    /*border: 1.8px dotted #197d62;
    /*border-radius: 20px;*/
    height: 100%;
    background-color: white;
    margin-top: 2%;
    margin-bottom: 2%;
  }
}
@media (max-width: 1200px){
  .content-container-forum {
    margin-left: 15%;
    margin-right: 15%;
    width: 70%;
    /*border: 1.8px dotted #197d62;
    /*border-radius: 20px;*/
    height: 100%;
    background-color: white;
    margin-top: 2%;
    margin-bottom: 2%;
  }
}
@media (max-width: 1000px){
  .content-container-forum {
    margin-left: 0%;
    margin-right: 0%;
    width: 100%;
    /*border: 1.8px dotted #197d62;
    /*border-radius: 20px;*/
    height: 100%;
    background-color: white;
    margin-top: 2%;
    margin-bottom: 2%;
  }
}

</style>
<div id="fh5co-page-title" style="background-image: url(<?= LINK; ?>views/uploads/img/<?php echo $Serveur_Config['bg']; ?>)">
  <div class="overlay"></div>
  <div class="text">
    <h1>Forum -> <a class="no" href="<?php echo LINK . 'forum/'; ?>"><?php echo $cat['titre']; ?></a> -> <a class="no" href="<?php echo LINK . 'forum/' . str_replace(' ', '-', $sous_cat[0]['titre']) . '/';?>"><?php echo $sous_cat[0]['titre']; ?> </a>-> <?php echo $post['titre_post']; ?></h1>
  </div>
</div>
<div class="content-container-forum">
  <?php if ($post['user'] != "Utilisateur inconnu"){ ?>
    <div id="f_cat"><h3><?php echo $post['titre_post'] . ' par <a style="text-decoration: underline;" class="no" href="' . LINK . 'compte/' .$post['user'] . '">' . $post['user'] .  '</a> le '. $post['date_post']; ?></h3></div>
  <?php }else { ?>
    <div id="f_cat"><h3><?php echo $post['titre_post'] . ' par ' . $controleur_def->echoRoleName($controleur_def->bddConnexion(), $post['user']) . $post['user'] .  ' le '. $post['date_post']; ?></h3></div>
  <?php } ?>
  <table class="table table-forum">
    <tr>
        <td class='border'><img width=100 height=100 src="<?= LINK; ?>getprofileimg/<?php echo $post['user']; ?>/100"></td>
      
      <td class="text-justify">
      <div 
          <?php
          if (isset($_SESSION['pseudo']) && $_SESSION['pseudo'] == $post['user']) {?>
          class="content-post-span"  data-id="<?php echo $post['id']; ?>" data-link="<?= LINK; ?>forum/com/edit_post/<?php echo $post['id']; ?>/"
          <?php } ?>
        >
      <?php echo $post['content_post']; ?></div><br />
      
      
      <?php
      if ($resolu) {?>
        <span class="bold" style="float: right;"><i class="fa fa-check" aria-hidden="true"></i> Résolu</span>
      <?php }else if (isset($_SESSION['pseudo']) && !$resolu && ($post['user'] == $_SESSION['pseudo']) || (isset($_SESSION['admin']) && $_SESSION['admin'])){?>
        <a id="sr_<?php echo $post['id']; ?>" href="" class="bold" style="float: right;"><i class="fa fa-check" aria-hidden="true"></i> Résolu ?</a>
        <script>
        $("#sr_<?php echo $post['id']; ?>").click(function(){
          var xhr = new XMLHttpRequest();
          xhr.open('GET', '<?php echo LINK . 'forum/solved/' . $post['id']; ?>');
          xhr.send(null);
        });
        </script>
      <?php } //var_dump($_SESSION);
      if (isset($_SESSION['admin']) && $_SESSION['admin']) {?>
        <br />
        <p class="text-right" style="margin: 0; padding: 0;">
        <select class="form-control" style="width: 30%; margin-left:100%; margin-bottom: 10px; margin-top: 10px; float: right; display: none" data-link="<?php echo LINK . 'forum/moove/' . $post['id']; ?>" id="cats_available">
          <?php foreach ($scats as $cat){ ?>
                <option value="<?= $cat['id']; ?>" <?php if ($cat['id'] == $post['id_scat']){ ?> selected <?php } ?>><?= $cat['titre'];?></option>
          <?php } ?>
        </select>
        <a class="text-info bold moove" data-id="<?= $post['id']; ?>" data-link="<?php echo LINK . 'forum/moove/' . $post['id']; ?>"><i class="fa fa-arrow-circle-o-right" aria-hidden="true"></i> Déplacer le sujet</a>
        
        </p>
        <span class="bold" style="float: right; color: red;"><a class="bold" style="color: red;" href="<?php echo LINK . 'forum/del/' . $post['id']; ?>"><i class="fa fa-trash-o" aria-hidden="true"></i> Supprimer le sujet </a></span>
      <?php } ?>
      </td>
    </tr>
  </table>
  <br />
  <?php $i =1; foreach ($coms as $key => $com) {?>
    <?php if ($com['user'] != "Utilisateur inconnu"){ ?>
      <div id="f_com" class="<?php echo $i; ?>"><h3>En réponse à "<?php echo $post['titre_post'] . '" par <a style="text-decoration: none;" class="no" href="' . LINK . 'compte/' . $com['user'] . '">'  . $com['role'] . '<span style="text-decoration: underline;">' . $com['user'] . '</span>' . '</a> le '. $com['date_com']; ?></h3></div>
    <?php } else { ?>
      <div id="f_com" class="<?php echo $i; ?>"><h3>En réponse à "<?php echo $post['titre_post'] . '" par ' . $com['role'] . $com['user'] . ' le '. $com['date_com']; ?></h3></div>
    <?php } ?>
    
    <table class="table table-forum-com <?php echo $i; ?>">
      <tr>
        <td class='border'><img width=90 height=90 src="<?= LINK; ?>getprofileimg/<?php echo $com['user']; ?>/90"></td>
        <td>
        
        <div 
          <?php
          if (isset($_SESSION['pseudo']) && $_SESSION['pseudo'] == $com['user']) {?>
          class="content-com-span"  data-id="<?php echo $com['id']; ?>" data-link="<?= LINK; ?>forum/com/edit/<?php echo $com['id']; ?>/"
          <?php } ?>
        ><span id="content-com-<?php echo $com['id']; ?>" class="text-justify"><?php echo $com['content_com']; ?></span></div>
        <?php
        if ((isset($_SESSION['admin']) && $_SESSION['admin']) || (isset($_SESSION['pseudo']) && $_SESSION['pseudo'] == $com['user'])) {?>
          <br /><p class="text-right" stye="text-align: right;"><a id="dl_<?php echo $com['id']; ?>" class="bold" style="color: red;" href=""><i class="fa fa-trash-o" aria-hidden="true"></i></a></span></p>
        <?php } ?><br />
        </td>
      </tr>
    </table>
    <br class="<?php echo $i; ?>" />
    <?php if ((isset($_SESSION['admin']) && $_SESSION['admin']) || (isset($_SESSION['pseudo']) && $_SESSION['pseudo'] == $com['user'])) {?>
    <script>
    $("#dl_<?php echo $com['id']; ?>").click(function(e){
      e.preventDefault();
      $.get( "<?php echo LINK . 'forum/del/com/' . $com['id']; ?>", function( data ) {
        location.reload();
      });
    });
    </script>
  <?php } $i++; } if ($i >= 11){ ?>
  <button class="hider btn pull-right btn-success acc sub">Voir les réponses cachées...</button><br /><br/>
  <?php } ?>
  <?php if (isset($_SESSION['pseudo']) && !empty($_SESSION['pseudo']) && !$resolu){ ?>
    <div class="alert alert-dismissable" style="background-color:#E5E5E5; padding-top: 8px; padding-bottom:8px; margin-left:8%;"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>Pour modifier vos messages précédents, cliquez sur leur contenu !</div>
    <form method="post" action=""style="padding-left: 8%;">
      <label for="form-control col-sm-2">Répondre à ce sujet :</label>
      <textarea class="form-control" cols="25" rows="6" name="comment"></textarea><br />
      <button type="submit" class="acc btn pull-right btn-danger sub">Valider</button>
    </form>
  <?php }else if ($resolu) {
    echo '<p class="text-right"><em>Vous ne pouvez plus répondre car le sujet est résolu.</em></p>';
  }else {
    echo '<p class="text-center"><em>Pour répondre <a class="no" href="'. LINK . 'connexion"><i class="fa fa-key" aria-hidden="true"></i> Connectez-vous !</a></em><p>';
  } ?>
</div>