<?php 
namespace PageBuilders; 

class DefaultAdminForm extends AdminForm implements renderUIComponent{
    public function addTextField(string $name, string $title="", string $val="", bool $needed=false, $disabled=false, string $helptext=""){
        array_push($this->fields, array(
            "type" => "text",
            "name" => $name,
            "title" => $title,
            "value" => $val, 
            "needed" => $needed,
            "default_disabled" => (is_bool($disabled)) ? $disabled : false,
            "helptext" => $helptext,
            "id" => $id=($this->shall_rename_ids) ? uniqid() . "_" . $name : $name,
        ));
        if (!is_bool($disabled) && $disabled instanceof AvailableIf){
            array_push($this->availableIfs, $disabled->setTarget($name, "text"));
        }
        \ob_start(); ?>
            <div class="form-group">
                <?php if ($title != ""){ ?>
                <label for="<?php echo $name; ?>" class="col-form-label"><?php echo $title; ?></label>
                <?php } ?>
                <input class="form-control" type="text" name="<?php echo $name; ?>" id="<?php echo $id; ?>" value="<?php echo $val; ?>" <?php if ($needed) { ?> data-neededForValidation="true" <?php } ?> <?php if ((is_bool($disabled) && $disabled) || ($disabled instanceof AvailableIf && !$disabled->eval())) { ?>disabled<?php }?>> 
                <?php if ($helptext != "") { ?>
                    <small class="form-text text-muted"><?php echo $helptext; ?></small>
                <?php } ?>
            </div>
        <?php $buff = \ob_get_clean();
        array_push($this->inBuffer, $buff);
        return $this;
    }

    public function addHiddenField(string $name, string $val="", bool $needed=false) {
        array_push($this->fields, array(
            "type" => "text",
            "name" => $name,
            "value" => $val, 
            "needed" => $needed,
            "id" => $id=($this->shall_rename_ids) ? uniqid() . "_" . $name : $name,
        ));
        \ob_start(); ?>
            <input type="hidden" name="<?php echo $name; ?>" id="<?php echo $id; ?>" value="<?php echo $val; ?>" <?php if ($needed) { ?> data-neededForValidation="true" <?php } ?> >
        <?php $buff = \ob_get_clean();
        array_push($this->inBuffer, $buff);
        return $this;
    }


    public function addTextAreaField(string $name, string $title="", string $val="", int $rows=5, bool $needed=false, $disabled=false, string $helptext=""){
        array_push($this->fields, array(
            "type" => "textarea",
            "name" => $name,
            "title" => $title,
            "value" => $val, 
            "needed" => $needed,
            "default_disabled" => (is_bool($disabled)) ? $disabled : false,
            "helptext" => $helptext,
            "id" => $id=($this->shall_rename_ids) ? uniqid() . "_" . $name : $name,
        ));
        if (!is_bool($disabled) && $disabled instanceof AvailableIf){
            array_push($this->availableIfs, $disabled->setTarget($name, "textarea"));
        }
        \ob_start(); ?>
            <div class="form-group floating-label-form-group controls">
                <?php if ($title != ""){ ?>
                <label><?php echo $title; ?></label>
                <?php } ?>
                <textarea rows="<?php echo $rows; ?>" class="form-control" name="<?php echo $name; ?>" id="<?php echo $id; ?>" <?php if ($needed) { ?> data-neededForValidation="true" <?php } ?> <?php if ((is_bool($disabled) && $disabled) || ($disabled instanceof AvailableIf && !$disabled->eval())) { ?>disabled readonly<?php }?>><?php echo $val; ?></textarea>
                <?php if ($helptext != "") { ?>
                    <small class="form-text text-muted"><?php echo $helptext; ?></small>
                <?php } ?>
            </div>
        <?php $buff = \ob_get_clean();
        array_push($this->inBuffer, $buff);
        return $this;
    }

