<?php echo $this->Html->script('Users/processUser'); ?>;

<?php
if (strpos(Router::url(), 'password') !== false) {
    require 'change_pw.ctp';
} else if (strpos(Router::url(), 'info') !== false) {
    require 'change_info.ctp';
}
?>

<script type="text/javascript">
    jQuery(document).ready(function () {
        Users.init();
    });
</script>
