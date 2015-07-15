<?php

class WalletsController extends AppController
{

    /**
     * $uses
     * 
     * @var array 
     */
    public $uses = array('Wallet', 'Unit', 'User', 'Category', 'Transaction');

    /**
     * $helpers
     * @var array
     */
    public $helpers = array(
        'Form',
        'Html',
        'Session',
    );

    /**
     * default function => redirect to /wallets/listWallet
     */
    public function index()
    {
        $this->redirect(array(
            'controller' => 'wallets',
            'action'     => 'listWallet'));
    }

    /**
     * add wallet information
     */
    public function add()
    {
        $this->set('title_for_layout', "New wallet");
        $this->set('unitObj', $this->Unit->find('all'));

        if ($this->request->is('get')) {
            return;
        }

        //process wallet's icon
        $walletIcon = '/img/wallet.png';
        if ($this->request->data['Wallet']['icon']['size'] > 0) {
            $walletIcon = $this->processUploadImage('uploads/', $this->request->data['Wallet']['icon']);
        }
        $this->request->data['Wallet']['icon'] = $walletIcon;

        $this->request->data['Wallet']['is_setup'] = true;
        $this->request->data['Wallet']['user_id']  = AuthComponent::user('id');


        $this->Wallet->set($this->request->data);
        if ($this->Wallet->validates()) {
            if ($this->Wallet->save($this->request->data)) {

                $manyWallet = $this->Wallet->find('count', array(
                    'conditions' => array(
                        'user_id' => AuthComponent::user('id'),
                    ),
                ));

                //check if user have not anything wallet => set default wallet
                if ($manyWallet == 1) {
                    $currentWallet_id = $this->Wallet->getInsertID();

                    $this->User->updateAll(array(
                        'User.current_wallet' => $currentWallet_id), array(
                        'User.id' => AuthComponent::user('id')
                    ));

                    //update session for current_wallet Auth's property
                    $currentWallet = $this->Wallet->findById($currentWallet_id);
                    $this->Session->write('Auth.User.current_wallet', $currentWallet['Wallet']);
                }

                $this->Session->setFlash("Create your wallet complete.");
                return $this->redirect(array(
                            'action' => 'listWallet'));
            }
        }
    }

    /**
     * show list wallet of user
     */
    public function listWallet()
    {
        $this->set('title_for_layout', "List wallet");

        //unbindmodel user
        $this->Wallet->unbindModel(array('belongsTo' => array('User')));

        $listWallet = $this->Wallet->find('all', array(
            'conditions' => array(
                'Wallet.user_id' => AuthComponent::user('id'),
            )
        ));

        //convert wallet information
        foreach ($listWallet as $key => $value) {
            $listWallet[$key]['Wallet']['balance'] = $this->convertMoney($value['Wallet']['balance']);
            $listWallet[$key]['Unit']              = $this->getUnitById($value['Wallet']['unit_id']);
        }

        $this->set('listWallet', $listWallet);
    }

    /**
     * edit wallet information
     * 
     * @param int $id Wallet id
     */
    public function edit($id)
    {
        if (empty($id)) {
            return $this->redirect(array(
                        'controller' => 'users',
                        'action'     => 'index',));
        }

        $walletObj = $this->Wallet->findById($id);
        if (empty($walletObj)) {
            return $this->redirect(array(
                        'controller' => 'users',
                        'action'     => 'index',));
        }

        //unbindmodel user
        $this->Wallet->unbindModel(array('belongsTo' => array('User')));

        $this->set('title_for_layout', "Edit wallet");
        $this->set('unitObj', $this->Unit->find('all'));
        $this->set('wallet', $walletObj);

        if ($this->request->is('get')) {
            return;
        }

        $this->Wallet->set($this->request->data);
        if ($this->Wallet->validates()) {

            //process wallet's icon
            $walletIcon = $walletObj['Wallet']['icon'];
            if ($this->request->data['Wallet']['icon']['size'] > 0) {
                $walletIcon = $this->processUploadImage('uploads/', $this->request->data['Wallet']['icon']);
            }
            $this->request->data['Wallet']['icon'] = $walletIcon;

            $this->Wallet->id = $id;
            if ($this->Wallet->save($this->request->data)) {

                $walletUpdated = $this->Wallet->findById($id);
                //update session
                $this->Session->write('Auth.User.current_wallet', $walletUpdated['Wallet']);

                $this->Session->setFlash("Update Wallet's information complete.");
                return $this->redirect(array(
                            'controller' => 'wallets',
                            'action'     => 'listWallet',
                ));
            }

            $this->Session->setFlash("Have error! Please try again.");
        }
    }

