<?php
/* This class is part of the XP framework
 *
 * $Id: TestClass.class.php 9080 2007-01-03 11:56:46Z friebe $ 
 */

  namespace net::xp_framework::unittest::reflection;

  ::uses(
    'util.Date', 
    'util.log.Traceable', 
    'net.xp_framework.unittest.reflection.AbstractTestClass'
  );

  /**
   * Test class
   *
   * @see      xp://net.xp_framework.unittest.reflection.ReflectionTest
   * @purpose  Test class
   */
  #[@test('Annotation')]
  class TestClass extends AbstractTestClass implements util::log::Traceable {
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

    static function __static() {
      self::$initializerCalled= TRUE;
    }

    /**
     * Constructor
     *
     * @param   mixed in default NULL
     */
    public function __construct($in= NULL) {
      $this->date= new util::Date($in);
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
     */    
    public function setDate($date) {
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
     * @throws  lang.IllegalStateException
     */
    public function setTrace($cat) {
      throw new lang::IllegalStateException('Not debuggable yet');
    }
      
    /**
     * Retrieve map as a PHP hashmap
     *
     * @return  array<string, lang.Object>
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
     * @param   array<string, lang.Object> map
     */
    final public function setMap($map) {
      $this->map= $map;
    }

    /**
     * Create a new instance statically
     *
     * @param   array<string, lang.Object> map
     * @return  net.xp_framework.unittest.reflection.TestClass
     */
    public static function fromMap($map) {
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
      $c= lang::Collection::forClass('lang.Object');
      $c->addAll(array_values($this->map));
      return $c;
    }
  } 
?>
