<?php 
namespace PageBuilders; 

class DefaultAdminModal extends AdminModal implements renderUIComponent{

    public function __construct(string $title, string $id, renderUIComponent $content, string $icon="", string $size=""){
        parent::__construct($title, $id, $content, $icon, $size);
        if ($icon != ""){
            $this->title = '<i class="fa '. $icon . ' fa-fw"></i> ' . $title;
        }
    }

    public function addAPIButton(renderUIComponent $button){
        array_push($this->buttonsBuffer, $button->render());
        return $this;
    }

    public function render() : string{
        \ob_start(); ?> 
        <div id="<?php echo $this->getId(); ?>" class="modal fade">
            <div class="modal-dialog <?php echo $this->size; ?>" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                    <h5 class="modal-title"><?php echo $this->title; ?></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    </div>
                    <div class="modal-body">
                        <?php echo $this->content->render(); ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button> 
                        <?php foreach ($this->buttonsBuffer as $buf) {
                                echo $buf;
                        } ?>
                    </div>
                </div>
            </div>
        </div>
        <?php 
        $this->buffer = \ob_get_clean();
        return $this->buffer;
    }
}