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
     * when guess visited website -> redirect to this function
     */
    public function index() {
        
    }

    /**
     * display form change password or change info when users redirect
     * @param type $option : change password or infomation
     */
    public function setting($option = null) {
        if ($option != 'password' && $option != 'info') {
            return $this->redirect(array('controller' => 'users', 'action' => 'index'));
        }

        if (!$this->request->is('post')) {
            return;
        }

        //if user choose change password
        if ($option == 'password') {

            //compare old_pw with password in database
            $query = $this->User->find('first', array(
                'conditions' => array(
                    'User.id' => AuthComponent::user('id'),
                    'User.password' => $this->Auth->password($this->request->data['User']['old_pw'])
                )
            ));

            if (empty($query)) {
                $this->Session->setFlash("Old password incorrect.");
                return;
            }

            //compare new_pw with confirm_pw, if not equal => false
            if ($this->request->data['User']['new_pw'] !== $this->request->data['User']['confirm_pw']) {
                $this->Session->setFlash("Confirm password incorrect.");
                return;
            }

            if ($this->User->updateAll(array(
                        'User.password' => '"' . $this->Auth->password($this->request->data['User']['new_pw']) . '"'
                            ), array(
                        'User.id' => AuthComponent::user('id'))
                    )) {
                $this->Session->setFlash("Change password complete.");
                return $this->redirect(Router::url());
            }
        }
    }

    /**
     * use for user want to login in system
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
                    __('Sorry, your account not active!'), 'default', array(), 'auth'
            );
            return;
        }
        $this->Session->setFlash(
                __('Username or password is incorrect!'), 'default', array(), 'auth'
        );
    }

    /**
     * when user logout in system
     * @return type
     */
    public function logout() {
        return $this->redirect($this->Auth->logout());
    }

    /**
     *  when guess want to create account    
     */
    public function register() {
        $this->set('title_for_layout', 'Register');

        if (!$this->request->is('post')) {
            return;
        }

        //encrypt password
        $this->request->data['User']['password'] = $this->Auth->password($this->request->data['User']['password']);
        //if email not exists => insert
        $activeCode = sha1($this->request->data['User']['email'] . rand(0, 100));
        $this->request->data['User']['active_code'] = $activeCode;

        if ($this->User->save($this->request->data)) {

            //Process send email
            App::uses('CakeEmail', 'Network/Email');

            $userEmail = $this->request->data['User']['email'];
            $msg = 'Hi! ' . $this->request->data('name') . "\n";
            $msg .= "Click on the link below to complete registration \n";
            $msg .= 'http://training.dev/users/verify/' . $activeCode . '/' . $this->request->data['User']['email'];

            $Email = new CakeEmail();
            $Email->config('default');
            $Email->from(array('timetolove9x36@gmail.com' => 'Administrator Training.dev'));
            $Email->to($userEmail);
            $Email->subject('Confirm Registration for Training.dev - register');

            if ($Email->send($msg)) {
                $this->Session->setFlash('Register completed! Please check your email for validation link!');
                $this->redirect(array('controller' => 'users', 'action' => 'register'));
            }
            $this->Session->setFlash('Have error! We cheking it!');
            return;
        } else {
            $this->Session->setFlash('Register failed! Please try again!');
        }
    }

    /**
     * verify email when user register on system
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

                    $this->Session->setFlash('Your registration is complete! You can login to system.');
                    $this->redirect('/users/index');
                    exit;
                } else {
                    $this->Session->setFlash('Your account was actived!');
                    $this->redirect('/users/register');
                }
            }
        } else {
            $this->Session->setFlash('Active code corrupted. Please re-register!');
            $this->redirect('/users/register');
        }
    }

    /**
     * get new password for user when they forgot
     * @return type
     */
    public function forgot_pw() {
        $this->set('title_for_layout', 'Forgot password');

        if ($this->request->is('get')) {
            return;
        }

        $userEmail = $this->request->data['User']['email'];

        //random password => send to user
        $char = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $new_pw = substr(str_shuffle($char), 0, 8);

        if ($this->User->updateAll(
                        array('User.password' => '"' . $this->Auth->password($new_pw) . '"'), array('User.email' => $userEmail))) {

            //Process send email 
            $subject = 'Training.dev - Forgot password';
            $msg = "Hi! We send you new password: \n";
            $msg .= "Your email: " . $userEmail . "\n";
            $msg .= "New password: " . $new_pw;

            $Email = new CakeEmail();
            $Email->config('default')
                    ->from(array('timetolove9x36@gmail.com' => 'Administrator Training.dev'))
                    ->to($userEmail)
                    ->subject($subject);

            if ($Email->send($msg)) {
                $this->Session->setFlash('Please check your email for new password!');
                return;
            }
            $this->Session->setFlash('Have error! We cheking it!');
            return;
        } else {
            $this->Session->setFlash('Get new password failed. Please try again!');
            $this->redirect('/users/forgot_pw');
        }
    }

    /**
     * check user password true or false (process by ajax)
     */
    public function checkOldPw() {
        if ($this->request->is('get')) {
            $this->redirect(array('controller' => 'users', 'action' => 'login'));
        }
        $send_pw = $this->Auth->password($this->request->data('value'));
        $user = $this->User->findById($this->request->data('key'));

        if (!empty($user) && ($user['User']['password'] === $send_pw)) {
            echo 1;
            exit;
        }
        echo 0;
        exit;
    }

    /**
     * update user info when user change password or change info
     * @param type $option: change password or info
     */
    public function updateUser($option = null) {
        if ($this->request->is('get')) {
            return $this->redirect(array('controller' => 'users', 'action' => 'index'));
        }

        //if user want to change password
        if ($option === 'password') {
            if ($this->User->updateAll(
                            array('User.password' => '"' . $this->Auth->password($this->request->data('new_pw')) . '"'), array('User.id' => AuthComponent::user('id'))
                    )) {
                $this->Session->setFlash("Change password completed!");
                return $this->redirect(array('controller' => 'users', 'action' => 'index'));
            }
            $this->Session->setFlash("Have error. Please try again!");
            return $this->redirect(array('controller' => 'users', 'action' => 'setting', "param1" => "password",));
        } elseif ($option === 'info') {
            
        }
    }

    /**
     * allow go to actions in controller without login 
     */
    public function beforeFilter() {
        parent::beforeFilter();
        // Allow users to register and logout.
//        $this->Auth->allow('index');
    }

}
