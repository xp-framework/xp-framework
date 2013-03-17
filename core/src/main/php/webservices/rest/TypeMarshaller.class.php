<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('webservices.rest.srv.Response');

  /**
   * Exception mapping
   *
   */
  interface TypeMarshaller {

    /**
     * Marshals the type
     *
     * @param  T type
     * @return var
     */
    public function marshal($t);

    /**
     * Unmarshals input
     *
     * @param  lang.Type target
     * @param  var in
     * @return T
     */
    public function unmarshal(Type $target, $in);
  }
?>