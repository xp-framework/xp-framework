<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('rmi.RMIException');

  /**
   * An UnmarshalException can be thrown while unmarshalling the 
   * parameters or results of a remote method call if any of the 
   * following conditions occur: if an exception occurs while 
   * unmarshalling the call header if the protocol for the return 
   * value is invalid if a io.IOException occurs unmarshalling 
   * parameters (on the server side) or the return value (on the 
   * client side)
   *
   * @purpose  Exception
   */
  class RMIUnmarshalException extends RMIException {
  
  }
?>
