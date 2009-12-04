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
    protected $colors= array();

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
      $this->colors= array(
        LogLevel::INFO    => $cinfo,
        LogLevel::WARN    => $cwarn,
        LogLevel::ERROR   => $cerror,
        LogLevel::DEBUG   => $cdebug,
        LogLevel::NONE    => $cdefault
      );
    }
    
    /**
     * Append data
     *
     * @param   util.log.LoggingEvent event
     */ 
    public function append(LoggingEvent $event) {
      $l= $event->getLevel();
      fwrite(STDERR, "\x1b[".$this->colors[isset($this->colors[$l]) ? $l : LogLevel::NONE]."m");
      fwrite(STDERR, $this->layout->format($event));
      fwrite(STDERR, "\x1b[0m");
    }
  }
?>