    /**
     * 
     * 
     * @param array $option : array(array("val" => val, "disp" => disp (Optionnel :, "selected" => true) ))
     */
    public function addSelectField(string $name, string $title, array $options, bool $needed=false, $disabled=false, string $helptext=""){
        $field_options = array(
            "type" => "select",
            "name" => $name,
            "title" => $title,
            "options" => $options, 
            "needed" => $needed,
            "default_disabled" => (is_bool($disabled)) ? $disabled : false,
            "helptext" => $helptext,
            "id" => $id=($this->shall_rename_ids) ? uniqid() . "_" . $name : $name,
        );
        \ob_start(); ?>
            <div class="form-group">
                <?php if ($title != ""){ ?>
                <label for="<?php echo $name; ?>" class="col-form-label"><?php echo $title; ?></label>
                <?php } ?>
                <select class="form-control" name="<?php echo $name; ?>" id="<?php echo $id; ?>" <?php if ($needed) { ?> data-neededForValidation="true" <?php } ?> <?php if ((is_bool($disabled) && $disabled) || ($disabled instanceof AvailableIf && !$disabled->eval())) { ?>disabled<?php }?>>
                    <?php foreach($options as $opt){ ?>
                        <option value="<?= $opt["val"]; ?>" <?php if (array_key_exists("selected", $opt) && is_bool($opt['selected']) && $opt['selected']): $selected = $opt["val"]; echo "selected"; endif; ?>><?= $opt["disp"]; ?></option>
                    <?php }?>
                </select>
                <?php if ($helptext != "") { ?>
                    <small class="form-text text-muted"><?php echo $helptext; ?></small>
                <?php } ?>
            </div>
        <?php $buff = \ob_get_clean();
        array_push($this->inBuffer, $buff);
        if (isset($selected))
            $field_options['value'] = $selected;
        else if (!empty($options))
            $field_options['value'] = $options[0]['val'];
        array_push($this->fields, $field_options);
        if (!is_bool($disabled) && $disabled instanceof AvailableIf){
            array_push($this->availableIfs, $disabled->setTarget($name, "select"));
        }
        return $this;
    }

    public function addMpField(string $name, string $title="", string $val="", bool $needed=false, $disabled=false, string $helptext=""){
        array_push($this->fields, array(
            "type" => "password",
            "name" => $name,
            "title" => $title,
            "value" => $val, 
            "needed" => $needed,
            "default_disabled" => (is_bool($disabled)) ? $disabled : false,
            "helptext" => $helptext,
            "id" => $id=($this->shall_rename_ids) ? uniqid() . "_" . $name : $name,
        ));
        if (!is_bool($disabled) && $disabled instanceof AvailableIf){
            array_push($this->availableIfs, $disabled->setTarget($name, "password"));
        }
        \ob_start(); ?>
            <div class="form-group">
                <?php if ($title != ""){ ?>
                <label for="<?php echo $name; ?>" class="col-form-label"><?php echo $title; ?></label>
                <?php } ?>
                <input class="form-control" type="password" name="<?php echo $name; ?>" id="<?php echo $id; ?>" value="<?php echo $val; ?>" <?php if ($needed) { ?> data-neededForValidation="true" <?php } ?> <?php if ((is_bool($disabled) && $disabled) || ($disabled instanceof AvailableIf && !$disabled->eval())) { ?>disabled<?php }?>>
            </div>
        <?php $buff = \ob_get_clean();
        array_push($this->inBuffer, $buff);
        return $this;
    }

    public function addNumberField(string $name, string $title="", string $val="", bool $needed=false, $disabled=false, string $helptext=""){
        array_push($this->fields, array(
            "type" => "number",
            "name" => $name,
            "title" => $title,
            "value" => $val, 
            "needed" => $needed,
            "default_disabled" => (is_bool($disabled)) ? $disabled : false,
            "helptext" => $helptext,
            "id" => $id=($this->shall_rename_ids) ? uniqid() . "_" . $name : $name,
        ));
        if (!is_bool($disabled) && $disabled instanceof AvailableIf){
            array_push($this->availableIfs, $disabled->setTarget($name, "number"));
        }
        \ob_start(); ?>
            <div class="form-group">
                <?php if ($title != ""){ ?>
                <label for="<?php echo $name; ?>" class="col-form-label"><?php echo $title; ?></label>
                <?php } ?>
                <input class="form-control" type="number" name="<?php echo $name; ?>" id="<?php echo $id; ?>" value="<?php echo $val; ?>" <?php if ($needed) { ?> data-neededForValidation="true" <?php } ?> <?php if ((is_bool($disabled) && $disabled) || ($disabled instanceof AvailableIf && !$disabled->eval())) { ?>disabled<?php }?>>
                <?php if ($helptext != "") { ?>
                    <small class="form-text text-muted"><?php echo $helptext; ?></small>
                <?php } ?>
            </div>
        <?php $buff = \ob_get_clean();
        array_push($this->inBuffer, $buff);
        return $this;
    }

