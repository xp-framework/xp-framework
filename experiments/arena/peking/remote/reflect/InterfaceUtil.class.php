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
  class InterfaceUtil extends Object {
  
    /**
     * Retrieve a unique list of implemented interfaces. Removes
     * derived interfaces from the list
     *
     * @model   static
     * @access  public
     * @param   &lang.XPClass class
     * @return  &lang.XPClass[]
     */
    function getUniqueInterfacesFor(&$class) {
      $interfaces= &$class->getInterfaces();
      $out= array();
      
      if (sizeof($interfaces) <= 1) return $interfaces;
      for ($i= 0; $i < sizeof($interfaces); $i++) {
        $if= &$interfaces[$i];
        
        for ($k= 0; $k < sizeof($interfaces); $k++) {
          if ($interfaces[$k]->isSubclassOf($if->getName())) {
            continue(2);
          }
        }
        
        $out[]= &$if;
      }
      
      return $out;
    }
  }
?>
