<?php

/**
 * Description of PaginationComponent
 *
 * @author dungtq
 */
App::uses('Component', 'Controller');

class MyPaginationComponent extends Component
{

    private $__controller;

    public function initialize(Controller $controller)
    {
        parent::initialize($controller);
        $this->__controller = $controller;
    }

    /**
     * pagination - setup pagination
     * @param int $totalRecords - total records
     * @param int $numPerPage - numbers of record per one page
     * @param string $url - link
     * @return array
     */
    public function pagination($totalRecords, $numPerPage, $url)
    {
        //get current page
        $currentPage = 1;
        if (!empty($this->__controller->request->params['named']['page'])) {
            $currentPage = $this->__controller->request->params['named']['page'];
        }

        //get total page
        $totalPages = ceil($totalRecords / $numPerPage);
        return array(
            'totalPages'  => $totalPages,
            'numPerPage'  => $numPerPage,
            'currentPage' => $currentPage,
            'url'         => $url,
        );
    }

}
