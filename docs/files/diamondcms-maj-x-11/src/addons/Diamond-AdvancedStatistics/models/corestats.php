<?php 
namespace DiamondAdvancedStatistics;
class CoreStats {
    private array $hits;
    
    public function __construct($db){
        $this->hits = array();
        $this->reloadHits($db);            
        return $this;
    }

    public function reloadHits($db){
        try {
            $this->hits = \simplifySQL\select($db, false, "d_statistics_hits", array("id", "HTTP_REFERER", "php_sessid", "internal_path", "date", array("date", "%d/%m/%Y", "date_formated")), false, "date", true, array(0, 100000000000));
            if ((is_bool($this->hits) && $this->hits == false) /*|| (is_array($this->hits) && empty($this->hits))*/)
                throw new \DiamondException("Unable to find last hits in database", "Diamond-AdvancedStatistics$100");
        }catch (\Throwable $e){
            //throw new \DiamondException("Unable to find last hits in database", "Diamond-AdvancedStatistics$100");
            // On préfère plutot renvoyer le tableau vide en comptant sur l'erreur d'installation
            $this->hits = array();
        }
        
        return $this;
    }

    public function totalHitsByDayOrMonth($should_count_admin=true){
        $hits = $this->hits;
        if (!$should_count_admin){
            foreach ($hits as $key => $h) {
                if (substr($h['internal_path'], 0, 5) == "admin")
                    unset($hits[$key]);
            }
        }
        
        $sorted_hits = array();
        $sorted_month_hits = array();
        $iso_lasthit_date = array();
        $sorted_user_hits = array();
        $sorted_month_user_hits = array();
        foreach($hits as $h){
            $d1 = new \DateTime($h["date"]);
            $d2 = new \DateTime((!empty($lst=array_key_last($iso_lasthit_date)) ? $lst : "now"));
            $diff = $d2->diff($d1, true);
            if ($diff->d > 1 && $d1 < $d2){
                $j=0;
                while($diff->d > 0){
                    $p1j = new \DateInterval('P1D');
                    $d2 = $d2->sub($p1j);
                    if (!array_key_exists($d2->format('d/m/Y'), $sorted_hits))
                        $sorted_hits[$d2->format('d/m/Y')] = 0;
                    if (!array_key_exists($d2->format("Y-m-d H:i:s"), $iso_lasthit_date))
                        $iso_lasthit_date[$d2->format("Y-m-d H:i:s")] = 0;
                    if (!array_key_exists($d2->format("d/m/Y"), $sorted_user_hits)){
                            $sorted_user_hits[$d2->format("d/m/Y")] = array();
                            $sorted_user_hits[$d2->format("d/m/Y")]["no_usr"] = 0;
                    }
                    $diff = $d2->diff($d1, true);
                    $j++;
                    if ($j > 100)
                        break;
                }
            }
            
            $d = new \DateTime($h["date"]);
            if (!array_key_exists($d->format('m/Y'), $sorted_month_hits)){
                $sorted_month_hits[$d->format('m/Y')] = 1;
            }else{
                $sorted_month_hits[$d->format('m/Y')] = $sorted_month_hits[$d->format('m/Y')]+1;
            }
    
            if (!array_key_exists($d->format('m/Y'), $sorted_month_user_hits)){
                $sorted_month_user_hits[$d->format('m/Y')] = array();
                $sorted_month_user_hits[$d->format('m/Y')][$h["php_sessid"]] = 1;
            }else{
                if (!array_key_exists($h["php_sessid"], $sorted_month_user_hits[$d->format('m/Y')]))
                    $sorted_month_user_hits[$d->format('m/Y')][$h["php_sessid"]] = 1;
                else
                    $sorted_month_user_hits[$d->format('m/Y')][$h["php_sessid"]] = $sorted_month_user_hits[$d->format('m/Y')][$h["php_sessid"]]+1;
            }
    
            if (!array_key_exists($h["date_formated"], $sorted_hits)){
                $sorted_hits[$h["date_formated"]] = 1;
                $iso_lasthit_date[$h["date"]] = 1;
            }else{
                $sorted_hits[$h["date_formated"]] = $sorted_hits[$h["date_formated"]]+1;
            }
    
            if (!array_key_exists($h["date_formated"], $sorted_user_hits)){
                $sorted_user_hits[$h["date_formated"]] = array();
                $sorted_user_hits[$h["date_formated"]][$h["php_sessid"]] = 1;
            }else{
                if (!array_key_exists($h["php_sessid"], $sorted_user_hits[$h["date_formated"]]))
                    $sorted_user_hits[$h["date_formated"]][$h["php_sessid"]] = 1;
                else
                    $sorted_user_hits[$h["date_formated"]][$h["php_sessid"]] = $sorted_user_hits[$h["date_formated"]][$h["php_sessid"]]+1;
            }
        }
        return array("total_hits" => $sorted_hits, "total_users" => $sorted_user_hits, "total_month_hits" => $sorted_month_hits, "total_month_users" => $sorted_month_user_hits);
    }
    
    
    public function bestPages($freq=true, $percentages=true, $should_count_admin=true){
        $hits = $this->hits;
        $paths = array();
        foreach ($hits as $key => $h){
            if ($h['internal_path'] == "")
                $h['internal_path'] = "accueil";
            if (!$should_count_admin && substr($h['internal_path'], 0, 5) == "admin"){
                    unset($hits[$key]);
            }else {
                if (array_key_exists($h['internal_path'], $paths)){
                    $paths[$h['internal_path']] = $paths[$h['internal_path']]+1;
                }else {
                    $paths[$h['internal_path']] = 1;
                }
            }
        }
        if ($freq){
            foreach ($paths as $key => $p){
                if (round($p/sizeof($hits), 3) <= 0.009)
                    unset($paths[$key]);
                else 
                    $paths[$key] = (!$percentages) ? round($p/sizeof($hits), 3) : round($p/sizeof($hits)*100, 1);
            }
        }
        arsort($paths);  
        return $paths;
    }

