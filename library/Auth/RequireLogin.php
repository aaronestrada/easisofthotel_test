<?php

class Auth_RequireLogin extends Zend_Controller_Plugin_Abstract {

    /**
     * Questa funzione verifica che l'utente abbia sesione nella applicazione,
     * sennò mostra la pagina per iniziare sesione
     * @param Zend_Controller_Request_Abstract $request
     * @return empty in caso che l'utente abbia sessione
     */
    public function preDispatch(Zend_Controller_Request_Abstract $request) {
        $authenticator = new Auth_Authenticator();  
        
        $layout = Zend_Layout::getMvcInstance();
        $view = $layout->getView();
        $view->logged_username = $authenticator->getUserName();
        $view->permissions = $authenticator->getAuthenticatedPermissions();
        
        if ($authenticator->hasIdentity()) {    
            //cancelare URL salvata in sesione
            $authenticator->clearRedirect();
            return;
        } else {
            if (($request->getControllerName() == 'login') && (in_array($request->getActionName(), array('getin', 'index', 'logout'))))
                return;
        }
        
        //salvare redirect in sesione
        $url = $request->getRequestUri();
        $authenticator->setRedirectURL($url);        

        /**
         * Utente non ha accesso, mostrare pagina per iniziare sesione
         */
        $request->setModuleName('default')
                ->setControllerName('login')
                ->setActionName('index')
                ->setDispatched(false);
    }

}
