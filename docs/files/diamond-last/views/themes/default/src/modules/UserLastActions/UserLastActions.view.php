<?php 
$not_user = !(isset($_SESSION['user']) && $_SESSION['user']->getId() === $user->getId());?>

<?php if (!$not_user){ ?>
  <h3 class="text-center">Vos dernières interventions :</h3>
<?php }else { ?>
  <h3 class="text-center">Ses dernières interventions :</h3>
<?php } ?>
<div class="container-fluid">
  <div class="rows">
    <div class="col-lg-2"></div><!-- ./col-lg-2 -->
    <div class="col-lg-8">
            <?php if (!empty($lastactions)){ ?>
              <br />
              <?php
                foreach ($lastactions as $key => $lastaction) { ?>
                  <p><strong class="bold">Le <?php echo $lastactions[$key]['date_com']; ?> sur le sujet "<?php echo $lastactions[$key]['id_post']['titre_post']; ?>" par <?php echo $lastactions[$key]['id_post']['user']; ?></strong><br />
                  <?php echo $lastactions[$key]['content_com']; ?></p>
                  <p class="text-right"><a href="<?php echo LINK . 'forum/com/'. $lastactions[$key]['id_post']['id']; ?>/">Retourner sur le sujet...</a></p><br />
                <?php } ?>
            <?php }else { ?>
              <?php if (!$not_user){ ?>
                <h4 class="text-center text-warning">Vous n'avez pas encore participé à un sujet !</h4>
              <?php }else { ?>
                <h4 class="text-center text-warning"><?php echo $user->getPseudo(); ?> n'a pas encore participé à un sujet !</h4>
              <?php } ?>
            <?php } ?>
    </div>
  </div>
</div>
<style>
.last {
  width: 80%;
  margin: auto;
}
</style>