<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Abstract base class for finder delegates
   *
   */
  abstract class FinderDelegate extends Object {
    protected $finder= NULL;

    /**
     * Creates a new GenericFinder instance with a given Peer object.
     *
     * @param   rdbms.Peer peer
     */
    public function __construct($finder) {
      $this->finder= $finder;
    }
    
    /**
     * Select implementation
     *
     * @param   rdbms.Criteria criteria
     * @return  var
     * @throws  rdbms.finder.FinderException
     */
    public abstract function select($criteria);
    
    /**
     * Fluent interface
     *
     * @param   string name
     * @param   var[] args
     * @return  var
     */
    public function __call($name, $args) {
      return $this->select(call_user_func_array(array($this->finder, $name), $args));
    }
  }
?>
