<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('text.parser.VFormatParser', 'org.imc.VEvent');

  define('VCAL_ID',         'VCALENDAR');

  /**
   * VCalendar
   * 
   * <quote>
   * vCalendar defines a transport and platform-independent format for exchanging 
   * calendaring and scheduling information in an easy, automated, and consistent 
   * manner. It captures information about event and "to-do" items that are
   * normally used by applications such as a personal information managers (PIMs) 
   * and group schedulers. Programs that use vCalendar can exchange important data 
   * about events so that you can schedule meetings with anyone who has a vCalendar-
   * aware program.
   * </quote>
   *
   * @see      rfc://2445
   * @see      rfc://2446
   * @see      rfc://2447
   * @see      http://www.imc.org/pdi/
   * @see      http://www.imc.org/pdi/pdiproddev.html
   * @purpose  Handle vCalendar
   */
  class VCalendar extends Object {

    /**
     * Parser callback
     *
     * @access  public
     * @param   array keys
     * @param   mixed value
     * @throws  FormatException
     */
    function addProperty($keys, $value) {
      #ifdef DEBUG
      echo $this->getClassName().'::addProperty(';
      var_export($keys);
      echo ', ';
      var_export($value);
      echo ")\n";
      #endif
    }
    
    /**
     * Creata a vCalendar from a stream
     *
     * <code>
     *   try(); {
     *     $cal= &VCalendar::fromStream(new File('/tmp/test.ics'));
     *   } if (catch('Exception', $e)) {
     *     $e->printStackTrace();
     *     exit(-1);
     *   }
     *   
     *   var_dump($cal);
     * </code>
     *
     * @model   static
     * @access  public
     * @param   &io.Stream stream
     * @return  &org.imc.VCard
     */
    function &fromStream(&$stream) {
      $cal= &new VCalendar();
      
      $p= &new VFormatParser(VCAL_ID);
      $p->setDefaultHandler(array(&$cal, 'addProperty'));
      
      try(); {
        $p->parse($stream);
      } if (catch('Exception', $e)) {
        return throw($e);
      }
      
      return $cal;
    }
  
    /**
     * Returns the textual representation of this vCalendar
     *
     * <code>
     *   [...]
     *   $f= &new File('calendar.ics');
     *   $f->open(FILE_MODE_WRITE);
     *   $f->write($cal->export());
     *   $f->close();
     * </code>
     *
     * @access  
     * @param   
     * @return  
     */
    function export() {
      
    }
  
  }
?>
