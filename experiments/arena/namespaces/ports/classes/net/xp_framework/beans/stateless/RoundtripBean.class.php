<?php
/* This class is part of the XP framework
 *
 * $Id: RoundtripBean.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace net::xp_framework::beans::stateless;

  ::uses('remote.beans.Bean', 'net.xp_framework.beans.stateless.Roundtrip');

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
  class RoundtripBean extends remote::beans::Bean implements Roundtrip {
  
    /**
     * EchoString method
     *
     * @param   string arg1
     * @return  string
     */
    #[@remote]
    public function echoString($arg1) {
      return $arg1;
    }

    /**
     * EchoInt method
     *
     * @param   integer arg1
     * @return  integer
     */
    #[@remote]
    public function echoInt($arg1) {
      return $arg1;
    }

    /**
     * EchoDouble method
     *
     * @param   double arg1
     * @return  double
     */
    #[@remote]
    public function echoDouble($arg1) {
      return $arg1;
    }

    /**
     * EchoBool method
     *
     * @param   boolean arg1
     * @return  boolean
     */
    #[@remote]
    public function echoBool($arg1) {
      return $arg1;
    }

    /**
     * EchoNull method
     *
     * @param   java.lang.Object arg1
     * @return  java.lang.Object
     */
    #[@remote]
    public function echoNull($arg1) {
      return $arg1;
    }

    /**
     * EchoDate method
     *
     * @param   util.Date arg1
     * @return  util.Date
     */
    #[@remote]
    public function echoDate($arg1) {
      return $arg1;
    }

    /**
     * EchoHash method
     *
     * @param   array<mixed, mixed> arg1
     * @return  array<mixed, mixed>
     */
    #[@remote]
    public function echoHash($arg1) {
      return $arg1;
    }

    /**
     * EchoArrayList method
     *
     * @param   lang.types.ArrayList arg1
     * @return  lang.types.ArrayList
     */
    #[@remote]
    public function echoArray($arg1) {
      if (!is('lang.types.ArrayList', $arg1)) throw(new lang::IllegalArgumentException(
        'arg1 is not an array.'
      ));
      return $arg1;
    }
  } 
?>
