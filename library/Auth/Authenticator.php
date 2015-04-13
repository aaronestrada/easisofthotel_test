<?php

/**
 * Descrizione di Authenticator
 * @author aarone
 */
class Auth_Authenticator {

    private $authenticator;
    private $adapter;
    private $session;

    public function __construct($username = '', $password = '') {
        $this->authenticator = Zend_Auth::getInstance();
        $this->adapter = new Auth_Adapter($username, $password);
        $this->session = new Zend_Session_Namespace();
    }

    public function authenticate() {
        return $this->authenticator->authenticate($this->adapter);
    }

    public function setRedirectURL($value) {
        $this->session->redirect = $value;
    }

    public function getRedirectURL() {
        if ($this->session->redirect !== '') {
            return $this->session->redirect;
        }
        return '/';
    }

    public function clearRedirect() {
        unset($this->session->redirect);
    }

    public function logout() {
        $this->authenticator->clearIdentity();
        $this->clearRedirect();
    }

    public function hasIdentity() {
        return $this->authenticator->hasIdentity();
    }

    private function getIdentityValue($value) {
        if($this->hasIdentity()) {
            $identity = $this->authenticator->getIdentity();
            if (isset($identity[$value]))
                return $identity[$value];
        }
        return null;
    }
    
    public function getUserId() {        
        return $this->getIdentityValue('user_id');
    }
    
    public function getUserName() {        
        return $this->getIdentityValue('name') . ' ' . $this->getIdentityValue('lastname');
    }
    
    public function getAuthenticatedPermissions() {
        return $this->getIdentityValue('rolePermissions');        
    }
    
    public function getUserRole() {
        $acl = $this->getAuthenticatedPermissions();
        return $acl->getRole();
    }    

}
