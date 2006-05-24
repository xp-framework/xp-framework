<?php
/* This file is part of the XP framework
 *
 * $Id$
 */

  /**
   * Remote interface for xp/demo/Roundtrip
   *
   * @purpose  EASC Client stub
   */
  class Roundtrip extends Interface {

    /**
     * EchoString method
     *
     * @access  public
     * @param   string arg1
     * @return  string
     */
    function echoString($arg1) { }

    /**
     * EchoInt method
     *
     * @access  public
     * @param   integer arg1
     * @return  integer
     */
    function echoInt($arg1) { }

    /**
     * EchoDouble method
     *
     * @access  public
     * @param   double arg1
     * @return  double
     */
    function echoDouble($arg1) { }

    /**
     * EchoBool method
     *
     * @access  public
     * @param   boolean arg1
     * @return  boolean
     */
    function echoBool($arg1) { }

    /**
     * EchoNull method
     *
     * @access  public
     * @param   java.lang.Object arg1
     * @return  java.lang.Object
     */
    function echoNull($arg1) { }

    /**
     * EchoDate method
     *
     * @access  public
     * @param   util.Date arg1
     * @return  util.Date
     */
    function echoDate($arg1) { }

    /**
     * EchoHash method
     *
     * @access  public
     * @param   array<mixed, mixed> arg1
     * @return  array<mixed, mixed>
     */
    function echoHash($arg1) { }

  }
?>
