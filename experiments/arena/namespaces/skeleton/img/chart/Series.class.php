<?php
/* This class is part of the XP framework
 *
 * $Id: Series.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace img::chart;

  /**
   * A series of data
   *
   * @see      xp://img.chart.Chart
   * @purpose  Value object
   */
  class Series extends lang::Object {
    public
      $name   = '',
      $values = array();
      
    /**
     * Constructor
     *
     * @param   string name
     * @param   float[] values default array()
     */
    public function __construct($name, $values= array()) {
      $this->name= $name;
      $this->values= $values;
    }
    
    /**
     * Adds a value to this series
     *
     * @param   float f
     */
    public function add($f) {
      $this->values[]= $f;
    }
  }
?>
