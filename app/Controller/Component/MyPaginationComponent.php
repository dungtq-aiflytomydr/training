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
     * setup pagination
     * 
     * @param int $totalRecords - total records
     * @param int $numPerPage - numbers of record per one page
     * @param string $url - link
     * @return array
     */
    public function setting($totalRecords, $numPerPage, $url)
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

    /**
     * process array want to display with custom pagination
     * 
     * @param array $list Array want to process
     * @param array $pagination Array pagination for process, ex(
     *      $pagination = array(
     *          'numPerPage' => 4, 
     *          'currentPage' => 3,
     *      )
     * )
     * @return array
     */
    public function pagination($list, $pagination = null)
    {
        $newList  = array();
        //position of first element want to display
        $startPos = ($pagination['currentPage'] - 1) * $pagination['numPerPage'];
        //find first element
        $findPos  = 0;
        //count nums records want to display
        $count    = 0;

        foreach ($list as $value) {
            if ($findPos >= $startPos && $count < $pagination['numPerPage']) {
                $newList[] = $value;
                $count++;
            }
            $findPos++;
        }
        return $newList;
    }

}
