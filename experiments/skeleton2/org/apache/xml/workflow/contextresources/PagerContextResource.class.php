<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('org.apache.xml.workflow.ContextResource');

  /**
   * Context resource for pager
   *
   * @see      xp://org.apache.xml.workflow.handles.PagerHandler
   * @purpose  ContextResource for PagerHandler
   */
  class PagerContextResource extends ContextResource {
    public
      $begin        = 0,
      $count        = 0,
      $interval     = 10;

    /**
     * Set interval
     *
     * @access  public
     * @param   int interval
     */
    public function setInterval($interval) {
      $this->interval= $interval;
    }

    /**
     * Get interval
     *
     * @access  public
     * @return  int
     */
    public function getInterval() {
      return $this->interval;
    }
      
    /**
     * Set this pager to the beginning
     *
     * @access  public
     */
    public function showFirst() {
      $this->begin= 0;
    }

    /**
     * Set this pager to the end
     *
     * @access  public
     */
    public function showLast() {
      $this->begin= $this->count - ($this->count % $this->interval);
    }

    /**
     * Set this pager to the next
     *
     * @access  public
     */
    public function showNext() {
      $this->begin= min(
        $this->begin + $this->interval, 
        $this->count - ($this->count % $this->interval)
      );
    }

    /**
     * Set this pager to the previous
     *
     * @access  public
     */
    public function showPrevious() {
      $this->begin= max($this->begin - $this->interval, 0);
    }
    
    /**
     * Get beginning
     *
     * @access  public
     * @return  int
     */
    public function getShowFrom() {
      return $this->begin;
    }

    /**
     * Set this pager's beginning to a specified value
     *
     * @access  public
     * @param   int from
     */
    public function setShowFrom($from) {
      $this->begin= $from;
    }
    
    /**
     * Get end
     *
     * @access  public
     * @return  int
     */
    public function getShowTo() {
      return min($this->begin + $this->interval, $this->count);
    }
    
    /**
     * Set items
     *
     * @access  public
     * @param   &array values
     */
    public function setItems(&$values) {
      $this->values= $values;
      $this->count= sizeof($values);
    }
    
    /**
     * Check whether this pager has values
     *
     * @access  public
     * @return  bool TRUE if there is at least one value in the values list
     */
    public function hasItems() {
      return !empty($this->values);
    }
    
    /**
     * Callback for when this pager needs updating
     *
     * @model   abstract
     * @access  public
     */
    public abstract function update() ;
    
    /**
     * Insert status
     *
     * @access  public
     * @param   &xml.Node elem
     */
    public function insertStatus(&$elem) {
      $to=   self::getShowTo();
      $from= self::getShowFrom();
      $elem->addChild(Node::fromArray(array(
        'count' => $this->count,
        'from'  => $from,
        'to'    => $to
      ), 'pager'));
      
      for ($i= $from; $i < $to; $i++) {
        switch (gettype($this->values[$i])) {
          case 'object':
            $c= $elem->addChild(Node::fromObject($this->values[$i], 'item')); break;
          case 'array':
            $c= $elem->addChild(Node::fromArray($this->values[$i], 'item')); break;
          default:
            $c= $elem->addChild(new Node('item', $this->values[$i]));
        }
        $c->setAttribute('id', $i);
      }
    }
  }
?>
