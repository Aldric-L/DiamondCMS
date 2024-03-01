<?php global $money_get, $payement_type, $Serveur_Config, $errors; ?>
<div id="fh5co-page-title" style="background-image: url(<?php echo Manager::makeGetImageLink($Serveur_Config['bg']); ?>)">
  <div class="overlay"></div>
  <div class="text">
    <h1><a href="<?php echo LINK . 'boutique/' ?>">Boutique </a>-> Récupération des <?= $Serveur_Config['Serveur_money']; ?>s</h1>
  </div>
</div>
<br />
<!-- Page Content -->
<div class="container">
    <div class="rows">
    <?php if (!$errors){ ?>
        <div class="col-lg-12">
            <h1 class="bold">Merci pour votre achat !</h1>
            <p>Nous tenons à vous remercier vivement pour votre achat et pour votre confiance. Nous vous informons avoir bien pris en compte votre commande.<br></p>
            <?php if ($payement_type == "DDP"){ ?>
                <p><strong>Votre achat s'est déroulé avec succès via DediPass, votre compte a été crédité de <?= $money_get; ?> <?= $Serveur_Config['Serveur_money']; ?>(s)</strong></p>
            <?php }else if ($payement_type == "PP"){ ?>
                <p><strong>Votre achat s'est déroulé avec succès via PayPal, vous disposez désormais de <?= $_SESSION['user']->getMoney(); ?> <?= $Serveur_Config['Serveur_money']; ?>(s)</strong></p>
            <?php } ?>
            <p>Vous pouvez dès maintenant dépenser vos <?= $Serveur_Config['Serveur_money']; ?>s sur <a style="color: #197d62;" href="<?php echo LINK . 'boutique/' ?>">notre boutique !</p></p>
        </div>
    <?php }else { ?>
        <div class="col-lg-12">
            <h1 class="bold">Erreur !</h1>
            <p>Une erreur interne grave est survenue durant la transaction : contactez nous au plus vite pour régler le problème.<br>Veuillez nous excuser pour la gène occasionnée.</p>
        </div>
    <?php } ?>
        
    </div>
</div>