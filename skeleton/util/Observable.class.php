<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Observable - base class for Model/View/Controller architecture.
   *
   * A basic implementation might look like this:
   * <code>
   *   class ObservableValue extends Observable {
   *     var
   *       $n    = 0;
   *     
   *     function __construct($n) {
   *       $this->n= $n;
   *     }
   *     
   *     function setValue($n) {
   *       $this->n= $n;
   *       self::setChanged();
   *       self::notifyObservers();
   *     }
   *     
   *     function getValue() {
   *       return $this->n;
   *     }
   *   }
   *
   *   class TextObserver extends Object {
   *     function update(&$obs, $arg= NULL) {
   *       echo __CLASS__, ' was notified of update in value, is now ';
   *       var_dump($obs->getValue());
   *     }
   *   }
   * 
   *   $value= new ObservableValue(3);
   *   $value->addObserver(new TextObserver());
   *   $value->setValue(5);
   * </code>
   *
   * Note: Due to the restrictions of the Zend Engine 1, you can pass any
   * instance of a class containing a method named "update" to the addObserver()
   * method. It _is_ checked that such a method exists, though.
   *
   * The update method gets passed the instance of Observable as its first
   * argument and - if existant - the argument passed to notifyObservers as 
   * its second.
   *
   * @see      http://www.javaworld.com/javaworld/jw-10-1996/jw-10-howto.html
   * @purpose  Base class
   */
  class Observable extends Object {
    var
      $_obs      = array(),
      $_changed  = FALSE;
      
    /**
     * Add an observer
     *
     * @access  public
     * @param   &lang.Object observer
     * @throws  lang.IllegalArgumentException in case the argument is not an observer
     */
    function addObserver(&$observer) {
      if (!method_exists($observer, 'update')) {
        return throw(new IllegalArgumentException('Passed argument is not an observer'));
      }
      $this->_obs[]= &$observer;
    }
    
    /**
     * Notify observers
     *
     * @access  public
     * @param   mixed arg default NULL
     */
    function notifyObservers($arg= NULL) {
      if (!$this->hasChanged()) return;
      
      for ($i= 0, $s= sizeof($this->_obs); $i < $s; $i++) {
        $this->_obs[$i]->update($this, $arg);
      }
      
      $this->clearChanged();
    }
    
    /**
     * Sets changed flag
     *
     * @access  protected
     */
    function setChanged() {
      $this->_changed= TRUE;
    }

    /**
     * Clears changed flag
     *
     * @access  protected
     */
    function clearChanged() {
      $this->_changed= FALSE;
    }

    /**
     * Checks whether changed flag is set
     *
     * @access  public
     * @return  bool
     */
    function hasChanged() {
      return $this->_changed;
    }    
  }
?>
