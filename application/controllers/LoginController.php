<?php

/**
 * Description of LoginController
 *
 * @author aarone
 */
class LoginController extends Zend_Controller_Action {

    public function indexAction() {
        $this->_helper->layout->setLayout('login');
        $this->_helper->viewRenderer->setNoRender(TRUE);
        
        $authenticator = new Auth_Authenticator();
        if ($authenticator->hasIdentity()) {
            $this->redirect('/');
        }
    }

    public function getinAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        $request = $this->getRequest();
        if ($request->isPost()) {

            //gestione di accesso di utente
            $requestValues = $request->getPost();
            $authenticator = new Auth_Authenticator($requestValues['username'], $requestValues['password']);
            $result = $authenticator->authenticate();

            if ($result->isValid()) {
                //utente autenticato                                
                $redirectUrl = $authenticator->getRedirectURL();
                $authenticator->clearRedirect();

                $this->redirect($redirectUrl);
            }
        }
        $this->redirect('login/index');
    }

    public function logoutAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        $authenticator = new Auth_Authenticator();
        $authenticator->logout();
        $this->redirect('login/index');
    }

}
