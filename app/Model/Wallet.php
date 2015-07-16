<?php

class Wallet extends AppModel
{

    /**
     * $validate
     * 
     * @var array 
     */
    public $validate = array(
        'name'    => array(
            'required' => array(
                'rule'    => 'notBlank',
                'message' => "Please fill out wallet's name."
            ),
            'length'   => array(
                'rule'    => array('minLength', 4),
                'message' => "Wallet's name must be length greater than 4 characters."
            ),
        ),
        'balance' => array(
            'required'      => array(
                'rule'    => 'notBlank',
                'message' => "Please fill out Wallet's value."
            ),
            'naturalNumber' => array(
                'rule'    => 'naturalNumber',
                'message' => "Wallet's value contain only numberic and value > 0."
            ),
        ),
        'unit_id' => array(
            'required' => array(
                'rule'    => 'notBlank',
                'message' => "Please select unit."
            ),
        ),
        'icon'    => array(
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
    );

    /**
     * relationship hasOne Unit
     * 
     * @var array 
     */
    public $hasOne = array(
        'Unit' => array(
            'className'  => 'Unit',
            'foreignKey' => 'id',
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
     * create new wallet
     * 
     * @param array $data Wallet data
     * @return mixed
     */
    public function createWallet($data)
    {
        return $this->save($data);
    }

    /**
     * update wallet's data by id
     * 
     * @param int $id Wallet id
     * @param array $data Wallet data
     * @return mixed
     */
    public function updateWalletById($id, $data)
    {
        $this->id = $id;
        return $this->save($data);
    }

    /**
     * get Wallet information by id
     * 
     * @param int $id Wallet id
     * @return mixed
     */
    public function getWalletById($id)
    {
        return $this->findById($id);
    }

    /**
     * Get list wallet by User id
     * 
     * @param int $userId User id
     * @return array
     */
    public function getListWalletByUserId($userId)
    {
        return $this->find('all', array(
                    'conditions' => array(
                        'user_id' => $userId
                    ),
        ));
    }

    /**
     * Get number records wallet by user id
     * 
     * @param int $userId User id
     * @return number
     */
    public function getNumWalletsByUserId($userId)
    {
        return $this->find('count', array(
                    'conditions' => array(
                        'user_id' => $userId,
                    ),
        ));
    }

    /**
     * Bind model hasOne
     * 
     * @param string $model Model name
     */
    public function bindModelHasOne($model)
    {
        $this->bindModel(array(
            'hasOne' => array(
                $model => array(
                    'className'  => $model,
                    'foreignKey' => 'id',
                ),
            ),
        ));
    }

    /**
     * Bind model hasMany
     * 
     * @param string $model Model name
     */
    public function bindModelHasMany($model)
    {
        $this->bindModel(array(
            'hasMany' => array(
                $model => array(
                    'className'  => $model,
                    'foreignKey' => 'id',
                ),
            ),
        ));
    }

    /**
     * Bind model belongsTo
     * 
     * @param string $model Model name
     */
    public function bindModelBelongsTo($model)
    {
        $this->bindModel(array(
            'belongsTo' => array(
                $model => array(
                    'className'  => $model,
                    'foreignKey' => 'id',
                )
            ),
        ));
    }

    /**
     * Unbind model belongsTo
     * 
     * @param string $model Model name
     */
    public function unbindModelBelongsTo($model)
    {
        $this->unbindModel(array(
            'belongsTo' => array($model)
        ));
    }

    /**
     * Get wallet information by any conditions
     * 
     * @param int $userId User id
     * @return array
     */
    public function getFirstWalletByUserId($userId)
    {
        return $this->find('first', array(
                    'conditions' => array(
                        'user_id' => $userId,
                    ),
        ));
    }

    /**
     * Delete wallet by wallet id
     * 
     * @param int $id Wallet id
     * @return mixed
     */
    public function deleteWalletById($id)
    {
        return $this->delete($id);
    }

}
