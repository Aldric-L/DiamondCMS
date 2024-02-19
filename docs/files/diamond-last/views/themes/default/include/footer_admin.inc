<?php global $controleur_def;
$js = $controleur_def->getJS(); ?>
</div>
            
            <?php if (!$controleur_def->is_iframer){ ?>
            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; <a href="https://aldric-l.github.io/DiamondCMS/">DiamondCMS</a> - Version <?php echo DCMS_VERSION; ?> - Interface d'administration</span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->
            <?php } ?>

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <?php if (!$controleur_def->is_iframer){ ?>
    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>
    <?php } ?>

    <?php if (isset($controleur_def->is_iframer) && !$controleur_def->is_iframer){?>

    <?php
    $controleur_def->pluginloader(false, true);
    ?>

    <?php } ?>

    <?php 
    //On charge les JS demandés par le controleur
    if (!empty($js)){
      foreach($js as $j){
        echo '<script src="'. $j . '"></script>';
      }
    }
    ?>

    <?php if (isset($controleur_def->is_iframer) && !$controleur_def->is_iframer){?>

    <script src="<?php echo LINK . 'js/plugins/listener/plugins_listener.js'; ?>"></script>
    
    <?php } ?>


</body>

</html>
