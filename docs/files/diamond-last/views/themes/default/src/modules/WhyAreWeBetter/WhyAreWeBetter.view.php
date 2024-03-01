<section id="WhyAreWeBetter" data-baseLink="<?= LINK; ?>">
    <div class="container-fluid" id="wawb_render">
        <div class="rows">
          <div class="col-lg-4 <?php echo ($this->config["animations"]) ? "wow fadeInLeft" : ""; ?>" data-wow-delay="0.2s" ><center>
          <h3 id="wawb_parenticon_1">
            <?php if (substr($this->config["col_1"]['icon'], 0, 2) == "fa") { ?>
              <i class="fa-5x <?php echo $this->config["col_1"]['icon'];?> " id="wawb_icon_1" aria-hidden="true"></i>
            <?php }else { ?>
              <img width="220px" src="<?= $this->config["col_1"]['icon']; ?>" id="wawb_icon_1" alt="">
            <?php } ?>
          </h3>            
          <h2 id="wawb_title_1"><?php echo $this->config["col_1"]['title'];?></h2>
          <p id="wawb_desc_1"><?php echo $this->config["col_1"]['desc']?></p>
          </center></div><!-- /.col-lg-4 -->
          <div class="col-lg-4 <?php echo ($this->config["animations"]) ? "wow fadeInUp" : ""; ?> " data-wow-delay="0.2s" ><center>
          <h3 id="wawb_parenticon_2">
            <?php if (substr($this->config["col_2"]['icon'], 0, 2) == "fa") { ?>
              <i class="fa-5x <?php echo $this->config["col_2"]['icon']; ?> " id="wawb_icon_2" aria-hidden="true"></i>
            <?php }else { ?>
              <img width="220px" src="<?php echo $this->config["col_2"]['icon']; ?>" id="wawb_icon_2" alt="">
            <?php } ?>
          </h3>    
            <h2 id="wawb_title_2"><?php echo $this->config["col_2"]['title']; ?></h2>
            <p id="wawb_desc_2"><?php echo $this->config["col_2"]['desc']?></p>
          </center></div><!-- /.col-lg-4 -->
          <div class="col-lg-4 <?php echo ($this->config["animations"]) ? "wow fadeInRight" : ""; ?>" data-wow-delay="0.2s" ><center>
          <h3 id="wawb_parenticon_3">
            <?php if (substr($this->config["col_3"]['icon'], 0, 2) == "fa") { ?>
              <i class="fa-5x <?php echo $this->config["col_3"]['icon']; ?>" id="wawb_icon_3" aria-hidden="true"></i>
            <?php }else { ?>
              <img width="220px" src="<?php echo $this->config["col_3"]['icon']; ?>" id="wawb_icon_3" alt="">
            <?php } ?>
          </h3>                
            <h2 id="wawb_title_3"><?php echo $this->config["col_3"]['title']; ?></h2>
            <p id="wawb_desc_3"><?php echo $this->config["col_3"]['desc']; ?></p>
          </center></div><!-- /.col-lg-4 -->
        </div><!-- /.row -->
    </div>
    <?php if ($editing_mode){ ?>
      <p class="text-center"><br><button class="btn btn-lg btn-danger" data-toggle="modal" data-target="#WhyAreWeBetterModal"><strong>Modifier ces informations</strong></button></p>
    <?php } ?>
  </section>

<?php if ($editing_mode){ ?>
<div id="WhyAreWeBetterModal" class="modal fade">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">Editer le module WhyAreWeBetter</h4>
      </div>
      <div class="modal-body">
      <div id="WhyAreWeBetterDirectEdit">
      </div><br>
      <hr>
      <form id="wawb_edit_dorm">
        <input type="hidden" name="mm" value="<?php echo $this->mm_instance->getPageName(); ?>">
        <div class="container-fluid">
          <div class="rows">
            <div class="col-lg-4">
              <div class="form-group">
                <label  class="form-label">Titre (1) :</label>
                <input class="form-control wawb_edit_field" type="text" data-colid="1" data-originalName="title" value="<?php echo $this->config["col_1"]['title'];?>" name="title[1]" id="title_field_1">
              </div>
              <div class="form-group">
                <label  class="form-label">Contenu (1) :</label>
                <input class="form-control wawb_edit_field" type="text" data-colid="1" data-originalName="desc" value="<?php echo $this->config["col_1"]['desc'];?>" name="desc[1]" id="desc_field_1">
              </div>
              <div class="form-group">
                <label  class="form-label">Icone (1) :</label>
                <input class="form-control wawb_edit_field" type="text" data-colid="1" data-originalName="icon" value="<?php echo $this->config["col_1"]['icon'];?>" name="icon[1]" id="icon_field_1">
              </div>
            </div>
            <div class="col-lg-4">
              <div class="form-group">
                <label  class="form-label">Titre (2) :</label>
                <input class="form-control wawb_edit_field" type="text" data-colid="2" data-originalName="title" value="<?php echo $this->config["col_2"]['title'];?>" name="title[2]" id="title_field_2">
              </div>
              <div class="form-group">
                <label  class="form-label">Contenu (2) :</label>
                <input class="form-control wawb_edit_field" type="text" data-colid="2" data-originalName="desc" value="<?php echo $this->config["col_2"]['desc'];?>" name="desc[2]" id="desc_field_2">
              </div>
              <div class="form-group">
                <label  class="form-label">Icone (2) :</label>
                <input class="form-control wawb_edit_field" type="text" data-colid="2" data-originalName="icon" value="<?php echo $this->config["col_2"]['icon'];?>" name="icon[2]" id="icon_field_2">
              </div>
            </div>
            <div class="col-lg-4">
              <div class="form-group">
                <label  class="form-label">Titre (3) :</label>
                <input class="form-control wawb_edit_field" type="text" data-colid="3" data-originalName="title" value="<?php echo $this->config["col_3"]['title'];?>" name="title[3]" id="title_field_3">
              </div>
              <div class="form-group">
                <label class="form-label">Contenu (3) :</label>
                <input class="form-control wawb_edit_field" type="text" data-colid="3" data-originalName="desc" value="<?php echo $this->config["col_3"]['desc'];?>" name="desc[3]" id="desc_field_3">
              </div>
              <div class="form-group">
                <label class="form-label">Icone (3) :</label>
                <input class="form-control wawb_edit_field" type="text" data-colid="3" data-originalName="icon" value="<?php echo $this->config["col_3"]['icon'];?>" name="icon[3]" id="icon_field_3">
              </div>
            </div>
            <div class="col-lg-12">
              <div class="form-check">
                  <label class="animations">
                  <input class="form-check-input" type="checkbox" <?php echo ($this->config['animations']) ? "checked" : "";?> id="wawb_animations" name="animations">
                  Activer les animations au chargement de la page
                  </label>
              </div>
            </div>
            
          </div>
        </div>
      </form>
                          
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
        <button class="btn btn-custom ajax-simpleSend" data-api="<?= LINK; ?>api/" 
                data-module="editing/" data-verbe="set" data-func="whyAreWeBetterConfig" 
                data-tosend="#wawb_edit_dorm" data-useForm="true" data-reload="true">
                Sauvegarder</button>                 
      </div>
    </div>
  </div>
</div>
<?php } ?>