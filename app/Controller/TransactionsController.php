<?php

class TransactionsController extends AppController
{

    public $uses = array('Transaction', 'Category');

    /**
     * params for get information from transaction like: total income, total expense
     * 
     * @var $_totalIncome int 
     * @var $_totalExpense int 
     */
    private $_totalIncome  = 0, $_totalExpense = 0;

    /**
     * default function => redirect to listCategories
     */
    public function index()
    {
        $this->redirect(array(
            'controller' => 'transactions',
            'action'     => 'listTransaction',
        ));
    }

    /**
     * add new transaction
     */
    public function add()
    {
        date_default_timezone_set("Asia/Ho_Chi_Minh");

        $this->set('listCategory', $this->Category->getListCategoryByWalletId(
                        AuthComponent::user('current_wallet')['id']));
        $this->set('title_for_layout', 'Add Transaction');

        if ($this->request->is('get')) {
            return;
        }

        $this->Transaction->set($this->request->data);
        if ($this->Transaction->validates()) {

            //process create_time
            $create_time = time();
            if (!empty($this->request->data['Transaction']['create_time'])) {
                $create_time = strtotime(str_replace('/', '-', $this->request->data['Transaction']['create_time']));
            }

            //transaction data want to save
            $transactionObj = array(
                'Transaction' => array(
                    'category_id' => $this->request->data['Transaction']['category_id'],
                    'amount'      => $this->request->data['Transaction']['amount'],
                    'note'        => $this->request->data['Transaction']['note'],
                    'create_time' => $create_time,
                    'wallet_id'   => AuthComponent::user('current_wallet')['id'],
                )
            );

            //save transaction data
            if ($this->Transaction->save($transactionObj)) {
                $this->Session->setFlash('Add new transaction complete.');
                $this->redirect(array(
                    'controller' => 'transactions',
                    'action'     => 'listTransaction',
                    'sort_by_date',
                ));
            }
        }
        return;
    }

    /**
     * show list transaction of user
     */
    public function listTransaction($option = null)
    {
        //process request from url
        if (empty($option) || ($option != 'sort_by_date' && $option != 'sort_by_category')) {
            $this->redirect(array(
                'controller' => 'users',
                'action'     => 'index',
            ));
        }

        //get list transaction within current wallet
        $listTransaction = $this->Transaction->find('all', array(
            'conditions' => array(
                'Transaction.wallet_id' => AuthComponent::user('current_wallet')['id'],
            ),
            'order'      => 'Transaction.create_time DESC',
        ));

        //convert any properties of transaction: money (1000 => 1.000), category_id(int) => object
        foreach ($listTransaction as $key => $transaction) {

            //instead 'category_id' property = category information
            $listTransaction[$key]['Transaction']['category_id'] = $this->Category->getCategoryById(
                    $transaction['Transaction']['category_id']);

            $listTransaction[$key]['Transaction']['amount'] = $this->convertMoney(
                    $transaction['Transaction']['amount']);

            //process other infor like: total income, total expense...
            $this->processAmount($transaction['Transaction']['amount'], $listTransaction[$key]['Transaction']['category_id']['expense_type']);
        }

        //if select sort by date
        if ($option == 'sort_by_date') {
            $listTransaction = $this->showListTransactionByDate($listTransaction);
        } elseif ($option == 'sort_by_category') {
            $listTransaction = $this->showListTransactionByCategory($listTransaction);
        }

        //process other information of transaction like: total income, total expense,...
        $otherTransaction = array(
            'income'  => $this->convertMoney($this->_totalIncome),
            'expense' => $this->convertMoney($this->_totalExpense),
            'balance' => $this->convertMoney(AuthComponent::user('current_wallet')['balance']),
            'total'   => $this->convertMoney(
                    AuthComponent::user('current_wallet')['balance'] + $this->_totalIncome - $this->_totalExpense
            ),
        );

        //set params for view
        $this->set('title_for_layout', 'List Transaction');
        $this->set('otherTransaction', $otherTransaction);
        $this->set('listTransaction', $listTransaction);
    }

    /**
     * edit transaction information
     * 
     * @param int $id Transaction id
     */
    public function edit($id)
    {
        //process request from url
        $transactionObj = $this->Transaction->findById($id);
        if (empty($transactionObj)) {

            //if id not exists in database => redirect to /transactions/listTransaction
            $this->redirect(array(
                'controller' => 'transactions',
                'action'     => 'listTransaction',
                'sort_by_date',
            ));
        }

        //get data for view edit
        $this->set('title_for_layout', 'Edit Transaction');
        //get list category
        $this->set('listCategory', $this->Category->getListCategoryByWalletId(
                        AuthComponent::user('current_wallet')['id']));
        $this->set('transactionObj', $transactionObj);

        if ($this->request->is('get')) {
            return;
        }

        //check validates 
        $this->Transaction->set($this->request->data);
        if ($this->Transaction->validates()) {
            //process datetime
            $transactionTime = $transactionObj['Transaction']['create_time'];
            if (!empty($this->request->data['Transaction']['create_time'])) {
                $transactionTime = strtotime(str_replace('/', '-', $this->request->data['Transaction']['create_time']));
            }

            //update data
            if ($this->Transaction->updateAll(array(
                        'Transaction.category_id' => $this->request->data['Transaction']['category_id'],
                        'Transaction.amount'      => $this->request->data['Transaction']['amount'],
                        'Transaction.note'        => '"' . $this->request->data['Transaction']['note'] . '"',
                        'Transaction.create_time' => $transactionTime,
                            ), array(
                        'Transaction.id' => $id,
                    ))) {
                $this->Session->setFlash("Update transaction information complete.");
                $this->redirect(array(
                    'controller' => 'transactions',
                    'action'     => 'listTransaction',
                    'sort_by_date'
                ));
            }
            return $this->Session->setFlash("Have error! Please try again.");
        }
        return;
    }

    /**
     * delete transaction by id
     * 
     * @param int $id Transaction id
     */
    public function delete($id)
    {
        //not render view
        $this->autoRender = false;

        //process request from url
        if (empty($id)) {
            return $this->redirect(array(
                        'controller' => 'transactions',
                        'action'     => 'listTransaction',
            ));
        }

        $transactionObj = $this->Transaction->findById($id);
        if (empty($transactionObj)) {
            return $this->redirect(array(
                        'controller' => 'transactions',
                        'action'     => 'listTransaction',
            ));
        }

        $this->Transaction->delete($id);
        $this->Session->setFlash("Delete transaction complete.");
        $this->redirect(array(
            'controller' => 'transactions',
            'action'     => 'listTransaction',
            'sort_by_date',
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
     * process amount within expense type
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
    private function showListTransactionByDate($array)
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
    private function showListTransactionByCategory($array)
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
     * find all element have property equals property in object want to compare
     * 
     * @param array $array Array want to process
     * @param string $property Property want to compare
     * @param object $objCompare Object want to compare
     * @return array
     */
    private function findPropertyTogether($array, $property, $objCompare)
    {
        $newList   = array();
        $newList[] = $objCompare;

        foreach ($array as $key => $value) {
            if ($objCompare['Transaction'][$property] == $value['Transaction'][$property]) {
                $newList[] = $value;
            }
        }
        return $newList;
    }

}
