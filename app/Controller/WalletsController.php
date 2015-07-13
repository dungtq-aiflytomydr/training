<?php

class WalletsController extends AppController
{

    /**
     * $uses
     * 
     * @var array 
     */
    public $uses = array('Wallet', 'Unit', 'User');

    /**
     * $helpers
     * @var array
     */
    public $helpers = array(
        'Form',
        'Html',
        'Session',
        'Time',
        'Text'
    );

    /**
     * 
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

        //process wallet's info
        $walletIcon = '/img/wallet.png';
        if ($this->request->data['Wallet']['icon']['size'] > 0) {
            $walletIcon = $this->_processUploadImage('uploads/', $this->request->data['Wallet']['icon']);
        }

        //check validation
        $this->Wallet->set($this->request->data);
        if ($this->Wallet->validates()) {
            //wallet's infomation want to save
            $walletData = array(
                'Wallet' => array(
                    'name'     => $this->request->data['Wallet']['name'],
                    'balance'  => $this->request->data['Wallet']['balance'],
                    'is_setup' => true,
                    'icon'     => $walletIcon,
                    'unit_id'  => $this->request->data['Wallet']['unit_id'],
                    'user_id'  => AuthComponent::user('id'),
                )
            );

            if ($this->Wallet->save($walletData)) {
                //check if user have not anything wallet => set default wallet
                $manyWallet = $this->Wallet->find('count', array(
                    'conditions' => array(
                        'user_id' => AuthComponent::user('id'),
                    ),
                ));

                if ($manyWallet == 1) {
                    $currentWallet_id = $this->Wallet->getInsertID();

                    $this->User->updateAll(array(
                        'User.current_wallet' => $currentWallet_id), array(
                        'User.id' => AuthComponent::user('id')
                    ));

                    //update session for current_wallet property
                    $currentWallet = $this->Wallet->findById($currentWallet_id);
                    $this->Session->write('Auth.User.current_wallet', $currentWallet['Wallet']);
                }

                $this->Session->setFlash("Create your wallet complete.");
                return $this->redirect(array(
                            'action' => 'listWallet'));
            }
        }
        return;
    }

    /**
     * show list wallet of user
     */
    public function listWallet()
    {
        $this->set('title_for_layout', "List wallet");

        $listWallet = $this->Wallet->find('all', array(
            'conditions' => array(
                'Wallet.user_id' => AuthComponent::user('id'),
            )
        ));

        //convert wallet information
        foreach ($listWallet as $key => $value) {
            $listWallet[$key]['Wallet']['balance'] = $this->_convertMoney($value['Wallet']['balance']);
            $listWallet[$key]['Unit']              = $this->_getUnitById($value['Wallet']['unit_id']);
        }

        $this->set('listWallet', $listWallet);
    }

    /**
     * edit wallet information
     * 
     * @param int $id Wallet's id
     */
    public function edit($id)
    {
        //process request url
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

        //setup layout
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
                $walletIcon = $this->_processUploadImage('uploads/', $this->request->data['Wallet']['icon']);
            }

            //update wallet's info
            if ($this->Wallet->updateAll(array(
                        'Wallet.name'    => '"' . $this->request->data['Wallet']['name'] . '"',
                        'Wallet.icon'    => '"' . $walletIcon . '"',
                        'Wallet.unit_id' => $this->request->data['Wallet']['unit_id']), array(
                        'Wallet.id' => $id
                    ))) {

                $this->Session->setFlash("Update Wallet's information complete.");
                $this->redirect(array(
                    'controller' => 'wallets',
                    'action'     => 'listWallet',
                ));
            }
            $this->Session->setFlash("Have error! Please try again.");
            return;
        }
        return;
    }

    /**
     * select wallet for transactions
     * 
     * @param int $id Wallet id
     */
    public function select($id)
    {
        //process request url
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

        //update current_wallet for user
        if ($this->User->updateAll(array(
                    'User.current_wallet' => $id), array(
                    'User.id' => AuthComponent::user('id')
                ))) {

            //get wallet info => update session Auth.User.current_wallet
            $walletObj = $this->Wallet->findById($id);
            //update session Auth.User.current_wallet
            $this->Session->write('Auth.User.current_wallet', $walletObj['Wallet']);

            $this->redirect(array(
                'controller' => 'transactions',
                'action'     => 'listTransaction',
            ));
        }
        // this code is used for function without view
        $this->autoRender = false;
    }

    /**
     * delte wallet by id
     * 
     * @param int $id Wallet id
     */
    public function delete($id)
    {
        //process request url
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

        //if wallet want to delete have id equals current wallet id => update current_wallet  = next wallet
        if ($walletObj['Wallet']['id'] == AuthComponent::user('current_wallet')['id']) {
            //wallet choose
            $walletChoose = $this->Wallet->find('first', array(
                'conditions' => array(
                    'user_id' => AuthComponent::user('id'),
                )
            ));

            //update current_wallet for user
            $this->User->updateAll(array(
                'User.current_wallet' => $walletChoose['Wallet']['id']), array(
                'User.id' => AuthComponent::user('id')
            ));

            //update session Auth.User.current_wallet
            $this->Session->write('Auth.User.current_wallet', $walletChoose['Wallet']);
        }

        //update data
        $this->Wallet->delete($id);
        $this->redirect(array(
            'controller' => 'wallets',
            'action'     => 'listWallet',
        ));
        // this code is used for function without view
        $this->autoRender = false;
    }

    /**
     * process image file upload
     * 
     * @param type $rootFolder Folder contain file images
     * @param type $fileObj File image upload
     * @return string
     */
    private function _processUploadImage($rootFolder, $fileObj)
    {
        $target_dir  = WWW_ROOT . $rootFolder;
        $target_file = $target_dir . basename($fileObj["name"]);

        if (!move_uploaded_file($fileObj['tmp_name'], $target_file)) {
            $this->Session->setFlash("Have error. Please try again.");
            return;
        }
        return '/' . $rootFolder . $fileObj['name'];
    }

    /**
     * get unit information by id
     * 
     * @param int $unit_id Unit id
     */
    private function _getUnitById($unit_id)
    {
        return $this->Unit->findById($unit_id)['Unit'];
    }

    /**
     * convert money - ex: 123000 -> 123.000
     * 
     * @param int $money
     * @return string
     */
    private function _convertMoney($money)
    {
        return number_format($money, 0, '', '.');
    }

}
