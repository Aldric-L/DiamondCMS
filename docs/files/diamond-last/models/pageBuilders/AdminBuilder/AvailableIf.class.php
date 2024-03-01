<?php
namespace PageBuilders; 

class AvailableIf{
    const EQUAL = "==";
    const NOT_EQUAL = "!=";
    const NOT_NULL = "NOTNULL";
    const SUP = ">";
    const EQSUP = ">=";
    const INF = "<";
    const EQINF = "<=";
    //const CLICKED = "CLICK";

    const DISABLED = "disabled";
    const HIDE = "hide";

    const OR = "||";
    const AND = "&&";

    protected AdminForm $form;
    protected string $field_name;
    protected string $field_id;
    protected $action = self::DISABLED;

    protected array $conditions = array();

    protected string $target_name = "";
    protected string $target_type = "";

    public function __construct(AdminForm &$form, string $field_name, string $operator, $value=null){
        $consts = array(self::EQUAL, self::NOT_EQUAL, self::NOT_NULL, 
                        self::SUP, self::EQSUP, self::INF, 
                        self::EQINF/*, self::CLICKED*/);
        if (!in_array($operator, $consts))
            throw new \DiamondException("Illegal value for AvailableIf operator", "native$999");

        $this->form = $form;
        $this->field_name = $field_name;
        $fields = $form->getFields();
        $def_value = $this->field_name;
        foreach ($fields as $f){
            if (array_key_exists('name', $f) && $f['name'] == $this->field_name){
                if (array_key_exists('id', $f))
                    $def_value = $f['id'];
            }
        }
        $this->field_id = $def_value;
        array_push($this->conditions, array(
            "logical" => "FIRST",
            "operator" => $operator,
            "value" => $value
        ));
        return $this;
    }

    public function setTarget(string $target_name, string $target_type, $action=self::DISABLED){
        $this->target_name = $target_name;
        $this->target_type = $target_type;
        $consts = array(self::DISABLED, self::HIDE);
        if ($action != self::DISABLED && !in_array($action, $consts))
            throw new \DiamondException("Illegal value for AvailableIf action", "native$999");
        $this->action = $action;
        
        return $this;
    }

    public function getTarget() : array {
        $fields = $this->form->getFields();
        $def_value = $this->target_name;
        foreach ($fields as $f){
            if (array_key_exists('name', $f) && $f['name'] == $this->target_name){
                if (array_key_exists('id', $f))
                    $def_value = $f['id'];
            }
        }
        return array(
            "id" => $def_value,
            "name" => $this->target_name,
            "type" => $this->target_type,
            "action" => $this->action
        );
    }

    public function addCondition(string $logical_operator, string $operator, $value=null) {
        $logicals = array(self::OR, self::AND);
        if (!in_array($logical_operator, $logicals))
            throw new \DiamondException("Illegal value for AvailableIf action", "native$999");
        
        array_push($this->conditions, array(
            "logical" => $logical_operator,
            "operator" => $operator,
            "value" => $value
        ));
        return $this;
    }

    public function getAction() {
        return $this->action;
    }

    public function getCondFieldName() : string {
        return $this->field_name;
    }

    public function getCondFieldId() : string {
        return $this->field_id;
    }

    public function getVerbe() : string {
        return "change";
    }

    public function getCond() : string {
        $final_cond = "";
        foreach ($this->conditions as $cond){
            switch ($cond['operator']) {
                case self::NOT_NULL :
                    if ($cond['logical'] != "FIRST")
                        $final_cond .= $cond['logical'];
                    $final_cond .= ' value != null && typeof(value) != "undefined" ';
                    break;
                
                default:
                    if ($cond['logical'] != "FIRST")
                            $final_cond .= $cond['logical'];

                    if (is_bool($cond['value'])){
                        if ($cond['value'])
                            $final_cond .= " value " . $cond['operator'] . " true ";
                        else                
                            $final_cond .= " value " . $cond['operator'] . " false ";
                    }else {
                        if (is_string($cond['value']))
                            $final_cond .= " value " . $cond['operator'] . '"' . strval($cond['value']) . '" ';
                        else
                            $final_cond .= " value " . $cond['operator'] . strval($cond['value']) . " ";
                    }
                    break;
            }
        }
        return $final_cond;
    }

    public function eval() : bool{
        $fields = $this->form->getFields();
        $def_value = null;
        foreach ($fields as $f){
            if (array_key_exists('name', $f) && $f['name'] == $this->field_name){
                if (array_key_exists('value', $f))
                    $def_value = $f['value'];
            }
        }
        $cond_results = array();
        $raw_results = array();

        foreach ($this->conditions as $cond){
            switch ($cond['operator']) {
                case self::EQUAL:
                    array_push($cond_results, array(
                        "logical" => $cond['logical'],
                        "result" => $r=($def_value == $cond['value'])
                    ));
                    array_push($raw_results, $r);
                    break;
                
                case self::NOT_EQUAL:
                    array_push($cond_results, array(
                        "logical" => $cond['logical'],
                        "result" => $r=($def_value != $cond['value'])
                    ));
                    array_push($raw_results, $r);
                    break;
    
                case self::NOT_NULL:
                    array_push($cond_results, array(
                        "logical" => $cond['logical'],
                        "result" => $r=($def_value != null)
                    ));
                    array_push($raw_results, $r);
                    break;
    
                case self::SUP:
                    array_push($cond_results, array(
                        "logical" => $cond['logical'],
                        "result" => $r=($def_value > $cond['value'])
                    ));
                    array_push($raw_results, $r);
                    break;
    
                case self::EQSUP:
                    array_push($cond_results, array(
                        "logical" => $cond['logical'],
                        "result" => $r=($def_value >= $cond['value'])
                    ));
                    array_push($raw_results, $r);
                    break;
    
                case self::INF:
                    array_push($cond_results, array(
                        "logical" => $cond['logical'],
                        "result" => $r=($def_value < $cond['value'])
                    ));
                    array_push($raw_results, $r);
                    break;
    
                case self::EQINF:
                    array_push($cond_results, array(
                        "logical" => $cond['logical'],
                        "result" => $r=($def_value <= $cond['value'])
                    ));
                    array_push($raw_results, $r);
                    break;
                
                default:
                    array_push($cond_results, array(
                        "logical" => $cond['logical'],
                        "result" => $r=true
                    ));
                    array_push($raw_results, $r);
                    break;
            }
        }
        if (sizeof($cond_results) < 1){
            return true;
        }else if (sizeof($cond_results) == 1) {
            return $cond_results[0]['result'];
        }else {
            if ($cond_results[1]['logical'] == self::AND){
                if (array_sum($raw_results) == count($raw_results))
                    return true;
                return false;
            }else {
                if (array_sum($raw_results) > 0)
                    return true;
                return false;
            }
        }
    }

}