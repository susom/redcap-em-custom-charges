<?php

namespace Stanford\CustomCharges;

/** @var \Stanford\CustomCharges\CustomCharges $module */
try {
    if (!$module->isSuperUser()) {
        throw new \Exception('You are not allowed in this page.');
    }
    ?>
    <?php print loadJS('vue/vue-factory/dist/js/app.js') ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">


    <div id="app"></div>
    <script>
        window.ajaxURL = "<?php echo $module->getUrl('ajax/handle.php') ?>"
        window.csrf_token = "<?=$module->getCSRFToken()?>";
        window.GET_CHARGES = "<?=$module::GET_CHARGES?>";
        window.SAVE_CHARGE = "<?=$module::SAVE_CHARGE?>";
        window.MODULE_LIST = "<?=$module::MODULE_LIST?>";
        window.DELETE_CHARGE = "<?=$module::DELETE_CHARGE?>";
        window.EDIT_CHARGE = "<?=$module::EDIT_CHARGE?>";
        window.HAS_RMA = "<?=$module->doesProjectHaveRMA()?>";

        // window.addEventListener('DOMContentLoaded', function (event) {
        //     const componentPromise = window.renderVueComponent(custom_charges_vue, '#custom-charges')
        //     componentPromise.then(component => {
        //         console.log('component is ready')
        //     })
        // })
    </script>
    <script src="<?php echo $module->getUrl("frontend_3/public/js/bundle.js") ?>"></script>
    <?php
} catch (\Exception $e) {
    ?>
    <div class="alert alert-danger"><?php echo $e->getMessage(); ?></div>
    <?php
}
