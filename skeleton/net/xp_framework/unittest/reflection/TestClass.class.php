<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('util.Date');

  /**
   * Test class
   *
   * @see      xp://net.xp-framework.unittest.reflection.XPClassTest
   * @purpose  Test class
   */
  #[@test('Annotation')]
  class TestClass extends Object {
    var
      #[@type('util.Date')]
      $date= NULL;

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

  } implements(__FILE__, 'util.log.Traceable');
?>
