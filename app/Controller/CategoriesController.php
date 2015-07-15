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
    );

    /**
     * default function => redirect to listCategories
     */
    public function index()
    {
        return $this->redirect(array(
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

        $this->Category->set($this->request->data);
        if ($this->Category->validates()) {

            //process icon upload
            $catIcon = '/img/building.png';
            if ($this->request->data['Category']['icon']['size'] > 0) {
                $catIcon = $this->_processUploadImage('uploads/', $this->request->data['Category']['icon']);
            }
            $this->request->data['Category']['icon'] = $catIcon;

            if ($this->Category->save($this->request->data)) {
                $this->Session->setFlash("Add new category complete.");
                return $this->redirect(array(
                            'controller' => 'categories',
                            'action'     => 'listCategories',
                ));
            }
        }
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
        if (empty($id)) {
            return $this->redirect(array(
                        'controller' => 'users',
                        'action'     => 'index',
            ));
        }

        $catObj = $this->Category->findById($id);
        if (empty($catObj)) {
            return $this->redirect(array(
                        'controller' => 'users',
                        'action'     => 'index',
            ));
        }

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
        $this->request->data['Category']['icon'] = $catIcon;

        $this->Category->set($this->request->data);
        if ($this->Category->validates()) {

            $this->Category->id = $id;
            if ($this->Category->save($this->request->data)) {

                $this->Session->setFlash("Update Category information complete.");
                return $this->redirect(array(
                            'controller' => 'categories',
                            'action'     => 'listCategories',
                ));
            }
        }
    }

    /**
     * delete category by id
     * 
     * @param int $id Category id
     */
    public function delete($id)
    {
        $this->autoRender = false;

        if (empty($id)) {
            return $this->redirect(array(
                        'controller' => 'users',
                        'action'     => 'index',
            ));
        }

        $catObj = $this->Category->findById($id);
        if (empty($catObj)) {
            return $this->redirect(array(
                        'controller' => 'users',
                        'action'     => 'index',
            ));
        }

        $this->Category->delete($id);
        $this->Transaction->deleteTransactionsByCategoryId($id);

        return $this->redirect(array(
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
            return $this->Session->setFlash("Have error. Please try again.");
        }
        return '/' . $rootFolder . $fileObj['name'];
    }

}
