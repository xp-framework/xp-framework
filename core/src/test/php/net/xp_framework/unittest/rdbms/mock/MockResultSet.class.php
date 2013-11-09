<?php namespace net\xp_framework\unittest\rdbms\mock;

/**
 * Result set Mock Object
 */
class MockResultSet extends \rdbms\ResultSet {
  protected $offset= 0;
  protected $data= array();

  /**
   * Constructor
   *
   * @param   var[] data
   */
  public function __construct($data= array()) {
    $s= sizeof($data);
    parent::__construct($s, $s ? array_keys($data[0]) : array());
    $this->data= $data;
  }

  /**
   * Seek
   *
   * @param   int $offset
   * @return  bool success
   * @throws  rdbms.SQLException
   */
  public function seek($offset) {
    if ($offset < 0 || $offset >= sizeof($this->data)) {
      throw new \rdbms\SQLException('Seek to position #'.$offset.' failed');
    }
    $this->offset= $offset;
    return true;
  }
  
  /**
   * Iterator function. Returns a rowset if called without parameter,
   * the fields contents if a field is specified or FALSE to indicate
   * no more rows are available.
   *
   * @param   string $field default NULL
   * @return  var
   */
  public function next($field= null) {
    if ($this->offset >= sizeof($this->data)) return false;
    $this->offset++;
    
    if ($field) {
      return $this->data[$this->offset- 1][$field];
    }
    return $this->data[$this->offset- 1];
  }
  
  /**
   * Close resultset and free result memory
   *
   * @return  bool success
   */
  public function close() {
    return true;
  }  
}
