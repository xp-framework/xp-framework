<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  define('UUID_FMTSTRING',  '%08lx-%04x-%04x-%02x%02x-%02x%02x%02x%02x%02x%02x');

  /**
   *
   * <code>
   *   $uuid= &UUID::create();
   * </code>
   *
   * @see      http://www.ietf.org/internet-drafts/draft-mealling-uuid-urn-00.txt
   */
  class UUID extends Object {
    var
      $time_low                     = 0,
      $time_mid                     = 0,
      $time_hi_and_version          = 0,
      $clock_seq_low                = 0,
      $clock_seq_hi_and_reserved    = 0,
      $node                         = '';
        
    /**
     * Create a new UUID
     *
     * @model   static
     * @access  public
     * @return  &org.ietf.UUID
     * @see     http://www.ietf.org/internet-drafts/draft-mealling-uuid-urn-00.txt section 4.1.4
     */
    function &create() {
    
      // Get timestamp and convert it to UTC (based Oct 15, 1582).
      list($usec, $sec) = explode(' ', microtime());
      $t= ($sec * 10000000) + ($usec * 10) + 0x01B21DD213814000;
      $clock_seq= mt_rand();
      if ('' == ($host= getenv('HOSTNAME'))) {
        $host= getenv('COMPUTERNAME');
      }
      
      $uuid= &new UUID();
      $uuid->time_low= ($t & 0xFFFFFFFF);
      $uuid->time_mid= (($t >> 32) & 0xFFFF);
      $uuid->time_hi_and_version= (($t >> 48) & 0x0FFF);
      $uuid->time_hi_and_version |= (1 << 12);
      $uuid->clock_seq_low= $clock_seq & 0xFF;
      $uuid->clock_seq_hi_and_reserved= ($clock_seq & 0x3F00) >> 8;
      $uuid->clock_seq_hi_and_reserved |= 0x80;
      $uuid->node= $host;
      $uuid->node |= 80;
      
      return $uuid;
    }
    
    /**
     * Creates a string representation
     *
     * @access  public
     * @return  string
     */
    function toString() {
      return sprintf(
        UUID_FMTSTRING,
		$this->time_low, 
        $this->time_mid, 
        $this->time_hi_and_version,
		$this->clock_seq_hi_and_reserved, 
        $this->clock_seq_low,
		$this->node{0}, 
        $this->node{1}, 
        $this->node{2},
		$this->node{3}, 
        $this->node{4}, 
        $this->node{5}
      );
    }
  }
?>