    public function addEmailField(string $name, string $title="", string $val="", bool $needed=false, $disabled=false, string $helptext=""){
        array_push($this->fields, array(
            "type" => "email",
            "name" => $name,
            "title" => $title,
            "value" => $val, 
            "needed" => $needed,
            "default_disabled" => (is_bool($disabled)) ? $disabled : false,
            "helptext" => $helptext,
            "id" => $id=($this->shall_rename_ids) ? uniqid() . "_" . $name : $name,
        ));
        if (!is_bool($disabled) && $disabled instanceof AvailableIf){
            array_push($this->availableIfs, $disabled->setTarget($name, "email"));
        }
        \ob_start(); ?>
            <div class="form-group">
                <?php if ($title != ""){ ?>
                <label for="<?php echo $name; ?>" class="col-form-label"><?php echo $title; ?></label>
                <?php } ?>
                <input class="form-control" type="email" name="<?php echo $name; ?>" id="<?php echo $id; ?>" value="<?php echo $val; ?>" <?php if ($needed) { ?> data-neededForValidation="true" <?php } ?> <?php if ((is_bool($disabled) && $disabled) || ($disabled instanceof AvailableIf && !$disabled->eval())) { ?>disabled<?php }?>>
                <?php if ($helptext != "") { ?>
                    <small class="form-text text-muted"><?php echo $helptext; ?></small>
                <?php } ?>
            </div>
        <?php $buff = \ob_get_clean();
        array_push($this->inBuffer, $buff);
        return $this;
    }

    public function addCheckField(string $name, string $title, bool $checked=false, bool $needed=false, $disabled=false, string $helptext=""){
        array_push($this->fields, array(
            "type" => "check",
            "name" => $name,
            "title" => $title,
            "value" => $checked, 
            "needed" => $needed,
            "default_disabled" => (is_bool($disabled)) ? $disabled : false,
            "helptext" => $helptext,
            "id" => $id=($this->shall_rename_ids) ? uniqid() . "_" . $name : $name,
        ));
        if (!is_bool($disabled) && $disabled instanceof AvailableIf){
            array_push($this->availableIfs, $disabled->setTarget($name, "check"));
        }
        \ob_start(); ?>
            <div class="form-check">
                <input class="form-check-input" name="<?php echo $name; ?>" type="checkbox" id="<?php echo $id; ?>" <?php if ($checked) { ?> checked <?php } ?> <?php if ($needed) { ?> data-neededForValidation="true" <?php } ?> <?php if ((is_bool($disabled) && $disabled) || ($disabled instanceof AvailableIf && !$disabled->eval())) { ?>disabled<?php }?>>
                <label for="<?php echo $name; ?>" class="form-check-label"><?php echo $title; ?></label>
                <?php if ($helptext != "") { ?>
                    <small class="form-text text-muted"><?php echo $helptext; ?></small>
                <?php } ?>
            </div>
        <?php $buff = \ob_get_clean();
        array_push($this->inBuffer, $buff);
        return $this;
    }

