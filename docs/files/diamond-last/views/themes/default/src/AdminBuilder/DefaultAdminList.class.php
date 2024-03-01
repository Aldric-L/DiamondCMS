<?php 
namespace PageBuilders; 

class DefaultAdminList extends AdminList implements renderUIComponent{

    public function addField(renderUIComponent $left, $right=null, $callBack=null, $class=null, string $id="", $availableif=null, array $extra_attributes=array()){
        if ($id !== "" && !is_bool($availableif) && $availableif instanceof AvailableIf){
            array_push($this->availableIfs, $availableif->setTarget($id, "custom", AvailableIf::HIDE));
        }
        \ob_start(); ?>
            <a 
            <?php if(is_subclass_of($callBack, "PageBuilders\AdminModal")){ ?>
            data-toggle="modal" data-target="#<?php echo $callBack->getId(); ?>" href="#"
            <?php }else if(is_string($callBack) && $callBack != ""){ ?>
            href="<?php echo $callBack; ?>"
            <?php } ?>
            <?php if (!empty($extra_attributes)){
                foreach ($extra_attributes as $key => $a){
                    echo $key . "=" . '"' . $a . '" '; 
                }
            } ?>
             <?php if (($availableif instanceof AvailableIf && !$availableif->eval())) { ?>style="display:none;"<?php }?>
            <?php if ($id !== ""){ ?> id="<?php echo $id; ?>" <?php } ?>
            class="list-group-item <?php echo ($class != null && is_string($class)) ? $class : ""; ?>">
                <?php echo $left->render(); ?>
                <?php if($right != null && is_subclass_of($right, 'PageBuilders\renderUIComponent') ){ ?>
                    <span class="pull-right"><?php echo $right->render(); ?></span>
                <?php } ?>
            </a>
        <?php $buff = \ob_get_clean();
        array_push($this->fieldBuffer, $buff);
        return $this;
    }

    public function render() : string{
        \ob_start(); ?> 
        <div class="list-group">
            <?php foreach ($this->fieldBuffer as $buf) {
                echo $buf;
            } ?>
        </div>
        <?php
        if (is_array($this->availableIfs) && !empty($this->availableIfs)){ ?>
        <script>
            <?php foreach ($this->availableIfs as $if){ ?>
                $("#<?php echo $if->getCondFieldId(); ?>").on("<?php echo $if->getVerbe(); ?>", (e) => {
                    // ATTENTION, il ne faut pas renommer ces variables car elles sont utilisées dans la condition générée en PHP !
                    var value = getValue(e.target, true).value;
                    var targetValue = getValue($("#<?php echo $if->getTarget()['id']; ?>")[0], true).value;
                    
                    if (<?php echo $if->getCond(); ?>){
                        <?php if ($if->getAction() == AvailableIf::DISABLED){ ?>
                            $("#<?php echo $if->getTarget()['name']; ?>").attr('disabled', false);
                        <?php }else if ($if->getAction() == AvailableIf::HIDE){ ?>
                            $("#<?php echo $if->getTarget()['name']; ?>").show();
                        <?php } ?>
                    }else {
                        <?php if ($if->getAction() == AvailableIf::DISABLED){ ?>
                            $("#<?php echo $if->getTarget()['name']; ?>").attr('disabled', true);
                        <?php }else if ($if->getAction() == AvailableIf::HIDE){ ?>
                            $("#<?php echo $if->getTarget()['name']; ?>").hide();
                        <?php } ?>
                    }

                    //console.log(e, value, targetValue, <?php echo $if->getCond(); ?>);
                })
            <?php } ?>
        </script>
        <?php 
        } 
        $this->buffer = \ob_get_clean();
        return $this->buffer;
    }
}