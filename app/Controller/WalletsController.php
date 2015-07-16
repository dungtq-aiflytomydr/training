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
        return $this->redirect(array(
                    'controller' => 'wallets',
                    'action'     => 'listWallet',
        ));
    }

    /**
     * add wallet information
     */
    public function add()
    {
        $this->set('title_for_layout', "New wallet");
        $this->set('unitObj', $this->Unit->find('all'));

        if (!$this->request->is('post', 'put')) {
            return;
        }

        //process wallet's icon
        $walletIcon = null;
        if ($this->request->data['Wallet']['icon']['size'] > 0) {
            $walletIcon = $this->processUploadImage(
                    AppConstant::FOLDER_UPL, $this->request->data['Wallet']['icon']);
        }

        $this->request->data['Wallet']['icon']     = $walletIcon;
        $this->request->data['Wallet']['is_setup'] = true;
        $this->request->data['Wallet']['user_id']  = AuthComponent::user('id');

        $this->Wallet->set($this->request->data);
        if ($this->Wallet->validates()) {
            if ($this->Wallet->createWallet($this->request->data)) {

                $manyWallet = $this->Wallet->getNumWalletsByUserId(AuthComponent::user('id'));

                //check if user have not anything wallet => set default wallet
                if ($manyWallet == 1) {
                    $currentWalletId = $this->Wallet->getInsertID();

                    $dataUpdate = array(
                        'current_wallet' => $currentWalletId,
                    );

                    $this->User->updateUserById(AuthComponent::user('id'), $dataUpdate);

                    //update session for current_wallet Auth's property
                    $currentWallet = $this->Wallet->getWalletById($currentWalletId);
                    $this->Session->write('Auth.User.current_wallet', $currentWallet['Wallet']['id']);
                    $this->Session->write('Auth.User.current_wallet_info', $currentWallet['Wallet']);
                }

                $this->Session->setFlash("Create your wallet complete.");
                return $this->redirect(array(
                            'action' => 'listWallet',
                ));
            }
        }
    }

    /**
     * show list wallet of user
     */
    public function listWallet()
    {
        $this->set('title_for_layout', "List wallet");

        $listWallet = $this->Wallet->getListWalletByUserId(AuthComponent::user('id'));

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
        $walletObj = $this->Wallet->getWalletById($id);
        if (empty($walletObj)) {
            throw new BadRequestException('Could not find that wallet.');
        }

        $this->set('title_for_layout', "Edit wallet");
        $this->set('unitObj', $this->Unit->find('all'));
        $this->set('wallet', $walletObj);

        if (!$this->request->is('post', 'put')) {
            return;
        }

        $this->Wallet->set($this->request->data);
        if ($this->Wallet->validates()) {

            //process wallet's icon
            $walletIcon = $walletObj['Wallet']['icon'];
            if ($this->request->data['Wallet']['icon']['size'] > 0) {
                $walletIcon = $this->processUploadImage(
                        AppConstant::FOLDER_UPL, $this->request->data['Wallet']['icon']);
            }
            $this->request->data['Wallet']['icon'] = $walletIcon;

            $updateResult = $this->Wallet->updateWalletById($id, $this->request->data);
            if ($updateResult) {

                $walletInfo = $this->Wallet->getWalletById($id);
                //update session
                $this->Session->write('Auth.User.current_wallet_info', $walletInfo['Wallet']);

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
        $walletObj = $this->Wallet->getWalletById($id);
        if (empty($walletObj)) {
            throw new BadRequestException('Could not find that wallet.');
        }

        $dataUpdate = array(
            'current_wallet' => $id,
        );

        $updateResult = $this->User->updateUserById(AuthComponent::user('id'), $dataUpdate);
        if ($updateResult) {

            $walletInfo = $this->Wallet->getWalletById($id);
            //update session Auth.User.current_wallet
            $this->Session->write('Auth.User.current_wallet', $id);
            $this->Session->write('Auth.User.current_wallet_info', $walletInfo['Wallet']);
            return $this->redirect(array(
                        'controller' => 'transactions',
                        'action'     => 'listSortByDate',
            ));
        }

        $this->autoRender = false;
    }

    /**
     * delete wallet by id
     * 
     * @param int $id Wallet id
     */
    public function delete($id)
    {
        $this->autoRender = false;

        $walletObj = $this->Wallet->getWalletById($id);
        if (empty($walletObj)) {
            throw new BadRequestException('Cound not find that wallet.');
        }

        $listCatDel = $this->Category->deleteCategoriesByWalletId($id);

        //delete all transactions have category_id equal id of category was delete
        foreach ($listCatDel as $key => $category) {
            $this->Transaction->deleteTransactionsByCategoryId($category['Category']['id']);
        }

        $this->Wallet->deleteWalletById($id);

        //if wallet want to delete have id equals current wallet id => update current_wallet
        if ($id == AuthComponent::user('current_wallet')) {

            $walletChoose = $this->Wallet->getFirstWalletByUserId(AuthComponent::user('id'));

            $currentWalletId = null;
            if (!empty($walletChoose)) {
                $currentWalletId = $walletChoose['Wallet']['id'];
            }

            $dataUserUpdate = array(
                'current_wallet' => $currentWalletId,
            );

            $this->User->updateUserById(AuthComponent::user('id'), $dataUserUpdate);
            $this->Session->write('Auth.User.current_wallet', $walletChoose['Wallet']['id']);
            $this->Session->write('Auth.User.current_wallet_info', $walletChoose['Wallet']);
        }

        return $this->redirect(array(
                    'controller' => 'wallets',
                    'action'     => 'listWallet',
        ));
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
            return false;
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
