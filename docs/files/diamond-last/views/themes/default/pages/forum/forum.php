<?php global $content_explic; ?>
<div id="fh5co-page-title" style="background-image: url(<?php echo Manager::makeGetImageLink($Serveur_Config['bg']); ?>)">
  <div class="overlay"></div>
  <div class="text">
    <h1>Forum</h1>
  </div>
</div>
<div id="explic">
  <div class="content-editable" data-apilink="<?php echo LINK . "API/"; ?>" data-page="forum"><?php echo $content_explic; ?></div>
</div>
<div class="content-container-forum">
        <?php
          global $cats;
          if (!empty($cats)){
            foreach ($cats as $cat) {
              echo '<div id="f_cat"><h3 class="title">' . $cat['titre'] . "</h3></div>";
              if (!empty($cat['sous_cat'])){ ?> 
                <div class="container-fluid">
                  <div class="rows">
                  <div class="col-lg-12" style="margin-bottom: 10px;"></div>
                <?php foreach ($cat['sous_cat'] as $key => $scat['sous_cat']){ ?>
                  <a href=" <?php echo LINK . 'forum/' . str_replace(' ', '_', $scat['sous_cat']['titre']) . '/'; ?>">
                    <div class="col-lg-6">
                      <p style="font-size: 20px;"><i class="fa fa-comments-o fa-2x" aria-hidden="true"></i> <?= '| ' . $scat['sous_cat']['titre']; ?></p>
                    </div>
                    <div class="col-lg-6">
                      <p style="margin-top:8px; text-align: right; font-size: 20px;">Nombre de sujets : <?= $scat['sous_cat']['nb_sujets']; ?></p>
                    </div>
                    <?php if($key != sizeof($cat['sous_cat'])-1){ ?>
                    <div class="col-lg-12"><hr style="width: 94%;"></div>
                    <?php } ?>
                  </a>
                <?php } ?>
                </div>
                </div>
                <?php
              }else {
                  echo '<p id="f_sous_cat">Aucune sous-catégorie n\'a été trouvée !</p>';
              }
            }
          }else {
            echo '<p class="text-center">Aucune catégorie n\'a été trouvée !</p>';
          }
         ?>
  <br />
</div>
