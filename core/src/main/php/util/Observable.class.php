<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('util.Observer');

  /**
   * Observable - base class for Model/View/Controller architecture.
   *
   * A basic implementation might look like this:
   *
   * TextObserver class:
   * <code>
   *   class TextObserver extends Object implements Observer {
   *
   *     public function update($obs, $arg= NULL) {
   *       Console::writeLine(__CLASS__, ' was notified of update in value, is now ', $obs->getValue());
   *     }
   *   }
   * </code>
   *
   * ObservableValue class:
   * <code>
   *   uses('util.Observable');
   *
   *   class ObservableValue extends Observable {
   *     private $n= 0;
   *     
   *     public function __construct($n) {
   *       $this->n= $n;
   *     }
   *     
   *     public function setValue($n) {
   *       $this->n= $n;
   *       $this->setChanged();
   *       $this->notifyObservers();
   *     }
   *     
   *     public function getValue() {
   *       return $this->n;
   *     }
   *   }
   * </code>
   *
   * Main program:
   * <code>
   *   uses('de.thekid.util.TextObserver', 'de.thekid.util.ObservableValue');
   *
   *   $value= new ObservableValue(3);
   *   $value->addObserver(new TextObserver());
   *   $value->setValue(5);
   * </code>
   *
   * The update method gets passed the instance of Observable as its first
   * argument and - if existant - the argument passed to notifyObservers as 
   * its second.
   *
   * @see   http://www.javaworld.com/javaworld/jw-10-1996/jw-10-howto.html
   * @test  xp://net.xp_framework.unittest.util.ObservableTest
   */
  class Observable extends Object {
    public
      $_obs      = array(),
      $_changed  = FALSE;
      
    /**
     * Add an observer
     *
     * @param   util.Observer observer a class implementing the util.Observer interface
     * @return  util.Observer the added observer
     * @throws  lang.IllegalArgumentException in case the argument is not an observer
     */
    public function addObserver(Observer $observer) {
      $this->_obs[]= $observer;
      return $observer;
    }
    
    /**
     * Notify observers
     *
     * @param   var arg default NULL
     */
    public function notifyObservers($arg= NULL) {
      if (!$this->hasChanged()) return;
      
      for ($i= 0, $s= sizeof($this->_obs); $i < $s; $i++) {
        $this->_obs[$i]->update($this, $arg);
      }
      
      $this->clearChanged();
      unset($arg);
    }
    
    /**
     * Sets changed flag
     *
     */
    public function setChanged() {
      $this->_changed= TRUE;
    }

    /**
     * Clears changed flag
     *
     */
    public function clearChanged() {
      $this->_changed= FALSE;
    }

    /**
     * Checks whether changed flag is set
     *
     * @return  bool
     */
    public function hasChanged() {
      return $this->_changed;
    }
  }
?>
