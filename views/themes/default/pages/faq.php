<div id="fh5co-page-title">
  <div class="overlay"></div>
  <div class="text">
    <h1>Foire aux questions</h1>
  </div>
</div>
<div class="content-container">
  <?php global $faqs;
  if (!empty($faqs)){ ?>
    <h1 class="bree-serif">Questions fréquament posées sur le serveur</h1><br/>
    <div class="container">
    <?php foreach ($faqs as $faq) {
      echo "<h2 style=\"margin-left: 0px\">" . $faq['question'] . "</h2>";
      echo "<p>" . $faq['reponse'] . "</p><hr>";
    }?>
  </div>
  <?php }else {
    echo '<p class="text-center text-warning">Aucune question n\'a encore été enregistrée !</p>';
  } ?>
</div>
