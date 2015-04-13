<?php

class UserController extends ACL_Controller {

    public function init() {
        //configurazioni di permessi e accesso
        $this->accessControl = array(
            array(
                'actions' => array('index'),
                'permissions' => array('list_users')
            ),
            array(
                'actions' => array('add', 'savenew'),
                'permissions' => array('add_user')
            ),
            array(
                'actions' => array('overview', 'edituser'),
                'permissions' => array('edit_user')
            ),
        );
    }

    /**
     * Mostra lista di utenti registrati
     */
    public function indexAction() {
        $userList = Model_UserTable::getInstance()->findAll();
        $this->view->userList = $userList;
    }

    /**
     * Mostra forma per aggiungere utente
     */
    public function addAction() {
        $this->view->roleData = Model_RolesNoRootTableFunctions::getRolesNoRoot();
        $this->view->statusData = Model_StatusTableFunctions::getUserStatusList();
        $this->view->actionEdit = false;
        $this->_helper->viewRenderer('overview');
    }

    /**
     * Azione chiamata per salvare data di utente
     */
    public function savenewAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);

        $request = $this->getRequest();
        if ($request->isPost()) {
            if (trim($request->getParam('pwd')) !== '') {
                $objUser = new Model_User();
                foreach (array_keys($objUser->getData()) as $userProp) {
                    $objUser->$userProp = ($userProp != 'pwd') ? $request->getParam($userProp) : md5($request->getParam($userProp));
                }

                if ($objUser->isValid(true)) {
                    $objUser->save();
                    if ($objUser->id != '') {
                        $this->redirect('/user/overview/id/' . $objUser->id);
                        exit;
                    }
                }
            }
        }
        $this->redirect('/user/add');
    }

    /**
     * Mostra forma con data di utente già registrato
     * @throws Zend_Controller_Action_Exception
     */
    public function overviewAction() {
        $auth = new Auth_Authenticator();
        $request = $this->getRequest();
        $userId = $request->getParam('id');
        $userData = false;

        if (is_numeric($userId)) {
            $userData = Model_UserTable::getInstance()->find($userId);
            if ($userData !== false) {                
                $this->view->roleData = Model_RolesNoRootTableFunctions::getRolesNoRoot();
                $this->view->statusData = Model_StatusTableFunctions::getUserStatusList();
                $this->view->userData = $userData;
                $this->view->actionEdit = true;
                $this->_helper->viewRenderer('overview');
            }
        }

        //Nel caso che l'utente non esista, mostra eccezione 404
        if (!$userData) {
            throw new Zend_Controller_Action_Exception('This page does not exist', 404);
        }
    }

    /**
     * Azione chiamata per modificare data di utente
     * @throws Zend_Controller_Action_Exception
     */
    public function edituserAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);

        $request = $this->getRequest();
        $objUser = false;

        //valida chiamata POST
        if ($request->isPost()) {
            $userId = $request->getParam('id');
            if (is_numeric($userId)) {
                $objUser = Model_UserTable::getInstance()->find($userId);
                if ($objUser !== false) {
                    foreach (array_keys($objUser->getData()) as $userProp) {
                        if (!in_array($userProp, array('id', 'pwd')))
                            $objUser->$userProp = $request->getParam($userProp);
                    }
                    if ($objUser->isValid(true)) {
                        $objUser->save();
                        $this->redirect('/user/overview/id/' . $objUser->id);
                    }
                }
            }
        }

        //Nel caso che l'utente non esista, mostra eccezione 404
        if (!$objUser) {
            throw new Zend_Controller_Action_Exception('This page does not exist', 404);
        }
    }

}

?>
