<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'util.Date', 
    'util.log.Traceable', 
    'util.collections.HashTable',
    'net.xp_framework.unittest.reflection.AbstractTestClass'
  );

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
      $date   = NULL,
      $map    = array();
    
    protected
      $size   = 0;
    
    private
      $factor = 5;
    
    public static
      $initializerCalled= FALSE;
    
    private static
      $cache  = array();

    static function __static() {
      self::$initializerCalled= TRUE;
    }
    
    const
      CONSTANT_STRING = 'XP Framework',
      CONSTANT_INT    = 15,
      CONSTANT_NULL   = NULL;

    /**
     * Constructor
     *
     * @param   mixed in default NULL
     */
    public function __construct($in= NULL) {
      $this->date= new Date($in);
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
     * @throws  lang.IllegalArgumentException in case the given argument is of incorrect type
     * @throws  lang.IllegalStateException if date is before 1970
     */    
    public function setDate($date) {
      if (!$date instanceof Date) {
        throw new IllegalArgumentException('Given argument is not a util.Date');
      } else if ($date->getYear() < 1970) {
        throw new IllegalStateException('Date must be after 1970');
      }
      $this->date= $date;
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
     * @throws  lang.IllegalStateException *ALWAYS*
     */
    public function setTrace($cat) {
      throw new IllegalStateException('Not debuggable yet');
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
     *
     */
    protected function clearMap() {
      $this->map= array();
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
     * @return  lang.Collection<lang.Object>
     */
    public function mapValues() {
      $c= Collection::forClass('lang.Object');
      $c->addAll(array_values($this->map));
      return $c;
    }

    /**
     * Retrieve values filtered by a given pattern
     *
     * @param   string pattern default NULL
     * @return  lang.Collection<lang.Object>
     */
    public function filterMap($pattern= NULL) {
      // TBI
    }
  } 
?>
