<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'webservices.rest.srv.Response',
    'webservices.rest.srv.ExceptionMapper',
    'util.collections.HashTable'
  );

  /**
   * The context of a rest call
   *
   * @test  xp://net.xp_framework.unittest.webservices.rest.srv.RestContextTest
   */
  class RestContext extends Object {
    protected $mappers;

    /**
     * Constructor
     */
    public function __construct() {
      $this->mappers= create('new HashTable<XPClass, ExceptionMapper>');
    }

    /**
     * Adds an exception mapper
     *
     * @param  var type either a full qualified type name or an XPClass instance
     * @param  webservices.rest.srv.ExceptionMapper m
     */
    public function addExceptionMapping($type, ExceptionMapper $m) {
      $this->mappers[$type instanceof XPClass ? $type : XPClass::forName($type)]= $m;
    }

    /**
     * Maps an exception
     *
     * @param  lang.Throwable t
     * @return webservices.rest.srv.Response
     */
    public function mapException($t) {
      foreach ($this->mappers->keys() as $type) {
        if ($type->isInstance($t)) return $this->mappers[$type]->asResponse($t);
      }
      return Response::status(HttpConstants::STATUS_BAD_REQUEST)->withPayload($t);
    }
  }
?>