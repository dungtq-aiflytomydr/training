<?php

/**
 * Description of UsersController
 *
 * @author dungtq
 */
class UsersController extends AppController {
    
    /**
     * index() - when guess visited website -> redirect to this function
    */
    public function index() {
        
    }

    public function setting($option = null) {
        if (!$this->Session->read('loggedIn') || $option == null) {
            $this->redirect(array('controller' => 'users'));
        }
    }
    
    /**
     * listUser() - function demo when user logged in system
     */
    public function listUser() {
        $this->set('title_for_layout', "Welcome to Home");
    }
    
    /**
     * login() - use for user want to login in system
    */
    public function login() {
        $this->set('title_for_layout', "Home");

        if (!$this->request->is('post')) {
            return;
        }

        if ($this->Auth->login()) {
            if ($this->Auth->user('is_active')) {
                return $this->redirect($this->Auth->redirectUrl());
            }
            $this->Session->setFlash(
                    __('Sorry, your account not active !'), 'default', array(), 'auth'
            );
            return;
        }
        $this->Session->setFlash(
                __('Username or password is incorrect'), 'default', array(), 'auth'
        );
    }

    /**
     *  register() - when guess want to create account    
     */
    public function register() {
        $this->set('title_for_layout', 'Register');

        if (!$this->request->is('post')) {
            return;
        }
        
        //check email if exists
        $query = $this->User->find('all', array(
                                            'condition' => array('User.email' => $this->request->data('email')))
                                );
        if(!empty($query)){
            $this->Session->setFlash("Sorry, your email had been used !");
            return;
        }
        
        //if email not exists => insert
        $this->request->data['password'] = $this->Auth->password($this->request->data('password'));

        if ($this->User->save($this->request->data)) {

            if ($this->Auth->login()) {
                if ($this->Auth->user('is_active')) {
                    return $this->redirect($this->Auth->redirectUrl());
                }
                $this->Session->setFlash(
                        __('Sorry, your account not active !'), 'default', array(), 'auth'
                );
                return;
            }
        } else {
            $this->Session->setFlash('Register failed !');
        }
    }

    /**
     * logout() - when user logout in system
     * @return type
     */
    public function logout() {
        return $this->redirect($this->Auth->logout());
    }

    /**
     * beforFilter() - allow go to actions in controller without login 
     */
    public function beforeFilter() {
        parent::beforeFilter();
        // Allow users to register and logout.
//        $this->Auth->allow('index');
    }

}
