<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  $package= 'net.xp_framework.unittest.core.generics';

  uses('net.xp_framework.unittest.core.generics.IDictionary');

  /**
   * Lookup map
   *
   */
  #[@generic(self= 'K, V', IDictionary= 'K, V')]
  abstract class net·xp_framework·unittest·core·generics·AbstractDictionary extends Object implements net·xp_framework·unittest·core·generics·IDictionary {
    
    /**
     * Constructor
     *
     * @param   array<string, var> initial
     */
    public function __construct($initial= array()) {
      foreach ($initial as $key => $value) {
        $this->put($key, $value);
      }
    }
  }
?>
