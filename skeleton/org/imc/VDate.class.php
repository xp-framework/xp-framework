<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  class VDate extends Object {
    var
      $name=      NULL,
      $date=      NULL,
      $timezone=  NULL;

    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */    
    function __construct() {
      $args= func_get_args();
      
      if (0 == count ($args))
        return FALSE;
      
      if (is_object ($args[0])) {
        $this->date= &new Date (VFormatParser::decodeDate ($args[0]->_value));
        $this->timezone= $args[0]->tzid;
        return TRUE;
      }
      
      $this->date= &new Date (VFormatParser::decodeDate ($args[0]));
      return TRUE;
    }
    
    function toString() {
      return $this->date->toString ('Ymd').'T'.$this->date->toString ('His').'Z';
    }
    
    function export() {
      return ($this->name.
        (NULL !== $this->timezone ? ';TZID='.$this->timezone : '').
        ':'.
        $this->toString()
      );
    }
  }
?>
