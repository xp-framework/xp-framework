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
  interface ExceptionMapper {

    /**
     * Maps an exception
     *
     * @param  lang.Throwable t
     * @return webservices.rest.srv.Response
     */
    public function asResponse($t);
  }
?>