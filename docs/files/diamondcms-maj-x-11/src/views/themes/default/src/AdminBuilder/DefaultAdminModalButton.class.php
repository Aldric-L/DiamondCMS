<?php 
namespace PageBuilders; 

class DefaultAdminModalButton extends AdminModalButton implements UIButton{
    public function __construct(string $title, string $class, AdminModal $modal, bool $disabled=false, string $id=""){
        parent::__construct($title, $class, $modal, $disabled, $id);
        $this->buffer = new DefaultAdminButton($title, $class);

        $this->buffer->addAttrS(array(
            "data-toggle" => "modal",
            "data-target" => "#" + $this->modal->getId(),
        ));

        if ($disabled)
            $this->buffer->addAttr("disabled", "disabled");

        if ($id !== "")
            $this->buffer->addAttr("id", $id);

    }

    public function render() : string{
        return $this->buffer->render();
    }

    public function customRender(string $html_type, string $base_class="") : string{
        return $this->buffer->customRender($html_type, $base_class);
    }
}