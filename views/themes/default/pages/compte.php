<?php
global $infos, $lastactions, $pseudo, $not_user, $can_ban;

if (empty($infos)){?>
    <div id="emptyServer">
    <br />
    <h1>Erreur !</h1>
    <h2>Le compte demandé n'existe pas !</h2>
    <br /><br /><br />
  </div><br /><br /><br />
<?php }else {
 ?>
 <div id="fh5co-page-title">
   <div class="overlay"></div>
   <div class="text">
   <?php if (!$not_user){ ?>
     <h1>Mon Compte</h1>
   <?php }else { ?>
    <h1>Le compte de <?php echo $pseudo; ?></h1>
   <?php } ?>
   </div>
 </div>
<?php if (!$infos[0]['is_ban']){ ?>
<div class="content-container container content-page">
  <h1 class="text-center">Informations sur le compte de <?php echo $pseudo; ?></h1>
  <br /><br />
  <div class="row">
    <div class="col-sm-4 col-lg-4 col-sm-offset-2 col-lg-offset-2">
        <img class="img-responsive img-rounded" src="http://api.diamondcms.fr/skin.php?id=<?php echo $Serveur_Config['id_cms']; ?>&u=<?php echo $pseudo; ?>&s=500">
    </div>
    <div class="col-sm-4 col-lg-4">
        <h2>Infos : </h2>
        <p><strong>Pseudo :</strong> <?php echo $infos[0]['pseudo'];?><br/>
        <?php if (!$not_user){ ?>
           <strong>Mot de passe :</strong> Vous seul le connaissez !<br/>
          <?php } ?>
          <?php if (!$not_user){ ?>
           <strong>Email :</strong> <?php echo $infos[0]['email'];?><br/>
          <?php }else { ?>
           <strong>Email :</strong> Nous ne divulgons pas les emails de nos memebres.<br/>
          <?php } ?>
           <strong>Nombre de votes :</strong> <?php echo $infos[0]['votes'];?><br/>
           <strong>Argent en ligne :</strong> <?php echo $infos[0]['money'];?> <?php echo $Serveur_Config['Serveur_money'];?>(s)<br/>
           <strong>Pseudo :</strong> <?php echo $infos[0]['pseudo'];?><br/>
           <strong>Inscrit le :</strong> <?php echo $infos[0]['date_inscription'];?><br/>
           <strong>Admin :</strong> <?php if ($infos[0]['admin'] == 1){ echo "Oui"; }else { echo "Non"; }?><br/>
           <?php if (!$not_user){ ?>
              <strong><a href="<?php echo $Serveur_Config['protocol']; ?>://<?php echo $_SERVER['HTTP_HOST'] . WEBROOT; ?>compte/deconnexion/" class="bold">Se déconnecter...</a></strong><br/>
           <?php } ?>
        </p>
    </div>
  </div>
  <?php if ($can_ban){?>
    <hr>
    <p class="text-center"><a class="ban" href="#" style="text-decoration: none; color: black;"><i class="fa fa-ban text-danger" aria-hidden="true"></i> Bannir cet utilisateur</a> - 
    <a class="supp" href="#" style="text-decoration: none; color: black;"><i class="fa fa-trash-o fa-lg"></i> Supprimer toutes ses interventions</a></p>
    <?php if (!$not_user){?>
      <p class="text-center text-danger">Se bannir sois-même est une idée comme une autre.</p>
    <?php } ?>
  <?php }else {?>
    <p class="text-center text-danger">Vous n'avez pas l'autorisation de bannir ce compte.</p>
  <?php } ?>
  <?php if ($can_ban && $not_user){ ?>
    <script>
        $(".ban").click(function(){
           $("#ban_modal").modal('show');
        });
        $(".supp").click(function(){
           $("#supp_modal").modal('show');
        });
    </script>
  <?php } // $can_ban && not_user ?>
  <hr>
  <?php if (!$not_user){ ?>
    <h2 class="text-center">Vos dernières interventions :</h1>
  <?php }else { ?>
    <h2 class="text-center">Ses dernières interventions :</h1>
  <?php } 
    if (!empty($lastactions)){
      echo '<br /><br /><div class="last">';
      foreach ($lastactions as $key => $lastaction) {
        echo '<p><strong class="bold">Le ' . $lastactions[$key]['date_com'] . ' sur le sujet "' . $lastactions[$key]['id_post']['titre_post'] . '" par ' . $lastactions[$key]['id_post']['user'] . "</strong><br />";
        echo $lastactions[$key]['content_com'] . "</p>";
        echo '<p class="text-right"><a href="'  .$Serveur_Config['protocol'] . '://'. $_SERVER['HTTP_HOST'] . WEBROOT . 'forum/com/'. $lastactions[$key]['id_post']['id'] .'/">Retourner sur le sujet...</a></p><br />';
      }
      echo "</div>";
    }else {
      if (!$not_user){
          echo '<h4 class="text-center text-warning">Vous n\'avez pas encore participé à un sujet !</h4>';
        }else {
          echo '<h4 class="text-center text-warning">'. $pseudo . ' n\'a pas encore participé à un sujet !</h4>';
        }    
      }
 ?>
</div>
<style>
.last {
  width: 80%;
  margin: auto;
}
</style>
<div id="ban_modal" class="modal fade">
  <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title">Validation requise !</h4>
        </div>
        <div class="modal-body">
          <h5 class="text-danger">Attention ! Vous vous apretez a éffectuer une action irrévertible !</h5>
          <p>Êtes vous sûr de vouloir bannir définitivement <?php echo $infos[0]['pseudo'];?> ?</p>
          <br />
          <p>Veuillez indiquer une raison au bannissement : <input type="text" id="reason"></input></p>
          
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default">Fermer</button>
          <!--<a href="#" id="ban_button">--><button type="button" class="btn btn-danger" id="ban_button">Valider</button><!--</a> -->
        </div>
      </div>
  </div>
</div>

<script>
$("#ban_button").click(function(){
  $.ajax({
    url : '<?php echo $Serveur_Config['protocol']; ?>://<?= $_SERVER['HTTP_HOST']; ?><?=WEBROOT; ?>compte/ban/<?php echo $infos[0]['pseudo']; ?>/',
    type : 'POST',
    data : 'reason=' + $('#reason').val(),
    dataType : 'html',
    success: function (data_rep) {
      if (data_rep != "Success"){
        alert("Erreur, Code 112, Merci de contacter les administrateurs du site.");
      }else {    
        $(".content-page").hide();               
        $(".ban_user").show();
      }
    },
    error: function() {
      alert("Erreur, Code 111, Merci de contacter les administrateurs du site.");
    }
  });
  $("#ban_modal").modal('hide');
  $('#emptyServer').show();
  $('.content-page').hide();
});
$(".supp").click(function(){
  $.ajax({
    url : '<?php echo $Serveur_Config['protocol']; ?>://<?= $_SERVER['HTTP_HOST']; ?><?=WEBROOT; ?>compte/supp/<?php echo $infos[0]['pseudo']; ?>/',
    type : 'GET',
    dataType : 'html',
    success: function (data_rep) {
      if (data_rep != "Success"){
        alert("Erreur, Code 112, Merci de contacter les administrateurs du site.");
      }else {    
        alert("Utilisateur purgé, actualisation de la page...");
        location.reload();
      }
    },
    error: function() {
      alert("Erreur, Code 111, Merci de contacter les administrateurs du site.");
    }
  });
});
</script>
<?php if ($can_ban && $not_user) { ?>
  <div id="emptyServer" style="display: none;">
      <br />
      <h1>Compte banni !</h1>
      <h2>Le compte demandé a été banni.</h2>
      <br /><br /><br />
  </div><br /><br /><br />
<?php } ?>
<?php }else { /*!$is_ban*/ ?>
  <div id="emptyServer" style="">
    <br />
    <h1>Compte banni !</h1>
    <h2>Le compte demandé a été banni.</h2>
    <br /><br /><br />
</div><br /><br /><br />
<?php } 
 } // !empty($infos) ?>