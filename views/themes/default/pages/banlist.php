<?php
global $banlist;
global $empty;
if ($empty == true){?>
  <div id="emptyServer">
    <h1>Erreur !</h1>
    <h2>Aucun serveur n'est connecté !</h2>
    <h3>Rechargez la page dans quelques minutes...</h3>
    <br />
  </div><?php
}else { ?>
  <div class="content-container">
    <h1>La BanList du serveur :</h1><br />
    <div style="width: 90%; margin-right: 5%; margin-left: 5%;">
      <table class="table table-striped">
        <thead>
          <tr>
            <th>Id</th>
            <th>Pseudo</th>
          </tr>
        </thead>
        <tbody>
          <?php
            for ($i=0; $i<sizeof($banlist[0]['success']); $i++){
              echo '<tr>';
              echo "<td>$i</td>";
              echo '<td><img width=26 height=26 src="http://api.diamondcms.fr/face.php?id='. $Serveur_Config['id_cms'] . '&u='. $banlist[0]['success'][$i] . '&s=26">  ' . $banlist[0]['success'][$i] .'</td>';
              echo '</tr>';
            }
           ?>
        </tbody>
      </table>
    </div>
  </div>
  <?php
  /*for ($i=0; $i<sizeof($banlist[0]['success']); $i++){
    echo $banlist[0]['success'][$i];
  }*/
  /*foreach ($banlist[0]['success'] as $key){
    echo $banlist[0]['success'][$key];
  }*/
}
