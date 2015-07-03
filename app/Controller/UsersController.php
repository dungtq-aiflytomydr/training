<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of UsersController
 *
 * @author dungtq
 */
class UsersController extends AppController{
    
    public function index()
    {
    }
    
    public function listUser()
    {
        $this->set('listUser', $this->User->find('all'));
    }
    
    public function detail($id = NULL)
    {
        $this->set('mUser', $this->User->read(NULL, $id));
    }
}
