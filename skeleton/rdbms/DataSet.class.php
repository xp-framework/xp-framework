<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('rdbms.ConnectionManager', 'rdbms.Criteria');

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
   *   try(); {
   *     $news= &News::getByNewsId($id);
   *   } if (catch('SQLException', $e)) {
   *     $e->printStackTrace();
   *     exit(-1);
   *   }
   *
   *   echo $news->toString();
   * </code>
   *
   * 2) Create a new entry
   * <code>
   *   with ($n= &new News()); {
   *     $n->setCategoryId($cat);
   *     $n->setTitle('Welcome');
   *     $n->setBody(NULL);
   *     $n->setAuthor('Timm');
   *     $n->setCreatedAt(Date::now());
   *
   *     try(); {
   *       $n->insert();
   *     } if (catch('SQLException', $e)) {
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
   *   try(); {
   *     if ($news= &News::getByNewsId($id)) {
   *       $news->setCaption('Good news, everyone!');
   *       $news->setAuthor('Hubert Farnsworth');
   *       $news->update();
   *     }
   *   } if (catch('SQLException', $e)) {
   *     $e->printStackTrace();
   *     exit(-1);
   *   }
   *
   *   echo $news->toString();
   * </code>
   *
   * Note: Fields are created as public members (aka "implicit public").
   *
   * @see      xp://rdbms.ConnectionManager
   * @purpose  Base class
   */
  class DataSet extends Object {
    var
      $_changed     = array();
    
    /**
     * Constructor. Supports the array syntax, where an associative
     * array is passed to the constructor, the keys being the member
     * variables and the values the member's values.
     *
     * @access  public
     * @param   array params default NULL
     */
    function __construct($params= NULL) {
      if (is_array($params)) {
        foreach (array_keys($params) as $key) $this->$key= &$params[$key];
      }
    }

    /**
     * Dataset registry
     *
     * @model   static
     * @access  public
     * @param   string key
     * @param   mixed val default NULL
     * @return  mixed
     */
    function registry($key, $val= NULL) {
      static $registry= array();
      
      if (NULL === $val) return $registry[$key]; else $registry[$key]= $val;
    }
    
    /**
     * Changes a value by a specified key and returns the previous value.
     *
     * @access  protected
     * @param   string key
     * @param   &mixed value
     * @return  &mixed previous value
     */
    function &_change($key, &$value) {
      $this->_changed[$key]= TRUE;
      $previous= &$this->{$key};
      $this->{$key}= &$value;
      return $previous;
    }
    
    /**
     * Returns a portion of the SQL query suitable for copying into an 
     * update statement.
     *
     * @access  protected
     * @param   &rdbms.DBConnection db
     * @return  string sql
     */
    function _updated(&$db) {
      $types= DataSet::registry(get_class($this).'.types');
      $sql= '';
      foreach (array_keys($this->_changed) as $key) {
        $sql.= $key.$db->prepare('= '.$types[$key], $this->{$key}).', ';
      }
      return substr($sql, 0, -2);
    }

    /**
     * Returns a portion of the SQL query suitable for copying into an 
     * insert statement.
     *
     * @access  protected
     * @param   &rdbms.DBConnection db
     * @return  string sql
     */
    function _inserted(&$db) {
      $types= DataSet::registry(get_class($this).'.types');
      $sql= implode(', ', array_keys($this->_changed)).') values (';
      foreach (array_keys($this->_changed) as $key) {
        $sql.= $db->prepare($types[$key], $this->{$key}).', ';
      }
      return substr($sql, 0, -2);
    }

    /**
     * Returns the conditional portion of the SQL query (everything after the
     * WHERE keyword) based on criteria given.
     *
     * @model   static
     * @access  protected
     * @param   &rdbms.DBConnection db
     * @param   &rdbms.Criteria c
     * @return  string sql
     * @throws  rdbms.SQLStateException
     */
    function criteria(&$db, &$c, $class= NULL) {
      $types= DataSet::registry(($class ? $class : get_class($this)).'.types');
      $sql= '';
      
      // 1: Process conditions
      if (!empty($c->conditions)) {
        $sql.= ' where ';
        foreach ($c->conditions as $condition) {
          if (!isset($types[$condition[0]])) {
            return throw(new SQLStateException('Field "'.$condition[0].'" unknown'));
          }
          $sql.= $condition[0].' '.$db->prepare(
            str_replace('?', $types[$condition[0]], $condition[2]).' and ', 
            $condition[1]
          );
        }
        $sql= substr($sql, 0, -4);
      }

      // 2: Process order by
      if (!empty($c->orderings)) {
        $sql.= ' order by ';
        foreach ($c->orderings as $order) {
          if (!isset($types[$order[0]])) {
            return throw(new SQLStateException('Field "'.$order[0].'" unknown'));
          }
          $sql.= $order[0].' '.$order[1].', ';
        }
        $sql= substr($sql, 0, -2);
      }
      
      return $sql;
    }

    /**
     * Creates a string representation of this dataset. In this default
     * implementation, it will look like the following:
     *
     * <pre>
     *   de.thekid.db.News(0.86699200 1086297326)@{
     *     [newsId              ] 76288
     *     [categoryId          ] 12
     *     [caption             ] 'Hello'
     *     [body                ] NULL
     *     [author              ] 'Timm'
     *     [createdAt           ] Thu,  3 Jun 2004 22:26:15 +0200
     *   }
     * </pre>
     *
     * Note: Keys with a leading "_" will be omitted from the list, they
     * indicate "protected" members.
     *
     * @access  public
     * @return  string
     */
    function toString() {
      
      // Retrieve object variables and figure out the maximum length 
      // of a key which will be used for the key "column". The minimum
      // width of this column is 20 characters.
      $vars= get_object_vars($this);
      $max= 20;
      foreach (array_keys($vars) as $key) {
        $max= max($max, strlen($key));
      }
      $fmt= '  [%-'.$max.'s] %s';
      
      // Build string representation.
      $s= $this->getClassName().'@('.$this->hashCode()."){\n";
      foreach (array_keys($vars) as $key) {
        if ('_' == $key{0}) continue;

        $s.= sprintf($fmt, $key, is_a($this->$key, 'Object') 
          ? $this->$key->toString()
          : var_export($this->$key, 1)
        )."\n";
      }
      return $s.'}';
    }

    /**
     * Update this object in the database
     *
     * @model   final
     * @access  public
     * @param   &rdbms.Criteria criteria
     * @param   string class
     * @param   int max default 0
     * @return  rdbms.DataSet[]
     * @throws  rdbms.SQLException in case an error occurs
     */
    function doSelect(&$criteria, $class, $max= 0) {
      $cm= &ConnectionManager::getInstance();  
      try(); {
        $db= &$cm->getByHost(DataSet::registry($class.'.connection'), 0);
        $q= &$db->query(
          'select '.implode(', ', array_keys(DataSet::registry($class.'.types'))).
          ' from '.DataSet::registry($class.'.table').
          DataSet::criteria($db, $criteria, $class)
        );
      } if (catch('SQLException', $e)) {
        return throw($e);
      }
      
      $r= array();
      for ($i= 1; $record= $q->next(); $i++) {
        if ($max && $i > $max) break;
        $r[]= &new $class($record);
      }
      return $r;
    }

    /**
     * Inserts this object into the database
     *
     * @model   final
     * @access  public
     * @param   string identity default NULL the identity field's name
     * @return  int number of affected rows
     * @throws  rdbms.SQLException in case an error occurs
     */
    function doInsert($identity= NULL) {
      $cm= &ConnectionManager::getInstance();  
      try(); {
        $db= &$cm->getByHost(DataSet::registry(get_class($this).'.connection'), 0);
        $affected= $db->insert(
          ' into '.DataSet::registry(get_class($this).'.table').
          $this->_inserted($db)
        );
        $identity && $this->{$identity}= $db->identity();
      } if (catch('SQLException', $e)) {
        return throw($e);
      }

      return $affected;
    }

    /**
     * Update this object in the database by specified criteria
     *
     * @model   final
     * @access  public
     * @param   &rdbms.Criteria criteria
     * @return  int number of affected rows
     * @throws  rdbms.SQLException in case an error occurs
     */
    function doUpdate(&$criteria) {
      $cm= &ConnectionManager::getInstance();  
      try(); {
        $db= &$cm->getByHost(DataSet::registry(get_class($this).'.connection'), 0);
        $affected= $db->update(
          DataSet::registry(get_class($this).'.table').
          ' set '.$this->_updated($db).
          DataSet::criteria($db, $criteria)
        );
      } if (catch('SQLException', $e)) {
        return throw($e);
      }

      return $affected;
    }

    /**
     * Update this object in the database by specified criteria
     *
     * @model   final
     * @access  public
     * @param   &rdbms.Criteria criteria
     * @return  int number of affected rows
     * @throws  rdbms.SQLException in case an error occurs
     */
    function doDelete(&$criteria) {
      $cm= &ConnectionManager::getInstance();  
      try(); {
        $db= &$cm->getByHost(DataSet::registry(get_class($this).'.connection'), 0);
        $affected= $db->delete(
          ' from '.DataSet::registry(get_class($this).'.table').
          ' set '.$this->_updated($db).
          DataSet::criteria($db, $criteria)
        );
      } if (catch('SQLException', $e)) {
        return throw($e);
      }

      return $affected;
    }
    
    /**
     * Insert this dataset (create a new row in the table). Does nothing
     * in this default implementation and may be overridden in subclasses 
     * where it makes sense.
     *
     * @access  public
     * @return  int affected rows
     * @throws  rdbms.SQLException
     */
    function insert() { }

    /**
     * Update this dataset (change an existing row in the table). Does 
     * nothing in this default implementation and may be overridden in 
     * subclasses where it makes sense.
     *
     * @access  public
     * @return  int affected rows
     * @throws  rdbms.SQLException
     */
    function update() { }

    /**
     * Delete this dataset (remove the corresponding row from the table). 
     * Does nothing in this default implementation and may be overridden 
     * in subclasses where it makes sense.
     *
     * @access  public
     * @return  int affected rows
     * @throws  rdbms.SQLException
     */
    function delete() { }
  }
?>
