<?php

/**
 * Model_Base_Role
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property string $name
 * @property string $internal_code
 * @property Doctrine_Collection $RolePermission
 * @property Doctrine_Collection $User
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class Model_Base_Role extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('role');
        $this->hasColumn('id', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => true,
             'primary' => true,
             'autoincrement' => true,
             ));
        $this->hasColumn('name', 'string', 75, array(
             'type' => 'string',
             'length' => 75,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('internal_code', 'string', 50, array(
             'type' => 'string',
             'length' => 50,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasMany('Model_RolePermission as RolePermission', array(
             'local' => 'id',
             'foreign' => 'role_id'));

        $this->hasMany('Model_User as User', array(
             'local' => 'id',
             'foreign' => 'role_id'));
    }
}