<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('text.parser.VFormatParser', 'org.imc.VEvent', 'util.Date');

  define('VCAL_ID',             'VCALENDAR');

  define('VCAL_METHOD_REQUEST', 'REQUEST');

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
    var
      $method   = '',
      $events   = array();
      
    /**
     * Parser callback
     *
     * @access  public
     * @param   array keys
     * @param   mixed value
     * @throws  FormatException
     */
    function addProperty($keys, $value) {
      static $context= array();
      static $event;
      
      // Cascaded objects      
      if (0 == strcasecmp('BEGIN', $keys[0])) $context[]= strtolower($value);
      
      switch (implode('/', $context)) {
      
        // An event
        case 'vevent':
          switch ($keys[0]) {
            case 'BEGIN':
              $event= new VEvent();
              break;
              
            case 'END':
              $this->events[]= &$event;
              break;
            
            case 'DTSTAMP':     // DTSTAMP:20030220T101358Z
              $event->date= &new Date(VFormatParser::decodeDate($value));
              break;
              
            case 'DTSTART':
              $event->starts= &new Date(VFormatParser::decodeDate($value));
              break;

            case 'DTEND':
              $event->ends= &new Date(VFormatParser::decodeDate($value));
              break;
            
            case 'SUMMARY':
              $event->summary= VFormatParser::decodeString($value);
              break;

            case 'LOCATION':
              $event->location= VFormatParser::decodeString($value);
              break;

            case 'DESCRIPTION':
              $event->description= VFormatParser::decodeString($value);
              break;
              
            case 'ATTENDEE':
              $event->attendee[]= $value;
              break;
              
            case 'ORGANIZER':
              $event->organizer= $value;
              break;
          }
          break;

        // An alarm for an event          
        case 'vevent/valarm':
          switch ($keys[0]) {
            case 'ACTION':
              $event->alarm['action']= $value;
              break;
              
            case 'DESCRIPTION':
              $event->alarm['description']= $value;
              break;
            
            case 'TRIGGER':     // TRIGGER;RELATED=START:-PT00H15M00S
              $event->alarm['trigger']= $value;
              break;
         }
          
      }
      
      #ifdef DEBUG
      echo '/'.implode('/', $context)."@";
      echo $this->getClassName().'::addProperty(';
      var_export($keys);
      echo ', ';
      var_export($value);
      echo ")\n\n";
      #endif
      
      if (0 == strcasecmp('END', $keys[0])) array_pop($context);
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
