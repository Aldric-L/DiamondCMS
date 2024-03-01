<?php
global $lastactions, $user, $Serveur_Config;
$not_user = !(isset($_SESSION['user']) && $_SESSION['user']->getId() === $user->getId()); ?>
<div id="fh5co-page-title" style="background-image: url(<?php echo Manager::makeGetImageLink($Serveur_Config['bg']); ?>)">
   <div class="overlay"></div>
   <div class="text">
   <?php if ($not_user){ ?>
     <h1>Mon Compte</h1>
   <?php }else { ?>
    <h1>Le compte de <?php echo $user->getPseudo(); ?></h1>
   <?php } ?>
   </div>
 </div>
<div class="content-container container content-page">
  <h1 class="text-center">Informations sur le compte de <?php echo $user->getPseudo(); ?></h1>
  <br /><br />
  <div class="row">
    <div class="col-sm-4 col-lg-4 col-sm-offset-2 col-lg-offset-2">
        <p style="text-align: right;"><br><img class="" src="<?= LINK; ?>getprofileimg/<?php echo $user->getPseudo(); ?>/200"></p>
    </div>
    <div class="col-sm-4 col-lg-4">
        <h2>Infos : </h2>
        <p><strong>Pseudo :</strong> <?php echo $user->getPseudo();?><br/>
        <?php if (!$not_user){ ?>
           <strong>Mot de passe :</strong> Vous seul le connaissez !<br/>
        <?php } ?>
        <?php if (!$not_user){ ?>
           <strong>Email :</strong> <?php echo $user->getPseudo();?><br/>
        <?php } ?>
        <strong>Nombre de votes :</strong> <?php echo $user->getVotes();?><br/>
        <strong>Argent en ligne :</strong> <?php echo $user->getMoney();?> <?php echo $Serveur_Config['Serveur_money'];?>(s)<br/>
        <strong>Inscrit le :</strong> <?php echo $user->getInfo()['date_inscription'];?><br/>
        <strong>Rang :</strong> <?php echo $user->getRoleName(); ?><br/>
        <?php if ($not_user){ ?>
        <strong>Signature forum :</strong> <?php echo (($sgn=$user->getForumSignature()) == "") ? "Aucune." : $sgn; ?><br/>
        <?php } ?>
        <?php if (!$not_user){ ?>
              <strong><a href="<?= LINK; ?>compte/deconnexion/" class="bold">Se déconnecter...</a></strong><br/>
        <?php } ?>
        </p>
    </div>
  </div>
  <?php if ($user->can_ban()){?>
    <hr>
    <p class="text-center"><a href="#" data-toggle="modal" data-target="#ban_modal" style="text-decoration: none; color: black;"><i class="fa fa-ban text-danger" aria-hidden="true"></i> Bannir cet utilisateur</a> - 
    <a data="<?= LINK; ?>compte/supp/<?php echo $user->getPseudo();?>/" href="#" style="text-decoration: none; color: black;"
    class="ajax-simpleSend" data-module="comptes/" data-verbe="set" data-func="deleteLastActions" data-tosend="user_id=<?php echo $user->getId(); ?>" data-reload="true"><i class="fa fa-trash-o fa-lg"></i> Supprimer toutes ses interventions</a></p>
    <?php if (!$not_user){?>
      <p class="text-center text-danger">Se bannir soi-même est une idée comme une autre.</p>
    <?php } ?>
  <?php }else {?>
    <p class="text-center text-danger">Vous ne pouvez pas bannir ce compte.</p>
  <?php } ?>
  <hr>
  <?php 
  //Affichage de l'interface pour modifier le compte
  if (!$not_user){ ?>
    <div class="container-fluid">
      <div class="rows">
            <h3 class="text-center">Modifier votre profil :</h1><br>
            <div class="col-lg-2"></div><!-- ./col-lg-2 -->
            <div class="col-lg-8">
              <form method="POST" action="" enctype="multipart/form-data" class="" id="modify_profil">
                <input type="hidden" name="user_id" value="<?php echo $user->getId();?>">
                <div class="form-group row">
                  <label for="pseudo" class="col-sm-2 col-form-label">Pseudo</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control" name="pseudo" id="pseudo" value="<?php echo $user->getPseudo();?>">
                  </div>
                </div>
                <div class="form-group row">
                  <label for="email" class="col-sm-2 col-form-label">Email</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control" id="email" name="email" value="<?php echo $user->getEmail();?>">
                  </div>
                </div>
                <div class="form-group row">
                  <label for="email" class="col-sm-2 col-form-label">Mot de passe</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control" id="password" name="password" placeholder="Laisser vide pour ne pas modifier">
                  </div>
                </div>
                  <div class="form-group row">
                    <label for="img" class="col-sm-2 col-form-label">Photo de profil</label>
                    <div class="col-sm-10">
                      <input type="file" class="form-control-file" placeholder="file" name="pdp" id="pdp">
                      <small id="imgHelpBlock" class="form-text text-muted">
                        Vous pouvez renvoyer ce formulaire sans impacter votre photo de profil en laissant ce champ vide.<br>
                        <span style="color: red;">Attention ! Votre photo de profil doit <strong>impérativement être un carré</strong> et être un png ou un jpeg</span>
                      </small>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="email" class="col-sm-2 col-form-label">Signature</label>
                    <div class="col-sm-10">
                      <textarea class="form-control" cols="25" rows="6" name="signature"><?php echo $user->getForumSignature(); ?></textarea><br />
                    </div>
                  </div>
                <div class="form-check row">
                  <label class="col-sm-2 col-form-label"></label>
                  <div class="col-sm-10">
                    <input class="form-check-input" name="news" type="checkbox" id="news" <?php if ($user->isOkToGetMails()) { ?> checked <?php } ?>>
                    <label for="news" class="form-check-label">Autoriser la réception de mail de notre part</label>
                  </div>
                </div>
                  <p style="text-align: right;">
                  <!--<button type="submit" id="submit-all" class="btn btn-custom btn-md">Envoyer</button>-->
                  <button class="btn btn-custom ajax-simpleSend" data-api="<?= LINK; ?>api/" 
                    data-module="comptes/" data-verbe="get" data-func="modifAccount" data-tosend="#modify_profil" data-useform="true" data-reload="true" data-showReturn="true">Envoyer</button></P>
              </form>
            </div><!-- ./col-lg-8 -->
            <div class="col-lg-2"></div><!-- ./col-lg-2 -->
      </div><!-- ./rows -->
    </div><!-- ./container -->
    <hr>
  <?php } ?>
  <?php 
  //Affichage des dernières notifications
  if (!$not_user){ ?>
    <div class="container-fluid">
      <div class="rows">
            <h3 class="text-center">Vos dernières notifications :</h1>
            <div class="col-lg-2"></div><!-- ./col-lg-2 -->
            <div class="col-lg-8">
                <?php
                if (empty($controleur_def->getNotifyLog($user->getId()))){ ?>
                    <p style="text-align: center;">Aucune notification à afficher</p>
                <?php } ?>
                <ul>
                <?php foreach ($controleur_def->getNotifyLog($user->getId()) as $n) {  ?>
                <li>
                  <strong><?php echo $n['title']; ?></strong> - 
                 <?php echo $n['content']; ?> - <a href="<?= $n['link']; ?>">En savoir plus...</a>
                </li>
              <?php  }   ?>
              </ul>
            </div><!-- ./col-lg-8 -->
            <div class="col-lg-2"></div><!-- ./col-lg-2 -->
      </div><!-- ./rows -->
    </div><!-- ./container -->
    <hr>
  <?php } ?>
  <?php 
  //Affichage des dernières commandes
  if (!$not_user){ $commandes = $user->getCommandes($controleur_def->bddConnexion()); ?>
    <div class="container-fluid">
      <div class="rows">
            <h3 class="text-center">Vos dernières commandes :</h1>
            <div class="col-lg-2"></div><!-- ./col-lg-2 -->
            <div class="col-lg-8">
                <?php
                if (empty($commandes)){ ?>
                    <p style="text-align: center;">Vous n'avez pour le moment rien acheté sur notre boutique !</p>
                <?php } ?>
                <ul>
                <?php foreach ($commandes as $c) {  ?>
                <li>
                  <strong>Achat de l'article "<?php if ($c['article'] != false) { echo $c['article']['name']; }else{ echo "Inconnu"; } ?>" pour <?php echo $c['price']; ?> <?= $Serveur_Config['Serveur_money']; ?>(s)</strong> - 
                 Numéro de commande : <?php echo $c['uuid']; ?> (passée le <?= $c['date']; ?>)
                 <?php if ($c['success']){ ?>
                    - <span style="color: green;"><strong>Terminée avec succès</strong></span> <em>(<a href="<?= LINK; ?>boutique/getback/<?=$c['uuid']; ?>">Accèder au reçu</a>)</em>
                 <?php }else { ?>
                    - <a href="<?= LINK; ?>boutique/getback/<?=$c['uuid']; ?>">Finir de réceptionner la commande</a>
                 <?php } ?>
                </li>
              <?php  }   ?>
              </ul>
            </div><!-- ./col-lg-8 -->
            <div class="col-lg-2"></div><!-- ./col-lg-2 -->
      </div><!-- ./rows -->
    </div>
      <hr>
    <div class="container-fluid">
      <div class="rows">
            <h3 class="text-center">Vos derniers paiements réels :</h1>
            <div class="col-lg-2"></div><!-- ./col-lg-2 -->
            <div class="col-lg-8">
                <?php $payements = $user->getRealPayements($controleur_def->bddConnexion());
                if (empty($payements) || (empty($payements['Dedipass']) && empty($payements['PayPal'])) ){ ?>
                    <p style="text-align: center;">Vous n'avez pas acheté de monnaie virtuelle pour le moment !</p>
                <?php } ?>
                <ul>
                <?php foreach ($payements['PayPal'] as $p) {  ?>
                <li>
                  <strong>Paiement PayPal d'une valeur de <?php echo $p['payment_amount']; ?> <?php echo $p['payment_currency']; ?></strong> réalisé le <?php echo $p['payment_date']; ?> avec le compte <?php echo $p['payer_email']; ?> en échange de <?php echo $p['money_get']; ?> <?= $Serveur_Config['Serveur_money']; ?>(s).<br> <strong>Numéro de transaction :</strong> <?php echo $p['payment_id']; ?></strong> 
                </li>
              <?php  }   ?>
              <?php foreach ($payements['Dedipass'] as $p) {  ?>
                <li>
                  <strong>Paiement Dedipass d'une valeur de <?php echo $p['payout']; ?> <?= $Serveur_Config['money']; ?></strong> réalisé le <?php echo $p['date']; ?> en échange de <?php echo $p['virtual_currency']; ?> <?= $Serveur_Config['Serveur_money']; ?>(s).<br> <strong>Code de transaction :</strong> <?php echo $p['code']; ?></strong> 
                </li>
              <?php  }  ?>
              </ul>
            </div><!-- ./col-lg-8 -->
            <div class="col-lg-2"></div><!-- ./col-lg-2 -->
      </div><!-- ./rows -->
    </div><!-- ./container -->
    <hr>
  <?php } ?>
  <?php if ($this->registered_modules_manager != null){ 
    try {
      $this->registered_modules_manager->renderModules($controleur_def, (isset($_SESSION['user']) && isset($_SESSION['editing_mode']) && $_SESSION['editing_mode'] && $_SESSION['user']->getLevel() >= 4)); 
    } catch (\DiamondException $e) {
      $controleur_def->addError($e->getCode());
    }
    }?>

