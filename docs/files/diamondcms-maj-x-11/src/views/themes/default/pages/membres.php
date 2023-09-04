<?php global $comptes; ?>
<div id="fh5co-page-title" style="background-image: url(<?php echo Manager::makeGetImageLink($Serveur_Config['bg']); ?>)">
  <div class="overlay"></div>
  <div class="text">
    <h1>Nos membres</h1>
  </div>
</div>
<div class="content-container">
    <?php if (empty($comptes)): ?>
        <p class="text-center"><em>Aucun membre n'est pour l'instant inscrit et disponible.</em></p>
    <?php else: ?>
        <?php foreach ($comptes as $c): ?>
            <div class="col-sm-2">
                <div class="divprofileimg">
                    <p class="text-center"><img src="<?= LINK; ?>getprofileimg/<?php echo $c->getPseudo(); ?>/240" alt="<?php echo $c->getPseudo(); ?>" width="70%" class="rounded-circle profileimg"></p>
                </div>
                <h2 class="pseudo text-center" style="margin-bottom: 0;"><?php echo $c->getPseudo(); ?></h2>
                <p class="role text-center" style="margin-top: 0;"><?php echo $c->getRoleName(); ?></p>
                <p class="text-center">
                    <a href="<?php echo LINK . 'compte/' ?><?php echo $c->getPseudo(); ?>"><button class="btn btn-sm btn-custom">Voir le profil</button></a>
                </p>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
    <div class="rows"></div>
</div>