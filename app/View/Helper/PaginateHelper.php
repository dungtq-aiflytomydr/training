<?php

/**
 * Description of PaginationHelper
 *
 * @author dungtq
 */
App::uses('AppHelper', 'View/Helper');

class PaginateHelper extends AppHelper
{

    /**
     * pagination - setup pagination
     * @param int $totalRecords - total records
     * @param int $numPerPage - numbers of record per one page
     * @param string $url - link
     * @return array
     */
    public function pagination($totalRecords, $numPerPage, $url)
    {
        $totalPages = ceil($totalRecords / $numPerPage);
        return array(
            'totalPages' => $totalPages,
            'numPerPage' => $numPerPage,
            'url'        => $url,
        );
    }

}
