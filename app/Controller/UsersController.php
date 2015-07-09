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

    public $uses = array('User');

    /**
     * Helpers
     *
     * @var array
     */
    public $helpers    = array(
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
    public function index()
    {
        
    }

    /**
     * display form change password or change info when users redirect
     * @param type $option : change password or infomation
     */
    public function setting($option = null)
    {
        if ($option != 'password' && $option != 'info') {
            return $this->redirect(array(
                        'controller' => 'users',
                        'action'     => 'index'));
        }

        if (!$this->request->is('post')) {
            return;
        }

        //if user select change password
        if ($option == 'password') {

            //compare old_pw with password in database
            $query = $this->User->find('first', array(
                'conditions' => array(
                    'User.id'       => AuthComponent::user('id'),
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
        } elseif ($option == 'info') {
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
                $this->Session->write('Auth', $this->User->read(null, $this->Auth->User('id')));
                $this->Session->setFlash("Update profile complete.");
                return $this->redirect(Router::url());
            }
            $this->Session->setFlash("Have error. Please try again.");
            return;
        }
    }

    /**
     * process image file upload
     * @param type $rootFolder : folder contain file images
     * @param type $fileObj : file image upload
     * @return string
     */
    private function processUploadImage($rootFolder, $fileObj)
    {
        $target_dir  = WWW_ROOT . $rootFolder;
        $target_file = $target_dir . basename($fileObj["name"]);

        // Allow certain file formats
        if ($fileObj["type"] !== "image/jpg" && $fileObj["type"] !== "image/png" && $fileObj["type"] !== "image/jpeg" && $fileObj["type"] !== "image/gif") {
            $this->Session->setFlash("Sorry, your format image incorrect.");
            return;
        }

        if ($fileObj['size'] > 3000000) {
            $this->Session->setFlash("Sorry, your image is too large.");
            return;
        }

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
            return $this->redirect($this->Auth->redirectUrl());
        }

        //if user login failed
        $this->Session->setFlash('Email or password incorrect! Please try again.', 'default', array(), 'auth');
        return;
    }

    /**
     * when user logout in system
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
        $this->redirect('/');
    }

    /**
     * Activate user after registration
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
     * @return type
     */
    public function forgot_pw()
    {
        $this->set('title_for_layout', 'Forgot password');

        if ($this->request->is('get')) {
            return;
        }

        $userEmail = $this->request->data['User']['email_forgot_pw'];

        //random password => create new password for user account
        $forgot_pw_code = uniqid();

        $this->User->set($this->request->data);
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
     * when user click link from email
     * 
     * @param int $id User id
     * @param string $forgot_pw_code Forgot_code to confirm users' email
     * @return type
     */
    public function reset_pw($id = null, $forgot_pw_code = null)
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
     * check user password true or false (process by ajax)
     */
    public function checkOldPw()
    {
        if ($this->request->is('get')) {
            $this->redirect(array(
                'controller' => 'users',
                'action'     => 'login'));
        }
        $send_pw = $this->Auth->password($this->request->data('value'));
        $user    = $this->User->findById($this->request->data('key'));

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
    public function updateUser($option = null)
    {
        if ($this->request->is('get')) {
            return $this->redirect(array(
                        'controller' => 'users',
                        'action'     => 'index'));
        }

        //if user want to change password
        if ($option === 'password') {
            if ($this->User->updateAll(array(
                        'User.password' => '"' . $this->Auth->password($this->request->data('new_pw')) . '"'), array(
                        'User.id' => AuthComponent::user('id'))
                    )) {
                $this->Session->setFlash("Change password completed!");
                return $this->redirect(array(
                            'controller' => 'users',
                            'action'     => 'index'));
            }
            $this->Session->setFlash("Have error. Please try again!");
            return $this->redirect(array(
                        'controller' => 'users',
                        'action'     => 'setting',
                        'param1'     => 'password',));
        } elseif ($option === 'info') {
            
        }
    }

    /**
     * allow go to actions in controller without login 
     */
    public function beforeFilter()
    {
        parent::beforeFilter();
        // Allow users to register and logout.
        $this->Auth->allow('register', 'activate', 'forgot_pw', 'reset_pw');
    }

}
