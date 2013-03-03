<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('webservices.rest.srv.ExceptionMapper');

  /**
   * Default exception mapping
   *
   */
  class DefaultExceptionMapper extends Object implements ExceptionMapper {
    protected $statusCode;

    /**
     * Creates a new instance with a given statuscode
     * *
     * @param int $statusCode The statuscode to use for this exception
     */
    public function __construct($statusCode) {
      $this->statusCode= $statusCode;
    }

    /**
     * Maps an exception
     *
     * @param  lang.Throwable t
     * @param  webservices.rest.srv.RestContext ctx
     * @return webservices.rest.srv.Response
     */
    public function asResponse($t, RestContext $ctx) {
      return Response::error($this->statusCode)->withPayload(
        new Payload(array('message' => $t->getMessage()))
      );
    }
  }
?>