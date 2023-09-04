<?php 
if (!defined("DEFAULT_MAIL_TEMPLATE")){ 
ob_start(); ?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.1/css/all.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/4.5.6/css/ionicons.min.css" />
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <br />
                <br />
                {CONTENT}
            </div>
            <hr>
            <div class="col-lg-12">
                {UNSUBSECTION}
            </div>
        </div>
    </div>
</body>
</html>
<?php 
    define("DEFAULT_MAIL_TEMPLATE", ob_get_clean());
}

if (!defined("NO_MAIL_TEMPLATE"))
    define("NO_MAIL_TEMPLATE", "{CONTENT}");

if (!defined("DEFAULT_MAIL_UNSUBSECTION"))
    define("DEFAULT_MAIL_UNSUBSECTION", '<p class="test-center"><em>Vous recevez ce message car vous êtes inscrit sur nos listes de diffusion. Pour vous désinscrire, <a href="' . LINK . 'unsubscribe">cliquez-ici.</a></em></p>');

    
/**
 * sendMail - Fonction pour envoyer un mail avec PHPMailer
 * Cette fonction doit absolument être utilisée dans le cadre d'un bloc try catch
 * 
 * @author Aldric L.
 * @copyright 2023s
 * @param array $smtp_config : array de strings, adresses email brutes
 * @param string $subject : objet du mail
 * @param string $html_content : contenu html du mail
 * @param string $template : header html bootstrap DEFAULT_MAIL_TEMPLATE, sinon NO_MAIL_TEMPLATE
 * @param string $unsubsection : footer ajouté à la fin du mail
 * @return array "success", "error"
 */
function sendMail(array $smtp_config, array $recipients, string $subject, string $html_content, string $template=DEFAULT_MAIL_TEMPLATE, string $unsubsection=DEFAULT_MAIL_UNSUBSECTION){
    require_once 'libs/phpmailer/Exception.php';
    require_once 'libs/phpmailer/PHPMailer.php';
    require_once 'libs/phpmailer/SMTP.php';

    //Create an instance; passing `true` enables exceptions
    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    $mail->isSMTP();      
    $mail->Host       = $smtp_config["host"];    
    $mail->SMTPAuth   = true; 
    $mail->Username   = $smtp_config["username"];
    $mail->Password   = $smtp_config["password"];
    switch ($smtp_config["encryption"]) {
        case 'ENCRYPTION_STARTTLS':
            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;      
            break;
        case 'ENCRYPTION_SMTPS':
            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;      
            break;
            
        default:
            //$mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;      
            break;
    }  
    $mail->Port = $smtp_config["port"]; 
    $mail->CharSet = "UTF-8"; 
    $mail->Encoding = 'base64';
    $mail->setFrom($smtp_config["adress"], 'Mailer');            
    if (!$mail->SmtpConnect())
        return array("success" => false, "error" => $mail->ErrorInfo);

    foreach ($recipients as $r){
        $mail->addAddress($r);  
    }
    $mail->isHTML(true);       
    $mail->Subject = $subject;
    if (defined("TEXT_ALIAS") && is_array(TEXT_ALIAS)){
        foreach (TEXT_ALIAS as $key => $a){
            $html_content = str_replace($key, $a, $html_content);
        }
    }
    $mail->Body    = str_replace("{UNSUBSECTION}", $unsubsection, str_replace("{CONTENT}", $html_content, $template));
    if (!$mail->send())
        return array("success" => false, "error" => $mail->ErrorInfo);

    return array("success" => true);
}
