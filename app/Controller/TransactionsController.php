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
        $this->set('listCategory', $this->Category->getListCategoryByWalletId(
                        AuthComponent::user('current_wallet')['id']));
        $this->set('title_for_layout', 'Add Transaction');

        if ($this->request->is('get')) {
            return;
        }

        $this->Transaction->set($this->request->data);
        if ($this->Transaction->validates()) {

            //transaction data want to save
            $transactionObj = array(
                'Transaction' => array(
                    'category_id' => $this->request->data['Transaction']['category_id'],
                    'amount'      => $this->request->data['Transaction']['amount'],
                    'note'        => $this->request->data['Transaction']['note'],
                    'wallet_id'   => AuthComponent::user('current_wallet')['id'],
                )
            );

            //save transaction data
            if ($this->Transaction->save($transactionObj)) {
                $this->Session->setFlash('Add new transaction complete.');
                $this->redirect(array(
                    'controller' => 'transactions',
                    'action'     => 'listTransaction',
                ));
            }
        }
        return;
    }

    /**
     * show list transaction of user
     */
    public function listTransaction()
    {
        $this->set('title_for_layout', 'List Transaction');

        //get list transaction within current wallet
        $listTransaction = $this->Transaction->find('all', array(
            'conditions' => array(
                'Transaction.wallet_id' => AuthComponent::user('current_wallet')['id'],
            ),
        ));

        //convert any properties of transaction: money (1000 => 1.000), category_id(int) => object
        foreach ($listTransaction as $key => $transaction) {
            $listTransaction[$key]['Transaction']['category_id'] = $this->Category->getCategoryById(
                    $transaction['Transaction']['category_id']);
            $listTransaction[$key]['Transaction']['amount']      = $this->convertMoney(
                    $transaction['Transaction']['amount']);
            $this->processAmount($transaction['Transaction']['amount'], $listTransaction[$key]['Transaction']['category_id']['expense_type']);
        }

        //process other information of transaction like: total income, expense,...
        $otherTransaction = array(
            'income'  => $this->convertMoney($this->_totalIncome),
            'expense' => $this->convertMoney($this->_totalExpense),
            'balance' => $this->convertMoney(AuthComponent::user('current_wallet')['balance']),
            'total'   => $this->convertMoney(
                    AuthComponent::user('current_wallet')['balance'] + $this->_totalIncome - $this->_totalExpense
            ),
        );

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
            //update data
            if ($this->Transaction->updateAll(array(
                        'Transaction.category_id' => $this->request->data['Transaction']['category_id'],
                        'Transaction.amount'      => $this->request->data['Transaction']['amount'],
                        'Transaction.note'        => '"' . $this->request->data['Transaction']['note'] . '"',
                            ), array(
                        'Transaction.id' => $id,
                    ))) {
                $this->Session->setFlash("Update transaction information complete.");
                $this->redirect(array(
                    'controller' => 'transactions',
                    'action'     => 'listTransaction',
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

}
