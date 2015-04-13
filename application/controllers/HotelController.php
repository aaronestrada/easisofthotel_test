<?php

class HotelController extends ACL_Controller {

    public function init() {
        //configurazioni di permessi e accesso
        $this->accessControl = array(
            array(
                'actions' => array('add', 'savenew'),
                'permissions' => array('add_hotel')
            ),
            array(
                'actions' => array('edithotel'),
                'permissions' => array('edit_hotel')
            ),
            array(
                'actions' => array('addprice', 'savenewpricing'),
                'permissions' => array('add_price')
            ),
            array(
                'actions' => array('assignuser', 'assignnewuser', 'removeuser'),
                'permissions' => array('edit_price')
            ),
            array(
                'actions' => array('priceoverview', 'editprice'),
                'permissions' => array('add_user')
            ),
        );
    }

    /**
     * Mostra lista di hotel registrati
     */
    public function indexAction() {
        $authenticator = new Auth_Authenticator();
        $userRole = $authenticator->getUserRole();
        $hotelList = array();
        
        $permissions = $authenticator->getAuthenticatedPermissions();
        $showAddLink = $permissions->hasPermission('add_hotel');

        switch ($userRole) {
            case 'root':
                $hotelList = Model_HotelTable::getInstance()->findAll();
                break;
            case 'admin':
                $hotelList = Model_HotelTable::getInstance()->findBy('user_id', $authenticator->getUserId());
                break;
            case 'utente':
            default:
                $hotelListQuery = Model_HotelTable::getInstance()->createQuery('h')->
                        innerJoin('h.HotelUser hu')->
                        where('hu.user_id = ? AND h.status_id = 5', array($authenticator->getUserId()));
                $hotelList = $hotelListQuery->execute();
                break;
        }
        $this->view->hotelList = $hotelList;
        $this->view->showAddLink = $showAddLink;
    }

    /**
     * Mostra forma per aggiungere hotel
     */
    public function addAction() {
        $this->view->statusData = Model_StatusTableFunctions::getHotelStatusList();
        $this->view->actionEdit = false;
        $this->_helper->viewRenderer('overview');
    }

    /**
     * Azione chiamata per salvare data di hotel
     */
    public function savenewAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $objHotel = new Model_Hotel();
            foreach (array_keys($objHotel->getData()) as $hotelProp) {
                if ($hotelProp != 'user_id')
                    $objHotel->$hotelProp = $request->getParam($hotelProp);
            }

            $authenticator = new Auth_Authenticator();
            $createdUserId = $authenticator->getUserId();
            $objHotel->user_id = $createdUserId;

