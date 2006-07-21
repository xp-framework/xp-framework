<?php
/* This file is part of the XP framework
 *
 * $Id$
 */

  uses('remote.beans.RemoteInterface');

  /**
   * Remote interface for xp/demo/Roundtrip
   *
   * @purpose  EASC Client stub
   */
  interface Roundtrip {

    /**
     * EchoString method
     *
     * @access  public
     * @param   string arg1
     * @return  string
     */
    public function echoString($arg1);

    /**
     * EchoInt method
     *
     * @access  public
     * @param   integer arg1
     * @return  integer
     */
    public function echoInt($arg1);

    /**
     * EchoDouble method
     *
     * @access  public
     * @param   double arg1
     * @return  double
     */
    public function echoDouble($arg1);

    /**
     * EchoBool method
     *
     * @access  public
     * @param   boolean arg1
     * @return  boolean
     */
    public function echoBool($arg1);

    /**
     * EchoNull method
     *
     * @access  public
     * @param   java.lang.Object arg1
     * @return  java.lang.Object
     */
    public function echoNull($arg1);

    /**
     * EchoDate method
     *
     * @access  public
     * @param   util.Date arg1
     * @return  util.Date
     */
    public function echoDate($arg1);

    /**
     * EchoHash method
     *
     * @access  public
     * @param   array<mixed, mixed> arg1
     * @return  array<mixed, mixed>
     */
    public function echoHash($arg1);

    /**
     * EchoArrayList method
     *
     * @access  public
     * @param   lang.types.ArrayList arg1
     * @return  lang.types.ArrayList
     */
    public function echoArray($arg1);

  }
?>
