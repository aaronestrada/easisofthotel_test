<?php

class IndexController extends Zend_Controller_Action {

    public function init() {
        $this->accessControl = array(
            array (
                'actions' => array('index2'),
                'permissions' => array('list_hotels')
            ),
            array (
                'actions' => array('index'),
                'permissions' => array('list_hotel')
            )
        );
    }

    public function indexAction() {
        print_r(Zend_Auth::getInstance()->getIdentity());
        $this->view->message = 'OK';
    }

}
