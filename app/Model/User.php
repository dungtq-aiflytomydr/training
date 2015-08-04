<?php

App::uses('SimplePasswordHasher', 'Controller/Component/Auth');

/**
 * Description of UserModel
 *
 * @author dungtq
 */
class User extends AppModel
{

    /**
     * validates
     * 
     * @var array 
     */
    public $validate = array(
        'email'      => array(
            'required' => array(
                'rule'    => 'notBlank',
                'message' => 'Please fill out your email.'
            ),
            'format'   => array(
                'rule'    => 'email',
                'message' => 'Email not correct.',
            ),
            'unique'   => array(
                'rule'    => 'isUnique',
                'message' => 'This email had been already used.'
            ),
        ),
        'password'   => array(
            'required' => array(
                'rule'    => 'notBlank',
                'message' => 'Please fill out your password.'
            ),
            'length'   => array(
                'rule'    => array('minLength', 6),
                'message' => 'Password length must be greater than 6 characters.',
            ),
        ),
        'name'       => array(
            'required' => array(
                'rule'    => 'notBlank',
                'message' => 'Please fill out your name.'
            ),
            'length'   => array(
                'rule'    => array('minLength', 4),
                'message' => 'Your name length must be greater than 4 characters.'
            ),
        ),
        'avatar'     => array(
            'fileType' => array(
                'rule'    => array(
                    'extension',
                    array('gif', 'jpeg', 'png', 'jpg')
                ),
                'message' => "Please supply a valid image('.gif', '.jpeg', '.png', '.jpg')."
            ),
            'fileSize' => array(
                'rule'    => array('fileSize', '<=', '1MB'),
                'message' => 'Image must be less than 1MB'
            ),
        ),
        'current_pw' => array(
            'required'      => array(
                'rule'    => 'notBlank',
                'message' => 'Please fill out your current password.'
            ),
            'checkPassword' => array(
                'rule'    => 'checkPassword',
                'message' => 'Password is not correct.'
            ),
        ),
        'confirm_pw' => array(
            'required' => array(
                'rule'    => 'notBlank',
                'message' => 'Please fill out your confirm password.'
            ),
            'match'    => array(
                'rule'    => 'matchPassword',
                'message' => 'Passwords do not match.'
            ),
        )
    );

    /**
     * Hash password before saving
     */
    public function beforeSave($options = array())
    {
        if (!empty($this->data[$this->alias]['password'])) {
            $passwordHasher                       = new SimplePasswordHasher(array('hashType' => 'sha256'));
            $this->data[$this->alias]['password'] = $passwordHasher->hash(
                    $this->data[$this->alias]['password']
            );
        }
        return true;
    }

    /**
     * if user not setup avatar => unset property avatar
     * 
     * @return boolean
     */
    public function beforeValidate($options = array())
    {
        if (empty($this->data[$this->alias]['avatar']['size'])) {
            unset($this->validate['avatar']);
        }

        return true;
    }

    /**
     * Create new user
     * 
     * @param array $data
     * @return boolean
     */
    public function createUser($data)
    {
        $data['User']['token'] = uniqid();

        $this->create();
        return $this->save($data);
    }

    /**
     * Get user by Id
     * 
     * @param int $id User Id
     * @return array
     */
    public function getById($id)
    {
        return $this->findById($id);
    }

    /**
     * Get user information by User id and token
     * 
     * @param int $id User id
     * @param string $token String token
     */
    public function getByToken($id, $token)
    {
        return $this->find('first', array(
                    'conditions' => array(
                        'id'    => $id,
                        'token' => $token,
                    ),
        ));
    }

    /**
     * Get user information by email
     * 
     * @param String $email User email
     * @return mixed
     */
    public function getByEmail($email)
    {
        return $this->find('first', array(
                    'conditions' => array(
                        'email' => $email,
                    ),
        ));
    }

    /**
     * BindModel hasMany
     * 
     * @param string $model Model name
     * @param array $options Setting more attribute, example: 'order' => 'Model.created DESC'
     */
    private function __bindModelHasMany($model, $options = null)
    {
        $this->bindModel(array(
            'hasMany' => array(
                $model => array(
                    'className' => $model,
                    $options,
                ),
            ),
        ));
    }

    /**
     * Bind wallet model
     */
    public function bindWallet()
    {
        $this->__bindModelHasMany('Wallet');
    }

    /**
     * update user info by id
     * 
     * @param int id User id
     * @param array $data User's data
     * @return boolean
     */
    public function updateById($id, $data)
    {
        $this->id = $id;
        return $this->save($data);
    }

    /**
     * compare current password with user's password -> use for change password
     * 
     * @return boolean
     */
    public function checkPassword()
    {
        $user = $this->getById(AuthComponent::user('id'));

        if (empty($user['User'])) {
            return false;
        }

        // Get hash of input password
        $passwordHasher = new SimplePasswordHasher(array('hashType' => 'sha256'));
        $pwdHash        = $passwordHasher->hash($this->data[$this->alias]['current_pw']);

        // Compare above hash with user's password
        return $pwdHash == $user['User']['password'];
    }

    /**
     * check password equal confirm password 
     * 
     * @return boolean
     */
    public function matchPassword()
    {
        return $this->data[$this->alias]['password'] == $this->data[$this->alias]['confirm_pw'];
    }

}
