<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  $package= 'net.xp_lang.tests.execution.source';

  /**
   * Class used by operator overloading tests
   *
   */
  class net·xp_lang·tests·execution·source·StringBuffer extends Object {
    protected $buffer;
    
    /**
     * Creates a new stringbuffer
     *
     * @param   string initial
     */
    public function __construct($initial= '') {
      $this->buffer= $initial;
    }
    
    
    /**
     * Overloads the "%" operator with "sprintf" style functionality
     *
     * @see     php://sprintf
     * @param   net.xp_lang.tests.execution.source.StringBuffer self
     * @param   var arg
     * @return  net.xp_lang.tests.execution.source.StringBuffer
     */
    public static function operator··mod(self $self, $arg) {
      return new self(sprintf($self->buffer, $arg));
    }

    /**
     * Overloads the "~" operator with concatenation functionality
     *
     * @param   net.xp_lang.tests.execution.source.StringBuffer self
     * @param   string args
     * @param   net.xp_lang.tests.execution.source.StringBuffer
     */
    public static function operator··concat(self $self, $args) {
      return new self($self->buffer.$args);
    }
    
    /**
     * Returns this stringbuffer as a string
     *
     * @return  string
     */
    public function getBytes() {
      return $this->buffer;
    }
  }
?>
