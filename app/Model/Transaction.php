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
     * a transaction has one category
     * 
     * @var array 
     */
    public $hasOne = array(
        'Category' => array(
            'className'  => 'Category',
            'foreignKey' => 'id',
        )
    );

}
