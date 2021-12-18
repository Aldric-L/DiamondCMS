<?php global $erreur, $commande, $tasks, $tasks_man, $Serveur_Config, $tasks_done, $article; ?>
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
            <?php //foreach ($tasks as $k => $task){ ?>
                <?php if (defined("DServerLink") && DServerLink && !empty($tasks)){     ?>
                <p><em>Interface de consultation pour administrateur. Les commandes automatiques ne peuvent être exécutées que par le client.</em></p>
                <?php }else if (!defined("DServerLink") || !DServerLink) { ?>
                    Le lien automatique entre la boutique et les serveurs n'est pas activé, alors que des tâches automatiques ont été prévues. Contactez un administrateur pour recevoir manuellement votre dû.<br>
                <?php } ?>
            <?php //} ?>     
            </p>
            <?php if ($commande['success'] == '1' || $commande['success'] == true){ ?>
            <p style="text-align: center;">
                <h4 class="bold">Cette commande a été réceptionnée avec succès, vous pouvez quitter cette page.</h4>    
            </p>
            <?php } ?>
        </div>
    </div>
</div>
<style>
.navbar{
    display: none;
}
footer {
    display: none;
}
</style>