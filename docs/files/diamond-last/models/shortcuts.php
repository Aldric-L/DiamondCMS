<?php 

// Ce fichier condense les fonctions qui permettent d'aller plus vite dans la maintenance du code
// Il ne correpond pas forcément toujours aux meilleures pratiques en vigueur, mais maintenir un projet qui date de 2016 nécessite de faire quelques sacrifices...

namespace DiamondShortcuts {
    /**
     * utf8_encode remplace la version native éponyme qui est dépréciée
     * @author Aldric L.
     * @copyright 2023
     * @since 2.0
     * 
     */
    function utf8_encode(string $string) : string {
        return mb_convert_encoding($string, 'UTF-8', 'ISO-8859-7');
    }

    function utf8_decode(string $string) : string {
        return mb_convert_encoding($string, 'ISO-8859-7', 'UTF-8');
    }
}