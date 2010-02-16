<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'rdbms.ConnectionManager',
    'rdbms.Peer',
    'rdbms.Criteria',
    'rdbms.FieldType',
    'rdbms.join.JoinExtractable'
  );

  /**
   * A dataset represents a row of data selected from a database. Dataset 
   * classes usually provide getters and setters for every field in 
   * addition to insert(), update() and delete() methods and one or more 
   * static methods are provided to retrieve datasets from the database. 
   *
   * Note: All of these methods will rely on an instance of 
   * rdbms.ConnectionManager having been setup with a suitable connection. 
   * This way, there is no need to pass a connection instance to every
   * single method.
   *
   * For example, a table containing news  might provide a getByDate() 
   * method which returns an array of news objects and a getByNewsId() 
   * method returning one object.
   *
   * The basic ways to use the abovementioned example class would be:
   *
   * 1) Retrieve a news object
   * <code>
   *   try {
   *     $news= News::getByNewsId($id);
   *   } catch (SQLException $e) {
   *     $e->printStackTrace();
   *     exit(-1);
   *   }
   *
   *   echo $news->toString();
   * </code>
   *
   * 2) Create a new entry
   * <code>
   *   with ($n= new News()); {
   *     $n->setCategoryId($cat);
   *     $n->setTitle('Welcome');
   *     $n->setBody(NULL);
   *     $n->setAuthor('Timm');
   *     $n->setCreatedAt(Date::now());
   *
   *     try {
   *       $n->insert();
   *     } catch (SQLException $e) {
   *       $e->printStackTrace();
   *       exit(-1);
   *     }
   *
   *     echo $n->toString();
   *   }
   * </code>
   *
   * 3) Modify a news object
   * <code>
   *   try {
   *     with ($news= News::getByNewsId($id)); {
   *       $news->setCaption('Good news, everyone!');
   *       $news->setAuthor('Hubert Farnsworth');
   *       $news->update();
   *     }
   *   } catch (SQLException $e) {
   *     $e->printStackTrace();
   *     exit(-1);
   *   }
   *
   *   echo $news->toString();
   * </code>
   *
   * @test     xp://net.xp_framework.unittest.rdbms.DataSetTest
   * @see      xp://rdbms.Peer
   * @see      xp://rdbms.ConnectionManager
   * @purpose  Base class
   */
  abstract class DataSet extends Object implements JoinExtractable {
    public
      $_new         = TRUE,
      $_changed     = array();
    
    protected
      $cache= array(),
      $cached= array();

    /**
     * Constructor. Supports the array syntax, where an associative
     * array is passed to the constructor, the keys being the member
     * variables and the values the member's values.
     *
     * @param   array params default NULL
     */
    public function __construct($params= NULL) {
      if (is_array($params)) {
        foreach (array_keys($params) as $key) {
          $k= substr(strrchr('#'.$key, '#'), 1);
          $this->{$k}= $params[$key];
        }
        $this->_new= FALSE;
      }
    }
    
    /**
     * Retrieve associated peer
     *
     * @return  rdbms.Peer
     */
    public static function getPeer() { }

    /**
     * implements JoinExtractable
     * add object to cache
     *
     * @param   string role name of relation
     * @param   string key unique ojbect key
     * @param   lang.object obj 
     */
    public function setCachedObj($role, $key, $obj) {
      $this->cache[$role][$key]= $obj;
    }

    /**
     * implements JoinExtractable
     * get cached object
     *
     * @param   string role name of relation
     * @param   string key unique ojbect key
     * @return  lang.object obj 
     */
    public function getCachedObj($role, $key) {
      return $this->cache[$role][$key];
    }

    /**
     * implements JoinExtractable
     * test if obect with key ist cached
     *
     * @param   string role name of relation
     * @param   string key unique ojbect key
     * @return  bool
     */
    public function hasCachedObj($role, $key) {
      return isset($this->cache[$role][$key]);
    }

    /**
     * implements JoinExtractable
     * mark role as cached
     *
     * @param   string role name of relation
     * @return  bool
     */
    public function markAsCached($role) {
      $this->cached[$role]= TRUE;
    }
    
    /**
     * Changes a value by a specified key and returns the previous value.
     *
     * @param   string key
     * @param   var value
     * @return  var previous value
     */
    protected function _change($key, $value) {
      $this->_changed[$key]= $value;
      $previous= $this->{$key};
      $this->{$key}= $value;
      return $previous;
    }
    
    /**
     * Sets a field's value by the field's name and returns the previous value.
     *
     * @param   string field name
     * @param   var value
     * @return  var previous value
     * @throws  lang.IllegalArgumentException in case the field does not exist
     */
    public function set($field, $value) {
      if (!isset(Peer::forInstance($this)->types[$field])) {
        throw new IllegalArgumentException('Field "'.$field.'" does not exist for DataSet '.$this->getClassName());
      }
      return $this->_change($field, $value);
    }

    /**
     * Gets a field's value by the field's name
     *
     * @param   string field name
     * @throws  lang.IllegalArgumentException in case the field does not exist
     */
    public function get($field) {
      if (!isset(Peer::forInstance($this)->types[$field])) {
        throw new IllegalArgumentException('Field "'.$field.'" does not exist for DataSet '.$this->getClassName());
      }
      return $this->{$field};
    }
    
    /**
     * Returns an array of fields that were changed suitable for passing
     * to Peer::doInsert() and Peer::doUpdate()
     *
     * @return  array
     */
    public function changes() {
      return $this->_changed;
    }

    /**
     * Returns whether this record is new
     *
     * @return  bool
     */    
    public function isNew() {
      return $this->_new;
    }
    
    /**
     * Creates a string representation of this dataset. In this default
     * implementation, it will look like the following:
     *
     * <pre>
     *   de.thekid.db.News(0.86699200 1086297326)@{
     *     [newsId         PK,I] 76288
     *     [categoryId         ] 12
     *     [caption            ] 'Hello'
     *     [body               ] NULL
     *     [author             ] 'Timm'
     *     [createdAt          ] Thu,  3 Jun 2004 22:26:15 +0200
     *   }
     * </pre>
     *
     * Note: Keys with a leading "_" will be omitted from the list, they
     * indicate "protected" members.
     *
     * @return  string
     */
    public function toString() {
      $peer= $this->getPeer();
            
      // Retrieve types from peer and figure out the maximum length 
      // of a key which will be used for the key "column". The minimum
      // width of this column is 20 characters.
      $max= 0xF;
      foreach (array_keys($peer->types) as $key) {
        $max= max($max, strlen($key));
      }
      $fmt= '  [%-'.$max.'s %2s%2s] %s';
      
      // Build string representation.
      $s= $this->getClassName().'@('.$this->hashCode()."){\n";
      foreach (array_keys($peer->types) as $key) {
        $s.= sprintf(
          $fmt, 
          $key,
          (in_array($key, $peer->primary) ? 'PK' : ''), 
          ($key == $peer->identity ? ',I' : ''),
          xp::stringOf($this->$key)
        )."\n";
      }
      return $s.'}';
    }

    /**
     * Update this object in the database by specified criteria
     *
     * @return  var identity value if applicable, else NULL
     * @throws  rdbms.SQLException in case an error occurs
     */  
    public function doInsert() {
      $peer= $this->getPeer();
      if ($id= $peer->doInsert($this->_changed)) {
      
        // Set identity value if requested. We do not use the _change()
        // method here since the primary key is not supposed to appear
        // in the list of changed attributes
        $this->{$peer->identity}= $id;
      }
      $this->_changed= array();
      return $id;
    }
  
    /**
     * Update this object in the database by specified criteria
     *
     * @param   rdbms.SQLExpression criteria
     * @return  int number of affected rows
     * @throws  rdbms.SQLException in case an error occurs
     */  
    public function doUpdate(SQLExpression $criteria) {
      $affected= $this->getPeer()->doUpdate($this->_changed, $criteria);
      $this->_changed= array();
      return $affected;
    }

    /**
     * Delete this object from the database by specified criteria
     *
     * @param   rdbms.SQLExpression criteria
     * @return  int number of affected rows
     * @throws  rdbms.SQLException in case an error occurs
     */  
    public function doDelete(SQLExpression $criteria) {
      $affected= $this->getPeer()->doDelete($criteria);
      $this->_changed= array();
      return $affected;
    }
    
    /**
     * Insert this dataset (create a new row in the table).
     *
     * @return  var identity value if applicable, else NULL
     * @throws  rdbms.SQLException
     */
    public function insert() {
      $identity= $this->doInsert();
      $this->_new= FALSE;
      return $identity;
    }

    /**
     * Update this dataset (change an existing row in the table). 
     * Updates the record by using the primary key(s) as criteria.
     *
     * @return  int affected rows
     * @throws  rdbms.SQLException
     */
    public function update() {
      if (empty($this->_changed)) return 0;

      $peer= $this->getPeer();
      if (empty($peer->primary)) {
        throw new SQLStateException('No primary key for table '.$peer->getTable());
      }
      $criteria= new Criteria();
      foreach ($peer->primary as $key) {
        $criteria->add($key, $this->{$key}, EQUAL);
      }
      $affected= $peer->doUpdate($this->_changed, $criteria);
      $this->_changed= array();
      return $affected;
    }
    
    /**
     * Save this dataset. Inserts if this dataset is new (that means: Has been
     * created by new DataSetName()) and updates if it has been retrieved by
     * the database (by means of doSelect(), getBy...() or iterators).
     *
     * @return  var identity value if applicable, else NULL
     * @throws  rdbms.SQLException
     */
    public function save() {
      $peer= $this->getPeer();
      
      $this->_new ? $this->insert() : $this->update();
      return $peer->identity ? $this->{$peer->identity} : NULL;
    }

    /**
     * Delete this dataset (remove the corresponding row from the table). 
     * Does nothing in this default implementation and may be overridden 
     * in subclasses where it makes sense.
     *
     * @return  int affected rows
     * @throws  rdbms.SQLException
     */
    public function delete() { 
      $peer= $this->getPeer();
      if (empty($peer->primary)) {
        throw new SQLStateException('No primary key for table '.$peer->getTable());
      }
      $criteria= new Criteria();
      foreach ($peer->primary as $key) {
        $criteria->add($key, $this->{$key}, EQUAL);
      }
      $affected= $peer->doDelete($criteria);
      $this->_changed= array();
      return $affected;
    }
  }
?>
