<section id="ServerState" data-link="<?= LINK . "api/serveurs/get/serverState/"; ?>" data-baseLink="<?= LINK; ?>">
  <div class="container-fluid">
    <div class="rows">
      <h1 class="text-center title">Etat du réseau <?php echo $GLOBALS['Serveur_Config']['Serveur_name']; ?></h1><br>
      <h3 id="loader" style="display: block;" class="text-center title"><img src="<?= LINK; ?>getimage/gif/-/ajax-loader" alt="loading" /> Chargement en cours...</h5>
      <div id="serverStateRenderBlock">
      </div>
      <div id="serverState-right-server" class="request_depend no_show">
          <div class="col-sm-2"></div>
          <div class="col-sm-4" style="padding: 0;"><div style="margin: auto; text-align: center;vertical-align: middle;">
            <h2 class="text-center name_serveur"></h2>
            <p class="desc_serveur"></p>
            <p class="slots_serveur"></p>
            <p class="etat_serveur"></p>
            <p><a class="btn btn-custom link_serveur" href="" role="button">Voir plus &raquo;</a></p>
          </div></div>
          <div class="col-sm-4" style="padding: 0;"><center><img class="img_serveur img-rounded img-centered img-responsive" style="vertical-align: middle;max-height:225px;" src="" alt=""></center></div>
          <div class="col-sm-2"><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /></div><br />
      </div>
      <div id="serverState-left-server" class="request_depend no_show">
          <div class="col-sm-2"></div>
          <div class="col-sm-4" style="padding: 0;"><center><img class="img_serveur img-rounded img-centered img-responsive" style="vertical-align: middle;max-height:225px;" src="" alt=""></center></div>
          <div class="col-sm-4" style="padding: 0;"><div style="margin: auto; text-align: center;vertical-align: middle;">
            <h2 class="text-center name_serveur"></h2>
            <p class="desc_serveur"></p>
            <p class="slots_serveur"></p>
            <p class="etat_serveur"></p>
            <p><a class="btn btn-custom link_serveur" href="" role="button">Voir plus &raquo;</a></p>
          </div></div>
          <div class="col-sm-2"><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /></div><br />
      </div>
    </div>
  </div>
</section>
