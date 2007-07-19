<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses('rdbms.DataSet', 'util.HashmapIterator');

  /**
   * Class wrapper for table prozent_encode_test, database Ruben_Test_PS
   * (Auto-generated on Thu, 19 Jul 2007 12:49:17 +0200 by ruben)
   *
   * @purpose  Datasource accessor
   */
  class Prozent_encode_test extends DataSet {
    public
      $id                 = 0,
      $userload           = '';
  
    protected
      $cache= array(
      );

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('Ruben_Test_PS.prozent_encode_test');
        $peer->setConnection('localhost');
        $peer->setIdentity('id');
        $peer->setPrimary(array('id'));
        $peer->setTypes(array(
          'id'                  => array('%d', FieldType::INT, FALSE),
          'userload'            => array('%s', FieldType::VARCHAR, FALSE)
        ));
      }
    }  

    /**
     * Retrieve associated peer
     *
     * @return  rdbms.Peer
     */
    public static function getPeer() {
      return Peer::forName(__CLASS__);
    }

    /**
     * column factory
     *
     * @param   string name
     * @return  rdbms.Column
     * @throws  lang.IllegalArgumentException
     */
    public static function column($name) {
      return Peer::forName(__CLASS__)->column($name);
    }
  
    /**
     * Gets an instance of this object by index "PRIMARY"
     * 
     * @param   int id
     * @return  de.schlund.db.rubentest.Prozent_encode_test entity object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getById($id) {
      $r= self::getPeer()->doSelect(new Criteria(array('id', $id, EQUAL)));
      return $r ? $r[0] : NULL;
    }

    /**
     * Gets an instance of this object by index "userload"
     * 
     * @param   string userload
     * @return  de.schlund.db.rubentest.Prozent_encode_test[] entity objects
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByUserload($userload) {
      return self::getPeer()->doSelect(new Criteria(array('userload', $userload, EQUAL)));
    }

    /**
     * Retrieves id
     *
     * @return  int
     */
    public function getId() {
      return $this->id;
    }
      
    /**
     * Sets id
     *
     * @param   int id
     * @return  int the previous value
     */
    public function setId($id) {
      return $this->_change('id', $id);
    }

    /**
     * Retrieves userload
     *
     * @return  string
     */
    public function getUserload() {
      return $this->userload;
    }
      
    /**
     * Sets userload
     *
     * @param   string userload
     * @return  string the previous value
     */
    public function setUserload($userload) {
      return $this->_change('userload', $userload);
    }
  }
?>