<?php

class TransactionsController extends AppController
{

    /**
     * $uses
     * 
     * @var type 
     */
    public $uses = array('Transaction', 'Category', 'Unit');

    /**
     * $helpers
     * 
     * @var type 
     */
    public $helpers = array(
        'Form',
        'Html',
        'Session',
    );

    /**
     * params for get information from transaction like: total income, total expense
     * 
     * @var $_totalIncome int 
     * @var $_totalExpense int 
     */
    private $_totalIncome  = 0, $_totalExpense = 0;
    private $_maxIncome    = 0, $_maxExpense   = 0;
    private $_eleMaxIncome, $_eleMaxExpense;

    /**
     * default function => redirect to listCategories
     */
    public function index()
    {
        return $this->redirect(array(
                    'controller' => 'transactions',
                    'action'     => 'listSortByDate',
        ));
    }

    /**
     * add new transaction
     */
    public function add()
    {
        $this->redirectIfCurrentWalletNotExists();

        date_default_timezone_set("Asia/Ho_Chi_Minh");

        $this->set('listCategory', $this->Category->getListCategoryByWalletId(
                        AuthComponent::user('current_wallet')));
        $this->set('title_for_layout', 'Add Transaction');

        if (!$this->request->is('post', 'put')) {
            return;
        }

        $this->Transaction->set($this->request->data);
        if ($this->Transaction->validates()) {

            //process create_time
            $create_time = time();
            if (!empty($this->request->data['Transaction']['create_time'])) {
                $create_time = strtotime(str_replace('/', '-', $this->request->data['Transaction']['create_time']));
            }
            $this->request->data['Transaction']['create_time'] = $create_time;
            $this->request->data['Transaction']['wallet_id']   = AuthComponent::user('current_wallet');

            if ($this->Transaction->createTransaction($this->request->data)) {

                $this->Session->setFlash('Add new transaction complete.');
                return $this->redirect(array(
                            'controller' => 'transactions',
                            'action'     => 'listSortByDate',
                ));
            }
        }
    }

    /**
     * show list transaction sort by date
     */
    public function listSortByDate()
    {
        $this->redirectIfCurrentWalletNotExists();

        $listTransaction = $this->getListTransaction();
        $listTransaction = $this->processShowByDate($listTransaction);

        $statistical_data = $this->getInfoForStatistical();

        $this->set('title_for_layout', 'List transaction');
        $this->set('statistical_data', $statistical_data);
        $this->set('listTransaction', $listTransaction);
    }

    /**
     * show list transaction sort by category
     */
    public function listSortByCategory()
    {
        $this->redirectIfCurrentWalletNotExists();

        $listTransaction = $this->getListTransaction();
        $listTransaction = $this->processShowByCategory($listTransaction);

        $statistical_data = $this->getInfoForStatistical();

        $this->set('title_for_layout', 'List transaction');
        $this->set('statistical_data', $statistical_data);
        $this->set('listTransaction', $listTransaction);
    }

    /**
     * get list transaction by current wallet
     */
    private function getListTransaction()
    {
        $listTransaction = $this->Transaction->getListTransactionsByWalletId(
                AuthComponent::user('current_wallet'));

        //convert any properties of transaction: money (1000 => 1.000), category_id(int) => object
        foreach ($listTransaction as $key => $transaction) {

            //instead 'category_id' property = category's information
            $listTransaction[$key]['Transaction']['category_id'] = $this->Category->getCategoryById(
                    $transaction['Transaction']['category_id']);

            $listTransaction[$key]['Transaction']['amount'] = $this->convertMoney(
                    $transaction['Transaction']['amount']);

            //process other infor like: total income, total expense...
            $this->processAmount(
                    $transaction['Transaction']['amount'], $listTransaction[$key]['Transaction']['category_id']['expense_type']);
        }

        return $listTransaction;
    }

