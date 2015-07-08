<?php
if (strpos(Router::url(), 'password') !== false) {
    require 'change_pw.ctp';
} else if (strpos(Router::url(), 'info') !== false) {
    require 'change_info.ctp';
}
?>