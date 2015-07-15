<h3 class="align-center">List transaction</h3>
<div class="right">
    <h3 style="text-align: right; display: inline-block">Sort by: </h3>
    <select id="sortBy">
        <option value="listSortByDate" <?php
        if (strpos(Router::url(), 'listSortByDate') !== false) : echo 'selected';
        endif;
        ?>>Date</option>
        <option value="listSortByCategory" <?php
        if (strpos(Router::url(), 'listSortByCategory') !== false) : echo 'selected';
        endif;
        ?>>Category</option>
    </select>
</div>