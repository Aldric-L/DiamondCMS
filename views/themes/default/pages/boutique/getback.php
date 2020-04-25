<?php global $erreur, $commande, $tasks, $Serveur_Config; ?>
<?php if (!empty($erreur)){ ?>
<div id="emptyServer">
    <br />
    <h1>Erreur !</h1>
    <h2>Une erreur grave est survenue :</h2>
    <p style="text-align: center; padding-left: 15px; padding-right: 15px;"><?= $erreur; ?></p>
    <br /><br /><br />
</div><br /><br /><br />
<?php die; } ?>

<div id="fh5co-page-title" style="margin-bottom: 0;">
  <div class="overlay"></div>
  <div class="text">
    <h1><a class="no" href="<?php echo $Serveur_Config['protocol']; ?>://<?php echo $_SERVER['HTTP_HOST'] . WEBROOT . 'boutique/' ?>">Boutique </a>-> Récupération des articles payés</h1>
  </div>
</div>
<style>
a.no {
  color: #197d62;
  text-decoration: none;
}
</style>
<br />
<!-- Page Content -->
<div class="container">
    <div class="rows">
        <div class="col-lg-12">
            <h1 class="bold">Merci pour votre achat !</h1>
            <p>Bienvenue sur notre interface sécurisée de réception de votre commande. Nous tenons à vous remercier vivement pour votre achat et pour votre confiance. <br>
            Nous vous prions, pour le bon fonctionnement de cette opération, de ne pas quitter cette page, tant que vous n'avez pas récupéré totalement votre lot.<br>
            Il est vous est possible de ne pas récuperer immédiatement votre commande, dans ce cas, enregistrez l'adresse de cette page : celle-ci reste accessible jusqu'à la réception de votre dû.</p>
            <p><strong>Commande réalisée le <?= $commande['date']; ?></strong></p>
            <hr>
            <h4>Voici les tâches qu'il nous faut réaliser :</h4>
            <?php if (empty($tasks)){ ?>
                <p>Il semblerait qu'il n'y ait rien à éxecuter. En cas de problème, nous vous conseillons de réaliser une capture d'écran de cette page et de contacter notre support.</p>
            <?php }else { ?>
            <ul>
            <?php foreach ($tasks as $task){ ?>
                <li>Execution d'une commande sur le serveur <?= $task['cmd']['server_game']; ?> nommé <?= $task['cmd']['server_name']; ?><br>
                <?php if ($task['cmd']['connexion_needed'] == '1'){ ?>
                    <span style="color: red;"><strong>Attention: </strong>Votre présence sur le serveur est indispensable pour l'éxecution de cette tâche.</span>
                <?php }else { ?>
                    Votre présence sur le serveur n'est pas nécessaire pour l'éxectution de cette tâche.
                <?php } ?>
                </li>
            <?php } } ?>
            </ul>
        </div>
    </div>
</div>