            if ($objHotel->isValid(true)) {
                $objHotel->save();
                if ($objHotel->id != '') {
                    $this->redirect('/hotel/overview/id/' . $objHotel->id);
                    exit;
                }
            }
        }
        $this->redirect('/hotel/add');
    }

    /**
     * Mostra forma con data di hotel già registrato
     * @throws Zend_Controller_Action_Exception
     */
    public function overviewAction() {
        $request = $this->getRequest();
        $hotelId = $request->getParam('id');
        $hotelData = false;

        list($hotelData, $userRole, $showHotel) = $this->hasAccess($hotelId);

        //Nel caso che hotel non esista, mostra eccezione 404
        if (!$showHotel) {
            throw new Zend_Controller_Action_Exception('This page does not exist', 404);
        } else {
            $overviewView = in_array($userRole, array('admin', 'root')) ? 'overview' : 'overview-noedit';
            $this->view->statusData = Model_StatusTableFunctions::getHotelStatusList();
            $hotelPriceData = Model_HotelPriceTable::getInstance()->findBy('hotel_id', $hotelId);
            $hotelUserData = Model_HotelUserTable::getInstance()->findBy('hotel_id', $hotelId);

            $this->view->hotelData = $hotelData;            
            $this->view->hotelPriceList = $hotelPriceData;            
            $this->view->hotelUserList = $hotelUserData;
            $this->view->actionEdit = true;
            $this->_helper->viewRenderer($overviewView);
        }
    }

    /**
     * Azione chiamata per modificare data generale di hotel
     * @throws Zend_Controller_Action_Exception
     */
    public function edithotelAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);

        $request = $this->getRequest();
        $objHotel = false;

        //valida chiamata POST
        if ($request->isPost()) {
            $hotelId = $request->getParam('id');
            list($objHotel, $userRole, $hasHotelAccess) = $this->hasAccess($hotelId);

            if ($hasHotelAccess) {
                foreach (array_keys($objHotel->getData()) as $hotelProp) {
                    if (!in_array($hotelProp, array('id', 'user_id')))
                        $objHotel->$hotelProp = $request->getParam($hotelProp);
                }
                if ($objHotel->isValid(true)) {
                    $objHotel->save();
                    $this->redirect('/hotel/overview/id/' . $objHotel->id);
                }
            }
        }

        //Nel caso che hotel non esista, mostra eccezione 404
        if (!$objHotel) {
            throw new Zend_Controller_Action_Exception('This page does not exist', 404);
        }
    }

    /**
     * Verifica se un utente ha accesso a vedere un hotel
     * @param integer $hotelId ID di hotel
     * @return array (userRole, hasAccess)
     */
    private function hasAccess($hotelId) {
        $hasAccess = false;
        $hotelData = false;
        $authenticator = new Auth_Authenticator();
        $userRole = $authenticator->getUserRole();
        $userId = $authenticator->getUserId();

        if (is_numeric($hotelId)) {
            $hotelData = Model_HotelTable::getInstance()->find($hotelId);
            if ($hotelData !== false) {
                switch ($userRole) {
                    case 'root':
                        $hasAccess = true;
                        break;
                    case 'admin':
                        $hasAccess = ($hotelData->user_id == $userId);
                        break;
                    case 'utente':
                    default:
                        $hotelUserTableQuery = Model_HotelUserTable::getInstance()->createQuery()->
                                where('hotel_id = ? AND user_id = ?', array($hotelData->id, $userId));
                        $hotelUserData = $hotelUserTableQuery->execute();
                        $hasAccess = ($hotelUserData->count() > 0) && ($hotelData->status_id == 5);
                        break;
                } //switch
            }
        }
        return array($hotelData, $userRole, $hasAccess);
    }

    /**
     * Mostra forma per aggiungere data a prezzario di hotel
     */
    public function addpriceAction() {
        $request = $this->getRequest();
        $hotelId = $request->getParam('hotelid');
        list($hotelData, $userRole, $showHotel) = $this->hasAccess($hotelId);

        if ($showHotel) {
            $this->view->hotelData = $hotelData; 
            $this->_helper->viewRenderer('price-overview');
            $this->view->actionEdit = false;
        } else {
            throw new Zend_Controller_Action_Exception('This page does not exist', 404);
        }
    }

    /**
     * Azione per salvare nuovo prezio per un hotel
     */
    public function savenewpricingAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $hotelId = $request->getParam('hotel_id');
            list($hotelData, $userRole, $hasAccessHotel) = $this->hasAccess($hotelId);
            if ($hasAccessHotel) {
                $objHotelPrice = new Model_HotelPrice();
                foreach (array_keys($objHotelPrice->getData()) as $hotelPriceProp) {
                    $objHotelPrice->$hotelPriceProp = $request->getParam($hotelPriceProp);
                }

                if ($objHotelPrice->isValid()) {
                    $objHotelPrice->save();
                    if ($objHotelPrice->id != '') {
                        $this->redirect('/hotel/priceoverview/id/' . $objHotelPrice->id);
                        exit;
                    }
                }
            }
        }
        $this->redirect('/hotel');
    }

    /**
     * Mostra forma con data di prezzo di hotel già registrato
     * @throws Zend_Controller_Action_Exception
     */
    public function priceoverviewAction() {
        $request = $this->getRequest();
        $priceData = false;
        $priceId = $request->getParam('id');

        if (is_numeric($priceId)) {
            $priceData = Model_HotelPriceTable::getInstance()->find($priceId);
            if ($priceData !== false) {

                list($hotelData, $userRole, $showHotel) = $this->hasAccess($priceData->hotel_id);

                if ($showHotel) {
                    $this->view->priceData = $priceData;
                    $this->view->hotelData = $hotelData;                    
                    $this->view->actionEdit = true;
                    $this->_helper->viewRenderer('price-overview');
                }
            }
        }

        //Nel caso che hotel non esista, mostra eccezione 404
        if (!$priceData) {
            throw new Zend_Controller_Action_Exception('This page does not exist', 404);
        }
    }

    /**
     * Azione chiamata per modificare data generale di prezzo di hotel
     * @throws Zend_Controller_Action_Exception
     */
    public function editpriceAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);

        $request = $this->getRequest();
        $priceData = false;

        //valida chiamata POST
        if ($request->isPost()) {
            $priceId = $request->getParam('id');

            if (is_numeric($priceId)) {
                $priceData = Model_HotelPriceTable::getInstance()->find($priceId);
                if ($priceData !== false) {
                    list($objHotel, $userRole, $hasHotelAccess) = $this->hasAccess($priceData->hotel_id);

                    if ($hasHotelAccess) {
                        foreach (array_keys($priceData->getData()) as $hotelPriceProp) {
                            if ($hotelPriceProp != 'hotel_id')
                                $priceData->$hotelPriceProp = $request->getParam($hotelPriceProp);
                        }
                        if ($priceData->isValid(true)) {
                            $priceData->save();
                            $this->redirect('/hotel/priceoverview/id/' . $priceData->id);
                        }
                    }
                }
            }
        }

        //Nel caso che hotel non esista, mostra eccezione 404
        if (!$priceData) {
            throw new Zend_Controller_Action_Exception('This page does not exist', 404);
        }
    }

    /**
     * Azione chiamata per modificare data generale di prezzo di hotel
     * @throws Zend_Controller_Action_Exception
     */
    public function deletepriceAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);

        $request = $this->getRequest();
        $priceData = false;

        $priceId = $request->getParam('id');
        if (is_numeric($priceId)) {
            $priceData = Model_HotelPriceTable::getInstance()->find($priceId);
            if ($priceData !== false) {
                $hotelId = $priceData->hotel_id;
                list($objHotel, $userRole, $hasHotelAccess) = $this->hasAccess($hotelId);
                if ($hasHotelAccess) {
                    $priceData->delete();
                    $this->redirect('/hotel/overview/id/' . $hotelId);
                }
            }
        }

        //Nel caso che hotel non esista, mostra eccezione 404
        if (!$priceData) {
            throw new Zend_Controller_Action_Exception('This page does not exist', 404);
        }
    }

    /**
     * Azione per mostrare forma d'assegnazione di utente per un hotel
     * Nella lista si mostrano soltanto gli utenti con il ruolo "utente"
     * @throws Zend_Controller_Action_Exception
     */
    public function assignuserAction() {
        $request = $this->getRequest();
        $hotelId = $request->getParam('hotelid');
        list($hotelData, $userRole, $showHotel) = $this->hasAccess($hotelId);

        if ($showHotel) {
            //richiesta di utenti assegnati a un hotel
            $hotelUserList = array();
            $hotelUserTableData = Model_HotelUserTable::getInstance()->findBy('hotel_id', $hotelData->id);
            foreach ($hotelUserTableData as $hotelUserItem) {
                array_push($hotelUserList, $hotelUserItem->user_id);
            }

            //lista di utenti che non appartengono al hotel
            $userQuery = Model_UserTable::getInstance()->createQuery('u')->
                    whereNotIn('u.id', $hotelUserList)->
                    andWhere('u.role_id = 3');

            $userListData = $userQuery->execute();
            $this->view->hotelData = $hotelData;            
            $this->view->userListData = $userListData;
            $this->_helper->viewRenderer('user-overview');
        } else {
            throw new Zend_Controller_Action_Exception('This page does not exist', 404);
        }
    }

    /**
     * Azione per assegnare un nuovo utente per un hotel
     */
    public function assignnewuserAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $hotelId = $request->getParam('hotel_id');
            list($hotelData, $userRole, $hasAccessHotel) = $this->hasAccess($hotelId);
            if ($hasAccessHotel) {
                $objHotelUser = new Model_HotelUser();
                $objHotelUser->user_id = $request->getParam('user_id');
                $objHotelUser->hotel_id = $hotelId;
                if ($objHotelUser->isValid()) {
                    $objHotelUser->save();
                    if ($hotelId != '') {
                        $this->redirect('/hotel/assignuser/hotelid/' . $hotelId);
                        exit;
                    }
                }
            }
        }
        $this->redirect('/hotel');
    }

    /**
     * Azione chiamata per cancellare l'assegna utente / hotel
     * @throws Zend_Controller_Action_Exception
     */
    public function removeuserAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);

        $request = $this->getRequest();
        $hotelUserData = false;

        $hotelId = $request->getParam('hotelid');
        $userId = $request->getParam('userid');

        list($objHotel, $userRole, $hasHotelAccess) = $this->hasAccess($hotelId);
        if ($hasHotelAccess) {
            if (is_numeric($userId)) {
                $hotelUserData = Model_HotelUserTable::getInstance()->createQuery('hu')->
                                where('hotel_id = ? AND user_id = ?', array($hotelId, $userId))->execute();
                if ($hotelUserData !== false) {
                    foreach($hotelUserData as $hotelUserItem) {
                        $hotelUserItem->delete();
                    }
                    $this->redirect('/hotel/overview/id/' . $hotelId);
                }
            }
        }       

        //Nel caso che hotel non esista, mostra eccezione 404
        if (!$hotelUserData) {
            throw new Zend_Controller_Action_Exception('This page does not exist', 404);
        }
    }

}

?>
