<div id="Staff" class="wow">
  <div class="container-fluid">
    <div class="rows">
      <h1 class="text-center title">Qui sommes nous ?</h1>
      <p class="text-center">Faites connaissance avec notre équipe de joueurs proffesionnels, prêts à vous aider en toute circonstance !</p>
        <br />
        <?php
        if (!empty($this->staff)){
          $i = 0;
          foreach ($this->staff as $staffs) {?>
            <div class="col-sm-3">
              <p class="text-center"><img class="rounded-circle" src="<?= LINK; ?>getprofileimg/<?php echo $staffs['pseudo']; ?>/140" alt="Un membre du staff" width="140" height="140"></p>
              <h2 class="text-center"><?php echo $staffs['pseudo']; ?></h2>
              <p class="text-center"><?php echo $staffs['role_name']; ?></p>
              <p class="text-center"><a href="<?php echo LINK . 'compte/' ?><?php echo $staffs['pseudo']; ?>"><button type="button" class="btn btn-custom">Voir le profil</button></a></p>
            </div><!-- /.col-lg-4 -->
            <?php $i = $i+1;
            if ($i == 4){ $i=0; ?>
              <div class="col-sm-12"><br><br></div>
              <?php }
       } }else { ?>
        <p class="text-center">Aucun membre du staff n'a encore été enregistré !</p>
        <?php } ?>
    </div>
  </div>
</div>