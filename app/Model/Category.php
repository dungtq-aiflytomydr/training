<?php

class Category extends AppModel
{

    /**
     * validation
     * 
     * @var array 
     */
    public $validate = array(
        'name'         => array(
            'required' => array(
                'rule'    => 'notBlank',
                'message' => "Please fill out Category's name."
            )
        ),
        'icon'         => array(
            'fileType' => array(
                'rule'    => array(
                    'extension',
                    array('gif', 'jpeg', 'png', 'jpg')
                ),
                'message' => 'Please supply a valid image (.gif, .jpeg, .png, .jpg).'
            ),
            'size'     => array(
                'rule'    => array('fileSize', '<=', '1MB'),
                'message' => 'Image must be less than 1MB'
            ),
        ),
        'expense_type' => array(
            'required' => array(
                'rule'    => 'notBlank',
                'message' => 'Please select expense type.'
            )
        ),
    );

    /**
     * if user not setup icon => unset property icon
     * 
     * @return boolean
     */
    public function beforeValidate($options = array())
    {
        if (empty($this->data[$this->alias]['icon']['size'])) {
            unset($this->validate['icon']);
        }

        return true;
    }

    /**
     * Create new category
     * 
     * @param array $data Category data
     * @return mixed
     */
    public function createCategory($data)
    {
        return $this->save($data);
    }

    /**
     * Update category data by id
     * 
     * @param int $id Category id
     * @param array $data Category's data
     * @return mixed
     */
    public function updateCategoryById($id, $data)
    {
        $this->id = $id;
        return $this->save($data);
    }

    /**
     * get category information by id
     * 
     * @param int $id Category id
     * @return mixed
     */
    public function getCategoryById($id)
    {
        return $this->findById($id)['Category'];
    }

    /**
     * get list category by wallet id
     * 
     * @param int $walletId Wallet id
     * @return array|null
     */
    public function getListCategoryByWalletId($walletId)
    {
        return $this->find('all', array(
                    'conditions' => array(
                        'Category.wallet_id' => array(0, $walletId),
                    ),
        ));
    }

    /**
     * delete all categories have $wallet_id equals id of current wallet
     * 
     * @param int $walletId Wallet id
     */
    public function deleteCategoriesByWalletId($walletId)
    {
        //get listCategory will be delete => delete transactions
        $listCategory = $this->find('all', array(
            'conditions' => array(
                'wallet_id' => $walletId,
            ),
        ));
        //delete
        $this->deleteAll(array(
            'wallet_id' => $walletId,
        ));

        return $listCategory;
    }

    /**
     * Delete category by id
     * 
     * @param int $id Category id
     */
    public function deleteCategoryById($id)
    {
        $this->delete($id);
    }

}