<?php if ($user->can_ban()){ ?>
<!-- Ban modal -->
<div id="ban_modal" class="modal fade">
  <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title">Validation requise !</h4>
        </div>
        <div class="modal-body">
          <p>Êtes vous sûr de vouloir bannir définitivement <strong><?php echo $user->getPseudo(); ?></strong> ?</p>
          <hr>
          <p><em>DiamondCMS vous rappelle que bannir un utilisateur ayant procédé à un paiement sur la boutique en ligne peut constituer une violation des textes réglementatifs en vigueur en France. Nous vous conseillons de ne pas exclure des utilisateurs possédant encore des tokens sur votre boutique.</em></p>
          <hr>
          <form action="" id="banform">
            <div class="form-group">
                <label for="reason"><strong>Veuillez indiquer une raison au bannissement :</strong></label>
                <input type="text" class="form-control" id="r_ban" name="r_ban" placeholder="Cette raison sera publique." required>
            </div>
            <input type="hidden" class="form-control" id="user_id" name="user_id" value="<?php echo $user->getId(); ?>">
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
            <button class="btn btn-danger ajax-simpleSend" data-api="<?= LINK; ?>api/" 
            data-module="comptes/" data-verbe="set" data-func="ban" data-tosend="#banform" data-useform="true" data-reload="true">
            Bannir</button>
        </div>
      </div>
  </div>
</div>
<!-- END Ban modal -->
<?php } ?>
</div>

