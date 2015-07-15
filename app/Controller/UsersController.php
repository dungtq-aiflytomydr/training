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
     * change user's password
     * 
     * @return type
     */
    public function changePwd()
    {
        $this->set('title_for_layout', "Change password");

        if ($this->request->is('get')) {
            return;
        }

        $this->User->set($this->request->data);
        if ($this->User->validates()) {

            $dataUpdate = array(
                'password' => $this->request->data['User']['password'],
            );
            if ($this->User->updateUserInfoById(AuthComponent::user('id'), $dataUpdate)) {

                $this->Session->setFlash("Change password complete.");
                return $this->redirect('/');
            }
            $this->Session->setFlash("Have error! Please try again.");
        }
    }

    /**
     * edit user's information
     * 
     * @return type
     */
    public function edit()
    {
        $this->set('title_for_layout', "Change profile");

        if ($this->request->is('get')) {
            return;
        }

        $this->User->set($this->request->data);
        if ($this->User->validates()) {

            $userAvatar = AuthComponent::user('avatar');
            if (!empty($this->request->data['User']['avatar']['size'])) {
                $userAvatar = $this->processUploadImage(AppConstant::FOLDER_UPL, $this->request->data['User']['avatar']);
            }
            $this->request->data['User']['avatar'] = $userAvatar;

            if ($this->User->updateUserInfoById(AuthComponent::user('id'), $this->request->data)) {

                //if update data success => update auth session
                $walletInfo = $this->Auth->user('current_wallet');
                $this->Session->write('Auth', $this->User->read(null, $this->Auth->User('id')));
                $this->Session->write('Auth.User.current_wallet', $walletInfo);

                $this->Session->setFlash("Update profile complete.");
                return $this->redirect('/');
            }
            $this->Session->setFlash("Have error. Please try again.");
        }
    }

    /**
     * process image file upload
     * 
     * @param string $rootFolder Folder contain file images
     * @param type $fileObj File image upload
     * @return string
     */
    private function processUploadImage($rootFolder, $fileObj)
    {
        $target_dir  = WWW_ROOT . $rootFolder;
        $target_file = $target_dir . basename($fileObj["name"]);

        if (!move_uploaded_file($fileObj['tmp_name'], $target_file)) {
            return $this->Session->setFlash("Have error. Please try again.");
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

        $this->User->bindModel(array(
            'hasMany' => array(
                'Wallet' => array(
                    'className' => 'Wallet',
                ),
            ),
        ));

        if ($this->Auth->login()) {
            $walletInfo = $this->Wallet->getWalletById($this->Auth->user('current_wallet'));
            $this->Session->write('Auth.User.current_wallet', $walletInfo['Wallet']);
            return $this->redirect($this->Auth->redirectUrl());
        }
        $this->Session->setFlash('Email or password incorrect! Please try again.', 'default', array(), 'auth');
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

        //config email
        $emailConfig = array(
            'subject' => 'Please active your account.',
            'view'    => 'activate',
        );

        if ($this->sendEmail($createdUser['User'], $emailConfig)) {
            $this->Session->setFlash('Register completed! Please check your email for validation link!');
            return $this->redirect(array(
                        'controller' => 'users',
                        'action'     => 'login',
            ));
        }

        $this->Session->setFlash("Have error! We are checking it.");
    }

    /**
     * Activate user after registration
     * 
     * @param int $id User id
     * @param string $activeCode Active code
     */
    public function activate($id, $activeCode)
    {
        $userObj = $this->User->getUser('first', array(
            'id'          => $id,
            'active_code' => $activeCode,
        ));

        if (empty($userObj)) {
            throw NotFoundException('Could not find that user.');
        }

        if (!$userObj['User']['is_active']) {
            $this->User->updateUserInfoById($id, array('is_active' => true));
            $this->Session->setFlash('Your registration is complete! You can login to system.');
        } else {
            $this->Session->setFlash('Your account was actived! Please check again.');
        }

        return $this->redirect(array(
                    'controller' => 'users',
                    'action'     => 'login',
        ));
    }

    /**
     * config and process send email
     * 
     * @param array $user - user infomation
     * @param array $emailConfig - email information: subject, view (layout for display email content)
     * @return type mixed
     */
    private function sendEmail($user, $emailConfig)
    {
        $Email = new CakeEmail();
        $Email->config('default')
                ->from(array('timetolove9x36@gmail.com' => 'Administrator Training.dev'))
                ->to($user['email'])
                ->subject($emailConfig['subject'])
                ->template($emailConfig['view'], 'default')
                ->viewVars($user);
        return $Email->send();
    }

    /**
     * get new password for user when they forgot
     * 
     * @return type
     */
    public function forgotPwd()
    {
        $this->set('title_for_layout', 'Forgot password');

        if ($this->request->is('get')) {
            return;
        }
        $userEmail = $this->request->data['User']['email'];

        $this->User->set($this->request->data);

        unset($this->User->validate['email']['unique']);
        if ($this->User->validates()) {
            $userObj    = $this->User->getUser('first', array(
                'email' => $userEmail,
            ));
            $dataUpdate = array(
                'forgot_pw_code' => uniqid(),
            );

            if ($this->User->updateUserInfoById($userObj['User']['id'], $dataUpdate)) {
                $emailConfig = array(
                    'subject' => 'Forgot password - Training.dev',
                    'view'    => 'forgot_pwd',
                );

                $userObj = $this->User->getById($userObj['User']['id']);

                if ($this->sendEmail($userObj['User'], $emailConfig)) {
                    return $this->Session->setFlash('Please check your email for new password!');
                }
                return $this->Session->setFlash('Have error! We cheking it!');
            }
        }
    }

    /**
     * when user click to link from email forgot password
     * 
     * @param int $id User id
     * @param string $forgot_pw_code Forgot_code to confirm users' email
     * @return type
     */
    public function resetPwd($id, $forgot_pw_code)
    {
        $userObj = $this->User->getUser('first', array(
            'id'             => $id,
            'forgot_pw_code' => $forgot_pw_code,
        ));
        if (empty($userObj)) {
            throw NotFoundException('Could not find that user.');
        }

        if ($this->request->is('post')) {

            $this->User->set($this->request->data);
            if ($this->User->validates()) {
                $dataUpdate = array(
                    'password' => $this->request->data['User']['password'],
                );

                $this->User->updateUserInfoById($id, $dataUpdate);

                $this->Session->setFlash("Change password was completed.");
                return $this->redirect(array(
                            'controller' => 'users',
                            'action'     => 'login'
                ));
            }
            $this->Session->setFlash("Have error! Please try again.");
        }
    }

    /**
     * allow go to actions in controller without login 
     */
    public function beforeFilter()
    {
        parent::beforeFilter();
        $this->Auth->allow('register', 'activate', 'forgotPwd', 'resetPwd');
    }

}
