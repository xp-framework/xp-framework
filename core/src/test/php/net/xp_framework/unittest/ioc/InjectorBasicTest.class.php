<?php
/* This class is part of the XP framework
 *
 * $Id: InjectorBasicTest.class.php 2997 2011-02-13 09:40:31Z mikey $
 */

  uses(
    'unittest.TestCase',
    'ioc.Binder',
    'net.xp_framework.unittest.ioc.helper.ImplicitDependencyBug102',
    'net.xp_framework.unittest.ioc.helper.ImplicitOptionalDependency',
    'net.xp_framework.unittest.ioc.helper.Tire',
    'net.xp_framework.unittest.ioc.helper.Vehicle'
  );

  /**
   * Unittest
   */
  class InjectorBasicTest extends TestCase {

    /**
     * test constructor injections
     */
    #[@test]
    public function constructorInjection() {
      $binder = new Binder();
      $binder->bind('net.xp_framework.unittest.ioc.helper.Tire')
             ->to('net.xp_framework.unittest.ioc.helper.Goodyear');
      $binder->bind('net.xp_framework.unittest.ioc.helper.Vehicle')
             ->to('net.xp_framework.unittest.ioc.helper.Car');

      $injector = $binder->getInjector();
      $this->assertTrue($injector->hasBinding('net.xp_framework.unittest.ioc.helper.Vehicle'));
      $this->assertTrue($injector->hasBinding('net.xp_framework.unittest.ioc.helper.Tire'));

      $vehicle = $injector->getInstance('net.xp_framework.unittest.ioc.helper.Vehicle');

      $this->assertInstanceOf('Vehicle', $vehicle);
      $this->assertInstanceOf('Car', $vehicle);
      $this->assertInstanceOf('Tire', $vehicle->tire);
      $this->assertInstanceOf('Goodyear', $vehicle->tire);
    }

    /**
     * test setter injections
     */
    #[@test]
    public function setterInjection() {
      $binder = new Binder();
      $binder->bind('net.xp_framework.unittest.ioc.helper.Tire')
             ->to('net.xp_framework.unittest.ioc.helper.Goodyear');
      $binder->bind('net.xp_framework.unittest.ioc.helper.Vehicle')
             ->to('net.xp_framework.unittest.ioc.helper.Bike');

      $injector = $binder->getInjector();

      $this->assertTrue($injector->hasBinding('net.xp_framework.unittest.ioc.helper.Vehicle'));
      $this->assertTrue($injector->hasBinding('net.xp_framework.unittest.ioc.helper.Tire'));

      $vehicle = $injector->getInstance('net.xp_framework.unittest.ioc.helper.Vehicle');

      $this->assertInstanceOf('Vehicle', $vehicle);
      $this->assertInstanceOf('Bike', $vehicle);
      $this->assertInstanceOf('Tire', $vehicle->tire);
      $this->assertInstanceOf('Goodyear', $vehicle->tire);
    }

    /**
     * test setter injections while passing stubReflectionClass instances
     * instead of class names
     */
    #[@test]
    public function setterInjectionWithClass() {
      $binder = new Binder();
      $binder->bind('net.xp_framework.unittest.ioc.helper.Tire')
             ->to(XPClass::forName('net.xp_framework.unittest.ioc.helper.Goodyear'));
      $binder->bind('net.xp_framework.unittest.ioc.helper.Vehicle')
             ->to(XPClass::forName('net.xp_framework.unittest.ioc.helper.Bike'));

      $injector = $binder->getInjector();

      $this->assertTrue($injector->hasBinding('net.xp_framework.unittest.ioc.helper.Vehicle'));
      $this->assertTrue($injector->hasBinding('net.xp_framework.unittest.ioc.helper.Tire'));

      $vehicle = $injector->getInstance('net.xp_framework.unittest.ioc.helper.Vehicle');

      $this->assertInstanceOf('Vehicle', $vehicle);
      $this->assertInstanceOf('Bike', $vehicle);
      $this->assertInstanceOf('Tire', $vehicle->tire);
      $this->assertInstanceOf('Goodyear', $vehicle->tire);
    }

    /**
     * test bindings to an invalid type
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function setterInjectionWithInvalidArgument() {
      $binder = new Binder();
      $binder->bind('net.xp_framework.unittest.ioc.helper.Vehicle')->to(313);
    }

    /**
     * test bindings to an instance
     */
    #[@test]
    public function setterInjectionByInstance() {
      $tire = new Goodyear();

      $binder = new Binder();
      $binder->bind('net.xp_framework.unittest.ioc.helper.Tire')
             ->toInstance($tire);
      $binder->bind('net.xp_framework.unittest.ioc.helper.Vehicle')
             ->to('net.xp_framework.unittest.ioc.helper.Bike');

      $injector = $binder->getInjector();

      $this->assertTrue($injector->hasBinding('net.xp_framework.unittest.ioc.helper.Vehicle'));
      $this->assertTrue($injector->hasBinding('net.xp_framework.unittest.ioc.helper.Tire'));

      $vehicle = $injector->getInstance('net.xp_framework.unittest.ioc.helper.Vehicle');

      $this->assertInstanceOf('Vehicle', $vehicle);
      $this->assertInstanceOf('Bike', $vehicle);
      $this->assertInstanceOf('Tire', $vehicle->tire);
      $this->assertInstanceOf('Goodyear', $vehicle->tire);
      $this->assertEquals($vehicle->tire, $tire);
    }

    /**
     * test bindings to an instance with an invalid type
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function setterInjectionByInvalidInstance() {
      $tire = new Goodyear();
      $binder = new Binder();
      $binder->bind('net.xp_framework.unittest.ioc.helper.Vehicle')->toInstance($tire);
    }

    /**
     * test setter injections
     */
    #[@test]
    public function optionalSetterInjection() {
      $tire = new Goodyear();

      $binder = new Binder();
      $binder->bind('net.xp_framework.unittest.ioc.helper.Tire')
             ->to('net.xp_framework.unittest.ioc.helper.Goodyear');
      $binder->bind('net.xp_framework.unittest.ioc.helper.Vehicle')
             ->to('net.xp_framework.unittest.ioc.helper.Convertible');

      $injector = $binder->getInjector();

      $vehicle = $injector->getInstance('net.xp_framework.unittest.ioc.helper.Vehicle');

      $this->assertInstanceOf('Vehicle', $vehicle);
      $this->assertInstanceOf('Convertible', $vehicle);

      $this->assertNull($vehicle->roof);
    }

    /**
     * test implicit bindings
     */
    #[@test]
    public function implicitBinding() {
      $injector = new Injector();
      $this->assertFalse($injector->hasExplicitBinding('net.xp_framework.unittest.ioc.helper.Goodyear'));
      $goodyear = $injector->getInstance('net.xp_framework.unittest.ioc.helper.Goodyear');
      $this->assertInstanceOf('Goodyear', $goodyear);
      $this->assertTrue($injector->hasExplicitBinding('net.xp_framework.unittest.ioc.helper.Goodyear'));
    }

    /**
     * test implicit bindings as a dependency
     */
    #[@test]
    public function implicitBindingAsDependency() {
      $injector = new Injector();
      $this->assertFalse($injector->hasExplicitBinding('net.xp_framework.unittest.ioc.helper.ImplicitDependency'));
      $obj      = $injector->getInstance('net.xp_framework.unittest.ioc.helper.ImplicitDependency');
      $this->assertInstanceOf('net.xp_framework.unittest.ioc.helper.ImplicitDependency', $obj);
      $this->assertInstanceOf('Goodyear', $obj->getGoodyearByConstructor());
      $this->assertInstanceOf('Goodyear', $obj->getGoodyearBySetter());
      $this->assertTrue($injector->hasExplicitBinding('net.xp_framework.unittest.ioc.helper.ImplicitDependency'));
    }

    /**
     * test method for original stubbles bug #102
     */
    #[@test]
    public function bug102() {
      $obj      = new ImplicitDependencyBug102();
      $injector = new Injector();
      $injector->handleInjections($obj);
      $this->assertInstanceOf('Goodyear', $obj->getGoodyearBySetter());
    }

    /**
     * optional implicit dependency will not be set
     */
    #[@test]
    public function optionalImplicitDependencyWillNotBeSet() {
      $obj      = new ImplicitOptionalDependency();
      $binder   = new Binder();
      $injector = $binder->getInjector();
      $injector->handleInjections($obj);
      $this->assertNull($obj->getGoodyearBySetter());

      $binder->bind('net.xp_framework.unittest.ioc.helper.Goodyear')
             ->to('net.xp_framework.unittest.ioc.helper.Goodyear');
      $obj = new ImplicitOptionalDependency();
      $injector->handleInjections($obj);
      $this->assertInstanceOf('Goodyear', $obj->getGoodyearBySetter());
    }

    /**
     * requesting a missing binding throws a binding exception
     */
    #[@test, @expect('ioc.BindingException')]
    public function missingBindingThrowsBindingException() {
      $injector = new Injector();
      $injector->getInstance('net.xp_framework.unittest.ioc.helper.Vehicle');
    }

    /**
     * requesting a missing binding throws a binding exception
     */
    #[@test, @expect('ioc.BindingException')]
    public function missingBindingOnInjectionHandlingThrowsBindingException() {
      $injector = new Injector();
      $class    = new Bike();
      $injector->handleInjections($class);
    }

    /**
     * added binding should be returned
     */
    #[@test]
    public function addBindingReturnsAddedBinding() {
      $injector    = new Injector();
      $mockBinding = newinstance('ioc.Binding', array(), '{
    public function named($name) { }

    public function getInstance($type, $name) { }

    public function getKey() { }
  }');
      $this->assertEquals($mockBinding, $injector->addBinding($mockBinding));
    }
  }
?>