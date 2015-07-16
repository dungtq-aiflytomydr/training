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
     * when user was logged -> redirect to this function
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

        if (!$this->request->is('post', 'put')) {
            return;
        }

        $this->User->set($this->request->data);
        if ($this->User->validates()) {

            $dataUpdate = array(
                'password' => $this->request->data['User']['password'],
            );

            $updateResult = $this->User->updateUserById(AuthComponent::user('id'), $dataUpdate);
            if ($updateResult) {

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

        if (!$this->request->is('post', 'put')) {
            return;
        }

        $this->User->set($this->request->data);
        if ($this->User->validates()) {

            $userAvatar = AuthComponent::user('avatar');
            if (!empty($this->request->data['User']['avatar']['size'])) {
                $userAvatar = $this->processUploadImage(
                        AppConstant::FOLDER_UPL, $this->request->data['User']['avatar']);
                if ($userAvatar) {
                    return $this->Session->setFlash("Upload image have error! Please check again.");
                }
            }

            $this->request->data['User']['avatar'] = $userAvatar;
            unset($this->request->data['User']['password']);

            $updateResult = $this->User->updateUserById(AuthComponent::user('id'), $this->request->data);
            if ($updateResult) {

                //if update data success => update auth session
                $walletInfo = $this->Auth->user('current_wallet');
                $this->Session->write('Auth', $this->User->read(null, $this->Auth->User('id')));
                if (!empty($walletInfo)) {
                    $this->Session->write('Auth.User.current_wallet_info', $walletInfo);
                }

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
     * @return mixed
     */
    private function processUploadImage($rootFolder, $fileObj)
    {
        $target_dir  = WWW_ROOT . $rootFolder;
        $target_file = $target_dir . basename($fileObj["name"]);

        if (!move_uploaded_file($fileObj['tmp_name'], $target_file)) {
            return false;
        }
        return '/' . $rootFolder . $fileObj['name'];
    }

    /**
     * use for user want to login in system
     */
    public function login()
    {
        $this->set('title_for_layout', "Home");

        if (!$this->request->is('post', 'put')) {
            return;
        }

        if ($this->Auth->login()) {
            $walletInfo = $this->Wallet->getWalletById($this->Auth->user('current_wallet'));
            if (!empty($walletInfo)) {
                $this->Session->write('Auth.User.current_wallet_info', $walletInfo['Wallet']);
            }
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

        if (!$this->request->is('post', 'put')) {
            return;
        }

        $this->User->validator()->remove('avatar');
        $createdUser = $this->User->createUser($this->request->data);

        if (empty($createdUser)) {
            return $this->Session->setFlash('Have error! Please try again.');
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

        $this->Session->setFlash("Have error! Please try again.");
    }

    /**
     * Activate user after registration
     * 
     * @param int $id User id
     * @param string $token String token
     */
    public function activate($id, $token)
    {
        $userObj = $this->User->getByToken($id, $token);

        if (empty($userObj)) {
            throw new BadRequestException('Could not find that user or that user was active.');
        }

        if (!$userObj['User']['is_active']) {
            $this->User->updateUserById($id, array(
                'is_active' => true,
            ));
        }

        $this->Session->setFlash('Your registration is complete! You can login to system.');
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

        if (!$this->request->is('post', 'put')) {
            return;
        }
        $userEmail = $this->request->data['User']['email'];

        $this->User->set($this->request->data);

        unset($this->User->validate['email']['unique']);
        if ($this->User->validates()) {
            $userObj    = $this->User->getByEmail($userEmail);
            $dataUpdate = array(
                'token' => uniqid(),
            );

            $updateResult = $this->User->updateUserById($userObj['User']['id'], $dataUpdate);
            if ($updateResult) {
                $emailConfig = array(
                    'subject' => 'Forgot password - Training.dev',
                    'view'    => 'forgot_pwd',
                );

                $userObj = $this->User->getById($userObj['User']['id']);

                if ($this->sendEmail($userObj['User'], $emailConfig)) {
                    return $this->Session->setFlash('Please check your email for new password.');
                }
            }
            return $this->Session->setFlash('Have error! Please try again.');
        }
    }

    /**
     * when user click to link from email forgot password
     * 
     * @param int $id User id
     * @param string $token String token
     * @return type
     */
    public function resetPwd($id, $token)
    {
        $userObj = $this->User->getByToken($id, $token);
        if (empty($userObj)) {
            throw NotFoundException('Could not find that user.');
        }

        if ($this->request->is('post')) {

            $this->User->set($this->request->data);
            if ($this->User->validates()) {
                $dataUpdate = array(
                    'password' => $this->request->data['User']['password'],
                );

                $this->User->updateUserById($id, $dataUpdate);

                $this->Session->setFlash("Change password was completed.");
                return $this->redirect(array(
                            'controller' => 'users',
                            'action'     => 'login'
                ));
            }
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
