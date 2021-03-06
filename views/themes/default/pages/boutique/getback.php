<?php global $erreur, $commande, $tasks, $tasks_man, $Serveur_Config, $tasks_done, $article; ?>
<?php if (!empty($erreur)){ ?>
<div id="emptyServer">
    <br />
    <h1>Erreur !</h1>
    <h2>Une erreur grave est survenue :</h2>
    <p style="text-align: center; padding-left: 15px; padding-right: 15px;"><?= $erreur; ?></p>
    <br /><br /><br />
</div><br /><br /><br />
<?php die; } ?>

<div id="fh5co-page-title" style="background-image: url(<?= LINK; ?>views/uploads/img/<?php echo $Serveur_Config['bg']; ?>)">
  <div class="overlay"></div>
  <div class="text">
    <h1><a href="<?php echo LINK . 'boutique/' ?>">Boutique </a>-> Récupération des articles payés</h1>
  </div>
</div>
<br />
<!-- Page Content -->
<div class="container">
    <div class="rows">
        <div class="col-lg-12">
            <h1 class="bold">Merci pour votre achat !</h1>
            <p>Bienvenue sur notre interface sécurisée de réception de votre commande. Nous tenons à vous remercier vivement pour votre achat et pour votre confiance. <br>
            <!--Nous vous prions, pour le bon fonctionnement de cette opération, de ne pas quitter cette page, tant que vous n'avez pas récupéré totalement votre lot.<br>-->
            Il vous est possible de ne pas récupérer immédiatement votre commande, dans ce cas, enregistrez l'adresse de cette page : celle-ci reste accessible jusqu'à la réception de votre dû.</p>
            <p><strong>Commande réalisée le <?= $commande['date']; ?></strong> -
            <em>Cette page fait office de reçu pour votre achat de l'article "<?= $article['name']; ?>" au prix de <?= $commande['price']; ?> <?= $Serveur_Config['Serveur_money']; ?>(s).</em></p>
            <hr>
            <h4>Voici les tâches qui ont été réalisées :</h4>
            <?php if (empty($tasks_done)){ ?>
                <p>Pour le moment, vous n'avez terminé aucune tâche.</p>
            <?php }else { ?>
            <ul>
            <?php foreach ($tasks_done as $task){ ?>
                <?php if (defined("DServerLink") && DServerLink && !$task['cmd']['is_manual']){     ?>
                    <li>Exécution d'une commande sur le serveur <?= $task['cmd']['server_game']; ?> nommé <?= $task['cmd']['server_name']; ?><br>
                <?php }else if ($task['cmd']['is_manual']) { ?>
                    <li>Exécution d'une tâche manuelle par un administrateur.<br>
                <?php } ?>
                <?php if ($task['stopped']){ ?>                
                    <span style="color: black;"><strong>Suspendue par un administrateur : </strong>le <?= $task['date_done']; ?></span> <em>Motif : <?= $task['stopped_reason']; ?></em>
                <?php }else { ?>
                    <span style="color: #197d62;"><strong>Terminée avec succès : </strong>le <?= $task['date_done']; ?></span>
                <?php } ?>
                </li>
            <?php } } ?>
            </ul>
            <hr>
            <h4>Voici les tâches automatiques qu'il nous faut réaliser :</h4>
            <?php if (empty($tasks)){ ?>
                <p>Il semblerait qu'il n'y ait rien à éxecuter. En cas de problème, nous vous conseillons de réaliser une capture d'écran de cette page et de contacter notre support.</p>
            <?php }else { ?>
            <ol>
            <?php foreach ($tasks as $task){ ?>
                <?php if (defined("DServerLink") && DServerLink && !$task['cmd']['is_manual']){     ?>
                    <li>Exécution d'une commande sur le serveur <?= $task['cmd']['server_game']; ?> nommé <?= $task['cmd']['server_name']; ?><br>
                    <?php if ($task['cmd']['connexion_needed'] == '1'){ ?>
                        <span style="color: red;"><strong>Attention: </strong>Votre présence sur le serveur est indispensable pour l'exécution de cette tâche.</span>
                    <?php }else { ?>
                        Votre présence sur le serveur n'est pas nécessaire pour l'éxectution de cette tâche.
                    <?php } ?>
                <?php }else if ($task['cmd']['is_manual']) { ?>
                    <li>Exécution d'une tâche manuelle par un administrateur.<br>
                <?php } ?>
                </li>
            <?php } } ?>
            </ol>
            <hr>
            <h4>Voici les tâches manuelles que notre équipe devra réaliser :</h4>
            <?php if (empty($tasks_man)){ ?>
                <p>Bonne nouvelle ! Il semblerait qu'aucune tâche manuelle n'ait été prévue. Vous allez donc pouvoir réceptionner votre article sans attendre.</p>
            <?php }else { ?>
            <ul>
            <?php foreach ($tasks_man as $task){ ?>
                <?php if ($task['cmd']['is_manual']) { ?>
                    <li>Exécution d'une tâche manuelle par un administrateur.<br>
                <?php } ?>
                </li>
            <?php } } ?>
            </ul>
            <hr>
            <p style="text-align: center;">
            <?php if (!empty($tasks)){ ?>
                <em>Comme votre commande comporte des tâches automatiques, vous pouvez initier l'exécution de celles-ci :</em><br>
            <?php } ?>
            <?php if (!empty($tasks_man)){ ?>
                <em style="color: red;">Comme votre commande comporte des tâches manuelles, vous devrez attendre qu'un administrateur les exécute pour réceptionner votre achat.</em>
            <?php } ?>
            <?php if (!empty($tasks) || !empty($tasks_man)){ ?>
                <br><br>
                <?php } ?>
            <?php foreach ($tasks as $k => $task){ ?>
                <?php if (defined("DServerLink") && DServerLink){     ?>
                    <button type="button" style="border-color: #197d62; background-color: #197d62;" class="btn btn-success task" data="<?= $k+1; ?>">Exécuter la tache <?= $k+1; ?></button>
                <?php }else { ?>
                    Le lien automatique entre la boutique et les serveurs n'est pas activé, alors que des tâches automatiques ont été prévues. Contactez un administrateur pour recevoir manuellement votre dû.
                <?php } ?>
            <?php } ?>     
            </p>
            <?php if ($commande['success'] == '1' || $commande['success'] == true){ ?>
            <p style="text-align: center;">
                <h4 class="bold">Cette commande a été réceptionnée avec succès, vous pouvez quitter cette page.</h4>    
            </p>
            <?php } ?>
        </div>
    </div>
