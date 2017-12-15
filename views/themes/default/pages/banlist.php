<?php
global $banlist, $empty, $json_servers, $jsonapi, $server_status;
if ($empty == true){?>
  <div id="emptyServer">
    <h1>Erreur !</h1>
    <h2>Aucun serveur n'est connecté !</h2>
    <h3>Rechargez la page dans quelques minutes...</h3>
    <br />
  </div><br /><br /><br /><?php
}else { ?>
  <div id="fh5co-page-title">
    <div class="overlay"></div>
    <div class="text">
      <?php
      if (sizeof($server_status) > 1){
        ?><h1>Banlist des serveurs</h1>
      <?php }else { ?>
      <h1>Banlist du serveur <?php echo $server_status[0]['ServerName']; ?></h1>
      <?php } ?>
    </div>
  </div>
  <div class="content-container">
      <?php
      if (sizeof($server_status) > 1){
        ?><h1>Les BanList des serveurs :</h1>
      <?php }else { ?>
      <h1>La BanList du serveur :</h1>
      <?php } ?>
    <br />
    <div style="width: 90%; margin-right: 5%; margin-left: 5%;">
        <?php for ($n=0; $n<=sizeof($server_status)-1; $n++){
      if (!$server_status[$n]['Connect']){?>
        <div class="alert alert-danger" role="alert">
          <strong>Erreur !</strong> Nous n'arrivons pas a contacter le serveur "<?php echo $server_status[$n]['ServerName']; ?> ", nous n'avons donc pas pu recuperer sa banlist.
        </div>
        <br />
      <?php } }?>
      <table class="table table-striped">
        <thead>
          <tr>
            <th>Id</th>
            <th>Pseudo</th>
            <th>Banni sur</th>
          </tr>
        </thead>
        <tbody>
          <?php
              for ($n=0; $n<=sizeof($json_servers); $n++){
                for ($i=0; $i<sizeof($banlist[$n][0]['success']); $i++){
                  echo '<tr>';
                  echo "<td>$i</td>";
                  echo '<td><img width=26 height=26 src="http://api.diamondcms.fr/face.php?id='. $Serveur_Config['id_cms'] . '&u='. $banlist[$n][0]['success'][$i] . '&s=26">  ' . $banlist[$n][0]['success'][$i] . '</td><td>' . $jsonapi->getNameofServer($n+1) .'</td>';
                  echo '</tr>';
                }
              }
           ?>
        </tbody>
      </table>
    </div>
  </div>
  <?php
}
