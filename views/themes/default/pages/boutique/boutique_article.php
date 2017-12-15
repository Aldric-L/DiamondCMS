<?php global $article, $reviews; ?>
<div id="fh5co-page-title" style="margin-bottom: 0;">
  <div class="overlay"></div>
  <div class="text">
    <h1><a class="no" href="<?php echo $Serveur_Config['protocol']; ?>://<?php echo $_SERVER['HTTP_HOST'] . WEBROOT . 'boutique/' ?>">Boutique </a>-> <?php echo $article['name']; ?></h1>
  </div>
</div>
<br />
<!-- Page Content -->
<div class="container">

      <div class="row">

        <div class="col-lg-3">
          <!--<h1 class="my-4">Panier</h1>-->
          <div class="list-group">
            <a href="#" style="background-color: #197d62; background-color: #197d62; font-family: Bree serif;" class="list-group-item active">Ajouter au panier</a>
            <a href="#" class="list-group-item">Laisser un avis</a>
            <!--<a href="#" class="list-group-item">Category 3</a>-->
          </div>
        </div>
        <!-- /.col-lg-3 -->

        <div class="col-lg-9">

          <div class="card mt-4">
            <img class="card-img-top img-fluid" width="300" hight="300" src="<?php echo $Serveur_Config['protocol']; ?>://<?= $_SERVER['HTTP_HOST']; ?><?=WEBROOT; ?>views/uploads/img/boutique/<?php echo $article['img']; ?>.png" alt="">
            <img class="card-img-top img-fluid" width="540" hight="300" src="<?php echo $Serveur_Config['protocol']; ?>://<?= $_SERVER['HTTP_HOST']; ?><?=WEBROOT; ?>views/uploads/img/boutique/<?php echo $article['background']; /*http://placehold.it/540x300*/?>.png" alt="">
            <div class="card-body">
              <h2 class="card-title"><?php echo $article['name']; ?></h2>
              <h4><span class="bree-serif">Prix : <strong><?php echo $article['prix']; ?><?php echo $Serveur_Config['money']; ?></strong> <small>(ttc)</small> </span></h4>
              <p class="card-text"><?php echo $article['description']; ?></p><br>
            </div>
          </div>
          <!-- /.card -->

          <div class="card card-outline-secondary my-4">
            <div class="card-header">
              <h3>Avis sur l'article - Note moyenne : 
                <?php $total = 0; if (!empty($reviews)){
                for ($i = 0; $i < sizeof($reviews); $i++) { 
                  $total = $total+$reviews[$i]['etoiles']; 
                } 
                echo ($total) /(sizeof($reviews)*5)*5; 
              }else {
                echo "Aucun avis.";
              } ?></h3>
            </div>
            <div class="card-body">
                <?php foreach ($reviews as $r){ ?>
                    <p><span class="text-warning"><?php for ($i = 0; $i < $r['etoiles']; $i++) { echo "&#9733"; } for ($i = $r['etoiles']; $i < 5; $i++) { echo "&#9734;"; } /* Etoile &#9733; Etoile vide : &#9734;*/ ?></span> <?php echo $r['etoiles']; ?> étoiles<br/>
                    <?php echo $r['text']; ?></p>
                    <small class="text-muted">Envoyé par <?php echo $r['user']; ?> le <?php echo date("d/m/Y", strtotime($r['date'])); ?></small>
                    <hr>
                <?php } ?>
              <a href="#" class="btn btn-success">Leave a Review</a>
            </div>
          </div>
          <!-- /.card -->

        </div>
        <!-- /.col-lg-9 -->

      </div>

    </div>
    <!-- /.container -->
<style>
a.no {
  color: #197d62;
  text-decoration: none;
}
</style>