<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('org.apache.xml.workflow.ContextResource');

  /**
   * Context resource for pager
   *
   * @see      reference
   * @purpose  ContextResource for PagerHandler
   */
  class PagerContextResource extends ContextResource {
    var
      $begin        = 0,
      $count        = 0,
      $interval     = 10;

    /**
     * Set interval
     *
     * @access  public
     * @param   int interval
     */
    function setInterval($interval) {
      $this->interval= $interval;
    }

    /**
     * Get interval
     *
     * @access  public
     * @return  int
     */
    function getInterval() {
      return $this->interval;
    }
      
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function showFirst() {
      $this->begin= 0;
    }

    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function showLast() {
      $this->begin= max($this->count - $this->interval, 0);
    }

    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function showNext() {
      $this->begin= max($this->begin + $this->interval, $this->count - $this->interval);
    }

    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function showPrevious() {
      $this->begin= max($this->begin - $this->interval, 0);
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function getShowFrom() {
      return $this->begin;
    }

    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function setShowFrom($from) {
      $this->begin= $from;
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function getShowTo() {
      return max($this->begin + $this->interval, $this->count);
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function setItems(&$values) {
      $this->values= &$values;
      $this->count= sizeof($values);
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function hasItems() {
      return !empty($this->values);
    }
    
    /**
     * (Insert method's description here)
     *
     * @model   abstract
     * @access  public
     * @return  
     */
    function update() { }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function insertStatus(&$elem) {
      for ($i= $this->getShowFrom(), $to= $this->getShowTo(); $i < $to; $i++) {
        switch (gettype($this->values[$i])) {
          case 'object':
            $elem->addFormResult(Node::fromObject($this->values[$i])); break;
          case 'array':
            $elem->addFormResult(Node::fromArray($this->values[$i])); break;
          default:
            $elem->addFormResult(new Node('item', $this->values[$i]));
        }
      }
    }
  }
?>
