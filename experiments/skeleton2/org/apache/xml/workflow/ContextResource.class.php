<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Generic context resource
   *
   * @purpose  Base class
   */
  class ContextResource extends Object {
    public
      $values       = array();

    /**
     * Notify observers
     *
     * @model   abstract
     * @access  protected
     */
    protected abstract function notifyAll() ;

    /**
     * Set a value
     *
     * @access  public
     * @param   string name
     * @param   &mixed data
     */
    public function setValue($name, &$data) {
      $this->values[$name]= $data;
      self::notifyAll();
    }

    /**
     * Get a value
     *
     * @access  public
     * @param   string name
     * @return  &mixed
     */
    public function getValue($name) {
      return $this->values[$name];
    }
    
    /**
     * Check whether a value exists
     *
     * @access  public
     * @param   string name
     * @return  bool
     */
    public function hasValue($name) {
      return isset($this->values[$name]);
    }
    
    /**
     * Insert status
     *
     * @access  public
     * @param   &xml.Node elem
     */
    public function insertStatus(&$elem) {
      $elem->addChild(Node::fromArray($this->values, 'values'));
    }
    
  }
?>
