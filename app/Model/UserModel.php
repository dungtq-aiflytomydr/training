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
            'rule' => 'notBlank',
            'massage' => 'Please fill out your email!'
        ),
        'password' => array(
            'rule' => 'notBlank',
            'massage' => 'Please fill out your password!'
        ),
        'name' => array(
            'rule' => 'notBlank',
            'massage' => 'Please fill out your name!'
        ),
    );

}
