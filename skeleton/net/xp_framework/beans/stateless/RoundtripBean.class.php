<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('remote.beans.Bean');

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  #[@lookupName('xp/demo/Roundtrip'),
  # @homeInterface('net.xp_framework.beans.stateless.RoundtripHome')
  #]
  class RoundtripBean extends Bean {
  
    /**
     * EchoString method
     *
     * @access  public
     * @param   string arg1
     * @return  string
     */
    #[@remote]
    function echoString($arg1) {
      return $arg1;
    }

    /**
     * EchoInt method
     *
     * @access  public
     * @param   integer arg1
     * @return  integer
     */
    #[@remote]
    function echoInt($arg1) {
      return $arg1;
    }

    /**
     * EchoDouble method
     *
     * @access  public
     * @param   double arg1
     * @return  double
     */
    #[@remote]
    function echoDouble($arg1) {
      return $arg1;
    }

    /**
     * EchoBool method
     *
     * @access  public
     * @param   boolean arg1
     * @return  boolean
     */
    #[@remote]
    function echoBool($arg1) {
      return $arg1;
    }

    /**
     * EchoNull method
     *
     * @access  public
     * @param   java.lang.Object arg1
     * @return  java.lang.Object
     */
    #[@remote]
    function echoNull($arg1) {
      return $arg1;
    }

    /**
     * EchoDate method
     *
     * @access  public
     * @param   util.Date arg1
     * @return  util.Date
     */
    #[@remote]
    function echoDate($arg1) {
      return $arg1;
    }

    /**
     * EchoHash method
     *
     * @access  public
     * @param   array<mixed, mixed> arg1
     * @return  array<mixed, mixed>
     */
    #[@remote]
    function echoHash($arg1) {
      return $arg1;
    }

    /**
     * EchoArrayList method
     *
     * @access  public
     * @param   lang.types.ArrayList arg1
     * @return  lang.types.ArrayList
     */
    #[@remote]
    function echoArray($arg1) {
      Console::writeLine(xp::stringOf($arg1));
      if (!is('lang.types.ArrayList', $arg1)) return throw(new IllegalArgumentException(
        'arg1 is not an array.'
      ));
      return $arg1;
    }
  } implements(__FILE__, 'net.xp_framework.beans.stateless.Roundtrip');
?>
