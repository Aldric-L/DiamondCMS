<?php

class tools extends DiamondAPI {

    public function __construct(array $paths, PDO $pdo, Controleur $controleur, int $level){
        parent::__construct($paths, $pdo, $controleur, $level);
        $this->params_needed = array(
            "get_errorcontent" => array("code"),
            "get_logNewError" => array("code"),
            "get_latestNews" => array(),
        );
    }

    public function get_errorcontent(){
        $this->errors_manager->log($this->args['code'], "XHR AJAX API");
        
        return $this->formatedReturn($this->errors_manager->getContentError($this->args['code']));
    }

    public function get_logNewError(){
        return $this->formatedReturn($this->errors_manager->addError($this->args['code']));
    }

    public function get_latestNews(){
        $pvcache = $this->getCacheInstance(self::CACHE_DYN);
        $cache = $pvcache->read("get_latestNews.dcms");
        if ($cache == false){
            $news = simplifySQL\select($this->getPDO(), false, "d_news", array("id", "name", "content_new", array("date", "%d/%m/%Y %h:%i", "date"), "img", "user"), false, "date", true, array(0, 6));
            foreach ($news as &$n){
                $n['user'] = \User::getPseudoById($this->getPDO(), $n['user']);
                $width = 400;
                if (substr($n['img'], 0, 4) == "http"){
                    $n['final_img_link'] = $n['img'];
                }else if (substr($n['img'], -4, 4) == ".png"){
                    $n['final_img_link'] =  "getimage/png/" . substr($n['img'], 0, -4). "/". (string)(round((9 * $width)/16)) . str_replace(" ", "", " /$width/ ");
                }else if (substr($n['img'], -4, 4) == ".jpg"){
                    $n['final_img_link'] =  "getimage/jpg/" . substr($n['img'], 0, -4). "/". (string)(round((9 * $width)/16)) . str_replace(" ", "", " /$width/ ");
                }else if (substr($n['img'], -4, 4) == "jpeg"){
                    $n['final_img_link'] =  "getimage/jpeg/" . substr($n['img'], 0, -5). "/". (string)(round((9 * $width)/16)) . str_replace(" ", "", " /$width/ ");
                }else{
                    $n['final_img_link'] =  "getimage/png/-/no_profile/". (string)(round((9 * $width)/16)) . str_replace(" ", "", " /$width/ ");
                }
            }
            
            $cache = $this->formatedReturn($news);
            $pvcache->write("get_latestNews.dcms", $cache);
        }
        return $cache;
    }

}