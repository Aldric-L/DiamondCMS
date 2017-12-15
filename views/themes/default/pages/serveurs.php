<?php global $servers, $server_id, $players, $empty; 
if ($empty == true){?>
  <div id="emptyServer">
    <br />
    <h1>Erreur !</h1>
    <h2>Le serveur demandé n'est pas connecté !</h2>
    <h3>Rechargez la page dans quelques minutes...</h3>
    <br /><br /><br />
  </div><br /><br /><br /><?php
}else { ?>
<div id="fh5co-page-title">
  <div class="overlay"></div>
  <div class="text">
    <h1>Serveurs - <?php echo $servers['ServerName']; ?></h1>
  </div>
</div>
<h1 class="bold text-center">Informations sur le serveur : <?php echo $servers['ServerName']; ?></h1>
<div class="content-container">
<h3>Joueurs du serveur : </h3>
<br />
    <table class="table table-striped">
        <thead>
          <tr>
            <th>Id</th>
            <th>Pseudo</th>
            <th>Status</th>
            <th>Whitlisté</th>
            <th> </th>
          </tr>
        </thead>
        <tbody>
<?php var_dump($players[0][0]['success'], $players[1][0]['success']);
    for ($i=0; $i<sizeof($players[0][0]['success']); $i++){
            echo '<tr>';
            echo "<td>$i</td>";
            echo '<td><img width=26 height=26 src="http://api.diamondcms.fr/face.php?id='. $Serveur_Config['id_cms'] . '&u='. $players[0][0]['success'][$i] . '&s=26">  ' . $players[0][0]['success'][$i] . '</td>';
            echo '<td><span style="color: green">Connecté</span></td>';
            echo '<td><a href="">Voir plus...</a></td>';
            echo '</tr>';
      }
      for ($i=0; $i<sizeof($players[1][0]['success']); $i++){
            echo '<tr>';
            echo "<td>$i</td>";
            echo '<td><img width=26 height=26 src="http://api.diamondcms.fr/face.php?id='. $Serveur_Config['id_cms'] . '&u='. $players[1][0]['success'][$i] . '&s=26">  ' . $players[1][0]['success'][$i] . '</td>';
            echo '<td><span style="color: red">Deconnecté</span></td>';
            echo '<td><a href="">Voir plus...</a></td>';
            echo '</tr>';
      } ?>
        </tbody>
      </table>
</div>
<?php } ?>