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
            global $post;
            echo '<div class="col-xs-4 col-sm-3 col-lg-2" id="bordure"><br /><p>' . $post['user'] . '</p>';
            echo '<p>Le ' . $post['date_post'] .'</p><br /></div>';
            echo '<div class="col-xs-8 col-sm-9 col-lg-10"><p class="bold">' . $post['titre_post'] . '</p>';
            echo '<p class="content_post">' . $post['content_post'] .'<br /><br /></p></div>';
          ?>
        </div>
      </div>
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
          echo '<div class="col-xs-2 col-sm-2 col-lg-2" id="bordure"><p>' . $com['user'] . '</p>';
          echo '<p>Le ' . $com['date_com'] .'</p></div>';
          echo '<div class="col-xs-8 col-sm-8 col-lg-8"><p>';
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
        <?php
      }else if ($resolu == true){
        echo '<p class="text-right"><em>Vous ne pouvez plus répondre car le sujet est résolu.</em></p>';
      }else {
        echo '<p class="text-center"><em>Pour répondre <a href="http://' . $_SERVER['HTTP_HOST'] . WEBROOT . 'inscription"><i class="fa fa-key" aria-hidden="true"></i> Connectez-vous !</a></em><p>';
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
