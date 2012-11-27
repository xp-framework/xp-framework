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
      return Response::status(HttpConstants::STATUS_BAD_REQUEST)->withPayload(array('message' => $t->getMessage()));
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

    protected function injectionArgs($routine) {
      if ($routine->numParameters() < 1) return array();

      $inject= $routine->getAnnotation('inject');
      $type= isset($inject['type']) ? $inject['type'] : $routine->getParameter(0)->getTypeName();
      switch ($type) {
        case 'util.log.LogCategory': 
          $args= array($this->cat);
          break;

        case 'webservices.rest.srv.RestContext':
          $args= array($this);
          break;

        default:
          throw new IllegalStateException('Unkown injection type '.$type);
      }

      return $args;
    }

    public function handlerInstanceFor($class) {

      // Constructor injection
      if ($class->hasConstructor()) {
        $c= $class->getConstructor();
        $instance= $c->newInstance($c->hasAnnotation('inject') ? $this->injectionArgs($c) : array());
      } else {
        $instance= $class->newInstance();
      }

      // Method injection
      foreach ($class->getMethods() as $m) {
        if ($m->hasAnnotation('inject')) $m->invoke($instance, $this->injectionArgs($m));
      }
      return $instance;
    }

    /**
     * Handle routing item
     *
     * @param  lang.Oject instance
     * @param  lang.reflect.Method method
     * @param  var[] args
     * @param  webservices.rest.srv.RestContext context
     * @return webservices.rest.srv.Response
     */
    public function handle($instance, $method, $args) {
      $this->cat && $this->cat->debug('->', $target);

      foreach ($args as $i => $arg) {
        $args[$i]= $this->unmarshal(typeof($arg), $arg);
      }

      // Instantiate the handler class and invoke method
      try {
        $result= $method->invoke($instance, $args);
        $this->cat && $this->cat->debug('<-', $result);
      } catch (TargetInvocationException $t) {
        $this->cat && $this->cat->warn('<-', $t);
        return $this->mapException($t->getCause());
      }

      // For "VOID" methods, set status to "no content". If a response is returned, 
      // use its status, headers and payload. For any other methods, set status to "OK".
      if (Type::$VOID->equals($method->getReturnType())) {
        return Response::status(HttpConstants::STATUS_NO_CONTENT);
      } else if ($result instanceof Response) {
        $result->payload= $this->marshal($result->payload);
        return $result;
      } else {
        return Response::status(HttpConstants::STATUS_OK)->withPayload($this->marshal($result));
      }
    }

    public function equals($cmp) {
      return (
        $cmp instanceof self && 
        $this->marshallers->equals($cmp->marshallers) && 
        $this->mappers->equals($cmp->mappers)
      );
    }
  }
?>