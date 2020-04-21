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
    <h1>Serveurs - <?php echo $servers['name']; ?></h1>
  </div>
</div>
<h1 class="bold text-center">Informations sur le serveur : <?php echo $servers['name']; ?></h1>
<div class="content-container">
<?php 
    if ($players['results'] != false) { ?>
<h3>Joueurs du serveur : </h3>
<br />
    <table class="table table-striped">
        <thead>
          <tr>
            <th>Pseudo</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
    <?php 
      foreach ($players['results'] as $p){
            echo '<tr>';
            echo '<td><img width=26 height=26 src="' . $Serveur_Config['api_url'] . 'face.php?id='. $Serveur_Config['id_cms'] . '&u='. $p . '&s=26">  ' . $p . '</td>';
            echo '<td><span style="color: green">Connecté</span></td>';
            echo '</tr>';
      }
      ?>
        </tbody>
      </table>
    <?php } else { ?>
      <h3 class="text-center">Aucun joueur n'est actuellement connecté.</h3>
      <br><br>
    <?php } ?>
</div>
<?php } ?>