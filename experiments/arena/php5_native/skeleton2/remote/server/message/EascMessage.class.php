<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class EascMessage extends Object {
    public
      $type   = NULL,
      $value  = NULL;

    /**
     * Set Type
     *
     * @access  public
     * @param   &lang.Object type
     */
    public function setType(&$type) {
      $this->type= &$type;
    }

    /**
     * Get Type
     *
     * @access  public
     * @return  &lang.Object
     */
    public function &getType() {
      return $this->type;
    }

    /**
     * Set Value
     *
     * @access  public
     * @param   &lang.Object value
     */
    public function setValue(&$value) {
      $this->value= &$value;
    }

    /**
     * Get Value
     *
     * @access  public
     * @return  &lang.Object
     */
    public function &getValue() {
      return $this->value;
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    public function readString($data, &$offset) {
      $string= '';
      do {
        $ctl= unpack('nlength/cnext', substr($data, $offset, 4));
        $string.= substr($data, $offset+ 3, $ctl['length']);
        $offset+= $ctl['length']+ 1;
      } while ($ctl['next']);

      return utf8_decode($string);
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    public function handle(&$event) { }
  }
?>
