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
  interface webservices·rest·Output {

    /**
     * Writes this payload to an output stream
     *
     * @param  scriptlet.Response response
     * @param  peer.URL base
     * @param  string format
     * @return bool handled
     */
    public function writeTo($response, $base, $format);
  }
?>
