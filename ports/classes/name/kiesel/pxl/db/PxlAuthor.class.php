<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 56677 2007-03-21 14:10:03Z rdoebele $
 */
 
  uses('rdbms.DataSet');
 
  /**
   * Class wrapper for table author, database main
   * (Auto-generated on Sun, 06 May 2007 20:53:05 +0200 by Alex)
   *
   * @purpose  Datasource accessor
   */
  class PxlAuthor extends DataSet {
    public
      $author_id          = 0,
      $username           = '',
      $password           = '',
      $realname           = '',
      $email              = NULL;

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('author');
        $peer->setConnection('pxl');
        $peer->setIdentity('author_id');
        $peer->setPrimary(array('author_id'));
        $peer->setTypes(array(
          'author_id'           => array('%d', FieldType::INT, FALSE),
          'username'            => array('%s', FieldType::VARCHAR, FALSE),
          'password'            => array('%s', FieldType::VARCHAR, FALSE),
          'realname'            => array('%s', FieldType::VARCHAR, FALSE),
          'email'               => array('%s', FieldType::VARCHAR, TRUE)
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
     * Gets an instance of this object by index "i_author_username"
     * 
     * @param   string username
     * @return  name.kiesel.pxl.db.PxlAuthor entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByUsername($username) {
      $r= self::getPeer()->doSelect(new Criteria(array('username', $username, EQUAL)));
      return $r ? $r[0] : NULL;
    }

    /**
     * Gets an instance of this object by index "pk_author"
     * 
     * @param   int author_id
     * @return  name.kiesel.pxl.db.PxlAuthor entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByAuthor_id($author_id) {
      $r= self::getPeer()->doSelect(new Criteria(array('author_id', $author_id, EQUAL)));
      return $r ? $r[0] : NULL;
    }

    /**
     * Retrieves author_id
     *
     * @return  int
     */
    public function getAuthor_id() {
      return $this->author_id;
    }
      
    /**
     * Sets author_id
     *
     * @param   int author_id
     * @return  int the previous value
     */
    public function setAuthor_id($author_id) {
      return $this->_change('author_id', $author_id);
    }

    /**
     * Retrieves username
     *
     * @return  string
     */
    public function getUsername() {
      return $this->username;
    }
      
    /**
     * Sets username
     *
     * @param   string username
     * @return  string the previous value
     */
    public function setUsername($username) {
      return $this->_change('username', $username);
    }

    /**
     * Retrieves password
     *
     * @return  string
     */
    public function getPassword() {
      return $this->password;
    }
      
    /**
     * Sets password
     *
     * @param   string password
     * @return  string the previous value
     */
    public function setPassword($password) {
      return $this->_change('password', $password);
    }

    /**
     * Retrieves realname
     *
     * @return  string
     */
    public function getRealname() {
      return $this->realname;
    }
      
    /**
     * Sets realname
     *
     * @param   string realname
     * @return  string the previous value
     */
    public function setRealname($realname) {
      return $this->_change('realname', $realname);
    }

    /**
     * Retrieves email
     *
     * @return  string
     */
    public function getEmail() {
      return $this->email;
    }
      
    /**
     * Sets email
     *
     * @param   string email
     * @return  string the previous value
     */
    public function setEmail($email) {
      return $this->_change('email', $email);
    }
  }
?>