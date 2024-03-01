<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <link rel="stylesheet" type="text/css" href="<?php echo LINK;?>installation/assets/css/bootstrap.css"/>
		    <link rel="stylesheet" type="text/css" href="<?php echo LINK;?>installation/assets/css/sources.css" />
        <title>Installation de Diamond CMS</title>
    </head>

    <body>
    <script src="//<?= $_SERVER['HTTP_HOST']; ?><?= WEBROOT; ?>installation/assets/js/jquery-3.1.1.js"></script>
    <style>
      h1, h2{
        text-align: center;
      }
      h1{
        font-size: 3em;
        color: #197d62;
      }
      h2{
        font-family: "Raleway-Light";
        font-size: 1.5em;
      }
      p{
        font-family: "Raleway-Light";
      }
    </style>
      <br>
    <center><?php if (!defined("DIAMOND_BCK") || !DIAMOND_BCK) { ?><br /><br /><?php } ?><br />
      <img class="img-responsive" width="750" style="margin: 0;" src="<?php echo LINK;?>/installation/assets/img/diamondcms.png">
      <h1>Le bon fonctionnement de ce site <br /> est fièrement assuré par DiamondCMS !</h1>
      <h2>Version <?= DCMS_VERSION; ?>. Ce CMS est 100% gratuit et disponible <a href="https://aldric-l.github.io/DiamondCMS/">ici</a> !<h2>
      <?php if (defined("DIAMOND_BCK") && DIAMOND_BCK) { ?>
      <div style="width: 50%; margin: auto;">
      <hr style="width: 75%;">
      <p><strong><span style="color: red;">Votre site internet a été bloqué par DiamondCMS pour non-respect des conditions générales d'utilisation du service</span> (par exemple en matière de footer). <br><br>Contactez l'équipe de développement par GitHub pour débloquer la situation.</strong></p>
      
      <hr style="width: 75%;">
      
      <p class="text-center">Clée de déchiffrement : <input type='file' style="display: inline;" id="scriptLoader" /></p>
      <p class="text-center" id="import_status"></p>

      </div>
      <?php } ?>
      <br><br>
      </center>
      <?php if (defined("DIAMOND_BCK") && DIAMOND_BCK) { ?>
        <script>
        async function digestMessage(message) {
          const msgUint8 = new TextEncoder().encode(message);                          
          const hashBuffer = await crypto.subtle.digest('SHA-256', msgUint8);         
          const hashArray = Array.from(new Uint8Array(hashBuffer));
          const hashHex = hashArray.map((b) => b.toString(16).padStart(2, '0')).join('');
          return hashHex;
        }
        var txt_buffer;
        if (typeof(diamondmasterpassword) == "undefined")
          var diamondmasterpassword = "null";
        $("#scriptLoader").change(function() {
            $("#import_status").html("Chargement du fichier...");
            var fileReader = new FileReader();
            //console.log(this.files[0], this.files[0].name.substr(-7), this.files[0].type);
            if (this.files[0].name.substr(-5) != ".dkey"){
                $("#import_status").html('<span style="color: red;"><strong>Erreur :</strong> Le fichier chargé n\'est pas de la bonne extension ou du bon type MIME.</span>');
                return;
            }
            var rawfile = this.files[0];    
            var fdata = new FormData();
            fdata.append( "unlock_key", rawfile );
            fdata.append( "password", diamondmasterpassword );
            fileReader.readAsText(this.files[0]);
            fileReader.onload = function(fileEvent) {
                txt_buffer = fileReader.result.split("\r\n");
                digestMessage(fileReader.result)
                .then((digestHex) => {
                  if (digestHex !== "5647c6cf40825be4f0db6b2acf92cba5eb91a3c1a092ffdfe2fb348c74107f4f"){
                    $("#import_status").html('<span style="color: red;"><strong>Erreur :</strong> La clée semble invalide.</span>');
                    return;
                  }
                  var to_send = {"unlock_key" : rawfile};
                  $.ajax({
                      type : 'POST',
                      data : fdata,
                      processData : false,
                      contentType : false,
                      dataType : false,
                      success: function (result) { 
                          console.log("Success !");
                          if (result != "Success"){
                            $("#import_status").html('<span style="color: red;"><strong>Erreur :</strong> Le processus de décryptage n\'a pas fonctionné.</span>');
                          }else {
                            $("#import_status").html('<span style="color: green;"><strong>Succès :</strong> Le processus de décryptage semble s\'être terminé avec succès.</span>');
                          }
                      },
                      error: function() {
                        $("#import_status").html('<span style="color: red;"><strong>Erreur :</strong> Le processus de décryptage n\'a pas fonctionné, la requête a échoué.</span>');
                      }
                  });
                  //console.log(digestHex)
                });
                
            }
        });
        
        </script>
      <?php }else { ?>
        <script>
          $(document).ready(function (e){
            $.ajax({type : 'POST',data : { bck: ""},dataType : 'html',});
          })
        </script>
      <?php } ?>
        
        <!-- LIB JavaScript -->
        <script src="<?= LINK; ?>installation/assets/js/font_awesome.js"></script>
        <script src="<?= LINK; ?>installation/assets/js/bootstrap.js"></script>
        <script src="<?= LINK; ?>installation/assets/js/global.js"></script>
    </body>
</html>