</div>
<?php foreach ($tasks as $k => $task){ ?>
<div id="task_<?= $k+1; ?>" class="modal fade">
  <div class="modal-dialog">
      <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h3 class="modal-title text-center">Exécution de la tâche N°<?= $k+1; ?></h3>
            </div>
            <?php if ($task['cmd']['connexion_needed'] == '1'){ ?>
                <div class="modal-body">
                    <h5><strong>Attention: </strong>Votre présence sur le serveur est indispensable pour l'exécution de cette tâche.</h5>
                    <p> <span class="bold" style="color: red;">Attention !</span> Pour éxecuter une tache, vous devez absolument vérifier - en appuyant sur le bouton "Tester la connexion" - que le serveur est disponible.</p>
                    <hr>
                    <h5>Merci d'inscrire votre pseudo en-jeu sur le serveur <?= $task['cmd']['server_name']; ?> :</h5>
                    <p><span class="bold" style="color: red;">Attention !</span> Ce pseudo doit être exactement (au caractère près) le même que celui en-jeu.</p>
                    <br>
                    <div class="form-inline">
                        <div class="form-group mx-sm-12">
                            <label class="sr-only">Pseudo en jeu</label>
                            <input type="text" class="form-control" id="psd_<?= $k+1; ?>" placeholder="Pseudo en jeu">
                        </div>
                        <button data-backdrop="false" id="psdbtn_<?= $k+1; ?>" style="border-color: #197d62; background-color: #197d62;" class="btn btn-primary mb-2 validate_psd" data="<?= $k+1; ?>">Valider</button>
                    </div>
                    <p style="display:none;" id="disp_error_<?= $k+1; ?>"><span class="bold" style="color: red;">Erreur !</span> <span id="error_<?= $k+1; ?>"></span></p>
                    <p style="display:none;" id="disp_success_<?= $k+1; ?>"><span class="bold" style="color: #197d62;">Succès !</span> <span id="success_<?= $k+1; ?>"></span></p>
                </div>        
            <?php }else { ?>
                <div class="modal-body">
                    <h5>Votre présence sur le serveur n'est pas obligatoire pour l'exécution de cette tache.</h5>
                    <p>Toutefois, il vous est fortement conseillé d'être connecté au serveur de jeu pour vérifier le bon déroulement de l'opération.</p>
                    <p> <span class="bold" style="color: red;">Attention !</span> Pour exécuter une tache, vous devez absolument vérifier - en appuyant sur le bouton "Tester la connexion" - que le serveur est disponible.</p>
                    <hr>
                    <h5>Merci d'inscrire votre pseudo en-jeu sur le serveur <?= $task['cmd']['server_name']; ?> :</h5>
                    <p><span class="bold" style="color: red;">Attention !</span> Ce pseudo doit être exactement (au caractère près) le même que celui en-jeu.</p>
                    <br>
                    <div class="form-inline">
                        <div class="form-group mx-sm-12">
                            <label class="sr-only">Pseudo en jeu</label>
                            <input type="text" class="form-control" id="psd_<?= $k+1; ?>" placeholder="Pseudo en jeu">
                        </div>
                        <button data-backdrop="false" id="psdbtn_<?= $k+1; ?>" style="border-color: #197d62; background-color: #197d62;" class="btn btn-primary mb-2 validate_psd" data="<?= $k+1; ?>">Valider</button>
                    </div>
                    <p style="display:none;" id="disp_error_<?= $k+1; ?>"><br><span class="bold" style="color: red;">Erreur !</span> <span id="error_<?= $k+1; ?>"></span></p>
                    <p style="display:none;" id="disp_success_<?= $k+1; ?>"><br><span class="bold" style="color: #197d62;">Succès !</span> <span id="success_<?= $k+1; ?>"></span></p>
                </div>            
            <?php } ?>
        <div class="modal-footer">
          <button type="button" id="<?= $k+1; ?>" class="btn btn-warning test" 
          data-origin="<?php echo LINK . 'boutique/getback/' . $commande['uuid'] . "/test/" . $task['id'] . '/'; ?>" disabled>Tester la connexion</button>
          <button type="button" style="border-color: #197d62; background-color: #197d62;" class="btn btn-success get" id="getbtn_<?= $k+1; ?>" data-id="<?= $k+1; ?>" disabled>Executer</button>
          <button type="button" data-backdrop="false" class="btn btn-default close_mod" id="close_mod_<?= $k+1; ?>" data="<?= $k+1; ?>">Fermer</button>
        </div>
      </div>
  </div>
</div>
<?php } ?>     