<?php
/**
 * Description of ACL_RolePermissions
 *
 * @author aarone
 */
class ACL_RolePermissions {
    private $permissions;   
    private $role_internal_code;    

    public function __construct($roleId) {
        $permissionList = array();
        $roleRow = Model_RoleTable::getInstance()->find($roleId);        
        if ($roleRow !== false) {
            $this->role_internal_code = $roleRow->internal_code;
            foreach ($roleRow->RolePermission as $rolePermissionItem) {
                array_push($permissionList, $rolePermissionItem->Permission->internal_code);
            }            
            $this->permissions = $permissionList;
        }        
    }   

    /**
     * Ottenere ruolo di utente
     * @return string
     */
    public function getRole() {
        return $this->role_internal_code;
    }
    
    /**
     * Verifica permesso per fare qualsiasi azione nell'applicazione
     * @param string $permissionValue internal_code di permesso
     * @return bool Vero se utente ha il permesso
     */
    public function hasPermission($permissionValue) {
        return in_array($permissionValue, $this->permissions);
    }

}