    public function addFileField(string $name, string $title, bool $needed=false, $disabled=false, string $helptext=""){
        array_push($this->fields, array(
            "type" => "file",
            "name" => $name,
            "title" => $title,
            "needed" => $needed,
            "default_disabled" => (is_bool($disabled)) ? $disabled : false,
            "helptext" => $helptext,
            "id" => $id=($this->shall_rename_ids) ? uniqid() . "_" . $name : $name,
        ));
        if (!is_bool($disabled) && $disabled instanceof AvailableIf){
            array_push($this->availableIfs, $disabled->setTarget($name, "file"));
        }
        \ob_start(); ?>
        <div class="form-group">
            <div class="custom-file">
    <input type="file" class="custom-file-input" name="<?php echo $name; ?>" id="<?php echo $id; ?>" <?php if ($needed) { ?> data-neededForValidation="true" <?php } ?> <?php if ((is_bool($disabled) && $disabled) || ($disabled instanceof AvailableIf && !$disabled->eval())) { ?>disabled<?php }?>>
                <label class="custom-file-label" for="<?php echo $name; ?>"><?php echo $title; ?></label>
            </div>
            <?php if ($helptext != "") { ?>
                <small class="form-text text-muted"><?php echo $helptext; ?></small>
            <?php } ?>
        </div>   
        <?php $buff = \ob_get_clean();
        array_push($this->inBuffer, $buff);
        return $this;
    }

    public function addAPIButton(AdminAPIButton $button){
        array_push($this->fields, array(
            "type" => "APIButton",
            "value" => $button
        ));
        if ($this->needAll && $button->getTosend() == $this){
            $button->setAllneeded();
        }
        array_push($this->buttonsBuffer, $button->render());
        return $this;
    }

    public function setButtonsLine(string $attr){
        $this->buttons_line = $attr;
        return $this;
    }

    public function addModalButton(AdminModalButton $button){
        array_push($this->fields, array(
            "type" => "ModalButton",
            "value" => $button
        ));
        array_push($this->buttonsBuffer, $button->render());
        return $this;
    }

    public function addCustom(renderUIComponent $custom, string $id="", $availableif=false){        
        if ($id !== "" && !is_bool($availableif) && $availableif instanceof AvailableIf){
            array_push($this->availableIfs, $availableif->setTarget($id, "custom", AvailableIf::HIDE));
        }
        if ($id !== "")
            array_push($this->inBuffer, '<span id="' . $id . '">' . $custom->render() . '</span>');
        else
            array_push($this->inBuffer, $custom->render());
        return $this;
    }

    public function setFilter(string $field_name, array $replacement){
        \ob_start(); ?>
        <script>
            $("#<?php echo $field_name; ?>").on("change", (e) => {
                var value = $("#<?php echo $field_name; ?>").val();
                <?php foreach($replacement as $key => $r){ 
                    if($key == '"') { ?>
                        value = value.replaceAll('<?php echo $key; ?>', '<?php echo $r; ?>');
                    <?php }else { ?>
                        value = value.replaceAll("<?php echo $key; ?>", "<?php echo $r; ?>");
                    <?php }
                 } ?>
                $("#<?php echo $field_name; ?>").val(value);
            });
            
        </script>
        <?php $buff = \ob_get_clean();
        array_push($this->inBuffer, $buff);
        return $this;
    }

    public function render() : string{
        \ob_start(); ?> 
        <form method="post" id="<?php echo $this->id; ?>">
            <?php foreach ($this->inBuffer as $buf) {
                    echo $buf;
            } ?>
            <p <?php echo $this->buttons_line; ?>>
            <?php foreach ($this->buttonsBuffer as $buf) {
                    echo $buf;
            } ?></p>
        </form>
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
                            $("#<?php echo $if->getTarget()['id']; ?>").attr('disabled', false);
                        <?php }else if ($if->getAction() == AvailableIf::HIDE){ ?>
                            $("#<?php echo $if->getTarget()['id']; ?>").show();
                        <?php } ?>
                    }else {
                        <?php if ($if->getAction() == AvailableIf::DISABLED){ ?>
                            $("#<?php echo $if->getTarget()['id']; ?>").attr('disabled', true);
                        <?php }else if ($if->getAction() == AvailableIf::HIDE){ ?>
                            $("#<?php echo $if->getTarget()['id']; ?>").hide();
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