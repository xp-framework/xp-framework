<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
 
  uses('util.Date');

  /**
   * VEvent
   * 
   * @see      xp://org.imc.VCalendar
   * @purpose  Represent a single event
   */
  class VEvent extends Object {
    var
      $date=         NULL,
      $starts=       NULL,
      $ends=         NULL,
      $summary=      '',
      $location=     '',
      $description=  '',
      $attendee=     array(),
      $organizer=    '';
  
  }
?>
