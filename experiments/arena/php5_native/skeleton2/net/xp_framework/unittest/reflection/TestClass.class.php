<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('util.Date', 'util.log.Traceable');

  /**
   * Test class
   *
   * @see      xp://net.xp_framework.unittest.reflection.ReflectionTest
   * @purpose  Test class
   */
  #[@test('Annotation')]
  class TestClass extends Object implements Traceable {
    public
      #[@type('util.Date')]
      $date= NULL,
      $map = array();

    /**
     * Constructor
     *
     * @access  public
     * @param   mixed in default NULL
     */
    public function __construct($in= NULL) {
      $this->date= &new Date($in);
    }
    
    /**
     * Static initializer
     *
     * @model   static
     * @access  public
     */
    public function __static() {
      TestClass::initializerCalled(TRUE);
    }
    
    /**
     * Static variables simulation
     *
     * @model   static
     * @access  public
     * @param   bool value default NULL
     * @return  bool
     */
    public function initializerCalled($value= NULL) {
      static $called;
      if (NULL !== $value) $called= $value;
      return $called;
    }
    
    /**
     * Retrieve date
     *
     * @access  public
     * @return  &util.Date
     */    
    public function &getDate() {
      return $this->date;
    }

    /**
     * Set date
     *
     * @access  public
     * @param   &util.Date date
     */    
    public function setDate(&$date) {
      $this->date= &$date;
    }
    
    /**
     * Retrieve current date as UN*X timestamp
     *
     * @access  public
     * @return  int
     */
    #[@webmethod, @security(roles= array('admin', 'god'))]
    public function currentTimestamp() {
      return time();
    }
    
    /**
     * Set a trace for debugging
     *
     * @access  public
     * @param   &util.log.LogCategory cat
     * @throws  lang.IllegalStateException
     */
    public function setTrace(&$cat) {
      throw(new IllegalStateException('Not debuggable yet'));
    }

      
    /**
     * Retrieve map as a PHP hashmap
     *
     * @access  public
     * @return  array<string, &lang.Object>
     */
    public function getMap() {
      return $this->map;
    }
    
    /**
     * Retrieve values
     *
     * @access  public
     * @return  &lang.Collection<lang.Object>
     */
    public function &mapValues() {
      $c= &Collection::forClass('lang.Object');
      $c->addAll(array_values($this->map));
      return $c;
    }

  } 
?>
