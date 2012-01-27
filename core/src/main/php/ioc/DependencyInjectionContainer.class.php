<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'lang.reflect.Routine',
    'ioc.DependencyInjectionException'
  );

  /**
   * Dependency Injector container
   *
   * Typical usage consists of 2 phases:
   *
   * 1. Register bindings: The container can hold 2 types of bindings:
   *    -> Type bindings:
   *       - If the seconds parameter is a FQCN string or an XPClass, a new instance will be created whenever the
   *         reference must be resolved
   *       - If the seconds paramter is an lang.Object instance, it will be returned every time the reference must
   *          be resolved (shared/singleton)
   *
   *       <code>
   *         // The next 2 lines are doing the same thing
   *         $c->bindType('util.Observable', 'my.project.ObservableImpl');
   *         $c->bindType(XPClass::forName('util.Observable'), XPClass::forName('my.project.ObservableImpl'));
   *
   *         // Shared instance
   *         $c->bindType('util.Observable', new ObservableImpl());
   *       </code>
   *
   *    -> Constant bindings:
   *       - Constant values are not checked for a type; they can be NULL, scalars or Objects
   *
   *       <code>
   *         $c->bindConstant('my.project.settings.username', 'root');
   *         $c->bindConstant('my.project.settings.timeout', 5.50);
   *       </code>
   *
   * 2. Ask container to create instances. The container uses 3 types of injections:
   *    -> Constructor injection:
   *       - This type of injections are always required; an exception is thrown when the constructor arguments
   *         cannot be resolved
   *       - @inject annotation is not required
   *
   *       <code>
   *         public class MyProcess {
   *           public class __construct(Observable $obs) { ... }
   *         }
   *         $p= $c->resolve('my.project.MyProcess');
   *       </code>
   *
   *    -> Class fields injection:
   *       - @inject annotation is required to tell apart what fields to try to inject and what fields to leave alone
   *       - Since class fields cannot be type-hinted, only constants can be injected
   *       - Class fields injections are optional; is if a constant referenced in the @inject annotation was not
   *         bound via bindConstant(), it will be silently ignored
   *       - Both public and protected class fields can be injected
   *
   *       <code>
   *         public class MyConnection {
   *           #[@inject(constant= 'my.project.settings.timeout')]
   *           protected $timeout= 1.00;
   *         }
   *         $conn= $c->resolve('my.project.MyConnection');
   *       </code>
   *
   *    -> Setter injection:
   *         @inject annotation is required to tell apart what methods to call and what methods to leave alone
   *       - Setter injections are optional (same as class field injections)
   *       - Both Constants and Types can be injected; when a Constant is injected, the setter must have
   *         exactly 1 argument (where the Constant value will be injected)
   *
   *       <code>
   *         public class MyConnection {
   *           #[@inject]
   *           public function setTrace(LogCategory $cat) { ... }
   *
   *           #[@inject(constant= 'my.project.settings.timeout')]
   *           public function setTimeout($timeout) { ... }
   *         }
   *         $conn= $c->resolve('my.project.MyConnection');
   *       </code>
   *
   *
   * About injection context:
   *
   * bindType(), bindConstant() and resolve() have a last optional argument called $context (a string value),
   * or better knows as Named Bindings. The counterpart is the @inject(context='my-context') annnotation:
   *
   * Suppose we have an Interface IWeapon and 2 implementations Sword and Bow and the following bindings:
   *
   * <code>
   *   // Bind the same type (IWeapon) to 2 different implementations (Sword and Bow) but with different contexts
   *   $c->bindType('IWeapon', 'Sword', 'melee');
   *   $c->bindType('IWeapon', 'Bow', 'ranged');
   * </code>
   *
   * Classes where IWeapon will be injected:
   *
   * <code>
   *   class Infantry {
   *     #[@inject(context= 'melee')]
   *     public function __construct(IWeapon $weapon) { ... }
   *   }
   *
   *   class Archer {
   *     #[@inject(context= 'ranged')]
   *     public function __construct(IWeapon $weapon) { ... }
   *   }
   * </code>
   *
   * Whan we ask the container for an instance of Infantry it will call it's constructor with a Sword instance. Same
   * logic applies to the Archer class, where Bow instances will be injected to its constructor:
   *
   * <code>
   *   $infantry = $c->resolve('Infantry');
   *   $archer   = $c->resolve('Archer');
   * </code>
   *
   */
  class DependencyInjectionContainer extends Object {
    protected
      $typeBindings     = array(),
      $constantBindings = array();

    /**
     * Add a new Type binding to this container
     *
     * @param  var $source either a FQCN string or a lang.XPClass
     * @param  var $destination a lang.Object for a shared binding, a FQCN string or lang.XPClass otherwise
     * @param  string $context default '*'
     * @return void
     * @throws lang.IllegalArgumentException when an invalid value was provided for $source or $destination
     * @throws lang.ClassNotFoundException when an invalid FQCN was provided as $source or $destination
     */
    public function bindType($source, $destination, $context= '*') {

      // Convert $source from string to XPClass
      if (is_string($source)) {
        $source= XPClass::forName($source);
      } else {

        // Check $source is an XPClass
        if (!$source instanceof XPClass) {
          throw new IllegalArgumentException(
            'Invalid value passed for Type Binding $source: not a string, nor an XPClass'
          );
        }
      }

      // Convert $destination from string to XPClass
      if (is_string($destination)) {
        $destination= XPClass::forName($destination);
      } else {

        // Check $destination is an XPClass or an Object
        if (!$destination instanceof XPClass && !$destination instanceof Object) {
          throw new IllegalArgumentException(
            'Invalid value passed for Type Binding $destination: not an XPClass, nor an Object'
          );
        }
      }

      // Add Type Binding
      if (!isset($this->typeBindings[$source->getName()])) {
        $this->typeBindings[$source->getName()]= array();
      }
      $this->typeBindings[$source->getName()][$context]= $destination;
    }

    /**
     * Add a new Constant binding to this container
     *
     * @param  string $name
     * @param  var $value
     * @param  string $context default '*'
     * @return void
     */
    public function bindConstant($name, $value, $context= '*') {

      // Check $name is a string
      if (!is_string($name)) {
        throw new IllegalArgumentException(
          'Invalid value passed for Constant Binding $name: not a string'
        );
      }

      // Check $name is empty
      if ('' === ($name= trim($name))) {
        throw new IllegalArgumentException(
          'Invalid value passed for Constant Binding $name: empty string'
        );
      }

      // Add Constant Binding
      if (!isset($this->constantBindings[$name])) {
        $this->constantBindings[$name]= array();
      }
      $this->constantBindings[$name][$context]= $value;
    }

    /**
     * Factory: create Instances using the provided Type and Constant Bindings
     *
     * @param  var $ref either a FQCN string or a lang.XPClass
     * @param  string $context
     * @return var
     * @throws lang.IllegalArgumentException when $ref is neither a string nor a lang.XPClass
     * @throws ioc.DependencyInjectionException when cannot create the specified instance
     */
    public function resolve($ref, $context= '*') {

      // Convert $ref to string
      if (!is_string($ref)) {

        // If $ref is not a string, then it must be a lang.XPClass
        if (!$ref instanceof XPClass) {
          throw new IllegalArgumentException('Invalid value passed for $ref: not a string, nor an XPClass');
        }

        $ref= $ref->getName();
      }

      // $ref points to a Type Binding
      if (isset($this->typeBindings[$ref][$context])) {
        $binding= $this->typeBindings[$ref][$context];

        // $ref points to a shared Type Binding; just return the provided shared instance
        if (!$binding instanceof XPClass) {
          return $binding;
        }

      // There is no Type Binding registered for this $ref
      } else {

        // Check $ref is a valid FQCN
        try {
          $binding= XPClass::forName($ref);
        } catch (ClassNotFoundException $ex) {
          throw new IllegalArgumentException('Cannot resolve undefined Class ['.$ref.']', $ex);
        }

        // Cannot resolve unbound Interfaces
        if ($binding->isInterface()) {
          throw new DependencyInjectionException('Cannot resolve unbound Interface ['.$ref.']');
        }

        // Cannot resolve unbound abstract Classes
        if ($binding->getModifiers() & MODIFIER_ABSTRACT) {
          throw new DependencyInjectionException('Cannot resolve unbound abstract Class ['.$ref.']');
        }
      }

      // 1. Create instance

      // Binding has no constructor; let XPClass create the instance
      if (!$binding->hasConstructor()) {
        $retVal= $binding->newInstance();

      // Resolve parameters and invoke constructor
      } else {
        $params= $this->resolveRoutineParameters($binding->getConstructor());
        $retVal= call_user_func_array(array($binding, 'newInstance'), $params);
      }

      // 2. Process bindings via class fields (can only be injected via constants)
      //  #[@inject(constant= 'username')]
      //  protected $username= NULL;
      foreach ($binding->getFields() as $field) {
        if (!$field->hasAnnotation('inject', 'constant')) continue;
        $constantName= $field->getAnnotation('inject', 'constant');

        // Bindings via class fields are optional
        // If the constant is not defined, quietly ignore it
        if (!isset($this->constantBindings[$constantName][$context])) {
          continue;
        }

        // Set field value (allow protected fields to receive injections)
        $retVal->{"\7".$field->getName()}= $this->constantBindings[$constantName][$context];
      }

      // 3. Process Type/Constant Bindings via setters
      //  #[@inject]
      //  public function setTrace(LogCategory $cat) { ... }
      //
      //  #[@inject(constant= 'username')]
      //  public function setUsername($username) { ... }
      foreach ($binding->getMethods() as $method) {
        if (!$method->hasAnnotation('inject')) continue;

        // Try to resolve setter argument(s)
        try {
          $params= $this->resolveRoutineParameters($method);

        // Bindings via setters are optional
        // If parameter(s) cannot be resolved, quietly ignore it
        } catch (DependencyInjectionException $ex) {
          continue;
        }

        // Invoke method
        $method->invoke($retVal, $params);
      }

      // Return resolved instance
      return $retVal;
    }

    /**
     * Resolve routine parameters using bindings defined in this container
     *
     * @param  lang.reflect.Routine $routine
     * @throws ioc.DependencyInjectionException when not all parameters could be resolved
     */
    protected function resolveRoutineParameters(Routine $routine) {
      $retVal= array();

      // Get routine parameters info
      $parameters= $routine->getParameters();
      $pn= count($parameters);

      // Quick exit: routine does not have parameters
      if (0 === $pn) return $retVal;

      // Get inject context; default to generic '*'
      $context= $routine->hasAnnotation('inject', 'context') ? $routine->getAnnotation('inject', 'context') : '*';

      // Inject method constant:
      // #[@inject(constant= 'username')]
      // public function setUsername($username) { ... }
      if ($routine->hasAnnotation('inject', 'constant')) {

        // Routine should only have one parameter
        if (1 !== $pn) {
          throw new DependencyInjectionException(sprintf(
            'Method %s::%s() defines a constant binding and should have just one parameter, %d found',
            $routine->getDeclaringClass()->getName(),
            $routine->getName(),
            $pn
          ));
        }

        // Broken dependency chain: constant not defined
        $constantName= $routine->getAnnotation('inject', 'constant');
        if (!isset($this->constantBindings[$constantName][$context])) {
          throw new DependencyInjectionException(sprintf(
            'Cannot resolve constant binding "%s" defined by %s::%s()',
            $constantName,
            $routine->getDeclaringClass()->getName(),
            $routine->getName()
          ));
        }

        // Return resolved parameter value
        $retVal[]= $this->constantBindings[$constantName][$context];
        return $retVal;
      }

      // Resolve parameters
      for ($pi= 0; $pi < $pn; $pi++) {
        $parameter= $parameters[$pi];

        // Missing parameter type hinting
        if (NULL === ($type= $parameter->getTypeRestriction())) {

          // If parameter is optional, use the default value
          if ($parameter->isOptional()) {
            $retVal[$pi]= $parameter->getDefaultValue();
            continue;
          }

          // Broken dependency chain: missing type hinting
          throw new DependencyInjectionException(sprintf(
            'Missing type hinting for parameter %d ($%s) passed to %s::%s()',
            $pi + 1,
            $parameter->getName(),
            $routine->getDeclaringClass()->getName(),
            $routine->getName()
          ));
        }

        // Try to resolve parameter
        try {
          $retVal[$pi]= $this->resolve($type->getName(), $context);

        // Cannot resolve this parameter
        } catch (IllegalArgumentException $ex) {

          // If parameter is optional, use the default value
          if ($parameter->isOptional()) {
            $retVal[$pi]= $parameter->getDefaultValue();
            continue;
          }

          // Broken dependency chain: cannot resolve binding
          throw new DependencyInjectionException(sprintf(
            'Cannot resolve parameter %d ($%s) passed to  %s::%s()',
            $pi + 1,
            $parameter->getName(),
            $routine->getDeclaringClass()->getName(),
            $routine->getName()
          ), $ex);
        }
      }

      return $retVal;
    }
  }
?>
