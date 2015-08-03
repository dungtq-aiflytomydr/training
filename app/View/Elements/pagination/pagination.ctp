<?php
echo $this->Html->css('pagination');

$currentPage = $pagination['currentPage'];
$totalPages  = $pagination['totalPages'];
$url         = $pagination['url'];
?>
<footer class="pagination-area">
    <div class="footer-info text-center"><?php echo 'Trang ' . $currentPage . '/' . $totalPages; ?></div>
    <div class="footer-content text-center">
        <!-- start button first  -->
        <?php if ($currentPage > 2) : ?>
            <a class='num-page' href="<?php echo $url; ?>">First</a>
        <?php endif; ?>

        <?php if ($currentPage > 1) : ?>
            <?php if ($currentPage - 1 > 1) : ?>
                <a class='num-page' href="<?php echo $url . '/page:' . ($currentPage - 1); ?>"><<</a>
            <?php else : ?>
                <a class='num-page' href="<?php echo $url; ?>"><<</a>
            <?php endif; ?>
        <?php endif; ?>

        <!-- start button numbers of pages  -->
        <?php
        $arrDot = array(''); //tao mang kiem tra phan tu giong nhau
        $lbPage = "";
        for ($i = 1; $i <= $totalPages; $i++) :
            if ($currentPage < 3) :
                if ($i == $currentPage) :
                    $lbPage = "<a class='page-current'>$i</a>";
                elseif ($i <= 5 || $i == ($currentPage + 10)) :
                    if ($i == 1) :
                        $lbPage = "<a class='num-page' href='" . $url . "'>" . $i . "</a>";
                    else :
                        $lbPage = "<a class='num-page' href='" . $url . "/page:$i'>" . $i . "</a>";
                    endif;
                elseif ($i < ($currentPage - 2) || $i > ($currentPage + 2)) :
                    $lbPage = "<a>...</a>";
                    if ($lbPage === $arrDot[$i - 1]) :
                        $lbPage = "";
                    endif;
                else :
                    $lbPage = "<a class='num-page' href='" . $url . "/page:$i'>" . $i . "</a>";
                endif;
            else :
                if ($i == $currentPage) :
                    $lbPage = "<a class='page-current'>$i</a>";
                elseif ($i == ($currentPage + 10)) :
                    $lbPage = "<a class='num-page' href='" . $url . "/page:$i'>" . $i . "</a>";
                elseif ($i < ($currentPage - 2) || $i > ($currentPage + 2)) :
                    $lbPage = "<a>...</a>";
                    if ($lbPage === $arrDot[$i - 1]) :
                        $lbPage = "";
                    endif;
                else :
                    $lbPage = "<a class='num-page' href='" . $url . "/page:$i'>" . $i . "</a>";
                endif;
            endif;
            $arrDot[$i] = $lbPage;
            if ($lbPage === "") :
                $arrDot[$i] = "<a>...</a>";
            endif;

            echo $lbPage;
        endfor;
        ?>
        <!-- end button numbers of pages  -->

        <?php if ($currentPage < $totalPages) : ?>
            <a class='num-page' href="<?php echo $url . '/page:' . ($currentPage + 1); ?>">>></a>
        <?php endif; ?>

        <?php if ($currentPage < $totalPages && $totalPages > 2) : ?>
            <a class='num-page' href="<?php echo $url . '/page:' . $totalPages; ?>">End</a>
        <?php endif; ?>
    </div>
</footer>