<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('rdbms.ConnectionManager');

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
      $_changed= array();
    
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
     * Changes a value by a specified key and returns the previous value.
     *
     * @access  protected
     * @param   string key
     * @param   &mixed value
     * @param   string type
     * @return  &mixed previous value
     */
    function &_change($key, &$value, $type) {
      if (!isset($this->_changed[$key])) {
        $this->_changed[$key]= array($type, &$this->{$key});
      }
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
      $sql= '';
      foreach (array_keys($this->_changed) as $key) {
        $sql.= $key.$db->prepare('= '.$this->_changed[$key][0], $this->{$key}).', ';
      }
      return substr($sql, 0, -2);
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
     * Insert this dataset (create a new row in the table).
     *
     * @access  public
     * @return  int affected rows
     * @throws  rdbms.SQLException
     */
    function insert() { }

    /**
     * Update this dataset (change an existing row in the table)
     *
     * @access  public
     * @return  int affected rows
     * @throws  rdbms.SQLException
     */
    function update() { }

    /**
     * Delete this dataset (remove the corresponding row from the table).
     *
     * @access  public
     * @return  int affected rows
     * @throws  rdbms.SQLException
     */
    function delete() { }
  }
?>
