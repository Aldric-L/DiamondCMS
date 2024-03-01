<?php global $faq, $config;  ?>
<div class="container-fluid">
    <h1 class="h3 mb-2 text-gray-800">Configuration de DiamondCMS - Page FAQ</h1>
    <p class="mb-4">Sur cette page, vous pouvez inscrire des questions (et leurs réponses) auxquelles sont souvent confrontés vos utilisateurs.</p>
    <div class="row">
        <div class="col-lg-6">
            <div class="card shadow lg-6">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-custom">Modifier la page</h6>
                </div>
                <div class="card-body">
                    <?php if ($config['en_faq']){ ?>
                        <p><strong>Pour désactiver cette page, cliquez-ici : </strong>
                        <button 
                            data-api="<?= LINK; ?>api/" data-module="configadmin/" data-verbe="set" data-func="enfac" data-reload="true"
                            class="ajax-simpleSend btn btn-danger btn-md">Désactiver
                        </button></p>
                    <?php }else { ?>
                        <p><strong>Pour activer cette page, cliquez-ici : </strong>
                        <button 
                            data-api="<?= LINK; ?>api/" data-module="configadmin/" data-verbe="set" data-func="enfac" data-reload="true"
                            class="ajax-simpleSend btn btn-success btn-md">Activer
                        </button></p>
                    <?php } ?>
                    <hr>
                    <form id="facform" method="POST">
                            <div class="form-group">
                                <label>Nouvelle question :</label>
                                <input class="form-control" type="text" name="question" id="question">
                                <p class="help-block text-danger"></p>
                            </div>
                            <div class="form-group">
                                <label>Nouveau contenu :</label>
                                <textarea rows="10" id="reponse" class="form-control" name="reponse"></textarea>
                                <p class="help-block text-danger"></p>
                            </div>
                        <p style="text-align: right">
                        <button type="button" class="btn btn-custom ajax-simpleSend" 
                        data-api="<?= LINK; ?>api/" data-module="configadmin/" data-verbe="set" data-func="facquestion" data-tosend="#facform" data-useform="true" data-reload="true"
                        >Ajouter</button>
                        </p>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card shadow lg-6">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-custom">Questions enregistrées</h6>
                </div>
                <div class="card-body">
                    <?php if (empty($faq)){ ?>
                        <p>Aucune question n'a été enregistrée pour le moment.</p>
                    <?php }else { 
                        foreach ($faq as $f){ ?>
                            <div id="line_<?= $f['id']; ?>">
                                <h5><strong>Question :</strong> <?= $f['question']; ?></h5>
                                <p><strong>Réponse :</strong> <?= $f['reponse']; ?></p>
                                <p>Pour supprimer cette question : 
                                <button type="button" class="btn btn-danger btn-sm ajax-simpleSend" 
                                data-api="<?= LINK; ?>api/" data-module="configadmin/" data-verbe="set" data-func="delfacquestion" data-tosend="id=<?= $f['id']; ?>" data-reload="true"
                                >Cliquez-ici</button></p>
                                <hr>
                            </div>
                        <?php }
                    } ?>
                </div>
            </div>
        </div>
    </div>
</div>
