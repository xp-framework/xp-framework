<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('util.log.ConsoleAppender');

  /**
   * ConsoleAppender which colorizes output depending on the
   * logger flag (error, warn, info or debug).
   *
   * Uses the terminal emulation escape sequences to set colors.
   *
   * @see      http://www.catalyst.com/support/help/cstools3/visual/terminal/escapeseq.html
   * @see      http://www.termsys.demon.co.uk/vtansi.htm#colors  
   * @see      xp://util.log.ConsoleAppender
   * @purpose  Appender
   */  
  class ColoredConsoleAppender extends ConsoleAppender {
    public
      $cerror    = '',
      $cwarn     = '', 
      $cinfo     = '', 
      $cdebug    = '',
      $cdefault  = '';

    /**
     * Constructor
     *
     * @param   string cerror default '01;31' color for errors
     * @param   string cwarn default '00;31' color for warnings
     * @param   string cinfo default '00;00' color for information
     * @param   string cdebug default '00;34' color for debug
     * @param   string cdefault default '07;37' default color
     */
    public function __construct(
      $cerror   = '01;31', 
      $cwarn    = '00;31', 
      $cinfo    = '00;00',
      $cdebug   = '00;34',
      $cdefault = '07;37'
    ) {
      $this->cerror     = $cerror; 
      $this->cwarn      = $cwarn;
      $this->cinfo      = $cinfo;
      $this->cdebug     = $cdebug;
      $this->cdefault   = $cdefault;
    }
    
    /**
     * Appends log data to STDERR
     *
     * @param  mixed args variables
     */
    public function append() {
      $a= func_get_args();
      
      // Colorize depending on the flag
      if (strstr($a[0], 'error')) {
        fwrite(STDERR, "\x1b[".$this->cerror."m");
      } else if (strstr($a[0], 'warn')) {
        fwrite(STDERR, "\x1b[".$this->cwarn."m");
      } else if (strstr($a[0], 'info')) {
        fwrite(STDERR, "\x1b[".$this->cinfo."m");      
      } else if (strstr($a[0], 'debug')) {
        fwrite(STDERR, "\x1b[".$this->cdebug."m");      
      } else {
        fwrite(STDERR, "\x1b[".$this->cdefault."m");
      }
      foreach ($a as $arg) {
        fwrite(STDERR, $this->varSource($arg).' ');
      }
      fwrite(STDERR, "\x1b[0m\n");
    }
  }
?>