    /**
     * get other transaction's information for show statistical
     */
    private function getInfoForStatistical()
    {
        return array(
            'income'  => $this->convertMoney($this->_totalIncome),
            'expense' => $this->convertMoney($this->_totalExpense),
            'balance' => $this->convertMoney(AuthComponent::user('current_wallet_info')['balance']),
            'total'   => $this->convertMoney(
                    AuthComponent::user('current_wallet_info')['balance'] + $this->_totalIncome - $this->_totalExpense
            ),
            'unit'    => $this->Unit->find('first', array(
                'conditions' => array(
                    'Unit.id' => AuthComponent::user('current_wallet_info')['unit_id'],
                ),
            ))['Unit'],
        );
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

        $this->set('title_for_layout', 'Edit transaction');
        $this->set('listCategory', $this->Category->getListCategoryByWalletId(
                        AuthComponent::user('current_wallet')));
        $this->set('transactionObj', $transactionObj);

        if (!$this->request->is('post', 'put')) {
            return;
        }

        $this->Transaction->set($this->request->data);
        if ($this->Transaction->validates()) {

            //process datetime
            $create_time = $transactionObj['Transaction']['create_time'];
            if (!empty($this->request->data['Transaction']['create_time'])) {
                $create_time = strtotime(str_replace('/', '-', $this->request->data['Transaction']['create_time']));
            }
            $this->request->data['Transaction']['create_time'] = $create_time;

            $isUpdated = $this->Transaction->updateTransactionById($id, $this->request->data);
            if ($isUpdated) {

                $this->Session->setFlash("Update transaction information complete.");
                $this->redirect(array(
                    'controller' => 'transactions',
                    'action'     => 'listSortByDate',
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

        $transactionObj = $this->Transaction->findById($id);
        if (empty($transactionObj)) {
            throw new NotFoundException('Could not find that transaction.');
        }

        $this->Transaction->deleteById($id);
        $this->Session->setFlash("Delete transaction complete.");
        return $this->redirect(array(
                    'controller' => 'transactions',
                    'action'     => 'listSortByDate',
        ));
    }

    /**
     * convert money (ex: 1000 => 1.000)
     * 
     * @param int $money
     * @return string
     */
    private function convertMoney($money)
    {
        return number_format($money, null, null, '.');
    }

    /**
     * process amount by expense type
     * 
     * @param int $amount Money was used in transaction
     * @param string $expense_type Income or Expense
     */
    private function processAmount($amount, $expense_type)
    {
        if ($expense_type == 'in') {
            $this->_totalIncome += $amount;
        } else {
            $this->_totalExpense += $amount;
        }
    }

    /**
     * show list transaction by date range
     * 
     * @param array $array list transaction get from database
     * @return array
     */
    private function processShowByDate($array)
    {
        $newList = array(); //new list after sort by date

        foreach ($array as $key => $value) {

            unset($array[$key]);

            //if $value have create_time not exists in list key of $newList => add
            if (!array_key_exists($value['Transaction']['create_time'], $newList)) {

                //find all transactions have create_time equals $value
                $newList[$value['Transaction']['create_time']] = array(
                    'listTransaction' => $this->findPropertyTogether($array, 'create_time', $value),
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
    private function processShowByCategory($array)
    {
        $newList = array(); //new list after sort by category

        foreach ($array as $key => $value) {

            unset($array[$key]);

            //if $value have create_time not exists in list key of $newList => add
            if (!array_key_exists($value['Transaction']['category_id']['id'], $newList)) {

                //find all transactions have create_time equals $value
                $newList[$value['Transaction']['category_id']['id']] = array(
                    'listTransaction' => $this->findPropertyTogether($array, 'category_id', $value),
                    'category'        => $this->Category->getCategoryById($value['Transaction']['category_id']),
                );
            }
        }
        return $newList;
    }

    /**
     * Report transactions
     * @param string $dateTime date time want to show report
     */
    public function report($dateTime = null)
    {
        date_default_timezone_set("Asia/Ho_Chi_Minh");

        //process date time
        if (!empty($dateTime)) {
            $month = substr($dateTime, 0, 2);
            $year  = substr($dateTime, 2, 4);
        } else {
            $dateTime = date('d-m-Y', time());
            $dateTime = explode('-', $dateTime);

            $month = $dateTime[1];
            $year  = $dateTime[2];
        }

        $startTime = strtotime($year . '-' . $month . '-' . '01');
        $endTime   = strtotime($year . '-' . $month . '-' . '31');
        if ($month == '02' || $month == '04' ||
                $month == '06' || $month == '09' || $month == '11') {
            $endTime = strtotime($year . '-' . $month . '-' . '30');
        }

        //array datetime want to show report
        $findTime = array(
            'start_time' => $startTime,
            'end_time'   => $endTime,
        );

        $listTransaction = $this->Transaction->getListTransactionsByDate($findTime);

        foreach ($listTransaction as $key => $transaction) {
            //instead 'category_id' property = category's information
            $listTransaction[$key]['Transaction']['category_id'] = $this->Category->getCategoryById(
                    $transaction['Transaction']['category_id']);

            //process other infor like: total income, total expense...
            $this->processAmount(
                    $transaction['Transaction']['amount'], $listTransaction[$key]['Transaction']['category_id']['expense_type']);

            $this->maxTransactionByExpenseType($listTransaction[$key]);
        }

        $listTransaction = $this->processShowReport($listTransaction);

        $statisticalData = array(
            'expense'    => $this->_totalExpense,
            'income'     => $this->_totalIncome,
            'maxIncome'  => $this->_eleMaxIncome['Transaction'],
            'maxExpense' => $this->_eleMaxExpense['Transaction'],
            'total'      => AuthComponent::user('current_wallet_info')['balance'] + $this->_totalIncome - $this->_totalExpense,
            'unit'       => $this->Unit->find('first', array(
                'conditions' => array(
                    'Unit.id' => AuthComponent::user('current_wallet_info')['unit_id'],
                ),
            ))['Unit'],
        );

        $this->set('statistical_data', $statisticalData);
        $this->set('listTransaction', $listTransaction);
    }

    /**
     * process display list transaction
     * 
     * @param array $array
     * @return array
     */
    private function processShowReport($array)
    {
        $newList = array(); //new list after sort by category

        foreach ($array as $key => $value) {

            unset($array[$key]);

            //if $value have create_time not exists in list key of $newList => add
            if (!array_key_exists($value['Transaction']['category_id']['id'], $newList)) {

                //find all transactions have create_time equals $value
                $newList[$value['Transaction']['category_id']['id']] = array(
                    'totalMoney' => $this->sumMoneyOfCategory($array, $value),
                    'category'   => $this->Category->getCategoryById($value['Transaction']['category_id']['id']),
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
    private function sumMoneyOfCategory($array, $objCompare)
    {
        $sumMoney = $objCompare['Transaction']['amount'];
        foreach ($array as $value) {
            if ($objCompare['Transaction']['category_id'] == $value['Transaction']['category_id']) {
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
    private function maxTransactionByExpenseType($transaction)
    {
        if ($transaction['Transaction']['category_id']['expense_type'] == 'in') {

            if ($transaction['Transaction']['amount'] > $this->_maxIncome) {
                $this->_maxIncome    = $transaction['Transaction']['amount'];
                $this->_eleMaxIncome = $transaction;
            }
        } else {

            if ($transaction['Transaction']['category_id']['expense_type'] == 'out') {
                if ($transaction['Transaction']['amount'] > $this->_maxExpense) {
                    $this->_maxExpense    = $transaction['Transaction']['amount'];
                    $this->_eleMaxExpense = $transaction;
                }
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
    private function findPropertyTogether($array, $property, $objCompare)
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
    private function redirectIfCurrentWalletNotExists()
    {
        if (empty(AuthComponent::user('current_wallet'))) {
            return $this->redirect(array(
                        'controller' => 'wallets',
                        'action'     => 'listWallet',
            ));
        }
    }

}
