<?php namespace net\xp_framework\unittest\reflection;

use util\Date;
use util\log\Traceable;
use util\collections\HashTable;
use util\collections\Vector;


/**
 * Test class
 *
 * @see      xp://net.xp_framework.unittest.reflection.ReflectionTest
 * @purpose  Test class
 */
#[@test('Annotation')]
  class TestClass extends AbstractTestClass implements Traceable {
  public
    #[@type('util.Date')]
    $date   = null,
    $map    = array();
  
  protected
    $size   = 0;
  
  private
    $factor = 5;
  
  public static
    $initializerCalled= false;
  
  private static
    $cache  = array();

  static function __static() {
    self::$initializerCalled= true;
  }
  
  const
    CONSTANT_STRING = 'XP Framework',
    CONSTANT_INT    = 15,
    CONSTANT_NULL   = null;

  /**
   * Constructor
   *
   * @param   mixed in default NULL
   */
  public function __construct($in= null) {
    $this->date= new Date($in);
  }

  /**
   * Checks whether this date is before another given test class' date
   *
   * @param   self test
   * @return  bool
   */
  public function isDateBefore(self $test) {
    return $this->date->isBefore($test->date);
  }
  
  /**
   * Returns whether static initializer was called
   *
   * @return  bool
   */
  public static function initializerCalled() {
    return self::$initializerCalled;
  }
  
  /**
   * Retrieve date
   *
   * @return  util.Date
   */    
  public function getDate() {
    return $this->date;
  }

  /**
   * Set date
   *
   * @param   util.Date date
   * @return  void
   * @throws  lang.IllegalArgumentException in case the given argument is of incorrect type
   * @throws  lang.IllegalStateException if date is before 1970
   */    
  public function setDate($date) {
    if (!$date instanceof Date) {
      throw new \lang\IllegalArgumentException('Given argument is not a util.Date');
    } else if ($date->getYear() < 1970) {
      throw new \lang\IllegalStateException('Date must be after 1970');
    }
    $this->date= $date;
  }

  public function notDocumented($param) {
  }

  /**
   * Set date
   *
   * @param   util.Date date
   * @return  self
   * @throws  lang.IllegalArgumentException in case the given argument is of incorrect type
   * @throws  lang.IllegalStateException if date is before 1970
   */
  public function withDate($date) {
    $this->setDate($date);
    return $this;
  }
  
  /**
   * Retrieve current date as UN*X timestamp
   *
   * @return  int
   */
  #[@webmethod, @security(roles= array('admin', 'god'))]
  public function currentTimestamp() {
    return time();
  }
  
  /**
   * Set a trace for debugging
   *
   * @param   util.log.LogCategory cat
   * @return  void
   * @throws  lang.IllegalStateException *ALWAYS*
   */
  public function setTrace($cat) {
    throw new \lang\IllegalStateException('Not debuggable yet');
  }
    
  /**
   * Retrieve map as a PHP hashmap
   *
   * @return  [:lang.Object]
   */
  public function getMap() {
    return $this->map;
  }
  
  /**
   * Clear map
   */
  protected function clearMap() {
    $this->map= array();
    return true;
  }

  /**
   * Initialite map to default values
   *
   */
  private function defaultMap() {
    $this->map= array(
      'binford' => 61
    );
  }

  /**
   * Initialize map to default values
   *
   * @param   [:lang.Object] map
   */
  final public function setMap($map) {
    $this->map= $map;
  }

  /**
   * Initialize map to default values
   *
   * @param   util.collections.HashTable h
   */
  public function fromHashTable(HashTable $h) {
    // TBI
  }

  /**
   * Create a new instance statically
   *
   * @param   [:lang.Object] map
   * @return  net.xp_framework.unittest.reflection.TestClass
   */
  public static function fromMap(array $map) {
    $self= new self();
    $self->setMap($map);
    return $self;
  }
  
  /**
   * Retrieve values
   *
   * @return  util.collections.Vector<lang.Object>
   */
  public function mapValues() {
    $c= create('new Vector<lang.Object>');
    $c->addAll(array_values($this->map));
    return $c;
  }

  /**
   * Retrieve values filtered by a given pattern
   *
   * @param   string pattern default NULL
   * @return  util.collections.Vector<lang.Object>
   */
  public function filterMap($pattern= null) {
    // TBI
  }
} 
