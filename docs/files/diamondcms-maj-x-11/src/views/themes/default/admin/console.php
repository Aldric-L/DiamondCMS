<div class="container-fluid">
    <h1 class="h3 mb-2 text-gray-800">Console de DiamondCMS</h1>
    <p class="mb-4">Cette page permet d'envoyer des commandes directement au noyau DiamondCMS sans passer par le GUI. <strong>Cette fonctionnalité s'adresse aux utilisateurs avancés, les sécurités étant désactivées par ce biais.</strong><br>
            <strong>Pour accèder à la documentation : <a href="https://github.com/Aldric-L/DiamondCMS/wiki/Boutique">Cliquez-ici</a></strong></p>
    <div class="row">
        <div class="col-lg-12">
            <p class="text-right">
              <button type="button" class="btn btn-icon-split btn-danger" data-toggle="modal" data-target="#importMod">
                <span class="icon text-white-50"><i class="fas fa-code"></i></span>
                <span class="text">Importer un script</span>
              </button>
            </p>
            <div id="console" class="shadow lg-12">
                <p></p>
            </div>
            <input type="text" id="command" data-api="<?= LINK; ?>api/" placeholder="Indiquer ici la commande à exécuter">
            <br>
            <div id="preview"></div>
        </div><!-- /.col-lg-12 -->
        <div class="dropdown mt-4" style="margin-left: auto;padding-right: 12px;">
          <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Récupérer les dernières actions
          </button>
          <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
            <a class="dropdown-item" href="#" id="getlogmod" data-toggle="modal" data-target="#logMod">Afficher les 50 derniers appels</a>
            <a class="dropdown-item" href="<?php echo LINK . "DiamondCMS/raw/log/api_set"; ?>">Télécharger le log (JSON)</a>
          </div>
        </div>
    </div>
</div>
<div id="importMod" class="modal fade">
  <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Validation requise !</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <p class="text-justify">L'importation d'un script est une action <strong>très dangeureuse.</strong> Les actions seront réalisées à partir de votre compte utilisateur qui dispose des autorisations du rôle <strong><?php echo $_SESSION['user']->getRoleName(); ?></Strong>. Les conséquences sont à priori irréversibles. N'exécutez jamais des scripts dont vous n'êtes pas sûr de la provenance, et qui n'émanent pas d'auteurs en qui vous avez toute confiance.</p>
            <hr>
            <p class="text-center"><input type='file' class="btn btn-custom btn" id="scriptLoader" /></p>
            <p id="import_status" class="text-center text-light"></p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
          <button type="button" id="exec" class="btn btn-danger" data-dismiss="modal" disabled>Exécuter le script</button>
        </div>
      </div>
  </div>
</div>

<div id="logMod" class="modal fade">
  <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Derniers appels API</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <div id="lastApiCalls">
              <img src="<?= LINK; ?>views/uploads/img/ajax-loader.gif" alt="loading" /> Chargement en cours...</h5>
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
        </div>
      </div>
  </div>
</div>