<?php
namespace PageBuilders; 

abstract class AdminList{
    protected string $buffer;
    protected array $fieldBuffer;
    protected array $availableIfs = array();

    public function __construct(){
        $this->buffer="";
        $this->fieldBuffer=array();
    }

    // On ne peut pas typer $right et $modalCallBack car les objects comme valeur par défaut des paramètres n'apparaissent en PHP qu'en 8.1... 
    // back to middle age so !
    public abstract function addField(renderUIComponent $left, $right=null, $callBack=null, $class=null, string $id="", $availableif=null, array $extra_attributes=array());

    public abstract function render();
}