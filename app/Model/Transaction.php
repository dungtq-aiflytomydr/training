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
     * Get list transaction by wallet id
     * 
     * @param int $walletId Wallet id
     * @return array
     */
    public function getTransactionsOfWallet($walletId)
    {
        return $this->find('all', array(
                    'conditions' => array(
                        'Transaction.wallet_id' => $walletId,
                    ),
                    'order'      => 'create_time DESC',
        ));
    }

    /**
     * Get list transaction within current wallet and array data contain time
     * 
     * @param array $dateTime Array have time want to find ex: (array('start_time' => 123213, 'end_time' => 200000))
     * @return array
     */
    public function getListTransactionsByDate($dateTime)
    {
        return $this->find('all', array(
                    'conditions' => array(
                        'Transaction.wallet_id'      => AuthComponent::user('current_wallet'),
                        'Transaction.create_time >=' => $dateTime['start_time'],
                        'Transaction.create_time <=' => $dateTime['end_time'],
                    ),
                    'order'      => 'create_time DESC',
        ));
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
