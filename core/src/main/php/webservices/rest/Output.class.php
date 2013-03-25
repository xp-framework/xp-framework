<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  $package= 'webservices.rest';

  /**
   * Represents output
   *
   */
  abstract class webservices·rest·Output extends Object {

    /**
     * Writes this payload to an output stream
     *
     * @param  scriptlet.Response response
     * @param  peer.URL base
     * @param  string format
     * @return bool handled
     */
    public abstract function writeTo($response, $base, $format);
  }
?>
