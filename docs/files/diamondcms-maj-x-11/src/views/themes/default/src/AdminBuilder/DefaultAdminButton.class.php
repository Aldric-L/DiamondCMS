<?php 
namespace PageBuilders; 

class DefaultAdminButton extends AdminButton implements UIButton{
    public function render() : string{
        \ob_start();  ?>
        <button type="button" 
        class="btn <?php echo $this->class; ?>" 
        <?php foreach ($this->attr as $key => $a){ ?>
            <?php echo $key; ?>="<?php echo $a; ?>"
        <?php } ?>
        ><?php echo $this->title; ?></button>    
        <?php 
        $buffer = \ob_get_clean();
        return $buffer;
    }

    public function customRender(string $html_type, string $base_class="") : string{
        \ob_start();  ?>
        <<?php echo $html_type; ?> <?php echo ($html_type == "button") ? 'type="button"' : ""; ?> 
        class="<?php echo $base_class . " " . $this->class; ?>" 
        <?php foreach ($this->attr as $key => $a){ ?>
            <?php echo $key; ?>="<?php echo $a; ?>"
        <?php } ?>
        ><?php echo $this->title; ?></<?php echo $html_type; ?>>    
        <?php 
        $buffer = \ob_get_clean();
        return $buffer;
    }
}