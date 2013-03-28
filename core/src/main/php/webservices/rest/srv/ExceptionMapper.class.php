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
     * @param  webservices.rest.srv.RestContext ctx
     * @return webservices.rest.srv.Response
     */
    public function asResponse($t, RestContext $ctx);
  }
?>