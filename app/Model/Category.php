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

}
