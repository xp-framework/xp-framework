<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('rdbms.finder.Finder');

  /**
   * Abstract base class for finder delegates
   *
   */
  abstract class FinderDelegate extends Object {
    protected $finder= NULL;

    /**
     * Creates a new instance with a given finder
     *
     * @param   rdbms.finder.Finder finder
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
