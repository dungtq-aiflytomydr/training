<?php

class TransactionsController extends AppController
{

    /**
     * $uses
     * 
     * @var array 
     */
    public $uses = array('Transaction', 'Category', 'Unit', 'Wallet');

    /**
     * $components
     * 
     * @var array
     */
    public $components = array('MyPagination');

    /**
     * params to save any information from transaction like: total income, total expense
     * 
     * @var $__totalIncome int 
     * @var $__totalExpense int 
     */
    private $__totalIncome  = 0, $__totalExpense = 0;

    /**
     * params to save max amount of transaction
     * 
     * @var $__maxIncome int Save max amount if transaction have expense_type equal in 
     * @var $__maxExpense int Save max amount if transaction have expense_type equal out 
     */
    private $__maxIncome  = 0, $__maxExpense = 0;

    /**
     * params to save transaction element have max amount
     *
     * @var$___maxExpense Save transaction element have max amount with expense_type equal in
     * @var $__eleMaxExpense Save transaction element have max amount with expense_type equal out
     */
    private $__eleMaxIncome, $__eleMaxExpense;

    /**
     * add new transaction
     */
    public function add()
    {
        $this->__redirectIfEmptyWallet();

        $this->set('listCategory', $this->Category->getCategoriesOfWallet(
                        AuthComponent::user('current_wallet')));
        $this->set('title_for_layout', 'Add Transaction');

        if (!$this->request->is(array('post', 'put'))) {
            return;
        }

        $this->Transaction->set($this->request->data);
        if ($this->Transaction->validates()) {

            //process create_time
            $create_time = time();
            if (!empty($this->request->data['Transaction']['create_time'])) {
                $create_time = strtotime($this->request->data['Transaction']['create_time']);
            }
            $this->request->data['Transaction']['create_time'] = $create_time;
            $this->request->data['Transaction']['wallet_id']   = AuthComponent::user('current_wallet');

            if ($this->Transaction->createTransaction($this->request->data)) {

                $this->Session->setFlash('Add new transaction complete.');
                return $this->redirect(array(
                            'controller' => 'transactions',
                            'action'     => 'listSortByDate',
                            date('mY', $create_time),
                ));
            }
        }
        $this->set('validationErrors', $this->Transaction->validationErrors);
    }

    /**
     * show list transaction sort by date (view in month)
     * 
     * @param $dateTime String
     */
    public function listSortByDate($dateTime = null)
    {
        $this->__redirectIfEmptyWallet();

        $findTime = $this->__processFindDateTime($dateTime);

        $listTransaction = $this->Transaction->getListTransactionsByDate($findTime);
        $listTransaction = $this->__convertElementInListTransaction($listTransaction);
        $listTransaction = $this->__processShowByDate($listTransaction);

        //process pagination
        $url = Router::fullBaseUrl() . '/transactions/listSortByDate';
        if (!empty($dateTime)) {
            $url = $url . '/' . $dateTime;
        }
        $numPerPage = 5;
        $this->MyPagination->initialize($this);
        $pagination = $this->MyPagination->pagination(count($listTransaction), $numPerPage, $url);

        $listTransaction = $this->__processPagination($listTransaction, $pagination);

        //information for show statistical
        $statistical_data = $this->__getInfoForStatistical();

        $this->set('title_for_layout', 'List transaction');
        $this->set('date_time', $findTime['time']);
        $this->set('statistical_data', $statistical_data);
        $this->set('listTransaction', $listTransaction);
        $this->set('pagination', $pagination);
    }

