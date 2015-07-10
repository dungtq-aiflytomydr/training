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
            'required' => array(
                'rule'    => 'notBlank',
                'message' => "Please fill out Wallet's value."
            ),
            'numeric'  => array(
                'rule'    => 'numeric',
                'message' => "Wallet's value contain only numberic."
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
        ),
    );

    /**
     * relation model
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

}