    /**
     * select wallet for transactions
     * 
     * @param int $id Wallet id
     */
    public function select($id)
    {
        if (empty($id)) {
            return $this->redirect(array(
                        'controller' => 'users',
                        'action'     => 'index',));
        }

        $walletObj = $this->Wallet->findById($id);
        if (empty($walletObj)) {
            return $this->redirect(array(
                        'controller' => 'users',
                        'action'     => 'index',));
        }

        $this->User->id = AuthComponent::user('id');
        if ($this->User->saveField('current_wallet', $id)) {

            $walletObj = $this->Wallet->findById($id);
            //update session Auth.User.current_wallet
            $this->Session->write('Auth.User.current_wallet', $walletObj['Wallet']);

            return $this->redirect(array(
                        'controller' => 'transactions',
                        'action'     => 'listSortByDate',
            ));
        }

        $this->autoRender = false;
    }

    /**
     * delte wallet by id
     * 
     * @param int $id Wallet id
     */
    public function delete($id)
    {
        if (empty($id)) {
            $this->redirect(array(
                'controller' => 'users',
                'action'     => 'index',));
        }

        $walletObj = $this->Wallet->findById($id);
        if (empty($walletObj)) {
            $this->redirect(array(
                'controller' => 'users',
                'action'     => 'index',));
        }

        //bindModel Category & Transaction
        $this->Wallet->bindModel(array(
            'hasMany' => array(
                'Category'    => array(
                    'className' => 'Category',
                ),
                'Transaction' => array(
                    'className' => 'Transaction',
                ),
            ),
        ));

        $listCatDel = $this->Category->deleteCategoriesByWalletId($id);

        //delete all transactions have category_id equal id of category was delete
        foreach ($listCatDel as $key => $category) {
            $this->Transaction->deleteTransactionsByCategoryId($category['Category']['id']);
        }

        $this->Wallet->delete($id);

        //if wallet want to delete have id equals current wallet id => update current_wallet
        if ($id == AuthComponent::user('current_wallet')['id']) {

            $walletChoose = $this->Wallet->find('first', array(
                'conditions' => array(
                    'user_id' => AuthComponent::user('id'),
                )
            ));

            $this->User->updateAll(array(
                'User.current_wallet' => $walletChoose['Wallet']['id']), array(
                'User.id' => AuthComponent::user('id')
            ));
            $this->Session->write('Auth.User.current_wallet', $walletChoose['Wallet']);
        }

        return $this->redirect(array(
                    'controller' => 'wallets',
                    'action'     => 'listWallet',
        ));

        $this->autoRender = false;
    }

    /**
     * process image file upload
     * 
     * @param type $rootFolder Folder contain file images
     * @param type $fileObj File image upload
     * @return string
     */
    private function processUploadImage($rootFolder, $fileObj)
    {
        $target_dir  = WWW_ROOT . $rootFolder;
        $target_file = $target_dir . basename($fileObj["name"]);

        if (!move_uploaded_file($fileObj['tmp_name'], $target_file)) {
            return $this->Session->setFlash("Have error. Please try again.");
        }
        return '/' . $rootFolder . $fileObj['name'];
    }

    /**
     * get unit information by id
     * 
     * @param int $unit_id Unit id
     */
    private function getUnitById($unit_id)
    {
        return $this->Unit->findById($unit_id)['Unit'];
    }

    /**
     * convert money - ex: 123000 -> 123.000
     * 
     * @param int $money
     * @return string
     */
    private function convertMoney($money)
    {
        return number_format($money, 0, '', '.');
    }

}
