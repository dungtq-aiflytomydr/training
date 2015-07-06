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
class UsersController extends AppController {

    public function index() {
//        $this->Session->destroy();
        $this->set('title_for_layout', "Home");
        
        if ($this->request->is('post')) {
            $conditions = array(
                'email' => $this->request->data('email'),
                'password' => $this->request->data('password')
            );
            $query = $this->User->find('first', array('conditions' => $conditions));
            if (!empty($query)) {
                $this->Session->write('loggedIn', true);
                $this->Session->write('email', $this->request->data('email'));
                $this->Session->write('name', $query['User']['name']);
                $this->Session->setFlash('Welcome to home');
                $this->redirect(array('action' => 'listUser'));
            } else {
                $this->Session->setFlash('Login failed! Please check your infomation!');
            }
        }
    }

    public function listUser() {
        $this->set('title_for_layout', "Welcome to Home");
    }

    public function register() {
        $this->set('title_for_layout', 'Register');
        
        if ($this->request->is('post')) {
            if (!empty($this->request->data('email')) 
                && !empty($this->request->data('password')) 
                && !empty($this->request->data('name')) 
                && $this->User->save($this->request->data)) {
                
                $this->Session->write('loggedIn', true);
                $this->Session->write('email', $this->request->data('email'));
                $this->Session->write('name', $this->request->data('name'));
                $this->Session->setFlash('Register Successfully!');
                $this->redirect(array('controller' => 'users', 'action' => 'listUser'));
            } else {
                $this->Session->setFlash('Register failed !');
            }
        }
    }

    public function logOut() {
        $this->Session->destroy();
        $this->redirect(array('controller' => 'users', 'action' => 'index'));
    }

}
