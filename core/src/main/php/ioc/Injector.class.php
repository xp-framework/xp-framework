<?php
/* This class is part of the XP framework
 *
 * $Id: Injector.class.php 3083 2011-03-09 13:14:01Z mikey $
 */

  uses(
    'ioc.Binding',
    'ioc.BindingException',
    'ioc.BindingScope',
    'ioc.BindingScopes',
    'ioc.ClassBinding',
    'ioc.ConstantBinding'
  );

  /**
   * Simple injection provider for a single predefined value.
   */
  class Injector extends Object {
    protected
      $scopes   = NULL,
      $bindings = array();

    private $bindingIndex = array();

    /**
     * constructor
     *
     * @param  ioc.BindingScopes  $scopes  optional
     */
    public function __construct(BindingScopes $scopes = NULL) {
      if (NULL === $scopes) {
        $this->scopes = new BindingScopes();
      } else {
        $this->scopes = $scopes;
      }
    }

    /**
     * sets session to be used with the session scope
     *
     * @param   ioc.BindingScope $sessionScope
     * @return  ioc.Injector
     */
    public function setSessionScope(BindingScope $sessionScope) {
      $this->scopes->setSessionScope($sessionScope);
      return $this;
    }

    /**
     * adds a new binding to the injector
     *
     * @param   ioc.Binding  $binding
     * @return  ioc.Binding
     */
    public function addBinding(Binding $binding) {
      $this->bindings[] = $binding;
      return $binding;
    }

    /**
     * creates and adds a class binding
     *
     * @param   string            $interface
     * @return  ioc.ClassBinding
     */
    public function bind($interface) {
      return $this->addBinding(new ClassBinding($this,
                                                $interface,
                                                $this->scopes
                               )
             );
    }

    /**
     * creates and adds a constanct binding
     *
     * @param   string  $name
     * @return  ioc.ConstantBinding
     */
    public function bindNamedConstant($name) {
      return $this->addBinding(new ConstantBinding($this, $name));
    }

    /**
     * check whether a binding for a type is available (explicit and implicit)
     *
     * @param   string   $type
     * @param   string   $name
     * @return  boolean
     */
    public function hasBinding($type, $name = NULL) {
      return ($this->getBinding($type, $name) != NULL);
    }

    /**
     * check whether an excplicit binding for a type is available
     *
     * Be aware that implicit bindings turn into explicit bindings when
     * hasBinding() or getInstance() are called.
     *
     * @param   string   $type
     * @param   string   $name
     * @return  boolean
     */
    public function hasExplicitBinding($type, $name = NULL) {
      $bindingIndex = $this->getIndex();
      if (NULL !== $name) {
        if (isset($bindingIndex[$type . '#' . $name])) {
          return TRUE;
        }
      }

      return isset($bindingIndex[$type]);
    }

    /**
     * get an instance
     *
     * @param   string  $type
     * @param   string  $name
     * @return  object
     * @throws  ioc.BindingException
     */
    public function getInstance($type, $name = NULL) {
      $binding = $this->getBinding($type, $name);
      if (NULL === $binding) {
        throw new BindingException('No binding for ' . $type . ' defined');
      }

      return $binding->getInstance($type, $name);
    }

    /**
     * check whether a constant is available
     *
     * There is no need to distinguish between explicit and implicit binding for
     * constant bindings as there are only explicit constant bindings and never
     * implicit ones.
     *
     * @param   string  $name  name of constant to check for
     * @return  bool
     */
    public function hasConstant($name) {
      return $this->hasBinding(ConstantBinding::TYPE, $name);
    }

    /**
     * returns constanct value
     *
     * @param   string  $name  name of constant value to retrieve
     * @return  scalar
     */
    public function getConstant($name) {
      return $this->getInstance(ConstantBinding::TYPE, $name);
    }

    /**
     * returns the binding for a name and type
     *
     * @param   string       $type
     * @param   string       $name
     * @return  ioc.Binding
     */
    protected function getBinding($type, $name = NULL) {
      $bindingIndex = $this->getIndex();
      if (NULL !== $name) {
        if (isset($bindingIndex[$type . '#' . $name])) {
          return $bindingIndex[$type . '#' . $name];
        }
      }

      if (isset($bindingIndex[$type])) {
        return $bindingIndex[$type];
      }

      // prevent illegal access to reflection class for constant type
      if (ConstantBinding::TYPE === $type) {
        return NULL;
      }

      // check for default implementation
      $typeClass = XPClass::forName($type);
      if ($typeClass->hasAnnotation('implementedBy')) {
        return $this->bind($type)
                    ->to(XPClass::forName($typeClass->getAnnotation('implementedBy')));
      } elseif ($typeClass->hasAnnotation('providedBy')) {
        return $this->bind($type)
                    ->toProviderClass(XPClass::forName($typeClass->getAnnotation('providedBy')));
      }

      // try implicit binding
      if (!$typeClass->isInterface()) {
          return $this->bind($type)
                      ->to($typeClass);
      }

      return NULL;
    }

    /**
     * returns the binding index
     *
     * @return  array<string,ioc.Binding>
     */
    protected function getIndex() {
      if (empty($this->bindings)) {
        return $this->bindingIndex;
      }

      foreach ($this->bindings as $binding) {
        $this->bindingIndex[$binding->getKey()] = $binding;
      }

      $this->bindings = array();
      return $this->bindingIndex;
    }

    /**
     * handle injections for given instance
     *
     * @param   lang.Generic  $instance
     * @param   lang.XPClass  $class     optional
     * @throws  ioc.BindingException
     */
    public function handleInjections(Generic $instance, XPClass $class = NULL) {
      if (NULL === $class) {
        $class = $instance->getClass();
      }

      foreach ($class->getMethods() as $method) {
        if (!($method->getModifiers() & MODIFIER_PUBLIC) || !$method->hasAnnotation('inject')) {
          continue;
        }

        try {
          $paramValues = $this->getInjectionValuesForMethod($method, $class);
        } catch (BindingException $be) {
          if (!$method->hasAnnotation('inject', 'optional')) {
            throw $be;
          }

          continue;
        }

        $method->invoke($instance, $paramValues);
      }
    }

    /**
     * returns a list of all injection values for given method
     *
     * @param   lang.reflect.Routine     $method
     * @param   lang.XPClass  $class
     * @return  array<mixed>
     * @throws  ioc.BindingException
     */
    public function getInjectionValuesForMethod(Routine $method, XPClass $class) {
      $paramValues = array();
      $namedMethod = (($method->hasAnnotation('named')) ? ($method->getAnnotation('named')) : (NULL));
      foreach ($method->getParameters() as $param) {
        $paramClass = $param->getTypeRestriction();
        $type       = (($paramClass instanceof XPClass) ? ($paramClass->getName()) : (ConstantBinding::TYPE));
        # XP does not support annotations for parameters
        #$name       = (($param->hasAnnotation('named')) ? ($param->getAnnotation('named', 'name')) : ($namedMethod));
        $name       = $namedMethod;
        if (!$this->hasExplicitBinding($type, $name) && $method->hasAnnotation('inject', 'optional')) {
          // Somewhat hackish... throwing an exception here which is catched and ignored in handleInjections()
          throw new BindingException('Could not find explicit binding for optional injection of type ' . $this->createTypeMessage($type, $name) . ' to complete  ' . $this->createCalledMethodMessage($class, $method, $param, $type));
        }

        if (!$this->hasBinding($type, $name)) {
          $typeMsg = $this->createTypeMessage($type, $name);
          throw new BindingException('Can not inject into ' . $this->createCalledMethodMessage($class, $method, $param, $type)  . '. No binding for type ' . $typeMsg . ' specified.');
        }

        $paramValues[] = $this->getInstance($type, $name);
      }

      return $paramValues;
    }

    /**
     * creates the complete type message
     *
     * @param   string  $type  type to create message for
     * @param   string  $name  name of named parameter
     * @return  string
     */
    protected function createTypeMessage($type, $name) {
      return ((NULL !== $name) ? ($type . ' (named "' . $name . '")') : ($type));
    }

    /**
     * creates the called method message
     *
     * @param   lang.XPClass  $class
     * @param   lang.reflect.Routine     $method
     * @param   lang.reflect.Parameter  $parameter
     * @param   string                   $type
     * @return  string
     */
    protected function createCalledMethodMessage(XPClass $class, Routine $method, $parameter, $type) {
      $message = $class->getName() . '::' . $method->getName() . '(';
      if (ConstantBinding::TYPE !== $type) {
        $message .= $type . ' ';
      } elseif ($parameter->getTypeRestriction() != NULL && $parameter->getTypeRestriction()->getName() === 'array') {
        $message .= 'array ';
      }

      return $message . '$' . $parameter->getName() . ')';
    }
  }
?>