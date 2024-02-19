<div id="fh5co-page-title" style="background-image: url(<?php echo Manager::makeGetImageLink($Serveur_Config['bg']); ?>)">
  <div class="overlay"></div>
  <div class="text">
    <h1>Contact</h1>
  </div>
</div>
<div class="content-container">
  <h1>Contact</h1>
  <p>Avec ce formulaire, vous pouvez nous envoyer un message, nous vous repondrons par mail.
  <?php if ($Serveur_Config['en_support']){ ?>
    <br> En cas de demandes intimmement liées à votre jeu sur le serveur, ou au service commercial, utilisez plutôt le support.
  <?php } ?>
  </p>
  <br />
  <div style="width: 67%; margin-left: 5%;">
    <form method="POST">
        <div class="row control-group">
            <div class="form-group col-xs-12 floating-label-form-group controls">
                <label>Votre nom</label>
                <input type="text" class="form-control" placeholder="Votre Nom" name="name" id="name" required data-validation-required-message="Merci d'entrer votre nom.">
                <p class="help-block text-danger"></p>
            </div>
        </div>
        <div class="row control-group">
            <div class="form-group col-xs-12 floating-label-form-group controls">
                <label>Email</label>
                <input type="email" class="form-control" placeholder="C'est sur celle-ci que vous recevrez la réponse." name="email" id="email" required data-validation-required-message="Merci de preciser votre adresse mail.">
                <p class="help-block text-danger"></p>
            </div>
        </div>
        <div class="row control-group">
            <div class="form-group col-xs-12 floating-label-form-group controls">
                <label>Message</label>
                <textarea rows="5" id="message" name="message"></textarea>
                <p class="help-block text-danger"></p>
            </div>
        </div>
            <button type="submit" class="btn btn-custom btn-lg">Envoyer</button>
    </form>
  </div>
  
</div>
</div>

