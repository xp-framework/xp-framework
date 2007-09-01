<?php
/* This class is part of the XP framework
 *
 * $Id: TimeZone.class.php 10594 2007-06-11 10:04:54Z friebe $ 
 */

  namespace util;

  ::uses('util.Date');

  /**
   * Time zone
   *
   * <code>
   *   $tz= &TimeZone::getByTimeZone('CST');
   *   printf("Offset is %s\n", $tz->getOffset());  // -0600
   * </code>
   *
   * @see      http://greenwichmeanTime.com/info/Timezone.htm
   * @see      http://www.worldtimezone.com/wtz-names/timezonenames.html
   * @see      http://scienceworld.wolfram.com/astronomy/TimeZone.html
   * @purpose  Time zone calculation
   */
  class TimeZone extends lang::Object {
    public 
      $offset=  '',
      $tz=      '';

    /**
     * Constructor.
     *
     * @param   string offset
     * @param   string timezone name default ''
     */
    public function __construct($offset, $tz= '') {
      $this->offset= $offset;
      $this->tz= $tz;
    }

    /**
     * Gets the name of the timezone
     *
     * @return  string name
     */
    public function getName() {
      return $this->tz;
    }

    /**
     * Retrieves the offset of the timezone
     *
     * @return  string offset
     */    
    public function getOffset() {
      return $this->offset;
    }

    /**
     * Get the offset string by timezone name
     *
     * @param   string string
     * @return  string
     */
    public static function getOffsetByTimeZoneString($string) {
      static $tz= array (
        // East of Greenwich
        'IDLE'=> '+1200',             // International Date Line East
        'NZST'=> '+1200',             // New Zealand Standard
        'GST' => '+1000',             // Guam Standard
        'JST' => '+0900',             // Japan Standard Time
        'CCT' => '+0800',             // China coast Time
        'BT'  => '+0300',             // Baghdad
        'EET' => '+0200',             // Eastern European Time
        'CET' => '+0100',             // Central European Time
        
        // Greenwich
        'Z'   => '+0000',             // Zulu time
        'GMT' => '+0000',             // Greenwich mean Time
        'UT'  => '+0000',             // Universal
        'UTC' => '+0000',             // Universal Co-ordinated
        'WET' => '+0000',             // Western Europe

        // West of Greenwich
        'WAT' => '-0100',             // West Africa
        'AT'  => '-0200',             // Azores
        'AST' => '-0400',             // Atlantic Standard
        'EST' => '-0500',             // Eastern Standard Time
        'CST' => '-0600',             // Central Standard Time
        'MST' => '-0700',             // Mountain Standard Time
        'PST' => '-0800',             // Pacific Standard Time
        'YST' => '-0900',             // Yukon Standard
        'AHST'=> '-1000',             // Alaska-Hawaii Standard
        'NT'  => '-1100',             // Nome
        'IDLE'=> '-1200',             // International Date Line West
        
        // Summer time
        'BST' => '+0100',             // British Summer Time
        'CEST'=> '+0200',             // Central European Summer Time
        'MEST'=> '+0200',             // Middle European Summer Time
        'MESZ'=> '+0200',             // Middle European Summer Time
        'EEST'=> '+0300',             // Eastern European Summer Time
        'SST' => '+0200',             // Swedish Summer
        'FST' => '+0200',             // French Summer
        'ADT' => '-0300',             // Atlantic Daylight
        'EDT' => '-0400',             // Eastern Daylight
        'CDT' => '-0500',             // Central Daylight
        'MDT' => '-0600',             // Mountain Daylight
        'PDT' => '-0700',             // Pacific Daylight
        'YDT' => '-0800',             // Yukon Daylight
        'HDT' => '-0900',             // Hawaii Daylight
      );

      if (!isset ($tz[$string]))
        return FALSE;
      
      return $tz[$string];
    }

    /**
     * Returns a TimeZone object by a time zone name.
     *
     * @param   string abbrev
     * @return  util.TimeZone
     * @throws  lang.IllegalArgumentException if timezone is unknown
     */    
    public static function getByName($abbrev) {
      if (FALSE === ($offset= ::getOffsetByTimeZoneString($abbrev))) {
        throw (new lang::IllegalArgumentException (
          'Unknown time zone abbreviation: '.$abbrev
        ));
      }
      
      $tz= new ($offset, $abbrev);
      return $tz;
    }
    
    /**
     * Get a timezone object for the machines local timezone.
     *
     * @return  util.TimeZone
     */
    public static function getLocal() {
      return ::getByName(date('T'));
    }

    /**
     * Retrieves the timezone offset to UTC/GMT
     *
     * @return  int offset
     */    
    public function getOffsetInSeconds() {
      list($s, $h, $m)= sscanf ($this->offset, '%c%2d%2d');
      return (('+' == $s ? 1 : -1) * ((3600 * $h) + (60 * $m)));
    }
    
    /**
     * Converts a date from one timezone to a date of this
     * timezone.
     *
     * @param   util.Date date
     * @param   util.TimeZone tz
     * @return  util.Date
     */
    public function convertDate($date, $tz) {
      return new Date($date->getTime() + ($this->getOffsetInSeconds() - $tz->getOffsetInSeconds()));
    }

    /**
     * Converts a date in the machines local timezone to a date in this
     * timezone.
     *
     * @param   util.Date date
     * @return  util.Date
     */    
    public function convertLocalDate($date) {
      return $this->convertDate($date, ::getLocal());
    }
  }
?>
