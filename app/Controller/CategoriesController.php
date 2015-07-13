<?php

class CategoriesController extends AppController
{

    /**
     * uses
     * 
     * @var array 
     */
    public $uses = array('Category', 'Transaction');

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
     * default function => redirect to listCategories
     */
    public function index()
    {
        $this->redirect(array(
            'controller' => 'categories',
            'action'     => 'listCategories',
        ));
    }

    /**
     * create category info 
     * 
     * @return mixed
     */
    public function add()
    {
        $this->set('title_for_layout', 'Add Category');

        if ($this->request->is('get')) {
            return;
        }

        //check validations
        $this->Category->set($this->request->data);
        if ($this->Category->validates()) {

            //process icon upload
            $catIcon = '/img/building.png';
            if ($this->request->data['Category']['icon']['size'] > 0) {
                $catIcon = $this->_processUploadImage('uploads/', $this->request->data['Category']['icon']);
            }

            //category data want to save
            $catData = array(
                'Category' => array(
                    'name'         => $this->request->data['Category']['name'],
                    'icon'         => $catIcon,
                    'expense_type' => $this->request->data['Category']['expense_type'],
                    'wallet_id'    => AuthComponent::user('current_wallet')['id'],
                )
            );

            //save category info
            if ($this->Category->save($catData)) {
                $this->Session->setFlash("Add new category complete.");
                $this->redirect(array(
                    'controller' => 'categories',
                    'action'     => 'listCategories',
                ));
            }
        }
        return;
    }

    /**
     * show list categories
     */
    public function listCategories()
    {
        $this->set('listCategories', $this->Category->find('all', array(
                    'conditions' => array(
                        'Category.wallet_id' => array(0, AuthComponent::user('current_wallet')['id']),
                    )
        )));
    }

    /**
     * edit category information by id
     * 
     * @param int $id Category id
     */
    public function edit($id)
    {
        //process request from url
        if (empty($id)) {
            $this->redirect(array(
                'controller' => 'users',
                'action'     => 'index',
            ));
        }

        //check if category exists or not
        $catObj = $this->Category->findById($id);
        if (empty($catObj)) {
            $this->redirect(array(
                'controller' => 'users',
                'action'     => 'index',
            ));
        }

        //setup layout
        $this->set('title_for_layout', 'Edit Category');
        $this->set('catObj', $catObj);

        if ($this->request->is('get')) {
            return;
        }

        //process icon upload
        $catIcon = $catObj['Category']['icon'];
        if (!empty($this->request->data['Category']['icon']['size'] > 0)) {
            $catIcon = $this->_processUploadImage('uploads/', $this->request->data['Category']['icon']);
        }

        //check validations
        $this->Category->set($this->request->data);
        if ($this->Category->validates()) {
            if ($this->Category->updateAll(array(
                        'Category.name'         => '"' . $this->request->data['Category']['name'] . '"',
                        'Category.icon'         => '"' . $catIcon . '"',
                        'Category.expense_type' => '"' . $this->request->data['Category']['expense_type'] . '"'), array(
                        'Category.id' => $id
                    ))) {
                $this->Session->setFlash("Update Category information complete.");
                $this->redirect(array(
                    'controller' => 'categories',
                    'action'     => 'listCategories',
                ));
            }
        }
        return;
    }

    /**
     * delete category by id
     * 
     * @param int $id Category id
     */
    public function delete($id)
    {
        // this code is used for function without view
        $this->autoRender = false;

        //process request from url
        if (empty($id)) {
            $this->redirect(array(
                'controller' => 'users',
                'action'     => 'index',
            ));
        }

        //check category exists or not
        $catObj = $this->Category->findById($id);
        if (empty($catObj)) {
            $this->redirect(array(
                'controller' => 'users',
                'action'     => 'index',
            ));
        }

        //delete category
        $this->Category->delete($id);
        //delete all transactions have category_id equals $id
        $this->Transaction->deleteTransactionsByCategoryId($id);
        $this->redirect(array(
            'controller' => 'categories',
            'action'     => 'listCategories',
        ));
    }

    /**
     * process image file upload
     * 
     * @param type $rootFolder Folder contain file images
     * @param type $fileObj File image upload
     * @return string
     */
    private function _processUploadImage($rootFolder, $fileObj)
    {
        $target_dir  = WWW_ROOT . $rootFolder;
        $target_file = $target_dir . basename($fileObj["name"]);

        if (!move_uploaded_file($fileObj['tmp_name'], $target_file)) {
            $this->Session->setFlash("Have error. Please try again.");
            return;
        }
        return '/' . $rootFolder . $fileObj['name'];
    }

}
