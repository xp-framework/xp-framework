<?php
/* Diese Klasse ist Teil des XP-Frameworks
 * 
 * $Id$
 */

  define('RE_SYBASE_DATE',      '/^([a-zA-Z]{3})[ ]+([0-9]{1,2})[ ]+([0-9]{2,4})[ ]+([0-9]{1,2}):([0-9]{1,2})([A|P]M)$/');

  uses('util.Date');
  
  /**
   * Kapselt Datum und Uhrzeit
   */
  class SybaseDate extends Date {
  
    /**
     * Erstellt ein Datum aus einem Sybase-Datum
     *
     * @param   array regs Die Matches auf die Sybase-Datumsregex (siehe SybaseData#RE_SYBASE_DATE)
     */
    function fromRegs($regs) {
      $map= array(
        'Jan' => 1,
        'Feb' => 2,
        'Mar' => 3,
        'Mrz' => 3,
        'Apr' => 4,
        'May' => 5,
        'Mai' => 5,
        'Jun' => 6,
        'Jul' => 7,
        'Aug' => 8,
        'Sep' => 9,
        'Oct' => 10,
        'Okt' => 10,
        'Nov' => 11,
        'Dec' => 12,
        'Dez' => 12
      );
      $regs[1]= $map[$regs[1]];
      if($regs[6]== "PM" && $regs[4]!= 12) $regs[4]+= 12; // 12 PM => 12:00
      if($regs[6]== "AM" && $regs[4]== 12) $regs[4]= 0;   // 12 AM => 00:00
      $this->_utime(mktime($regs[4], $regs[5], 0, $regs[1], $regs[2], $regs[3]));
    }
  }
?>
