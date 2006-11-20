<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('util.Date');

  /**
   * Test class
   *
   * @see      xp://net.xp_framework.unittest.reflection.ReflectionTest
   * @purpose  Test class
   */
  #[@test('Annotation')]
  class TestClass extends Object {
    var
      #[@type('util.Date')]
      $date= NULL,
      $map = array();

    /**
     * Constructor
     *
     * @access  public
     * @param   mixed in default NULL
     */
    function __construct($in= NULL) {
      $this->date= &new Date($in);
    }
    
    /**
     * Static initializer
     *
     * @model   static
     * @access  public
     */
    function __static() {
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
    function initializerCalled($value= NULL) {
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
    function &getDate() {
      return $this->date;
    }

    /**
     * Set date
     *
     * @access  public
     * @param   &util.Date date
     */    
    function setDate(&$date) {
      $this->date= &$date;
    }
    
    /**
     * Retrieve current date as UN*X timestamp
     *
     * @access  public
     * @return  int
     */
    #[@webmethod, @security(roles= array('admin', 'god'))]
    function currentTimestamp() {
      return time();
    }
    
    /**
     * Set a trace for debugging
     *
     * @access  public
     * @param   &util.log.LogCategory cat
     * @throws  lang.IllegalStateException
     */
    function setTrace(&$cat) {
      return throw(new IllegalStateException('Not debuggable yet'));
    }

      
    /**
     * Retrieve map as a PHP hashmap
     *
     * @access  public
     * @return  array<string, &lang.Object>
     */
    function getMap() {
      return $this->map;
    }
    
    /**
     * Retrieve values
     *
     * @access  public
     * @return  &lang.Collection<lang.Object>
     */
    function &mapValues() {
      $c= &Collection::forClass('lang.Object');
      $c->addAll(array_values($this->map));
      return $c;
    }

  } implements(__FILE__, 'util.log.Traceable');
?>
