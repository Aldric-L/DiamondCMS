<?php global $boutique_config, $paypal_offres; if (sizeof($paypal_offres) <= 3){ $i = 4;}else { $i= 3; } ?>
<div id="fh5co-page-title" style="background-image: url(<?= LINK; ?>views/uploads/img/<?php echo $Serveur_Config['bg']; ?>)">
  <div class="overlay"></div>
  <div class="text">
    <h1><a class="no" href="<?php echo LINK . 'boutique/' ?>">Boutique </a>-> Achat de <?= $Serveur_Config['Serveur_money']; ?>s</h1>
  </div>
</div>
<style>
a.no {
  color: #197d62;
  text-decoration: none;
}
</style>
<br />
<div id="explicboutique">
  <h1><?php echo $Serveur_Config['Serveur_name']; ?> propose une boutique fondée sur les <?= $Serveur_Config['Serveur_money']; ?>s.</h1>
  <p class="explicp">En effet, vous pouvez acheter du contenu sur notre boutique à partir d'une monnaie virtuelle, le <?= $Serveur_Config['Serveur_money']; ?>. <br>Sur cette page, vous pouvez acheter des <?= $Serveur_Config['Serveur_money']; ?>s pour pouvoir ensuite bénéficier de nos offres sur notre boutique. <br />Merci de votre soutien !</p>
  <p id="red" class="text-danger"><strong><i class="fa fa-exclamation-triangle" aria-hidden="true"></i>  En réalisant un achat sur cette boutique, vous acceptez nos conditions générales d'utilisation et de vente.  <i class="fa fa-exclamation-triangle" aria-hidden="true"></i></strong></p>
</div>
<?php if (isset($_SESSION['user']) && !empty($_SESSION['user'])){ ?>
<div class="container">
    <div class="rows">
        <div class="col-sm-4 col-lg-4 col-sm-offset-2 col-lg-offset-2">
            <p style="text-align: right;"><br><img class="" src="<?= LINK; ?>getprofileimg/<?php echo $_SESSION['user']->getPseudo(); ?>/110"></p>
        </div>
        <div class="col-sm-4 col-lg-4">
        <br>
            <h3><?= $_SESSION['user']->getPseudo(); ?></h3>
            <p>Vous disposez de <strong><?= $_SESSION['user']->getMoney(); ?> <?= $Serveur_Config['Serveur_money']; ?>(s)</strong></p>
        </div>
        
    </div>
</div>
<?php }else { ?>
    <br />
<?php } ?>
    
