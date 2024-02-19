<?php 
namespace PageBuilders; 

class DefaultAdminAlert  extends AdminAlert implements renderUIComponent{

    public function render() : string {
        \ob_start(); ?> 
        <div class="alert alert-<?php echo $this->type; ?> <?php echo $this->dismissible ? "alert-dismissible" :""; ?> fade show" role="alert">
            <strong><?php echo !empty($this->title) ? $this->title:""; ?></strong> <?php echo !empty($this->content) ? $this->content:""; ?>
            <?php if($this->dismissible): ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <?php endif; ?>
        </div>
        <?php 
        $buffer = \ob_get_clean();
        return $buffer;
    }
}