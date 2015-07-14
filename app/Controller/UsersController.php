<?php

App::uses('CakeEmail', 'Network/Email');
App::uses('SimplePasswordHasher', 'Controller/Component/Auth');

/**
 * Description of UsersController
 *
 * @author dungtq
 */
class UsersController extends AppController
{

    public $uses = array('User', 'Wallet');

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

    /**
     * Components
     *
     * @var array
     */
    public $components = array(
        'Email'
    );

    /**
     * when guess visited website -> redirect to this function
     */
    public function index()
    {
        
    }

    /**
     * display form change password or change info when users redirect
     * 
     * @param string $option Change password or infomation
     */
    public function setting($option = null)
    {
        if ($option != 'password' && $option != 'info') {
            return $this->redirect(array(
                        'controller' => 'users',
                        'action'     => 'index'));
        }

        if (!$this->request->is('post')) {
            if ($option == 'password') {
                $this->set('title_for_layout', "Change password");
            } elseif ($option == 'info') {
                $this->set('title_for_layout', "Change profile");
            }
            return;
        }

        //if user select change password
        if ($option == 'password') {

            $this->User->set($this->request->data);

            if ($this->User->validates()) {

                $passwordHasher = new SimplePasswordHasher(array('hashType' => 'sha256'));
                $pwdUser        = $passwordHasher->hash($this->request->data['User']['password']);

                if ($this->User->updateAll(array(
                            'User.password' => '"' . $pwdUser . '"'
                                ), array(
                            'User.id' => AuthComponent::user('id'))
                        )) {
                    $this->Session->setFlash("Change password complete.");
                    return $this->redirect(Router::url());
                }
                $this->Session->setFlash("Have error! Please try again.");
                return;
            }
            return;
        } elseif ($option == 'info') {

            $this->User->set($this->request->data);

            if ($this->User->validates()) {
                //if user select change info
                $rootFolder = "uploads/"; //folder contains image file
                //process upload avatar
                $userAvatar = AuthComponent::user('avatar');
                if (!empty($this->request->data['User']['avatar']['size'])) {
                    $userAvatar = $this->processUploadImage($rootFolder, $this->request->data['User']['avatar']);
                }

                if ($this->User->updateAll(array(
                            'User.name'    => '"' . $this->request->data['User']['name'] . '"',
                            'User.avatar'  => '"' . $userAvatar . '"',
                            'User.address' => '"' . $this->request->data['User']['address'] . '"'), array(
                            'User.id' => AuthComponent::user('id')
                        ))) {
                    //update success => update auth session
                    $walletInfo = $this->Auth->user('current_wallet');
                    $this->Session->write('Auth', $this->User->read(null, $this->Auth->User('id')));
                    $this->Session->write('Auth.User.current_wallet', $walletInfo);
                    $this->Session->setFlash("Update profile complete.");
                    return $this->redirect(Router::url());
                }
                $this->Session->setFlash("Have error. Please try again.");
                return;
            }
            return;
        }
    }

    /**
     * process image file upload
     * 
     * @param type $rootFolder Folder contain file images
     * @param type $fileObj File image upload
     * @return string
     */
    private function processUploadImage($rootFolder, $fileObj)
    {
        $target_dir  = WWW_ROOT . $rootFolder;
        $target_file = $target_dir . basename($fileObj["name"]);

        if (!move_uploaded_file($fileObj['tmp_name'], $target_file)) {
            $this->Session->setFlash("Have error. Please try again.");
            return;
        }
        return '/' . $rootFolder . $fileObj['name'];
    }

    /**
     * use for user want to login in system
     */
    public function login()
    {
        $this->set('title_for_layout', "Home");

        if ($this->request->is('get')) {
            return;
        }

        if ($this->Auth->login()) {
            $walletInfo = $this->Wallet->getWalletById($this->Auth->user('current_wallet'));
            $this->Session->write('Auth.User.current_wallet', $walletInfo['Wallet']);
            return $this->redirect($this->Auth->redirectUrl());
        }

        //if user login failed
        $this->Session->setFlash('Email or password incorrect! Please try again.', 'default', array(), 'auth');
        return;
    }

    /**
     * when user logout in system
     * 
     * @return type
     */
    public function logout()
    {
        return $this->redirect($this->Auth->logout());
    }

