<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('Serializable');

  /**
   * Date class
   *
   * @purpose  Demo
   */
  class Date extends Object implements Serializable {
    protected
      $utime    = -1,
      $info     = array();

    /**
     * Constructor
     *
     * @access  public
     * @param   mixed in default NULL
     */
    public function __construct($in= NULL) {
      switch (gettype($in)) {
        case 'int': 
        case 'float':
          $this->utime= $in; 
          break;
        
        case 'NULL':
          $this->utime= time();
          break;
        
        default:
          $this->utime= strtotime($in);
          break;
      }
      foreach (getdate($this->utime) as $key => $val) {
        if (is_string($key)) $this->info[$key]= $val;
      }
    }
    
    /**
     * Returns a string representation of this date
     *
     * @access  public
     * @return  string
     */
    public function toString() {
      return sprintf(
        "Date %s {\n  [utime] %s\n  [info ] %s\n}",
        date('r', $this->utime),
        var_export($this->utime, 1),
        var_export($this->info, 1)
      );
    }
  }
?>
