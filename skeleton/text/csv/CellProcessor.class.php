<?php
/* This class is part of the XP framework
 *
 * $Id: CellProcessor.class.php 11504 2009-09-15 13:36:13Z friebe $ 
 */

  /**
   * Processes a cell's value
   *
   */
  abstract class CellProcessor extends Object {
    protected $next= NULL;

    /**
     * Creates a new cell processor
     *
     * @param   text.csv.CellProcessor if omitted, no further processing will be done
     */
    public function __construct(CellProcessor $next= NULL) {
      $this->next= $next;
    }
    
    /**
     * Processes a cell value
     *
     * @param   var
     * @return  var
     */
    public abstract function process($in);

    /**
     * Processes a cell value
     *
     * @param   var
     * @return  var
     */
    public function proceed($in) {
      return $this->next ? $this->next->process($in) : $in;
    }
  }
?>
