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
            $this->request->data['Transaction']['create_time'] = $create_time;

            if ($this->Transaction->save($this->request->data)) {

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
        $listTransaction = $this->getListTransaction();
        $listTransaction = $this->showListTransactionByDate($listTransaction);

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
        $listTransaction = $this->getListTransaction();
        $listTransaction = $this->showListTransactionByCategory($listTransaction);

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
        $listTransaction = $this->Transaction->find('all', array(
            'conditions' => array(
                'Transaction.wallet_id' => AuthComponent::user('current_wallet')['id'],
            ),
            'order'      => 'Transaction.create_time DESC',
        ));

        //convert any properties of transaction: money (1000 => 1.000), category_id(int) => object
        foreach ($listTransaction as $key => $transaction) {

            //instead 'category_id' property = category's information
            $listTransaction[$key]['Transaction']['category_id'] = $this->Category->getCategoryById(
                    $transaction['Transaction']['category_id']);

            $listTransaction[$key]['Transaction']['amount'] = $this->convertMoney(
                    $transaction['Transaction']['amount']);

            //process other infor like: total income, total expense...
            $this->processAmount($transaction['Transaction']['amount'], $listTransaction[$key]['Transaction']['category_id']['expense_type']);
        }

        return $listTransaction;
    }

    /**
     * get other transaction's information for show statistical
     */
    private function getInfoForStatistical()
    {
        //add relationship with model Unit
        $this->Category->bindModel(array(
            'hasOne' => array(
                'Unit' => array(
                    'className'  => 'Unit',
                    'foreignKey' => 'id',
                )
            ),
        ));

        return array(
            'income'  => $this->convertMoney($this->_totalIncome),
            'expense' => $this->convertMoney($this->_totalExpense),
            'balance' => $this->convertMoney(AuthComponent::user('current_wallet')['balance']),
            'total'   => $this->convertMoney(
                    AuthComponent::user('current_wallet')['balance'] + $this->_totalIncome - $this->_totalExpense
            ),
            'unit'    => $this->Unit->find('first', array(
                'conditions' => array(
                    'Unit.id' => AuthComponent::user('current_wallet')['unit_id'],
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
        if (empty($id)) {
            return $this->redirect(array(
                        'controller' => 'transactions',
                        'action'     => 'listSortByDate',
            ));
        }

        $transactionObj = $this->Transaction->findById($id);
        if (empty($transactionObj)) {
            return $this->redirect(array(
                        'controller' => 'transactions',
                        'action'     => 'listSortByDate',
            ));
        }

        $this->set('title_for_layout', 'Edit transaction');
        $this->set('listCategory', $this->Category->getListCategoryByWalletId(
                        AuthComponent::user('current_wallet')['id']));
        $this->set('transactionObj', $transactionObj);

        if ($this->request->is('get')) {
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

            $this->Transaction->id = $id;
            if ($this->Transaction->save($this->request->data)) {

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
        //not render view
        $this->autoRender = false;

        if (empty($id)) {
            return $this->redirect(array(
                        'controller' => 'transactions',
                        'action'     => 'listSortByDate',
            ));
        }

        $transactionObj = $this->Transaction->findById($id);
        if (empty($transactionObj)) {
            return $this->redirect(array(
                        'controller' => 'transactions',
                        'action'     => 'listSortByDate',
            ));
        }

        $this->Transaction->delete($id);
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
        $newList   = array(); //array contains elements have same property
        $newList[] = $objCompare;

        foreach ($array as $key => $value) {
            if ($objCompare['Transaction'][$property] == $value['Transaction'][$property]) {
                $newList[] = $value;
            }
        }
        return $newList;
    }

}
