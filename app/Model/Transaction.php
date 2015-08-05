<?php

class Transaction extends AppModel
{

    public $validate = array(
        'category_id' => array(
            'required' => array(
                'rule'    => 'notBlank',
                'message' => 'Please select category.'
            ),
        ),
        'amount'      => array(
            'required' => array(
                'rule'    => 'notBlank',
                'message' => 'Please fill out amount.'
            ),
            'numeric'  => array(
                'rule'    => 'numeric',
                'message' => 'Amount contain only numeric.'
            ),
        ),
    );

    /**
     * create new transaction
     * 
     * @param array $data Transaction data
     * @return mixed
     */
    public function createTransaction($data)
    {
        $this->create();
        return $this->save($data);
    }

    /**
     * Update Transaction data
     * 
     * @param int $id Transaction id
     * @param array $data Transaction data
     * @return mixed
     */
    public function updateById($id, $data)
    {
        $this->id = $id;
        return $this->save($data);
    }

    /**
     * get Transaction information by id
     * 
     * @param int $id Transaction id
     * @return array|null
     */
    public function getById($id)
    {
        $this->bindCategory();
        return $this->findById($id);
    }

    /**
     * Get list transaction by wallet id
     * 
     * @param int $walletId Wallet id
     * @return array
     */
    public function getTransactionsOfWallet($walletId)
    {
        $this->bindCategory();
        return $this->find('all', array(
                    'conditions' => array(
                        'Transaction.wallet_id' => $walletId,
                    ),
                    'order'      => 'create_time DESC',
        ));
    }

    /**
     * Get list transaction within current wallet by from date and to date
     * 
     * @param int $fromDate From date
     * @param int $toDate To date
     * @param mixed $orderBy sort records by ASC or DESC with any field
     * @return array
     */
    public function getTransactionsByDateRange($fromDate, $toDate, $orderBy = 'Transaction.create_time DESC')
    {
        $this->bindCategory();

        return $this->find('all', array(
                    'conditions' => array(
                        'Transaction.wallet_id'      => AuthComponent::user('current_wallet'),
                        'Transaction.create_time >=' => $fromDate,
                        'Transaction.create_time <=' => $toDate,
                    ),
                    'order'      => $orderBy,
        ));
    }

    /**
     * bindModel Category in Transaction
     */
    public function bindCategory()
    {
        $this->bindModel(array('belongsTo' => array('Category')));
    }

    /**
     * Delete transaction by id
     * 
     * @param int $id Transaction id
     */
    public function deleteById($id)
    {
        $this->delete($id);
    }

    /**
     * delete multiple transactions within category id
     * 
     * @param int $categoryId Category id
     * @return mixed
     */
    public function deleteTransactionsOfCategory($categoryId)
    {
        return $this->deleteAll(array(
                    'category_id' => $categoryId,
        ));
    }

    /**
     * delete multiple transactions within wallet id
     * 
     * @param int $wallet_id Wallet id
     * @return mixed
     */
    public function deleteTransactionsOfWallet($wallet_id)
    {
        return $this->deleteAll(array(
                    'wallet_id' => $wallet_id,
        ));
    }

}
