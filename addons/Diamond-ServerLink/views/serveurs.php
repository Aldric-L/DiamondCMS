<?php global $servers, $server_id, $players, $empty, $game; unset($p);
if ($empty != true){?>
<div id="fh5co-page-title" style="background-image: url(<?php echo Manager::makeGetImageLink($Serveur_Config['bg']); ?>)">
  <div class="overlay"></div>
  <div class="text">
    <h1>Serveurs -> <?php echo $servers[$server_id]['name']; ?></h1>
  </div>
</div>
<h1 class="bold text-center">Informations sur le serveur : <?php echo $servers[$server_id]['name']; ?></h1>
<div class="content-container">
<?php 
    if ($players[$server_id]['results'] != false) { ?>
<h3>Joueurs du serveur : <span class="bold" style=""><?php echo sizeof($players[$server_id]['results']); ?> joueur<?php echo (sizeof($players[$server_id]['results']) > 1) ? "s" : ""; ?> connecté<?php echo (sizeof($players[$server_id]['results']) > 1) ? "s" : ""; ?> actuellement</span></h3>
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
          echo '<td>' . $p . '</td>';
          echo '<td><span style="color: green">Connecté</span></td>';
          echo '</tr>';
        }
      }else if (!empty($game) && $game == "Minecraft JSONAPI"){ ?>
          <thead>
            <tr>
              <th>Pseudo</th>
              <th>Niveau</th>
              <th>Santé</th>
              <th>Opérateur</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
        <?php 
        // BUG PHP qui nous force à faire ce détour en boucle for, incompréhensible...
        for ($i= 0; $i < sizeof($players[$server_id]['results']); $i++){
          echo '<tr>';
          echo '<td>' . $players[$server_id]['results'][$i]['N'] . '</td>';
          echo '<td>' . $players[$server_id]['results'][$i]['level'] . '</td>';
          echo '<td>' . round($players[$server_id]['results'][$i]['health'], 0)*100/20 . '%</td>';
          echo '<td>' . (($players[$server_id]['results'][$i]['op']) ? "Oui" : "Non") . '</td>';
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