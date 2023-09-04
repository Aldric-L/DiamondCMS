<?php
namespace PageBuilders; 

abstract class AdminForm{
    protected string $buffer;
    protected string $id;
    protected array $inBuffer;
    protected array $buttonsBuffer;
    protected string $buttons_line;
    protected bool $needAll;

    protected bool $shall_rename_ids = true;

    protected array $availableIfs = array();
    protected array $fields = array();

    public function __construct(string $id, bool $needAll=false, bool $shall_rename_ids=true){
        $this->buffer="";
        $this->id = $id;
        $this->inBuffer = array();
        $this->buttons_line = "";
        $this->buttonsBuffer = array();
        $this->needAll = $needAll;
        $this->shall_rename_ids = $shall_rename_ids;
    }

    public function getId(){
        return $this->id;
    }

    public function getFields() : array {
        return $this->fields;
    }

    public abstract function addTextField(string $name, string $title="", string $val="", bool $needed=false, $disabled=false, string $helptext="");
    public abstract function addHiddenField(string $name, string $val="", bool $needed=false);
    public abstract function addEmailField(string $name, string $title="", string $val="", bool $needed=false, $disabled=false, string $helptext="");
    public abstract function addNumberField(string $name, string $title="", string $val="", bool $needed=false, $disabled=false, string $helptext="");
    public abstract function addTextAreaField(string $name, string $title="", string $val="", int $rows=5, bool $needed=false, $disabled=false, string $helptext="");
    public abstract function addSelectField(string $name, string $title="", array $options, bool $needed=false, $disabled=false, string $helptext="");
    public abstract function addMpField(string $name, string $title="", string $val="", bool $needed=false, $disabled=false, string $helptext="");
    public abstract function addCheckField(string $name, string $title, bool $checked=false, bool $needed=false, $disabled=false, string $helptext="");
    public abstract function addFileField(string $name, string $title, bool $needed=false, $disabled=false, string $helptext="");
    public abstract function addCustom(renderUIComponent $custom);

    public abstract function setFilter(string $field_name, array $replacement);

    public abstract function addAPIButton(AdminAPIButton $button);
    public abstract function setButtonsLine(string $attr);
    public abstract function addModalButton(AdminModalButton $button);

    public abstract function render();
}