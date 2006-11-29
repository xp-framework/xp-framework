<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  /**
   * Class that represents a chunk of serialized data
   *
   * @test     xp://remote.protocol.Serializer
   * @purpose  Value object
   */
  class SerializedData extends Object {
    var
      $buffer= '',
      $offset= 0;

    /**
     * Constructor
     * 
     * @access  public
     * @param   string buffer
     */
    function __construct($buffer) {
      $this->buffer= &$buffer;
      $this->offset= 0;
    }
    
    /**
     * Consume a string ([length]:"[string]")
     * 
     * @access  public
     * @return  string
     */
    function consumeString() {
      $l= substr(
        $this->buffer, 
        $this->offset, 
        strpos($this->buffer, ':', $this->offset)- $this->offset
      );
      $b= strlen($l)+ 2;              // 1 for ':', 1 for '"'
      $v= substr($this->buffer, $this->offset + $b, $l);
      $this->offset+= $b + $l + 2;    // 1 for '"', +1 to set the marker behind
      return $v;
    }

    /**
     * Consume everything up to the next ";" and return it
     * 
     * @access  public
     * @param   string stop
     * @return  string
     */     
    function consumeWord() {
      $v= substr(
        $this->buffer, 
        $this->offset, 
        strpos($this->buffer, ';', $this->offset)- $this->offset
      ); 
      $this->offset+= strlen($v)+ 1;  // +1 to set the marker behind
      return $v;
    }

    /**
     * Consume everything up to the next ":" character and return it
     * 
     * @access  public
     * @param   string stop
     * @return  string
     */     
    function consumeSize() {
      $v= substr(
        $this->buffer, 
        $this->offset, 
        strpos($this->buffer, ':', $this->offset)- $this->offset
      ); 
      $this->offset+= strlen($v)+ 1;  // +1 to set the marker behind
      return $v;
    }
  }
?>
