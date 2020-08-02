<?php global $servers, $server_id, $players, $empty, $game; 
if ($empty == true){?>
  <div id="emptyServer">
    <br />
    <h1>Erreur !</h1>
    <h2>Le serveur demandé n'est pas connecté !</h2>
    <h3>Rechargez la page dans quelques minutes...</h3>
    <br /><br /><br />
  </div><br /><br /><br /><?php
}else { ?>
<div id="fh5co-page-title" style="background-image: url(<?php echo LINK; ?>views/uploads/img/<?php echo $Serveur_Config['bg']; ?>)">
  <div class="overlay"></div>
  <div class="text">
    <h1>Serveurs - <?php echo $servers[$server_id]['name']; ?></h1>
  </div>
</div>
<h1 class="bold text-center">Informations sur le serveur : <?php echo $servers[$server_id]['name']; ?></h1>
<div class="content-container">
<?php 
    if ($players[$server_id]['results'] != false) { ?>
<h3>Joueurs du serveur : <span class="bold" style=""><?php echo sizeof($players[$server_id]['results']); ?> joueurs connectés actuellement</span></h3>
<br />
    <table class="table table-striped">
    <?php 
      if (!empty($game) && ($game == "Minecraft-Java" || $game == "MCPE" )){ ?>
          <thead>
            <tr>
              <th>Pseudo</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
        <?php foreach ($players[$server_id]['results'] as $p){
          echo '<tr>';
          if (defined("DMcProfileImg") && DMcProfileImg){
            echo '<td><img width=26 height=26 src="' . LINK . 'getprofileimg/'. $p . '/26/26">  ' . $p . '</td>';
          }else {
            echo '<td>' . $p . '</td>';
          }
          echo '<td><span style="color: green">Connecté</span></td>';
          echo '</tr>';
        }
      }else { ?>
          <thead>
            <tr>
              <th>Pseudo</th>
              <th>Status</th>
              <th>Frags</th>
              <th>Temps de connexion</th>
            </tr>
          </thead>
          <tbody>
        <?php foreach ($players[$server_id]['results'] as $p){
          //var_dump($p);
          echo '<tr>';
          echo '<td>' . $p['Name'] . '</td>';
          echo '<td><span style="color: green">Connecté</span></td>';
          echo '<td>'. $p['Frags'] . '</td>';
          echo '<td>'. $p['TimeF'] . '</td>';
          echo '</tr>';
        }
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