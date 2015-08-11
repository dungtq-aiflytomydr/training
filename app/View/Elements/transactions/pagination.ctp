<?php

if ($this->request->params['paging']['Transaction']['pageCount'] > 1):
    echo "<div class='cus-pagination'>";
    echo "<div class='pagination-info'>{$this->Paginator->counter()}</div>";

    echo "<div class='pagination-content'>";
    if ($this->Paginator->current() > 1) :
        echo $this->Paginator->prev('<<');
    endif;
    echo $this->Paginator->numbers(array(
        'modulus'   => 3,
        'ellipsis'  => '...',
        'first'     => 1,
        'last'      => 1,
        'separator' => null,
    ));
    if ($this->Paginator->current() < $this->request->params['paging']['Transaction']['pageCount']) :
        echo $this->Paginator->next('>>');
    endif;
    echo "</div></div>";
    endif;