<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'peer.http.HttpConstants',
    'scriptlet.Preference',
    'webservices.rest.TypeMarshaller',
    'webservices.rest.srv.Response',
    'webservices.rest.srv.RestFormat',
    'webservices.rest.srv.RestParamSource',
    'webservices.rest.srv.ExceptionMapper',
    'webservices.rest.srv.ParamReader',
    'webservices.rest.srv.DefaultExceptionMapper',
    'util.collections.HashTable',
    'util.PropertyManager',
    'util.log.Traceable'
  );

  /**
   * The context of a rest call
   *
   * @test  xp://net.xp_framework.unittest.webservices.rest.srv.RestContextTest
   */
  class RestContext extends Object implements Traceable {
    protected $mappers;
    protected $marshallers;
    protected $cat= NULL;

    static function __static() {
      xp::extensions(__CLASS__, __CLASS__);
    }

    /**
     * Constructor
     */
    public function __construct() {
      $this->mappers= create('new HashTable<XPClass, ExceptionMapper>');
      $this->marshallers= create('new HashTable<Type, TypeMarshaller>');

      // Default exception mappings
      $this->addExceptionMapping('lang.IllegalAccessException', new DefaultExceptionMapper(403));
      $this->addExceptionMapping('lang.IllegalArgumentException', new DefaultExceptionMapper(400));
      $this->addExceptionMapping('lang.IllegalStateException', new DefaultExceptionMapper(409));
      $this->addExceptionMapping('lang.ElementNotFoundException', new DefaultExceptionMapper(404));
      $this->addExceptionMapping('lang.MethodNotImplementedException', new DefaultExceptionMapper(501));
      $this->addExceptionMapping('lang.FormatException', new DefaultExceptionMapper(422));
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
      static $properties= array('name' => 'exception');   // XML root node

      // See if we can find an exception mapper
      foreach ($this->mappers->keys() as $type) {
        if (!$type->isInstance($t)) continue;
        $r= $this->mappers[$type]->asResponse($t);
        $r->payload->properties= $properties;
        return $r;
      }

      // Default: Use error 500 ("Internal Server Error") and the exception message
      return Response::error(HttpConstants::STATUS_INTERNAL_SERVER_ERROR)
        ->withPayload(new Payload(array('message' => $t->getMessage()), $properties))
      ;
    }

    /**
     * Marshal a type
     *
     * @param  webservices.rest.Payload payload
     * @return webservices.rest.Payload
     */
    public function marshal(Payload $payload= NULL, $properties= array()) {
      if (NULL === $payload) return NULL;

      foreach ($this->marshallers->keys() as $type) {
        if (!$type->isInstance($payload->value)) continue;

        $payload->value= $this->marshallers[$type]->marshal($payload->value);
        break;
      }
      return NULL === $payload->value ? NULL : new Payload($payload->value, $properties);
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

    /**
     * Returns arguments used for injection 
     *
     * @param  lang.reflect.Routine routine
     * @return var[] args
     */
    protected function injectionArgs($routine) {
      if ($routine->numParameters() < 1) return array();

      $inject= $routine->getAnnotation('inject');
      $type= isset($inject['type']) ? $inject['type'] : $routine->getParameter(0)->getTypeName();
      switch ($type) {
        case 'util.log.LogCategory': 
          $args= array($this->cat);
          break;

        case 'util.Properties': 
          $args= array(PropertyManager::getInstance()->getProperties($inject['name']));
          break;

        case 'webservices.rest.srv.RestContext':
          $args= array($this);
          break;

        default:
          throw new IllegalStateException('Unkown injection type '.$type);
      }

      return $args;
    }

    /**
     * Creates a handler instance
     *
     * @param  lang.XPClass class
     * @return lang.Generic instance
     * @throws lang.reflect.TargetInvocationException If the constructor or routines used for injection raise an exception
     */
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
      foreach ($args as $i => $arg) {
        $args[$i]= $this->unmarshal(typeof($arg), $arg);
      }

      // HACK: Ungeneric XML-related
      $properties= array();
      if ($method->hasAnnotation('xmlfactory', 'element')) {
        $properties['name']= $method->getAnnotation('xmlfactory', 'element');
      } else if (($class= $method->getDeclaringClass()) && $class->hasAnnotation('xmlfactory', 'element')) {
        $properties['name']= $class->getAnnotation('xmlfactory', 'element');
      }

      // Invoke the method
      try {
        $result= $method->invoke($instance, $args);
        $this->cat && $this->cat->debug('<-', $result);
      } catch (TargetInvocationException $e) {
        $this->cat && $this->cat->warn('<-', $e);
        return $this->mapException($e->getCause());
      }

      // For "VOID" methods, set status to "no content". If a response is returned, 
      // use its status, headers and payload. For any other methods, set status to "OK".
      if (Type::$VOID->equals($method->getReturnType())) {
        return Response::status(HttpConstants::STATUS_NO_CONTENT);
      } else if ($result instanceof Response) {
        $result->payload= $this->marshal($result->payload, $properties);
        return $result;
      } else {
        return Response::status(HttpConstants::STATUS_OK)
          ->withPayload($this->marshal(new Payload($result), $properties))
        ;
      }
    }

    /**
     * Read arguments from request
     *
     * @param  [:var] target
     * @param  scriptlet.Request request
     * @return var[] args
     */
    public function argumentsFor($target, $request) {
      $args= array();
      foreach ($target['target']->getParameters() as $parameter) {
        $param= $parameter->getName();

        // Extract arguments according to definition. In case we don't have an explicit
        // source for an argument, look up according to the following rules:
        //
        // * If we have a segment named exactly like the parameter, use it
        // * If there is no incoming payload, check the parameters
        // * If there is an incoming payload, use that.
        //
        // Handle explicitely configured sources first.
        if (isset($target['params'][$param])) {
          $src= $target['params'][$param];
        } else if (isset($target['segments'][$param])) {
          $src= new RestParamSource($param, ParamReader::$PATH);
        } else if (NULL === $target['input']) {
          $src= new RestParamSource($param, ParamReader::$PARAM);
        } else {
          $src= new RestParamSource(NULL, ParamReader::$BODY);
        }

        if (NULL === ($arg= $src->reader->read($src->name, $parameter->getType(), $target, $request))) {
          if ($parameter->isOptional()) {
            $arg= $src->reader->convert($parameter->getType(), $parameter->getDefaultValue());
          } else {
            throw new IllegalArgumentException('Parameter "'.$param.'" required but found in '.$src->toString());
          }
        }
        $args[]= $arg;
      }
      return $args;
    }

    /**
     * Process a request
     *
     * @param   [:var] target
     * @param   scriptlet.Request request
     * @param   scriptlet.Response response
     * @return  bool
     */
    public function process($target, $request, $response) {

      // Invoke handler
      try {
        $this->cat && $this->cat->debug('->', $target);
        $result= $this->handle(
          $this->handlerInstanceFor($target['target']->getDeclaringClass()),
          $target['target'],
          $this->argumentsFor($target, $request)
        );
      } catch (TargetInvocationException $e) {
        $this->cat && $this->cat->error('<-', $e);
        $result= $this->mapException($e->getCause());
      } catch (Throwable $t) {                         // Marshalling, parameters, instantiation
        $this->cat && $this->cat->error('<-', $t);
        $result= $this->mapException($t);
      }

      // Have a result
      $response->setStatus($result->status);
      foreach ($result->headers as $name => $value) {
        if ('Location' === $name) {
          $url= clone $request->getURL();
          $response->setHeader($name, $url->setPath($value)->getURL());
        } else {
          $response->setHeader($name, $value);
        }
      }
      foreach ($result->cookies as $cookie) {
        $response->setCookie($cookie);
      }
      if (NULL !== $result->payload) {
        $response->setContentType($target['output']);
        RestFormat::forMediaType($target['output'])->write($response, $result->payload);
      }

      // Handled
      return TRUE;
    }

    /**
     * Set a log category for tracing
     *
     * @param  util.log.LogCategory cat
     */
    public function setTrace($cat) {
      $this->cat= $cat;
    }

    /**
     * Returns whether a given value is equal to this context instance
     *
     * @param  var cmp
     * @return bool
     */
    public function equals($cmp) {
      return (
        $cmp instanceof self && 
        $this->marshallers->equals($cmp->marshallers) && 
        $this->mappers->equals($cmp->mappers)
      );
    }
  }
?>