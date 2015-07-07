<?php

/**
 * params use for compare in verify
 */
define('NOT_ACTIVE', 0);
define('ACTIVED', 1);

/**
 * Description of UsersController
 *
 * @author dungtq
 */
class UsersController extends AppController {

    /**
     * Helpers
     *
     * @var array
     */
    public $helpers = array(
        'Html',
        'Form',
        'Session',
        'Time',
        'Text'
    );
    public $components = array(
        'Email'
    );

    /**
     * index() - when guess visited website -> redirect to this function
     */
    public function index() {
        
    }

    /**
     * 
     * @param type $option
     */
    public function setting($option = null) {
        
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
            //if account not active => session destroy
            $this->Session->destroy();
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
            'conditions' => array('email' => $this->request->data('email')))
        );
        if (!empty($query)) {
            $this->Session->setFlash("Sorry, your email had been used!");
            return;
        }

        //encrypt password
        $this->request->data['password'] = $this->Auth->password($this->request->data('password'));
        //if email not exists => insert
        $activeCode = sha1($this->request->data('email') . rand(0, 100));
        $this->request->data['active_code'] = $activeCode;

        if ($this->User->save($this->request->data)) {

            $this->Email->smtpOptions = array(
                'port' => '465',
                'timeout' => '30',
                'host' => 'ssl://smtp.gmail.com',
                'username' => 'timetolove9x36@gmail.com',
                'password' => 'lybeauty36'
            );

            //Process send email
            $userEmail = $this->request->data('email');
            $subject = 'Confirm Registration for Training.dev - register';
            $msg = 'Hi! ' . $this->request->data('name') . '<br />';
            $msg = 'Click on the link below to complete registration <br />';
            $msg .= 'http://training.dev/users/verify/' . $activeCode . '/' . $this->request->data('email');

            $this->Email->to = $userEmail;
            $this->Email->subject = $subject;
            $this->Email->from = 'timetolove9x36@gmail.com';
            $this->Email->delivery = 'smtp';
            if ($this->Email->send($msg)) {
                $this->Session->setFlash('Please Check your email for validation link');
                return;
            }
            $this->Session->setFlash('Have error! We cheking it!');
            return;
        } else {
            $this->Session->setFlash('Register failed! Please try again!');
        }
    }

    /**
     * verify() - verify email when user register on system
     * @param type $activeCode - active code
     * @param type $email - user use for register
     */
    public function verify($activeCode, $email) {
        //check if the token is valid
        if (!empty($activeCode) || !empty($email)) {
            $result = $this->User->find('first', array(
                'conditions' => array(
                    'email' => $email,
                    'active_code' => $activeCode
                )
            ));

            if (!empty($result)) {
                if ($result['User']['is_active'] == NOT_ACTIVE) {
                    //if not active => active
                    $result['User']['is_active'] = ACTIVED;
                    $this->User->save($result);

                    $this->Session->setFlash('Your registration is complete');
                    $this->redirect('/users/index');
                    exit;
                } else {
                    $this->Session->setFlash('Your account was actived!');
                    $this->redirect('/users/register');
                }
            }
        } else {
            $this->Session->setFlash('Active code corrupted. Please re-register');
            $this->redirect('/users/register');
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
