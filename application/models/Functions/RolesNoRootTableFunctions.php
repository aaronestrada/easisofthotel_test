<?php

class Model_RolesNoRootTableFunctions extends Model_RolesNorootTable {

    public function getRolesNoRoot() {       
        $roleTableData = parent::getInstance()->findAll();
        if ($roleTableData !== false) {
            return $roleTableData;
        }
        return array();
    }

}
