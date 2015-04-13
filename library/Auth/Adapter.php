<?php

/**
 * Descrizione di AuthAdapter
 * Gestione e verificazione di utenti e contrasegna con data banchi
 * @author aarone
 */
class Auth_Adapter implements Zend_Auth_Adapter_Interface {

    private $username;
    private $password;

    public function __construct($username, $password) {
        $this->username = trim(strtolower($username));
        $this->password = $password;
    }

    /**
     * Verifica utente e contraseña e ritorna risultato corrispondente
     * @return \Zend_Auth_Result
     */
    public function authenticate() {
        $authValue = Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID;
        $userData = array();

        //controllare utente e contrasegna vuote
        if (($this->username !== '') && ($this->password !== '')) {
            
            //cercare utenti attivi (status_id = 2)
            $userQuery = Model_UserTable::getInstance()
                    ->createQuery()
                    ->where('username = ? AND pwd = ? AND status_id = 2', array($this->username, MD5($this->password)));
            $userListData = $userQuery->execute();
            
            //nel caso di averlo trovato, ritornare Ok
            if ($userListData->count() == 1) {                 
                $authValue = Zend_Auth_Result::SUCCESS;

                foreach ($userListData as $userItem) {
                    $userData = array(
                        'user_id' => $userItem->id,
                        'name' => $userItem->name,
                        'lastname' => $userItem->lastname,
                        'role_id' => $userItem->role_id                        
                    );
                }

                //permessi di utente 
                $rolePermissions = new ACL_RolePermissions($userData['role_id']);                
                $userData['rolePermissions'] = $rolePermissions;                
            }
        }

        return new Zend_Auth_Result($authValue, $userData);
    }

}
