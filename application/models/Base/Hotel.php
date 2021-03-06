<?php

/**
 * Model_Base_Hotel
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property string $name
 * @property decimal $starnumber
 * @property integer $user_id
 * @property integer $status_id
 * @property Model_Status $Status
 * @property Model_User $User
 * @property Doctrine_Collection $HotelPrice
 * @property Doctrine_Collection $HotelUser
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class Model_Base_Hotel extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('hotel');
        $this->hasColumn('id', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => true,
             'primary' => true,
             'autoincrement' => true,
             ));
        $this->hasColumn('name', 'string', 100, array(
             'type' => 'string',
             'length' => 100,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('starnumber', 'decimal', 4, array(
             'type' => 'decimal',
             'length' => 4,
             'fixed' => false,
             'unsigned' => true,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             'scale' => '2',
             ));
        $this->hasColumn('user_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => true,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('status_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => true,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('Model_Status as Status', array(
             'local' => 'status_id',
             'foreign' => 'id'));

        $this->hasOne('Model_User as User', array(
             'local' => 'user_id',
             'foreign' => 'id'));

        $this->hasMany('Model_HotelPrice as HotelPrice', array(
             'local' => 'id',
             'foreign' => 'hotel_id'));

        $this->hasMany('Model_HotelUser as HotelUser', array(
             'local' => 'id',
             'foreign' => 'hotel_id'));
    }
}