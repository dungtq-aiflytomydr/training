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
     * paginate
     * 
     * @var array 
     */
    public $paginate = array(
        'limit' => 15,
    );

    /**
     * $components
     * 
     * @var array
     */
    public $components = array('Paginator');

    /**
     * params to save any information from transaction like: total income, total expense
     * 
     * @var $__totalIncome int 
     * @var $__totalExpense int 
     */
    private $__totalIncome  = 0, $__totalExpense = 0;

    /**
     * params for save max amout of list Transaction
     * 
     * @var $__maxIncome int max amount of transaction have category expense_type = in
     * @var $__maxExpense int max amount of transaction have category expense_type = out
     */
    private $__maxIncome  = 0, $__maxExpense = 0;

    /**
     * params to save transaction element have max amount
     *
     * @var $__maxExpense Save transaction element have max amount with expense_type equal in
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
        if (!$this->Transaction->validates()) {
            $this->set('validationErrors', $this->Transaction->validationErrors);
            return;
        }

        //process create_time
        $create_time = time();
        if (!empty($this->request->data['Transaction']['create_time'])) {
            $create_time = strtotime($this->request->data['Transaction']['create_time']);
        }
        $this->request->data['Transaction']['create_time'] = $create_time;
        $this->request->data['Transaction']['wallet_id']   = AuthComponent::user('current_wallet');

        if ($this->Transaction->createTransaction($this->request->data)) {

            //if insert transaction success => update balance in wallet
            $catInserted = $this->Category->getById($this->request->data['Transaction']['category_id']);
            $this->__updateBalance($catInserted['Category']['expense_type'], $this->request->data['Transaction']['amount']);

            $this->Session->setFlash('Add new transaction complete.');
            return $this->redirect(array(
                        'controller' => 'transactions',
                        'action'     => 'listSortByDate',
                        date('Y-m', $create_time),
            ));
        }
    }

    /**
     * show list transaction sort by date (view in month)
     * 
     * @param $dateTime String datetime
     */
    public function listSortByDate($dateTime = null)
    {
        $this->__redirectIfEmptyWallet();

        $findTime = $this->__processFindDateTime($dateTime);

        $listTransaction = $this->Transaction->getTransactionsByDateRange($findTime['fromDate'], $findTime['toDate']);

        $this->set('listTransaction', $listTransaction);
        $this->set('datetime', date('Y-m', $findTime['fromDate']));
        $this->set('unitInfo', $this->Unit->getById(AuthComponent::user('current_wallet_info')['unit_id']));
    }

    /**
     * show list transaction sort by category (view in month)
     * 
     * @param $dateTime String date time want to display list transaction (e.g 072150)
     */
    public function listSortByCategory($dateTime = null)
    {
        $this->__redirectIfEmptyWallet();

        $findTime        = $this->__processFindDateTime($dateTime);
        $orderBy         = array(
            'Transaction.category_id ASC',
            'Transaction.create_time DESC',
        );
        $listTransaction = $this->Transaction->getTransactionsByDateRange($findTime['fromDate'], $findTime['toDate'], $orderBy);

        $this->set('listTransaction', $listTransaction);
        $this->set('datetime', date('Y-m', $findTime['fromDate']));
        $this->set('unitInfo', $this->Unit->getById(AuthComponent::user('current_wallet_info')['unit_id']));
    }

    /**
     * edit transaction information
     * 
     * @param int $id Transaction id
     */
    public function edit($id)
    {
        $tranObj = $this->Transaction->getById($id);
        if (empty($tranObj)) {
            throw new NotFoundException('Could not find that transaction.');
        }

        if ($tranObj['Transaction']['wallet_id'] !== AuthComponent::user('current_wallet')) {
            throw new NotFoundException('Access is denied.');
        }

        $this->set('title_for_layout', 'Edit transaction');
        $this->set('listCategory', $this->Category->getCategoriesOfWallet(
                        AuthComponent::user('current_wallet')));

        if (empty($this->request->data)) {
            $this->request->data                               = $tranObj;
            $this->request->data['Transaction']['create_time'] = date('Y-m-d', $tranObj['Transaction']['create_time']);
        }

        if (!$this->request->is(array('post', 'put'))) {
            return;
        }

        //validations
        $this->Transaction->set($this->request->data);
        if (!$this->Transaction->validates()) {
            return;
        }

        //balance relationship within transaction amount
        $balance = AuthComponent::user('current_wallet_info')['balance'];
        if ($tranObj['Category']['expense_type'] == 'in') {
            $balance -= $tranObj['Transaction']['amount'];
        } else {
            $balance += $tranObj['Transaction']['amount'];
        }

        //process datetime
        if (!empty($this->request->data['Transaction']['create_time'])) {
            $create_time                                       = strtotime($this->request->data['Transaction']['create_time']);
            $this->request->data['Transaction']['create_time'] = $create_time;
        }

        $isUpdated = $this->Transaction->updateById($id, $this->request->data);
        if ($isUpdated) {

            //if update transaction success => update balance
            $catInfo = $this->Category->getById($this->request->data['Transaction']['category_id']);
            $this->__updateBalance($catInfo['Category']['expense_type'], $this->request->data['Transaction']['amount'], $balance);

            $this->Session->setFlash("Update transaction information complete.");
            return $this->redirect(array(
                        'controller' => 'transactions',
                        'action'     => 'listSortByDate',
                        date('Y-m', $create_time),
            ));
        }
        $this->Session->setFlash("Have error! Please try again.");
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

        $tranObj = $this->Transaction->getById($id);
        if (empty($tranObj)) {
            throw new NotFoundException('Could not find that transaction.');
        }

        $this->Transaction->deleteById($id);
        $this->__updateBalance($tranObj['Category']['expense_type'], $tranObj['Transaction']['amount'], null, true);

        $this->Session->setFlash("Delete transaction complete.");
        return $this->redirect(array(
                    'controller' => 'transactions',
                    'action'     => 'listSortByDate',
                    date('Y-m', $tranObj['Transaction']['create_time']),
        ));
    }

    /**
     * Report transactions
     * 
     * @param string $dateTime date time want to show report
     */
    public function report($dateTime = null)
    {
        $this->__redirectIfEmptyWallet();

        $findTime        = $this->__processFindDateTime($dateTime);
        $orderBy         = array(
            'Transaction.category_id ASC',
            'Transaction.create_time DESC',
        );
        $listTransaction = $this->Transaction->getTransactionsByDateRange($findTime['fromDate'], $findTime['toDate'], $orderBy);
        $listTransaction = $this->__processShowReport($listTransaction);

        $statistical = array(
            'totalIncome'  => $this->__totalIncome,
            'totalExpense' => $this->__totalExpense,
            'maxIncome'    => $this->__eleMaxIncome,
            'maxExpense'   => $this->__eleMaxExpense,
        );

        $this->set('listTransaction', $listTransaction);
        $this->set('datetime', date('Y-m', $findTime['fromDate']));
        $this->set('unitInfo', $this->Unit->getById(AuthComponent::user('current_wallet_info')['unit_id']));
        $this->set('statistical', $statistical);
    }

    /**
     * update balance when transaction chance
     * 
     * @param string $expenseType Expense_type('in' | 'out')
     * @param int $amount Amount value
     * @param int $balance Balance (default = null)
     * @param boolean $isDelete Action delete or not
     */
    private function __updateBalance($expenseType, $amount, $balance = null, $isDelete = false)
    {
        if (empty($balance)) {
            $balance = AuthComponent::user('current_wallet_info')['balance'];
        }

        if (!$isDelete) {
            if ($expenseType == 'in') {
                $balance += $amount;
            } else {
                $balance -= $amount;
            }
        } else {
            if ($expenseType == 'in') {
                $balance -= $amount;
            } else {
                $balance += $amount;
            }
        }

        unset($this->Wallet->validate['balance']['naturalNumber']);
        $this->Wallet->updateById(AuthComponent::user('current_wallet'), array(
            'balance' => $balance,
        ));
        $this->Session->write('Auth.User.current_wallet_info.balance', $balance);
    }

    /**
     * Process find date time
     * 
     * @param string $dateTime Date time can convert
     * @return array
     */
    private function __processFindDateTime($dateTime)
    {
        $refDate = false;
        if (!empty($dateTime)) {
            $refDate = strtotime($dateTime);
        }
        if ($refDate === false) {
            $refDate = time();
        }

        return array(
            'fromDate' => strtotime(date('Y-m-01', $refDate)),
            'toDate'   => strtotime('first day of next month', $refDate),
        );
    }

    /**
     * process display list transaction
     * 
     * @param array $listTransaction List transaction need process
     * @return array
     */
    private function __processShowReport($listTransaction)
    {
        $newList    = array(); //save category infor within sum amount of transaction through it
        $catCompare = 0;
        $sumMoney   = 0; //save sum amount of category

        foreach ($listTransaction as $tran) {
            if ($tran['Category']['id'] > $catCompare) {
                $catCompare = $tran['Category']['id'];

                //add index 'sumMoney' into element in $newList
                if (count($newList) > 0) {
                    $newList[count($newList) - 1]['sumMoney'] = $sumMoney;
                }

                //if category not exists in $newList => add
                $newList[] = array(
                    'Category' => $tran['Category'],
                );
                $sumMoney  = 0;
            }

            $sumMoney += $tran['Transaction']['amount'];

            if ($tran['Category']['expense_type'] == 'in') {
                $this->__totalIncome += $tran['Transaction']['amount'];
            } else {
                $this->__totalExpense += $tran['Transaction'][
                        'amount'];
            }

            $this->__maxTransactionByExpenseType($tran);
        }
        $newList [count($newList) - 1]['sumMoney'] = $sumMoney;
        return $newList;
    }

    /**
     * get transaction have max amount
     * 
     * @param object $tran Transaction object
     */
    private function __maxTransactionByExpenseType($tran)
    {
        if ($tran['Category'] ['expense_type'] == 'in') {
            if ($tran['Transaction']['amount'] > $this->__maxIncome) {
                $this->__maxIncome    = $tran['Transaction']['amount'];
                $this->__eleMaxIncome = $tran;
            }
        } else {
            if ($tran['Transaction']['amount'] > $this->__maxExpense) {
                $this->__maxExpense    = $tran['Transaction']['amount'];
                $this->__eleMaxExpense = $tran;
            }
        }
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