<!-- Page Content -->
<div class="container">
    <div class="rows">
    <?php if ($boutique_config['PayPal']['en_paypal'] == "true"){ ?>
        <center><h3>Nos offres utilisant PayPal : </h3>
        <p>PayPal est un intermédiaire de confiance vous permettant un paiement sécurisé, et plus rémunérateur pour <?php echo $Serveur_Config['Serveur_name']; ?></p></center>
        <br>
        <script src="https://www.paypalobjects.com/api/checkout.js"></script>
        <?php if (empty($paypal_offres)){ ?>
                <center><p><strong>Aucune offre d'achat par PayPal n'est pour le moment proposée.</strong></p></center>
        <?php }else { 
                foreach ($paypal_offres as $po) {?>
        <div class="col-md-<?= $i; ?>">
            <div class="panel panel-default">
                <div class="panel-heading">
                        <h3 class="text-center" style="margin-top: 10px;"><?= $po['name']; ?></h3>
                </div>
                <div class="panel-body text-center">
                        <p class="lead" style="font-size:30px;margin-bottom: 0;"><strong><?= $po['price']; ?><?= $Serveur_Config['money']; ?></strong><small style="font-size:10px;"><em>(ttc)</em></small></p>
                </div>
                <ul class="list-group list-group-flush text-center">
                        <li class="list-group-item">
                                <?= $po['tokens']; ?> <?= $Serveur_Config['Serveur_money']; ?>s
                        </li>
                        <li class="list-group-item">
                                Disponible immédiatement
                        </li>
                </ul>
                <div class="panel-footer text-center">
                        <?php if (isset($_SESSION['user']) && !empty($_SESSION['user'])){ ?>
                                <div id="bouton-paypal-<?= $po['id']; ?>"></div>
                        <?php }else { ?>
                        <p><em>Vous devez disposer d'un compte sur notre site internet pour procéder au paiement.</em></p>
                                <a class="inscription"><button class="btn btn-success">S'inscrire</button></a>
                                <a class="connexion"><button class="btn btn-info">Se connecter</button></a>
                        <?php } ?>
                </div>
            </div>
        </div>
        <?php if (isset($_SESSION['user']) && !empty($_SESSION['user'])){ ?>
        <script>
            paypal.Button.render({
                <?php if ($boutique_config['PayPal']['sandbox']){ ?>
                      env: 'sandbox', 
                <?php }else { ?>
                      env: 'production', 
                <?php } ?>
                commit: true, // Affiche le bouton  "Payer maintenant"
                locale: 'fr_FR',
                style: {
                        color: 'blue', // ou 'blue', 'silver', 'black'
                        size: 'responsive' // ou 'small', 'medium', 'large'
                        // Autres options de style disponibles ici : https://developer.paypal.com/docs/integration/direct/express-checkout/integration-jsv4/customize-button/
                },
              payment: function(data, actions) {
                var CREATE_URL = '<?php echo LINK . 'boutique/getmoney/createpaypal/' . $po['id']; ?>';
                  // On exécute notre requête pour créer le paiement
                  return paypal.request.post(CREATE_URL)
                        .then(function(data) { // Notre script PHP renvoie un certain nombre d'informations en JSON (vous savez, grâce à notre echo json_encode(...) dans notre script PHP !) qui seront récupérées ici dans la variable "data"
                                    console.log(data);
                        if (data.success) { // Si success est vrai (<=> 1), on peut renvoyer l'id du paiement généré par PayPal et stocké dans notre data.paypal_reponse (notre script en aura besoin pour poursuivre le processus de paiement)
                                return data.paypal_response.id;   
                        } else { // Sinon, il y a eu une erreur quelque part. On affiche donc à l'utilisateur notre message d'erreur généré côté serveur et passé dans le paramètre data.msg, puis on retourne false, ce qui aura pour conséquence de stopper net le processus de paiement.
                                alert(data.msg);
                                return false;   
                        }
                });
              },
              onAuthorize: function(data, actions) {
                  var EXECUTE_URL = '<?php echo LINK . 'boutique/getmoney/buypaypal/' . $po['id']; ?>';
                  var data = {
                        paymentID: data.paymentID,
                        payerID: data.payerID
                  };
                  return paypal.request.post(EXECUTE_URL, data)
                        .then(function (data) { 
                                    console.log(data);
                        if (data.success) { 
                                window.location.replace("<?php LINK . 'boutique/getmoney/successpaypal/'; ?>");
                        } else {
                                // Sinon, si "success" n'est pas vrai, cela signifie que l'exécution du paiement a échoué. On peut donc afficher notre message d'erreur créé côté serveur et stocké dans "data.msg".
                                alert(data.msg);
                        }
                  });
              },
              onCancel: function(data, actions) {
                  alert("Paiement annulé : vous avez fermé la fenêtre de paiement.");
              },
              onError: function(err) {
                  alert("Paiement annulé : une erreur est survenue. Merci de bien vouloir réessayer ultérieurement.");
              }
            }, '#bouton-paypal-<?= $po['id']; ?>');
          </script>
        <?php } } } ?>
        
    <?php } ?>
    </div>
</div>
    <?php if ($boutique_config['DediPass']['en_ddp'] == "true"){ ?>
<br>
<div class="container">
        <hr>
        <script src="//api.dedipass.com/v1/pay.js"></script>
        <center><h3>Un paiement par le service DediPass est aussi possible : </h3>
        <p style="color: red"><strong>Attention, vous devez être connecté pour recevoir vos <?= $Serveur_Config['Serveur_money']; ?>s.</strong></p></center>
        <div data-dedipass="<?php $boutique_config['DediPass']['public_key']; ?>" data-dedipass-custom=""></div>
    <?php } ?>
    <?php if ($boutique_config['DediPass']['en_ddp'] != "true" && $boutique_config['PayPal']['en_paypal'] != "true"){ ?>
    <br>
    <p style="text-align: center;">Aucune offre n'est pour le moment proposée.</p>
    <?php } ?>
</div>



