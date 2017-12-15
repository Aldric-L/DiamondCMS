<script>
  $(document).ready(function(){
    $("#err").hide();
    return true;
  });
</script>
<div class="container">
    <div id="firstpost">
        <div class="row">
          <br />
          <?php
            global $post_info;
            echo '<div class="col-xs-4 col-sm-3 col-lg-2" id="bordure"><img width=64 height=64 src="http://api.diamondcms.fr/face.php?id='. $Serveur_Config['id_cms'] . '&u='. $post_info['user'] . '&s=64"><br /></div>';
            //echo '<div class="col-xs-4 col-sm-3 col-lg-2" id="bordure"><br /><p>' . $post['user'] . '</p>';
            //echo '<p>Le ' . $post['date_post'] .'</p><br /></div>';
            echo '<div class="col-xs-8 col-sm-9 col-lg-10"><p class="bold">' . $post_info['titre_post'] . ' par ' . $post_info['user'] . ' le '. $post_info['date_post'] .'</p>';
            echo '<p class="content_post">' . $post_info['content_post'] .'<br /><br /></p></div>';
          ?>
        </div>
      </div>
      <p class="text-center"><em><br/>Partager sur  <a href="#" class="bold" data-type="facebook" class="csbuttons"><i class="fa fa-facebook-official" aria-hidden="true"></i> Facebook  </a>
      <a href="#" class="bold csbuttons" data-type="twitter"><i class="fa fa-twitter" aria-hidden="true"></i> Twitter  </a>
      <a href="#" class="bold csbuttons" data-type="google" data-lang="fr"><i class="fa fa-google-plus" aria-hidden="true"></i> Google+  </a></em></p>
      <style>
      img {
        float: none;
        margin-left: 2em;
        margin-top: 1em;
        margin-bottom: 1em;
      }
      </style>
      <br />
      <?php
      global $coms;
      global $erreurcom;
      global $param;
      if ($coms != null){ ?>
        <?php

        foreach ($coms as $com) {
          echo '<div class="row">';
          echo '<div class="col-xs-2 col-sm-2 col-lg-2"></div>';
          echo '<div class="col-xs-2 col-sm-2 col-lg-2" id="bordure"><img width=64 height=64 src="http://api.diamondcms.fr/face.php?id='. $Serveur_Config['id_cms'] . '&u='. $com['user'] . '&s=64"></p></div>';
          echo '<div class="col-xs-8 col-sm-8 col-lg-8"><p class="bold"> En réponse à ' . $post_info['titre_post'] . ' par ' . $com['user'] . ' le '. $com['date_com'] .'</p><p>';
          echo $com['content_com'] .'</p></div></div><br /><br /><br />';
        }
      }else {
        echo "<h4>Il n'y a aucune réponse à afficher !</h4>";
      }
      global $resolu;

      if (isset($_SESSION['pseudo']) && !empty($_SESSION['pseudo']) && $resolu != true){
        if (!empty($erreurcom)){
          echo '<div id="err">';
          echo '<h4>' . $erreurcom . '</h4>';
          echo '</div>';

        }
        ?>
        <br /><br />
        <div class="rows"><div class="col-xs-2 col-sm-2 col-lg-2"></div>
        <div class="col-xs-8 col-sm-8 col-lg-8"><form method="post" action="">
          <label for="form-control col-sm-2">Répondre à ce sujet :</label>
          <textarea class="form-control" cols="25" rows="6" name="comment"></textarea><br />
          <button type="submit" class="btn pull-right btn-danger sub">Valider</button>
        </form></div></div>
        <br /></br />
        <?php
        if ($_SESSION['pseudo'] == $post_info['user']){
          echo '<br /></br /><br /></br /><br /></br /><br /></br /><br /></br />
          <p class="text-right"><em>Actions sur le sujet : <a href="http://'. $_SERVER['HTTP_HOST'] . WEBROOT . 'forum/solved/'. $post['id'] . '"><i class="fa fa-check-circle" aria-hidden="true"></i> Résolu</a> ou  <a href="http://'. $_SERVER['HTTP_HOST'] . WEBROOT . 'forum/del/'. $post_info['id'] . '"><i class="fa fa-times" aria-hidden="true"></i> Supprimer</a></em></p>';
        }
      }else if ($resolu == true){
        echo '<p class="text-right"><em>Vous ne pouvez plus répondre car le sujet est résolu.</em></p>';
      }else {
        echo '<p class="text-center"><em>Pour répondre <a href="http://'. $_SERVER['HTTP_HOST'] . WEBROOT . 'connexion"><i class="fa fa-key" aria-hidden="true"></i> Connectez-vous !</a></em><p>';
      }?>
</div>
<style>
  h4 {
    text-align: center;
  }
  .bold{
      font-size: 16px;
      font-weight: bold;
  }
  .content_post {
    width: 95%;
    text-align: justify;
  }
</style>
<script>
  $(".sub").click(function(){
    $("#err").show();
  });
</script>
