<?php global $servers, $n_serveurs, $news, $content_photo; ?>
<div id="fh5co-hero" >
    <div class="overlay" style="background-image: url(<?php echo Manager::makeGetImageLink($Serveur_Config['bg']); ?>)"></div>
    <div class="container">
      <div class="col-md-8 col-md-offset-2">
        <div class="text wow fadeInUp " data-wow-duration="2s" data-wow-delay="0.2s" > 
          <div class="content-editable" data-apilink="<?php echo LINK . "API/"; ?>" data-page="accueil"><?php echo $content_photo; ?></div>
        </div>
      </div>
    </div>
</div>
<div class="container">
<?php if ($this->registered_modules_manager != null){ 
  try {
    $this->registered_modules_manager->renderModules($controleur_def, (isset($_SESSION['user']) && isset($_SESSION['editing_mode']) && $_SESSION['editing_mode'] && $_SESSION['user']->getLevel() >= 4)); 
  } catch (\DiamondException $e) {
    $controleur_def->addError($e->getCode());
  }
}
?>
</div>
<?php  if (isset($Serveur_Config['popup_accueil']) && isset($Serveur_Config['text_popup_accueil']) && $Serveur_Config['popup_accueil']){ ?>
        <div id="modalPopUpAccueil" class="modal fade">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title">Information</h4>
                    </div>
                    <div class="modal-body">
                        <p><?= htmlspecialchars_decode($Serveur_Config['text_popup_accueil']); ?></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
                    </div>
                </div>
            </div>
        </div>
        <script type="text/javascript"> $(document).ready(function(){ $("#modalPopUpAccueil").modal('show'); }); </script>
<?php } ?>