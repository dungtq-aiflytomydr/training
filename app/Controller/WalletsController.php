<?php

define('SETUP', 1);
define('NOT_SETUP', 0);

class WalletsController extends AppController
{

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
        $this->set('unitObj', $this->Wallet->Unit->find('all'));

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

        $this->set('unitObj', $this->Wallet->Unit->find('all'));
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

        // Allow certain file formats
        if ($fileObj["type"] !== "image/jpg" && $fileObj["type"] !== "image/png" && $fileObj["type"] !== "image/jpeg" && $fileObj["type"] !== "image/gif") {
            $this->Session->setFlash("Sorry, your format image incorrect.");
            return;
        }

        if ($fileObj['size'] > 1000000) {
            $this->Session->setFlash("Sorry, your image is too large.");
            return;
        }

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
        return $this->Wallet->Unit->findById($unit_id)['Unit'];
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
