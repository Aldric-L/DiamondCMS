
<div class="container">
      <div id="firstpost">
        <div class="row">
          <BR />
          <?php
            global $post;
            echo '<div class="col-xs-4 col-sm-3 col-lg-2" id="bordure"><br /><p>' . $post['user'] . '</p>';
            echo '<p>Le ' . $post['date_post'] .'</p><br /></div>';
            echo '<div class="col-xs-8 col-sm-9 col-lg-10"><p class="bold">' . $post['titre_post'] . ' ' . $post['last_user'] . $post['date_last_post'] .'. ';
            echo 'Le ' . $post['date_post'] .'.</p>';
            echo '<p class="content_post">' . $post['content_post'] .'<br /><br /></p></div>';
          ?>
        </div>
      </div>
      <br />
      <?php
      global $coms;
      if ($coms != null){ ?>
        <div class="row"><?php

        global $post;
        /*echo '<div class="col-xs-4 col-sm-3 col-lg-2" id="bordure"><p>' . $post['user'] . '</p>';
        echo '<p>Le ' . $post['date_post'] .'</p></div>';
        echo '<div class="col-xs-8 col-sm-9 col-lg-10"><p>' . $post['titre_post'] . ' ' . $post['last_user'] . $post['date_last_post'] .'</p>';
        echo '<p. Le ' . $post['date_post'] .'.</p>';
        echo $post['content_post'] .'</p></div><br /><br /><br /><br /><br /><br /><br /><br /><br />';*/

        foreach ($coms as $com) {
          echo '<div class="col-xs-1 col-sm-1 col-lg-1"></div>';
          echo '<div class="col-xs-4 col-sm-3 col-lg-2" id="bordure"><p>' . $com['user'] . '</p>';
          echo '<p>Le ' . $com['date_com'] .'</p></div>';
          echo '<div class="col-xs-8 col-sm-9 col-lg-8"><p>';
          echo $com['content_com'] .'</p></div><br /><br /><br />';
          /*echo '<div id="com">';
          echo '<table cellspacing="20">';
          echo '<tr><td><div id="user">' . $com['user'] . '</div>';
          echo '<p>Le ' . $com['date_com'] .'</p></td>';
          echo '<td><p>' . $com['content_com'] .'</p></td></tr>';
          echo '</table>';
          echo '</div>';
          echo '<br /><br /><br /><br />';*/
        } ?>
        <style>
         #com table{
           left: 10%;
           position: absolute;
         }
        </style> <?php
      }else {
        echo "<h4>Il n'y a aucune réponse à afficher !</h4><br /><br /><br />";
      }

       ?>
    </div>
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
  /*float: right;
  overflow: hidden;*/
  width: 95%;
  text-align: justify;
}
</style>