    public function bestReferer($reduce_to_hostnames=true, $freq=true, $percentages=true, $should_count_admin=true){
        $hits = $this->hits;
        $best_ref = array();
        foreach ($hits as $key => $h){
            if ($h['HTTP_REFERER'] == "")
                $h['HTTP_REFERER'] = "Accès direct";

            if (substr($h['HTTP_REFERER'], 0, strlen(LINK)) != LINK && !(!$should_count_admin && substr($h['internal_path'], 0, 5) == "admin")){
                if ($reduce_to_hostnames && substr($h['HTTP_REFERER'], 0,4) == "http"){
                    if (substr($h['HTTP_REFERER'], 0,7) == "http://"){
                        $l = explode("/", substr($h['HTTP_REFERER'], 7,strlen($h['HTTP_REFERER'])));
                        if (is_array($l) && sizeof($l) >= 1){
                            if (substr($l[0], -1, 1) == "/")
                                $l[0] = substr($l[0], 0, strlen($l[0])-1);
                            $h['HTTP_REFERER'] = $l[0];
                        }
                    }else if (substr($h['HTTP_REFERER'], 0,8) == "https://"){
                        $l = explode("/", substr($h['HTTP_REFERER'], 8, strlen($h['HTTP_REFERER'])));
                        if (is_array($l) && sizeof($l) >= 1){
                            if (substr($l[0], -1, 1) == "/")
                                $l[0] = substr($l[0], 0, strlen($l[0])-1);
                            $h['HTTP_REFERER'] = $l[0];
                        }
                    }
                }
                if (array_key_exists($h['HTTP_REFERER'], $best_ref))
                    $best_ref[$h['HTTP_REFERER']] = $best_ref[$h['HTTP_REFERER']]+1;
                else
                    $best_ref[$h['HTTP_REFERER']] = 1;
            }else{
                unset($hits[$key]);
            }
        }

        if ($freq){
            foreach ($best_ref as $key => $p){
                if (round($p/sizeof($hits), 3) <= 0.009)
                    unset($best_ref[$key]);
                else 
                    $best_ref[$key] = (!$percentages) ? round($p/sizeof($hits), 3) : round($p/sizeof($hits)*100, 1);
            }
        }
        arsort($best_ref);  
        return $best_ref;
    }

    public static function newHit(\PDO $db, string $internal_path="", $user_id=null, $HTTP_USER_AGENT=null, $HTTP_REFERER=null, $REQUEST_TIME=null){
        return \simplifySQL\insert($db, "d_statistics_hits", 
                array("internal_path", "link", "HTTP_USER_AGENT", "HTTP_REFERER", "REQUEST_TIME", "date", "user_id","php_sessid"),
                array($internal_path, 
                    LINK .$internal_path , 
                    (isset($HTTP_USER_AGENT) ? $HTTP_USER_AGENT : null), 
                    (isset($HTTP_REFERER) ? $HTTP_REFERER : null), 
                    (isset($REQUEST_TIME) ? $REQUEST_TIME : null), 
                    date("Y-m-d h:i:s"), 
                    (isset($user_id) ? $user_id : null),
                    session_id()
                )
        );
    }
}


