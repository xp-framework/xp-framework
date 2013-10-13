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
    protected $buffer= '';
    protected $offset= 0;

    /**
     * Constructor
     *
     * @param   string buffer
     */
    public function __construct($buffer) {
      $this->buffer= $buffer;
      $this->offset= 0;
    }

    /**
     * Consume
     *
     * @param   string expected
     * @throws  lang.FormatException in case the expected characters are not found
     */
    public function consume($expected) {
      $l= strlen($expected);
      if (0 === substr_compare($this->buffer, $expected, $this->offset, $l)) {
        $this->offset+= $l;
        return;
      }
      throw new FormatException('Expected '.$expected.', have '.substr($this->buffer, $this->offset, $l));
    }

    /**
     * Consume a token (x:... where x is the token)
     *
     * @return  string
     */
    public function consumeToken() {
      $token= $this->buffer{$this->offset};
      $this->offset+= 2;
      return $token;
    }

    /**
     * Consume a string ([length]:"[string]")
     * 
     * @return  string
     */
    public function consumeString() {
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
     * Consume everything up to the next ";" character and return it
     * 
     * @return  string
     */     
    public function consumeWord() {
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
     * @return  string
     */     
    public function consumeSize() {
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
