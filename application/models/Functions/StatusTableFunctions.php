<?php

class Model_StatusTableFunctions extends Model_StatusTable {

    public function getUserStatusList() {
        $statusTableData = parent::getInstance()->findBy('parent_id', 1);
        if ($statusTableData !== false) {
            return $statusTableData;
        }
        return array();
    }
    
    public function getHotelStatusList() {
        $statusTableData = parent::getInstance()->findBy('parent_id', 4);
        if ($statusTableData !== false) {
            return $statusTableData;
        }
        return array();
    }

}
