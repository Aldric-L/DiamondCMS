<?php 
namespace PageBuilders; 

class DefaultAdminAPIButton extends AdminAPIButton implements UIButton{
    public function __construct(string $title, string $class, string $api_link, string $api_module, string $api_verbe, string $func, $to_send, string $private_callback="", $should_reload="true", $show_return="false", $no_loading="false", bool $disabled=false, string $private_id="", bool $needAll=false){
        parent::__construct($title, $class, $api_link, $api_module, $api_verbe, $func, $to_send, $private_callback, $should_reload, $show_return, $no_loading, $disabled, $private_id, $needAll);

        $this->buffer = new DefaultAdminButton($title, "ajax-simpleSend " . $class);

        $this->buffer->addAttrS(array(
            "data-module" => $this->api_module . "/",
            "data-verbe" => $this->api_verbe,
            "data-func" => $this->func,
            "data-reload" => ($this->should_reload) ? "true" : "false",
            "data-noloading" => $this->no_loading,
            "data-showreturn" => $this->show_return,
            "data-func" => $this->func,
        ));

        // On ne spécifie plus le link pour éviter des pbs de cache et de mauvais lien
        if ($this->api_link != LINK . "api/")
            $this->buffer->addAttr("data-api", $this->api_link);

        if (!is_string($this->to_send) && is_subclass_of($this->to_send, "PageBuilders\AdminForm")){
            $this->buffer->addAttrS(array(
                "data-tosend" => "#" . $this->to_send->getId(),
                "data-useform" => "true"
            ));

            if ($this->allneeded)
                $this->buffer->addAttr("data-needAll", "true");
        }else if (is_string($this->to_send) && $this->to_send != null) {
            $this->buffer->addAttrS(array(
                "data-tosend" => $this->to_send
            ));
        }

        if ($this->disabled)
            $this->buffer->addAttr("disabled", "disabled");
        

        if ($this->private_id !== "")
            $this->buffer->addAttr("id", $this->private_id);
        
        if ($this->private_callback !== "")
            $this->buffer->addAttr("data-callback", $this->private_callback);
        return $this;
    }
    
    public function render() : string{
        return $this->buffer->render();
    }

    public function customRender(string $html_type, string $base_class="") : string{
        return $this->buffer->customRender($html_type, $base_class);
    }
}