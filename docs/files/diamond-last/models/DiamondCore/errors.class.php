<?php 

class Errors {
    private $content_errors_file;
    private $error_path;
    private $error_log_path;
    private $errors = array();
    public $namespaces = array();

    public function __construct($error_path, $error_log_path, $language=null){
        if ($language != null)
            $this->error_path = $error_path . "errors_" . $language . ".ini";
        else
            $this->error_path = $error_path . "errors.ini";
        //On charge le fichier des erreurs classées par code
        $this->content_errors_file = cleanIniTypes(parse_ini_file($this->error_path, true));
        $this->error_log_path = $error_log_path . "errors.json.log";
        $this->namespaces = array("native");
    }

    public function log($error_code, $user=null) {
        $log = array();
        if (file_exists($this->error_log_path) && json_decode(@file_get_contents($this->error_log_path)) != null)
          $log = json_decode(@file_get_contents($this->error_log_path), true);

        if (sizeof($log) >= 1000000){
          for($i=0; $i < 1000; $i++){
            unset($log[$i]);
          }
        }

        if (sizeof(explode(";", $error_code)) > 1){
          $true_code = explode(";", $error_code)[1];
          $error_code = explode(";", $error_code)[0];
        }else {
          $true_code = $error_code;
        }

        $error = array(
          "code" => $error_code,
          "truecode" => $true_code,
          "page" => str_replace(WEBROOT, '', $_SERVER['REQUEST_URI']),
          "date" => date("j/m/y à H:i:s"),
          "datetime" => time(),
          "user" => $user
        );
        array_push($log, $error);
        file_put_contents($this->error_log_path, json_encode($log));
    }

    public function addError($code_error, $user=null){
        if (is_numeric($code_error) || (is_array(explode("$", $code_error)) && sizeof(explode("$", $code_error)) === 1)){
          $code_error = "native$" . (string)$code_error;
        }else if (is_array(explode("$", $code_error)) && !(sizeof(explode("$", $code_error)) === 2 && in_array(explode("$", $code_error)[0], $this->namespaces))){
          throw new Exception("Bad error code");
        }
        if (array_key_exists($code_error, $this->content_errors_file) && isset($this->content_errors_file[$code_error]['msg'])){
          array_push($this->errors, $this->content_errors_file[$code_error]['msg'] . "</span> <span>(Code d'erreur " . $code_error . ".)");
          $this->log($code_error, $user);
          return true;
        }else {
          array_push($this->errors, "Une erreur inconnue est survenue.</span> <span>(Code d'erreur 121.)");
          $this->log("native$121;" . $code_error, $user);
          return false;
        }
      }

    public function getContentError($code_error){
        if (array_key_exists($code_error, $this->content_errors_file)){
          return $this->content_errors_file[$code_error]['msg'];
        }else if (array_key_exists("native$" . (string)$code_error, $this->content_errors_file)){
          return $this->content_errors_file["native$" . (string)$code_error]['msg'];
        }else {
          return $this->content_errors_file["native$121"]['msg'];
        }
    }

    public function getError($code_error){
        if (array_key_exists($code_error, $this->content_errors_file)){
          return $this->content_errors_file[$code_error];
        }else {
          return $this->content_errors_file["native$121"];
        }
    }

    public function getErrors(){
        if (empty($this->errors)){ return null; } return $this->errors;
    }

    public function purgeErrors(){
        unset($this->errors);
    }

    public function getErrorsInLog(){
      return (!is_array(json_decode(@file_get_contents($this->error_log_path)))) ? array() : json_decode(@file_get_contents($this->error_log_path), true);
    }

    public function extendErrorsKnown($errors){
      $ns = "";
      foreach ($errors as $key => $error){
        if (sizeof(explode("$", $key)) === 2){
          if ($ns === "")
            $ns = explode("$", $key)[0];
          else if ($ns != explode("$", $key)[0])
            throw new Exception("All errors added should have the same namespace.");
        }

        if (!isset($error['display_code']) || !isset($error['type']) || !isset($error['msg']) || !isset($error['icon']) || !isset($error['owner']))
          throw new Exception("An error is not properly initialised.");

      }
      if ($ns === "")
        throw new Exception("All errors added should have a namespace.");
      $this->content_errors_file = array_merge($this->content_errors_file, $errors);
      array_push($this->namespaces, $ns);
    }
}