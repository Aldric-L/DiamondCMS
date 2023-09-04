<?php global $config_serveurs; ?>
<br><br>
<h1 class="text-gray-800 text-center">Modifier la configuration d'un serveur de jeu</h1>
<p class="text-center">Vous pouvez, avant de modifier la configuration, utiliser le <a href="<?php echo LINK; ?>Diamond-ServerLink/diagnostic/">nouvel outil de diagnostic</a> de votre installation proposé par DiamondCMS.</p>
<hr style="max-width: 60%;">
<p class="text-center">Avant de poursuivre, vous devez choisir le serveur à configurer.<br></p>
<p class="text-center">
<?php
    foreach ($config_serveurs as $key => $serv) { ?>
        <a href="<?php echo LINK . "Diamond-ServerLink/config/" . $serv['id'] ; ?>"><button class="btn btn-custom btn-lg"><?php echo $serv['name']; ?></button></a>
<?php } 
    if (empty($config_serveurs)) { ?>
    <p class="text-center"><em>Aucun serveur n'a pour l'instant été configuré.</em></p>
<?php } ?>
</p>
