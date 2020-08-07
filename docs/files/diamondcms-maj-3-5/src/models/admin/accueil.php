<?php

function getNumberErrorLog(){
    $descFic = fopen (ROOT . 'logs/errors.log', "r");
    $errors = array();
    if (filesize(ROOT . 'logs/errors.log') > 0){
        while ($ligne = fgets ($descFic, filesize(ROOT . 'logs/errors.log')))
        {
            array_push($errors, substr($ligne, 0, 4));
        }
    }
    fclose ($descFic);
    return sizeof($errors);
}

function analiserLog($controleur_def, $limit=0){
    $descFic = fopen (ROOT . 'logs/errors.log', "r");
    $errors = array();
    $i = 0;
    if (filesize(ROOT . 'logs/errors.log') > 0){
        while ($ligne = fgets ($descFic, filesize(ROOT . 'logs/errors.log'))){
            if (substr($ligne, 3, 1) == " "){
                if (substr($ligne, 0, 3) == "121"){
                    if (substr($ligne, 17, 1) == ")"){
                        array_push($errors, array(substr($ligne, 0, 4), substr($ligne, 20, 20), $controleur_def->getContentError(substr($ligne, 0,3)) . " Code originel : " . substr($ligne, 14, 3)));  
                    }else {
                        array_push($errors, array(substr($ligne, 0, 4), substr($ligne, 21, 20), $controleur_def->getContentError(substr($ligne, 0,3)) . " Code originel : " . substr($ligne, 14, 4)));  
                    }
                }else {
                    array_push($errors, array(substr($ligne, 0, 4), substr($ligne, 5, 20), $controleur_def->getContentError(substr($ligne, 0,3))));  
                }
            }else {
                array_push($errors, array(substr($ligne, 0, 4), substr($ligne, 5, 20), $controleur_def->getContentError(substr($ligne, 0, 4))));
            }
            if ($limit != 0 && $i == $limit-1){
                fclose ($descFic);
                return $errors;
            }
            $i++;
        }
    }
    
    fclose ($descFic);
    return $errors;
}