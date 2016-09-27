<div id="news">
  <?php
  global $coms;
  global $post;

  echo '<table cellspacing="20">';
  echo '<tr><td>';
  echo '<div id="user">' . $post['user'] . '</div>';
  echo '<p>Le ' . $post['date_post'] .'</p></td>';
  echo '<td><p>' . $post['titre_post'] .'</p>';
  echo '<p>' . $post['content_post']  .'</p></td></tr>';
  echo '</table>';

  if ($coms != null){
    foreach ($coms as $com) {
      //echo '<right>';
      echo '<div id="com">';
      echo '<table cellspacing="20">';
      echo '<tr><td><div id="user">' . $com['user'] . '</div>';
      echo '<p>Le ' . $com['date_com'] .'</p></td>';
      echo '<td><p>' . $com['content_com'] .'</p></td></tr>';
      echo '</table>';
      echo '</div>';
      echo '<br /><br /><br /><br />';
      //echo '</right>';
    } ?>
    <br /><br /><br /><br />
    <style>
     #com table{
       left: 10%;
       position: absolute;
     }
    </style> <?php
  }else {
    echo "<p>Il n'y a aucune réponse à afficher !</p>";
  }

   ?>
</div>
