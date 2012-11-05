<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'webservices.rest.TypeMarshaller',
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
    protected $marshallers;

    static function __static() {
      xp::extensions(__CLASS__, __CLASS__);
    }

    /**
     * Constructor
     */
    public function __construct() {
      $this->mappers= create('new HashTable<XPClass, ExceptionMapper>');
      $this->marshallers= create('new HashTable<Type, TypeMarshaller>');
    }

    /**
     * Adds an exception mapper
     *
     * @param  var type either a full qualified class name or an XPClass instance
     * @param  webservices.rest.srv.ExceptionMapper m
     */
    public function addExceptionMapping($type, ExceptionMapper $m) {
      $this->mappers[$type instanceof XPClass ? $type : XPClass::forName($type)]= $m;
    }

    /**
     * Adds a type marshaller
     *
     * @param  var type either a full qualified type name or a type instance
     * @param  webservices.rest.TypeMarshaller m
     */
    public function addMarshaller($type, TypeMarshaller $m) {
      $this->marshallers[$type instanceof Type ? $type : Type::forName($type)]= $m;
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

    /**
     * Marshal a type
     *
     * @param  var t
     * @return webservices.rest.srv.Response
     */
    public function marshal($t) {
      foreach ($this->marshallers->keys() as $type) {
        if ($type->isInstance($t)) return $this->marshallers[$type]->marshal($t);
      }
      return $t;
    }

    /**
     * Returns whether this type instance is assignable from a given type "t"
     *
     * @param  lang.Type self
     * @param  lang.Type type 
     */
    protected static function isAssignableFrom($self, $t) {
      if ($self instanceof XPClass) {
        return $self->equals($t) || $t->isSubclassOf($self);
      } else {
        return $self->equals($t);
      }
    }

    /**
     * Unmarshal a type to a given target
     *
     * @param  lang.Type target
     * @param  var in
     * @return webservices.rest.srv.Response
     */
    public function unmarshal(Type $target, $in) {
      foreach ($this->marshallers->keys() as $type) {
        if ($type->isAssignableFrom($target)) return $this->marshallers[$type]->unmarshal($in);
      }
      return $in;
    }
  }
?>