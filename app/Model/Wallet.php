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

}
