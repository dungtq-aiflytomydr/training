<?php

if (strpos(Router::url(), 'password') !== false) :
    require 'change_pw.ctp';
elseif (strpos(Router::url(), 'info') !== false) :
    require 'change_info.ctp';
endif;
;
?>