    /**
     * show list transaction sort by category (view in month)
     */
    public function listSortByCategory($dateTime = null)
    {
        $this->__redirectIfEmptyWallet();
        $findTime = $this->__processFindDateTime($dateTime);

        $listTransaction = $this->Transaction->getListTransactionsByDate($findTime);
        $listTransaction = $this->__convertElementInListTransaction($listTransaction);
        $listTransaction = $this->__processShowByCategory($listTransaction);

        //process pagination
        $url = Router::fullBaseUrl() . '/transactions/listSortByCategory';
        if (!empty($dateTime)) {
            $url = $url . '/' . $dateTime;
        }
        $numPerPage = 4;
        $this->MyPagination->initialize($this);
        $pagination = $this->MyPagination->pagination(count($listTransaction), $numPerPage, $url);

        $listTransaction = $this->__processPagination($listTransaction, $pagination);

        //information for show statistical
        $statistical_data = $this->__getInfoForStatistical();

        $this->set('title_for_layout', 'List transaction');
        $this->set('date_time', $findTime['time']);
        $this->set('statistical_data', $statistical_data);
        $this->set('listTransaction', $listTransaction);
        $this->set('pagination', $pagination);
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
    private function __processPagination($list, $pagination = null)
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

    /**
     * edit transaction information
     * 
     * @param int $id Transaction id
     */
    public function edit($id)
    {
        $transactionObj = $this->Transaction->findById($id);
        if (empty($transactionObj)) {
            throw new NotFoundException('Could not find that transaction.');
        }

        if ($transactionObj['Transaction']['wallet_id'] !== AuthComponent::user('current_wallet')) {
            throw new NotFoundException('Access is denied.');
        }

        $this->set('title_for_layout', 'Edit transaction');
        $this->set('listCategory', $this->Category->getCategoriesOfWallet(
                        AuthComponent::user('current_wallet')));
        $this->set('transactionObj', $transactionObj);

        if (empty($this->request->data)) {
            $this->request->data                               = $transactionObj;
            $this->request->data['Transaction']['create_time'] = date('d-m-Y', $transactionObj['Transaction']['create_time']);
        }

        if (!$this->request->is(array('post', 'put'))) {
            return;
        }

        $this->Transaction->set($this->request->data);
        if ($this->Transaction->validates()) {

            //process datetime
            if (!empty($this->request->data['Transaction']['create_time'])) {
                $create_time                                       = strtotime($this->request->data['Transaction']['create_time']);
                $this->request->data['Transaction']['create_time'] = $create_time;
            }

            $isUpdated = $this->Transaction->updateById($id, $this->request->data);
            if ($isUpdated) {

                $this->Session->setFlash("Update transaction information complete.");
                $this->redirect(array(
                    'controller' => 'transactions',
                    'action'     => 'listSortByDate',
                    date('mY', $create_time),
                ));
            }
            $this->Session->setFlash("Have error! Please try again.");
        }
    }

    /**
     * delete transaction by id
     * 
     * @param int $id Transaction id
     */
    public function delete($id)
    {
        $this->autoRender = false;

        if (!$this->request->is('post')) {
            throw new BadRequestException('Could not found request.');
        }

        $transactionObj = $this->Transaction->findById($id);
        if (empty($transactionObj)) {
            throw new NotFoundException('Could not find that transaction.');
        }

        $this->Transaction->deleteById($id);

        $this->Session->setFlash("Delete transaction complete.");
        return $this->redirect(array(
                    'controller' => 'transactions',
                    'action'     => 'listSortByDate',
                    date('mY', $transactionObj['Transaction']['create_time']),
        ));
    }

    /**
     * Report transactions
     * @param string $dateTime date time want to show report
     */
    public function report($dateTime = null)
    {
        $findTime = $this->__processFindDateTime($dateTime);

        $listTransaction = $this->Transaction->getListTransactionsByDate($findTime);

        //get Category information
        $listCategory = $this->Category->getCategoriesOfWallet(AuthComponent::user('current_wallet'));
        //remove key 'Category' in $listCategory
        $listCategory = array_column($listCategory, 'Category');

        foreach ($listTransaction as $key => $transaction) {
            //get category's information of each transaction
            $keyCategory                                           = array_search($transaction['Transaction']['category_id'], array_column($listCategory, 'id'));
            $listTransaction[$key]['Transaction']['category_info'] = $listCategory[$keyCategory];

            //find transaction have max amount within each expense_type
            $this->__maxTransactionByExpenseType($listTransaction[$key]);
        }

        $listTransaction = $this->__processShowReport($listTransaction);

        $statisticalData = $this->__getInfoForStatistical();

        $this->set('date_time', $findTime['time']);
        $this->set('statistical_data', $statisticalData);
        $this->set('listTransaction', $listTransaction);
    }

    /**
     * Process find date time
     * 
     * @param string $dateTime Date time can convert
     * @return array
     */
    private function __processFindDateTime($dateTime)
    {
        if (!empty($dateTime)) {
            $month = substr($dateTime, 0, 2);
            $year  = substr($dateTime, 2, strlen($dateTime));
        } else {
            $month = date('m', time());
            $year  = date('Y', time());
        }

        $firstDayOfMonth = date_create($year . '-' . $month)
                ->modify('first day of this month')
                ->format('d');

        $lastDayOfMonth = date_create($year . '-' . $month)
                ->modify('last day of this month')
                ->format('d');

        $startTime = strtotime($year . '-' . $month . '-' . $firstDayOfMonth);
        $endTime   = strtotime($year . '-' . $month . '-' . $lastDayOfMonth);

        //array datetime want to show report
        return array(
            'start_time' => $startTime,
            'end_time'   => $endTime,
            'time'       => date('m-Y', $startTime),
        );
    }

    /**
     * get list transaction by current wallet
     * 
     * @param array $listTransaction List transaction
     * @return array
     */
    private function __convertElementInListTransaction($listTransaction)
    {
        $listCategory = $this->Category->getCategoriesOfWallet(AuthComponent::user('current_wallet'));
        //remove key 'Category' in $listCategory
        $listCategory = array_column($listCategory, 'Category');

        foreach ($listTransaction as $key => $transaction) {

            //instead 'category_id' property = category's information
            $keyCategory                                           = array_search($transaction['Transaction']['category_id'], array_column($listCategory, 'id'));
            $listTransaction[$key]['Transaction']['category_info'] = $listCategory[$keyCategory];

            //process other infor like: total income, total expense...
            $this->__processAmount($listTransaction[$key]);
        }

        return $listTransaction;
    }

    /**
     * get other transaction's information for show statistical
     * 
     * @return array
     */
    private function __getInfoForStatistical()
    {
        return array(
            'income'     => $this->__totalIncome,
            'expense'    => $this->__totalExpense,
            'maxIncome'  => $this->__eleMaxIncome['Transaction'],
            'maxExpense' => $this->__eleMaxExpense['Transaction'],
            'balance'    => AuthComponent::user('current_wallet_info')['balance'],
            'total'      => AuthComponent::user('current_wallet_info')['balance'] + $this->__totalIncome - $this->__totalExpense,
            'unit'       => $this->Unit->getById(AuthComponent::user('current_wallet_info')['unit_id'])['Unit'],
        );
    }

    /**
     * process display list transaction
     * 
     * @param array $array
     * @return array
     */
    private function __processShowReport($array)
    {
        $newList = array(); //new list after sort by category

        foreach ($array as $key => $value) {

            unset($array[$key]);

            //if $value have create_time not exists in list key of $newList => add
            if (!array_key_exists($value['Transaction']['category_info']['id'], $newList)) {

                //find all transactions have category_id equals category_id of $value
                $newList[$value['Transaction']['category_info']['id']] = array(
                    'totalMoney' => $this->__sumMoneyOfCategory($array, $value),
                    'category'   => $value['Transaction']['category_info'],
                );
            }
        }
        return $newList;
    }

    /**
     * process amount by expense type
     * 
     * @param object $transaction Transaction data
     */
    private function __processAmount($transaction)
    {
        if ($transaction['Transaction']['category_info']['expense_type'] == 'in') {
            $this->__totalIncome += $transaction['Transaction']['amount'];
        } else {
            $this->__totalExpense += $transaction['Transaction']['amount'];
        }
    }

    /**
     * show list transaction by date range
     * 
     * @param array $array list transaction get from database
     * @return array
     */
    private function __processShowByDate($array)
    {
        $newList = array(); //new list after sort by date

        foreach ($array as $key => $value) {

            unset($array[$key]);

            //if $value have create_time not exists in list key of $newList => add
            if (!array_key_exists($value['Transaction']['create_time'], $newList)) {

                //find all transactions have create_time equals create_time of $value
                $newList[$value['Transaction']['create_time']] = array(
                    'listTransaction' => $this->__findPropertyTogether($array, 'create_time', $value),
                    'create_time'     => $value['Transaction']['create_time'],
                );
            }
        }

        return $newList;
    }

    /**
     * show list transaction by category
     * 
     * @param array $array List transaction get from database
     * @return array
     */
    private function __processShowByCategory($array)
    {
        $newList = array(); //new list after sort by category

        foreach ($array as $key => $value) {

            unset($array[$key]);

            //if $value have create_time not exists in list key of $newList => add
            if (!array_key_exists($value['Transaction']['category_info']['id'], $newList)) {

                //find all transactions have category_id equals category_id of $value
                $newList[$value['Transaction']['category_info']['id']] = array(
                    'listTransaction' => $this->__findPropertyTogether($array, 'category_info', $value),
                    'category'        => $value['Transaction']['category_info'],
                );
            }
        }
        return $newList;
    }

    /**
     * get Sum money of all transactions have same category
     * 
     * @param array $array Transaction array
     * @param object $objCompare Transaction object want to add sum
     * @return int
     */
    private function __sumMoneyOfCategory($array, $objCompare)
    {
        $sumMoney = $objCompare['Transaction']['amount'];
        foreach ($array as $value) {
            if ($objCompare['Transaction']['category_id'] === $value['Transaction']['category_id']) {
                $sumMoney += $value['Transaction']['amount'];
            }
        }
        return $sumMoney;
    }

    /**
     * get transaction have max amount
     * 
     * @param object $transaction Transaction object
     */
    private function __maxTransactionByExpenseType($transaction)
    {
        if ($transaction['Transaction']['category_info']['expense_type'] == 'in') {
            //sum amount of transaction have expense_type = income
            $this->__totalIncome += $transaction['Transaction']['amount'];

            if ($transaction['Transaction']['amount'] > $this->__maxIncome) {
                $this->__maxIncome    = $transaction['Transaction']['amount'];
                $this->__eleMaxIncome = $transaction;
            }
        } else {
            //sum amount of transaction have expense_type = expense
            $this->__totalExpense += $transaction['Transaction']['amount'];

            if ($transaction['Transaction']['amount'] > $this->__maxExpense) {
                $this->__maxExpense    = $transaction['Transaction']['amount'];
                $this->__eleMaxExpense = $transaction;
            }
        }
    }

    /**
     * find all element have property equals property in object want to compare
     * 
     * @param array $array Array want to process
     * @param string $property Property want to compare
     * @param object $objCompare Object want to compare
     * @return array
     */
    private function __findPropertyTogether($array, $property, $objCompare)
    {
        $newList   = array(); //array contains elements have same property
        $newList[] = $objCompare;

        foreach ($array as $value) {
            if ($objCompare['Transaction'][$property] == $value['Transaction'][$property]) {
                $newList[] = $value;
            }
        }
        return $newList;
    }

    /**
     * Check current wallet exists or not
     * 
     * If not exists -> not add & show list category
     */
    private function __redirectIfEmptyWallet()
    {
        if (empty($this->Wallet->countUserWallets(AuthComponent::user('id')))) {
            return $this->redirect(array(
                        'controller' => 'wallets',
                        'action'     => 'listWallet',
            ));
        }
    }

}
