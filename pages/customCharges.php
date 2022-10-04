<?php

namespace Stanford\CustomCharges;

/** @var \Stanford\CustomCharges\CustomCharges $module */
try {
    if (!defined('SUPER_USER') or SUPER_USER == 0) {
        throw new \Exception('You are not allowed in this page.');
    }
    ?>
    <?php print loadJS('vue/vue-factory/dist/js/app.js') ?>
    <script src="<?php echo $module->getUrl("frontend/dist/custom_charges_vue.umd.js") ?>"></script>
    <div id="custom-charges"></div>
    <script>
        window.ajaxURL = "<?php echo $module->getUrl('ajax/handle.php') ?>"
        window.csrf_token = "<?=$module->getCSRFToken()?>";
        window.GET_CHARGES = "<?=$module::GET_CHARGES?>";
        window.SAVE_CHARGE = "<?=$module::SAVE_CHARGE?>";
        window.MODULE_LIST = "<?=$module::MODULE_LIST?>";
        window.DELETE_CHARGE = "<?=$module::DELETE_CHARGE?>";

        window.addEventListener('DOMContentLoaded', function (event) {
            const componentPromise = window.renderVueComponent(custom_charges_vue, '#custom-charges')
            componentPromise.then(component => {
                console.log('component is ready')
            })
        })
    </script>
    <?php
} catch (\Exception $e) {
    ?>
    <div class="alert alert-danger"><?php echo $e->getMessage(); ?></div>
    <?php
}
