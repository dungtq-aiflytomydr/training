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
        )
    );

    /**
     * create new transaction
     * 
     * @param array $data Transaction data
     * @return mixed
     */
    public function createTransaction($data)
    {
        return $this->save($data);
    }

    /**
     * Update Transaction data
     * 
     * @param int $id Transaction id
     * @param array $data Transaction data
     * @return mixed
     */
    public function updateTransactionById($id, $data)
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
    public function getListTransactionsByWalletId($walletId)
    {
        return $this->find('all', array(
                    'conditions' => array(
                        'Transaction.wallet_id' => $walletId,
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
     * delete multiple transactions have category_id equals category id identify
     * 
     * @param int $categoryId Category id
     */
    public function deleteTransactionsByCategoryId($categoryId)
    {
        $this->deleteAll(array(
            'category_id' => $categoryId,
        ));
    }

}
