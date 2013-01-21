<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('webservices.rest.srv.ExceptionMapper');

  /**
   * Default exception mapping
   *
   * <code>
   *   { "message" : "Exception message" }
   * </code>
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
     * @return webservices.rest.srv.Response
     */
    public function asResponse($t) {
      return Response::error($this->statusCode)->withPayload(
        new Payload(array('message' => $t->getMessage()))
      );
    }
  }
?>