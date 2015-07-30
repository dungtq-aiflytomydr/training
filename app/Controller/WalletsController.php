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
     * add wallet information
     */
    public function add()
    {
        $this->set('title_for_layout', "New wallet");
        $this->set('unitObj', $this->Unit->getAllUnit());

        if (!$this->request->is(array('post', 'put'))) {
            return;
        }

        //process wallet's icon
        $walletIcon = null;
        if ($this->request->data['Wallet']['icon']['size'] > 0) {
            $walletIcon = $this->__processUploadImage(
                    AppConstant::FOLDER_UPL, $this->request->data['Wallet']['icon']);
        }

        $this->request->data['Wallet']['icon']     = $walletIcon;
        $this->request->data['Wallet']['is_setup'] = true;
        $this->request->data['Wallet']['user_id']  = AuthComponent::user('id');

        $this->Wallet->set($this->request->data);
        if ($this->Wallet->validates()) {
            if ($this->Wallet->createWallet($this->request->data)) {

                //check if user have not anything wallet => set default wallet
                $manyWallet = $this->Wallet->countUserWallets(AuthComponent::user('id'));

                if ($manyWallet == 1) {
                    $currentWalletId = $this->Wallet->getInsertID();

                    $dataUpdate = array(
                        'current_wallet' => $currentWalletId,
                    );

                    $this->User->updateById(AuthComponent::user('id'), $dataUpdate);

                    //update session for current_wallet Auth's property
                    $currentWallet = $this->Wallet->getById($currentWalletId);
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

        $listWallet = $this->Wallet->getWalletsOfUser(AuthComponent::user('id'));

        //convert wallet information
        $listUnit = $this->Unit->getAllUnit();
        //remove key 'Unit' in $listUnit
        $listUnit = array_column($listUnit, 'Unit');

        foreach ($listWallet as $key => $wallet) {
            $keyUnit                  = array_search($wallet['Wallet']['unit_id'], array_column($listUnit, 'id'));
            $listWallet[$key]['Unit'] = $listUnit[$keyUnit];
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
        $walletObj = $this->Wallet->getById($id);
        if (empty($walletObj)) {
            throw new NotFoundException('Could not find that wallet.');
        }

        if ($walletObj['Wallet']['user_id'] !== AuthComponent::user('id')) {
            throw new NotFoundException('Access is denied.');
        }

        $this->set('title_for_layout', "Edit wallet");
        $this->set('unitObj', $this->Unit->getAllUnit());
        $this->set('wallet', $walletObj);

        if (empty($this->request->data)) {
            $this->request->data = $walletObj;
        }

        if (!$this->request->is(array('post', 'put'))) {
            return;
        }

        $this->Wallet->set($this->request->data);
        if ($this->Wallet->validates()) {

            //process wallet's icon
            $walletIcon = $walletObj['Wallet']['icon'];
            if ($this->request->data['Wallet']['icon']['size'] > 0) {
                $walletIcon = $this->__processUploadImage(
                        AppConstant::FOLDER_UPL, $this->request->data['Wallet']['icon']);
            }
            $this->request->data['Wallet']['icon'] = $walletIcon;

            $updateResult = $this->Wallet->updateById($id, $this->request->data);
            if ($updateResult) {

                //update session for current_wallet_info
                $walletInfo = $this->Wallet->getById($id);
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
        $this->autoRender = false;

        if (!$this->request->is('post')) {
            throw new BadRequestException('Request not found.');
        }

        $walletObj = $this->Wallet->getById($id);
        if (empty($walletObj)) {
            throw new NotFoundException('Could not find that wallet.');
        }

        $dataUpdate = array(
            'current_wallet' => $id,
        );

        $updateResult = $this->User->updateById(AuthComponent::user('id'), $dataUpdate);
        if ($updateResult) {

            //update session Auth.User.current_wallet
            $walletInfo = $this->Wallet->getById($id);
            $this->Session->write('Auth.User.current_wallet', $id);
            $this->Session->write('Auth.User.current_wallet_info', $walletInfo['Wallet']);

            return $this->redirect(array(
                        'controller' => 'transactions',
                        'action'     => 'listSortByDate',
            ));
        }
    }

    /**
     * delete wallet by id
     * 
     * @param int $id Wallet id
     */
    public function delete($id)
    {
        $this->autoRender = false;

        if (!$this->request->is('post')) {
            throw new BadRequestException('Could not found request.');
        }

        $walletObj = $this->Wallet->getById($id);
        if (empty($walletObj)) {
            throw new NotFoundException('Cound not find that wallet.');
        }

        //delete data with transaction
        $dbSource   = $this->Wallet->getDataSource();
        $dbSource->begin();
        $flagDelete = true; //flag save status function delete

        if (!$this->Category->deleteCategoriesOfWallet($id)) {
            $flagDelete = false;
        }

        //delete all transactions have category_id equal id of category was delete
        if (!$this->Transaction->deleteTransactionsOfWallet($id)) {
            $flagDelete = false;
        }

        if (!$this->Wallet->deleteById($id)) {
            $flagDelete = false;
        }

        if ($flagDelete) {
            $dbSource->commit();
        } else {
            $dbSource->rollback();
        }

        //if wallet want to delete have id equals current_wallet id => set default wallet for other wallet
        if ($id == AuthComponent::user('current_wallet')) {

            $walletChoose = $this->Wallet->getFirstWalletOfUser(AuthComponent::user('id'));

            $currentWalletId = null;
            if (!empty($walletChoose)) {
                $currentWalletId = $walletChoose['Wallet']['id'];

                $this->Session->write('Auth.User.current_wallet', $walletChoose['Wallet']['id']);
                $this->Session->write('Auth.User.current_wallet_info', $walletChoose['Wallet']);
            } else {
                $this->Session->write('Auth.User.current_wallet', null);
                $this->Session->write('Auth.User.current_wallet_info', null);
            }

            $dataUserUpdate = array(
                'current_wallet' => $currentWalletId,
            );

            $this->User->updateById(AuthComponent::user('id'), $dataUserUpdate);
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
    private function __processUploadImage($rootFolder, $fileObj)
    {
        $target_dir  = WWW_ROOT . $rootFolder;
        $target_file = $target_dir . basename($fileObj["name"]);

        if (!move_uploaded_file($fileObj['tmp_name'], $target_file)) {
            return false;
        }
        return '/' . $rootFolder . $fileObj['name'];
    }

}
