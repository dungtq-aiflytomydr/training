<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of UserModel
 *
 * @author dungtq
 */
class User extends AppModel {

    public $validate = array(
        'email' => array(
            'required' => array(
                'rule' => 'notBlank',
                'message' => 'Please fill out your email.'
            ),
            'unique' => array(
                'rule' => 'isUnique',
                'message' => 'This email had been already used.'
            )
        ),
        'password' => array(
            'required' => array(
                'rule' => 'notBlank',
                'message' => 'Please fill out your password.'
            ),
            'length' => array(
                'rule' => array('minLength', 8),
                'message' => 'Password length must be greater than 6 characters.',
            ),
        ),
        'name' => array(
            'required' => array(
                'rule' => 'notBlank',
                'message' => 'Please fill out your name.'
            ),
            'length' => array(
                'rule' => array('minLength', 4),
                'message' => 'Your name length must be greater than 4 characters.'
            )
        ),
        'old_pw' => array(
            'required' => array(
                'rule' => 'notBlank',
                'message' => 'Please fill out your old password.'
            ),
            'length' => array(
                'rule' => array('minLength', 6),
                'message' => 'Password length must be greater than 6 characters.',
            ),
            'checkOldPw' => array(
                'rule' => 'checkOldPw',
                'message' => 'Old password incorrect.'
            )
        ),
        'new_pw' => array(
            'required' => array(
                'rule' => 'notBlank',
                'message' => 'Please fill out your new password.'
            )
        ),
        'confirm_pw' => array(
            'required' => array(
                'rule' => 'notBlank',
                'message' => 'Please fill out your confirm password.'
            ),
            'confirmPassword' => array(
                'rule' => array('confirmPassword'),
                'message' => 'Please re-enter your password twice so that the values match.',
            )
        )
    );

    public function checkOldPw($check) {
        debug($check);die;
        $condition = array(
            "User.id" => AuthComponent::user('id'),
            "User.password" => AuthComponent::password($this->data["User"]["old_pw"])
        );

        $result = $this->find("count", array("conditions" => $condition));

        return ($result == 1);
    }

}