    /**
     *  when guess want to create account    
     */
    public function register()
    {
        $this->set('title_for_layout', 'Register');

        if (!$this->request->is('post')) {
            return;
        }

        $this->User->validator()->remove('avatar');
        $this->request->data['User']['avatar'] = '/img/ava_default.jpeg'; //set user's avatar default
        $createdUser                           = $this->User->createUser($this->request->data);

        if (empty($createdUser)) {
            return;
        }

        // Send activation email
        $emailConfig = array(
            'subject' => 'Please active your account.',
            'view'    => 'activate',
        );
        $this->_sendActivationEmail($createdUser['User'], $emailConfig);
        $this->Session->setFlash('Register completed! Please check your email for validation link!');
        $this->redirect(array(
            'controller' => 'users',
            'action'     => 'login',
        ));
    }

    /**
     * Activate user after registration
     * 
     * @param int $id User id
     * @param string $activeCode Active code
     */
    public function activate($id, $activeCode)
    {
        //check if the active_code is valid
        if (!empty($id) || !empty($activeCode)) {
            $result = $this->User->find('first', array(
                'conditions' => array(
                    'id'          => $id,
                    'active_code' => $activeCode
                )
            ));

            if (!empty($result)) {
                if (!$result['User']['is_active']) {
                    //if not active => active
                    $this->User->updateAll(array(
                        'User.is_active' => true), array(
                        'User.id' => $id
                    ));

                    $this->Session->setFlash('Your registration is complete! You can login to system.');
                } else {
                    $this->Session->setFlash('Your account was actived! Please check again.');
                }

                $this->redirect(array(
                    'controller' => 'users',
                    'action'     => 'login',
                ));
            }
        }

        $this->Session->setFlash('Active code corrupted! Please re-register.');
        $this->redirect(array(
            'controller' => 'users',
            'action'     => 'register',
        ));
    }

    /**
     * config and process send email
     * 
     * @param array $user - user infomation
     * @param array $emailConfig - email information: subject, view (layout for display email content)
     * @return type mixed
     */
    private function _sendActivationEmail($user, $emailConfig)
    {
        $Email = new CakeEmail();
        $Email->config('default');
        $Email->emailFormat('html');
        $Email->from(array('timetolove9x36@gmail.com' => 'Administrator Training.dev'));
        $Email->to($user['email']);
        $Email->subject($emailConfig['subject']);
        $Email->template($emailConfig['view'], 'default');
        $Email->viewVars($user);
        return $Email->send();
    }

    /**
     * get new password for user when they forgot
     * 
     * @return type
     */
    public function forgotPw()
    {
        $this->set('title_for_layout', 'Forgot password');

        if ($this->request->is('get')) {
            return;
        }

        $userEmail = $this->request->data['User']['email'];

        //random password => create new password for user account
        $forgot_pw_code = uniqid();

        $this->User->set($this->request->data);
        $this->User->validator()->remove('email', 'unique');

        if ($this->User->validates()) {
            if ($this->User->updateAll(array(
                        'User.forgot_pw_code' => '"' . $forgot_pw_code . '"'), array(
                        'User.email' => $userEmail,
                    ))) {

                //get user information by email
                $user = $this->User->find('first', array(
                    'conditions' => array(
                        'email' => $userEmail,
                )));

                $emailConfig = array(
                    'subject' => 'Forgot password - Training.dev',
                    'view'    => 'forgot_pw',
                );

                if ($this->_sendActivationEmail($user['User'], $emailConfig)) {
                    $this->Session->setFlash('Please check your email for new password!');
                    return;
                }

                $this->Session->setFlash('Have error! We cheking it!');
                return;
            }
        }

        $this->Session->setFlash('Get password failed. Please try again!');
        return;
    }

    /**
     * when user click to link from email forgot password
     * 
     * @param int $id User id
     * @param string $forgot_pw_code Forgot_code to confirm users' email
     * @return type
     */
    public function resetPw($id = null, $forgot_pw_code = null)
    {
        if (empty($id) || empty($forgot_pw_code)) {
            $this->redirect(array(
                'controller' => 'users',
                'action'     => 'login'
            ));
        }

        if ($this->request->is('post')) {
            $this->User->set($this->request->data);

            //hash password
            $passwordHasher = new SimplePasswordHasher(array('hashType' => 'sha256'));
            $pwdUser        = $passwordHasher->hash($this->request->data['User']['password']);

            if ($this->User->validates()) {
                $this->User->updateAll(array(
                    'User.password' => '"' . $pwdUser . '"',
                        ), array(
                    'User.id' => $id
                ));

                $this->Session->setFlash("Change password was completed.");
                $this->redirect(array(
                    'controller' => 'users',
                    'action'     => 'login'
                ));
            }
            return;
        }
    }

    /**
     * allow go to actions in controller without login 
     */
    public function beforeFilter()
    {
        parent::beforeFilter();
        // Allow users to register and logout.
        $this->Auth->allow('register', 'activate', 'forgotPw', 'resetPw');
    }

}
