<?php global $post, $posts, $scats, $coms, $sous_cat, $resolu, $cat, $Serveur_Config, $controleur_def; 
?>

<div id="fh5co-page-title" style="background-image: url(<?php echo Manager::makeGetImageLink($Serveur_Config['bg']); ?>)">
  <div class="overlay"></div>
  <div class="text">
    <h1>Forum -> <a href="<?php echo LINK . 'forum/'; ?>"><?php echo $cat['titre']; ?></a> -> <a href="<?php echo LINK . 'forum/' . str_replace(' ', '_', $sous_cat[0]['titre']) . '/';?>"><?php echo $sous_cat[0]['titre']; ?> </a>-> <?php echo $post['titre_post']; ?></h1>
  </div>
</div>
<div class="container">
  <?php if ($post['user'] == "Utilisateur inconnu"){ ?>
    <div id="f_cat"><h3 class="title"><?php if ($resolu) {?><span class="bold" style="padding-right: 10px; float: left;"><i class="fa fa-check" aria-hidden="true"></i></span><?php } ?> 
    <span style="color: var(--main-text-color) ;"><?php echo $post['titre_post'] . '</span> <small>par <a style="text-decoration: underline;" class="title">' . $post['user'] .  '</a> le '. $post['date_p']; ?></small></h3></div>
  <?php }else { ?>
    <div id="f_cat"><h3 class="title"><?php if ($resolu) {?><span class="bold" style="padding-right: 10px; float: left;"><i class="fa fa-check" aria-hidden="true"></i></span><?php } ?> 
    <span style="color: var(--main-text-color) ;"><?php echo $post['titre_post'] . '</span> <small>par <a style="text-decoration: underline;" class="title" href="' . LINK . 'compte/' .$post['user'] . '">' . $controleur_def->echoRoleName($controleur_def->bddConnexion(), $post['user']) . $post['user'] .  '</a> le '. $post['date_p']; ?></small></h3></div>
  <?php } ?>
  <table class="table table-forum">
    <tr>
        <td style="border-top: none;" class='border'><img width=100 height=100 src="<?= LINK; ?>getprofileimg/<?php echo $post['user']; ?>/100"></td>
      
      <td style="border-top: none;" class="text-justify">
      <div 
          <?php
          if (isset($_SESSION['pseudo']) && $_SESSION['pseudo'] == $post['user'] || (isset($_SESSION['user']) && $_SESSION['user']->getLevel() >= 3)) {?>
          class="content-post-span"  data-apilink="<?= LINK; ?>api/" data-id="<?php echo $post['id']; ?>" data-link="<?= LINK; ?>forum/com/edit_post/<?php echo $post['id']; ?>/"
          <?php } ?>
        >
      <?php echo htmlspecialchars_decode(DiamondShortcuts\utf8_decode($post['content_post'])); ?>
      </div>
      <br>
      <?php if (isset($post['last_edit_date']) && array_key_exists("last_editer", $post)){ ?>
          <p style="margin: 0;" class="text-right"><small><em>Modifié le <?php echo $post['last_edit_date']; ?> <?php echo ($post['user_id'] != $post['last_editer']) ? "par " . User::echoRoleName($controleur_def->bddConnexion(), $post['last_editer']) . User::getPseudoById($controleur_def->bddConnexion(), $post['last_editer']) : ""; ?> </em></small></p>
        <?php } ?>     
      <?php 
      $sign = User::getOneForumSignature($controleur_def->bddConnexion(), $post['user_id']);
      if ($sign != false && $sign != ""){
        echo "<hr style='border-top: 1px solid darkgray;'>" . $sign ;
      } ?> 

      <?php if (!$resolu && (isset($_SESSION['pseudo']) && !$resolu && ($post['user'] == $_SESSION['pseudo']) || (isset($_SESSION['user']) && $_SESSION['user'] instanceof User && $_SESSION['user']->isAdmin()))){?>
        <a href="" class="ajax-simpleSend bold"  data-module="forum/" data-verbe="set" data-func="solved" data-tosend="id=<?php echo $post['id']; ?>" 
        data-useform="false" data-reload="true" style="float: right;"><i class="fa fa-check" aria-hidden="true"></i> Marquer comme Résolu</a><br>
      <?php } 
      if (isset($_SESSION['user']) && $_SESSION['user'] instanceof User && $_SESSION['user']->getLevel() >= 3) {?>
        <p class="text-right" style="margin: 0; padding: 0;">
        <select class="form-control" style="width: 30%; margin-left:100%; margin-bottom: 10px; margin-top: 10px; float: right; display: none" data-link="<?php echo LINK . 'forum/moove/' . $post['id']; ?>" id="cats_available">
          <?php foreach ($scats as $cat){ ?>
                <option value="<?= $cat['id']; ?>" <?php if ($cat['id'] == $post['id_scat']){ ?> selected <?php } ?>><?= $cat['titre'];?></option>
          <?php } ?>
        </select>
        <a class="text-info bold moove" data-id="<?= $post['id']; ?>" data-apilink="<?php echo LINK . 'api/';?>" data-link="<?php echo LINK . 'forum/moove/' . $post['id']; ?>"><i class="fa fa-arrow-circle-o-right" aria-hidden="true"></i> Déplacer le sujet</a>
        
        </p>
        <span class="bold" style="float: right; color: red;"><a class="ajax-simpleSend bold" style="color: red;" href="" data-module="forum/" data-verbe="set" data-func="deletePost" data-tosend="id=<?php echo $post['id']; ?>" 
        data-useform="false" data-reload="true"><i class="fa fa-trash-o" aria-hidden="true"></i> Supprimer le sujet </a></span>
      <?php } ?>
      </td>
    </tr>
  </table>
  <br />
  <?php $i =1; foreach ($coms as $key => $com) {?>
    <?php if ($com['user'] != "Utilisateur inconnu"){ ?>
      <div id="f_com_<?php echo $com['id']; ?>" class="f_com <?php echo $i; ?>"><h3 class="title"><span style="color: var(--main-text-color) ;">Réponse de </span><?php echo '<a style="text-decoration: none;" class="title" href="' . LINK . 'compte/' . $com['user'] . '"><span style="text-decoration: underline;">' . $com['role'] . $com['user'] . '</span>' . '</a><span style="color: var(--main-text-color) ;"> le '. $com['date_com']; ?></span>
      <?php if ((isset($_SESSION['user']) && $_SESSION['user']->getLevel() >= 3) || (isset($_SESSION['pseudo']) && $_SESSION['pseudo'] == $com['user'])) {?>
        <a class="bold ajax-simpleSend" data-module="forum/" data-verbe="set" data-func="deleteComment" data-tosend="id=<?php echo $com['id']; ?>" 
        data-useform="false" data-reload="true" href=""><span style="display: inline;float: right;color: red;"><i class="fa fa-trash-o" aria-hidden="true"></i></span></a>
      <?php } ?>
    </h3></div>
    <?php } else { ?>
      <div id="f_com_<?php echo $com['id']; ?>" class="f_com <?php echo $i; ?>"><h3 class="title"><span style="color: var(--main-text-color) ;">Réponse de </span><?php echo $com['role'] . $com['user'] . ' <span style="color: var(--main-text-color) ;">le '. $com['date_com'] . "</span>"; ?>
      <?php if ((isset($_SESSION['user']) && $_SESSION['user']->getLevel() >= 3) || (isset($_SESSION['pseudo']) && $_SESSION['pseudo'] == $com['user'])) {?>
        <a class="bold ajax-simpleSend" data-module="forum/" data-verbe="set" data-func="deleteComment" data-tosend="id=<?php echo $com['id']; ?>" 
        data-useform="false" data-reload="true" href=""><span style="display: inline;float: right;color: red;"><i class="fa fa-trash-o" aria-hidden="true"></i></span></a>
      <?php } ?>
    </h3></div>
    <?php } ?>
    
    <table class="table table-forum-com <?php echo $i; ?>">
      <tr>
        <td class='border'><img width=90 height=90 src="<?= LINK; ?>getprofileimg/<?php echo $com['user']; ?>/90"></td>
        <td>
        
        <div 
          <?php
          if (isset($_SESSION['pseudo']) && $_SESSION['pseudo'] == $com['user'] || (isset($_SESSION['user']) && $_SESSION['user']->getLevel() >= 3)) {?>
          class="content-com-span"  data-id="<?php echo $com['id']; ?>" data-apilink="<?= LINK; ?>api/" data-link="<?= LINK; ?>forum/com/edit/<?php echo $com['id']; ?>/"
          <?php } ?>
        ><span id="content-com-<?php echo $com['id']; ?>" class="text-justify">
        <?php echo htmlspecialchars_decode(DiamondShortcuts\utf8_decode($com['content_com'])); ?>
      </span>
      </div>
      <br>
      <?php if (isset($com['last_edit_date']) && array_key_exists("last_editer", $com)){ ?>
          <p class="text-right"><em>Modifié le <?php echo $com['last_edit_date']; ?> <?php echo ($com['user_id'] != $com['last_editer']) ? "par " . User::echoRoleName($controleur_def->bddConnexion(), $com['last_editer']) . User::getPseudoById($controleur_def->bddConnexion(), $com['last_editer']) : ""; ?> </em></p>
        <?php } ?>
      <?php 
      $sign = User::getOneForumSignature($controleur_def->bddConnexion(), $com['user_id']);
      if ($sign != false && $sign != ""){
        echo "<hr style='border-top: 1px solid darkgray;'>" . $sign ;
      } ?> 

      
        <br />
        </td>
      </tr>
    </table>
    <br class="<?php echo $i; ?>" />
  <?php $i++; } if ($i >= 11){ ?>
  <p class="text-center"><button style="width: 65%;" class="hider btn btn-custom sub">Voir les réponses cachées...</button></p><br /><br/>
  <?php } ?>
  <?php if (isset($_SESSION['pseudo']) && !empty($_SESSION['pseudo']) && !$resolu){ ?>
    <hr><br>
    <div class="alert alert-custom alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>Pour modifier vos messages précédents, cliquez sur leur contenu !</div>
    <form method="post" action="" id="newcomform" style="padding-left: 8%;">
      <input type="hidden" name="post" value="<?php echo $post['id']; ?>">
      <label for="form-control col-sm-2">Répondre à ce sujet :</label>
      <textarea class="form-control" cols="25" rows="6" name="content"></textarea><br />
      <button type="submit" class="btn pull-right btn-custom ajax-simpleSend"
      data-module="forum/" data-verbe="get" data-func="createComment" data-needAll="true" data-tosend="#newcomform" data-useform="true" data-reload="true">Valider</button>
    </form>
  <?php }else if ($resolu) {
    echo '<p class="text-right"><em>Vous ne pouvez plus répondre car le sujet est résolu.</em></p>';
  }else {
    echo '<p class="text-center"><em>Pour répondre  <a class="title connexion"> <i class="fa fa-key" aria-hidden="true"></i> Connectez-vous !</a></em><p>';
  } ?>
</div>