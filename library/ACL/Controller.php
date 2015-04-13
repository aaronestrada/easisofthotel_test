<?php

/**
 * Descrizione di ACL_Controller
 *
 * @author aarone
 */
class ACL_Controller extends Zend_Controller_Action {

    public $accessControl = null;
    private $actionPermissionList;

    /**
     * Questo metodo verifica se un controlloro ha permesso di accedere
     * secondo la configurazione della variabile "accessControl" in ogni
     * controlloro di tipo ACL_Controller
     */
    public function preDispatch() {        
        $this->setPermissionList($this->accessControl);
        if (!$this->hasControllerAccess($this->getRequest()->getActionName())) {
            $this->_forward('forbidden', 'error');
        }
    }
    
    /**
     * Conversione della variabile $accessControl per ottenere ogni permesso associato
     * a ogni azione
     * @param array $accessControl
     */
    private function setPermissionList($accessControl) {
        $actionPermissionList = array();

        if (is_array($accessControl)) {
            foreach ($accessControl as $accessControlItem) {
                if (is_array($accessControlItem)) {
                    if (isset($accessControlItem['actions']) && (is_array($accessControlItem['actions']))) {
                        foreach ($accessControlItem['actions'] as $actionPermissionItem) {
                            if (!array_key_exists($actionPermissionItem, $actionPermissionList)) {
                                $actionPermissionList[$actionPermissionItem] = array();
                            }

                            if (isset($accessControlItem['permissions'])) {
                                foreach ($accessControlItem['permissions'] as $permissionItem) {
                                    if (!in_array($permissionItem, $actionPermissionList[$actionPermissionItem]))
                                        array_push($actionPermissionList[$actionPermissionItem], $permissionItem);
                                }
                            }
                        }
                    }
                }
            }
        }
        $this->actionPermissionList = $actionPermissionList;
    }

    /**
     * Verifica di accesso di un controlloro
     * @param type $controllerName
     * @return boolean
     */
    private function hasControllerAccess($controllerName) {
        $authenticator = new Auth_Authenticator();
        $rolePermissions = $authenticator->getAuthenticatedPermissions();
        
        $hasAccess = true;
        if(array_key_exists($controllerName, $this->actionPermissionList)) {            
            $controllerPermissions = $this->actionPermissionList[$controllerName];
            
            foreach($controllerPermissions as $permissionValue) {
                if(!$rolePermissions->hasPermission($permissionValue)) {
                    $hasAccess = false;
                }
            }
        }
        return $hasAccess;
    }

}