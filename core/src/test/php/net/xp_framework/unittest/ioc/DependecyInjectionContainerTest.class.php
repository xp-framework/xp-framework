<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestCase',
    'util.log.LogCategory',
    'ioc.DependecyInjectionContainer',
    'net.xp_framework.unittest.ioc.stub.weapon.Bow',
    'net.xp_framework.unittest.ioc.stub.weapon.Sword',
    'net.xp_framework.unittest.ioc.stub.weapon.Dagger'
  );

  /**
   * Testcase for DependecyInjectionContainer
   *
   * @see xp://ioc.DependecyInjectionContainer
   */
  class DependecyInjectionContainerTest extends TestCase {
    protected $fixture= NULL;

    /**
     * Setup testcase fixture
     *
     */
    public function setUp() {
      $this->fixture= new DependecyInjectionContainer();
    }

    /**
     * Test bindType() with NULL $source
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function cantBindTypeNullSource() {
      $this->fixture->bindType(NULL, new Sword());
    }

    /**
     * Test bindConstant() with NULL $source
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function cantBindConstantNullSource() {
      $this->fixture->bindConstant(NULL, 'value');
    }

    /**
     * Test bindConstant() with non-string $source
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function cantBindConstantNonStringSource() {
      $this->fixture->bindConstant(new Sword(), 'value');
    }

    /**
     * Test bindConstant() with empty $source
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function cantBindConstantEmptySource() {
      $this->fixture->bindConstant('', 'value');
    }

    /**
     * Test bindType() with NULL $destination
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function cantBindTypeNullDestination() {
      $this->fixture->bindType('net.xp_framework.unittest.ioc.stub.IWeapon', NULL);
    }

    /**
     * Test bindType() with Instance $source
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function cantBindTypeInstanceSource() {
      $this->fixture->bindType(new Sword(), new Sword());
    }

    /**
     * Test bindType() with invalid $source string
     *
     */
    #[@test, @expect('lang.ClassNotFoundException')]
    public function cantBindTypeInvalidSource() {
      $this->fixture->bindType('this.is.an.invalid.FQCN', new Sword());
    }

    /**
     * Test bindType() with invalid $destination string
     *
     */
    #[@test, @expect('lang.ClassNotFoundException')]
    public function cantBindTypeInvalidDestination() {
      $this->fixture->bindType('net.xp_framework.unittest.ioc.stub.IWeapon', 'this.is.an.invalid.FQCN');
    }

    /**
     * Test bindType() with non-Object $destination
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function cantBindTypeNonObjectDestination() {
      $this->fixture->bindType('net.xp_framework.unittest.ioc.stub.IWeapon', new stdClass());
    }

    /**
     * Test resolve() with scalar $ref
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function cantResolveScalarRef() {
      $this->fixture->resolve(1);
    }

    /**
     * Test resolve() with invalid $ref
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function cantResolveInvalidRef() {
      $this->fixture->resolve(new stdClass());
    }

    /**
     * Test resolve() should not resolve unbound Interfaces
     *
     */
    #[@test, @expect('ioc.DependecyInjectionException')]
    public function cantResolveUnboundInterfaces() {
      $this->fixture->resolve('util.Observer');
    }

    /**
     * Test resolve() should not resolve unbound Abstract classes
     *
     */
    #[@test, @expect('ioc.DependecyInjectionException')]
    public function cantResolveUnboundAbstractClasses() {
      $this->fixture->resolve('util.cmd.Command');
    }

    /**
     * Test resolve() unbound classes
     *
     */
    #[@test]
    public function resolveUnboundClasses() {
      $this->assertClass(
        $this->fixture->resolve('net.xp_framework.unittest.ioc.stub.weapon.Sword'),
        'net.xp_framework.unittest.ioc.stub.weapon.Sword'
      );
    }

    /**
     * Test resolve() with non-existing $ref
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function cantResolveNonExistingRef() {
      $this->fixture->resolve('this.is.an.invalid.FQCN');
    }

    /**
     * Test resolve() shared instance
     *
     */
    #[@test]
    public function resolveSharedInstance() {
      $sharedInstance= new Sword();
      $this->fixture->bindType('net.xp_framework.unittest.ioc.stub.IWeapon', $sharedInstance);

      // Ask container to resolve an interface
      $weapon1= $this->fixture->resolve('net.xp_framework.unittest.ioc.stub.IWeapon');
      $this->assertEquals($sharedInstance, $weapon1);

      // Ask again
      $weapon2= $this->fixture->resolve('net.xp_framework.unittest.ioc.stub.IWeapon');
      $this->assertEquals($sharedInstance, $weapon2);
    }

    /**
     * Test resolve() shared instance with context
     *
     */
    #[@test]
    public function resolveSharedInstanceWithContext() {
      $sharedSword= new Sword();
      $this->fixture->bindType('net.xp_framework.unittest.ioc.stub.IWeapon', $sharedSword, 'melee');

      $sharedBow= new Bow();
      $this->fixture->bindType('net.xp_framework.unittest.ioc.stub.IWeapon', $sharedBow, 'ranged');

      $this->assertEquals(
        $sharedSword,
        $this->fixture->resolve('net.xp_framework.unittest.ioc.stub.IWeapon', 'melee')
      );

      $this->assertEquals(
        $sharedBow,
        $this->fixture->resolve('net.xp_framework.unittest.ioc.stub.IWeapon', 'ranged')
      );
    }

    /**
     * Test resolve() not-shared instance
     *
     */
    #[@test]
    public function resolveNotSharedInstance() {
      $this->fixture->bindType(
        'net.xp_framework.unittest.ioc.stub.IWeapon',
        'net.xp_framework.unittest.ioc.stub.weapon.Sword'
      );

      // Ask container to resolve an interface
      $weapon1= $this->fixture->resolve('net.xp_framework.unittest.ioc.stub.IWeapon');
      $this->assertClass($weapon1, 'net.xp_framework.unittest.ioc.stub.weapon.Sword');

      // Ask again
      $weapon2= $this->fixture->resolve('net.xp_framework.unittest.ioc.stub.IWeapon');
      $this->assertClass($weapon2, 'net.xp_framework.unittest.ioc.stub.weapon.Sword');

      // Should not resolve to the same instance
      $this->assertNotEquals($weapon1, $weapon2);
    }

    /**
     * Test resolve() non-shared instance with context
     *
     */
    #[@test]
    public function resolveNonSharedInstanceWithContext() {
      $sharedSword= new Sword();
      $this->fixture->bindType(
        'net.xp_framework.unittest.ioc.stub.IWeapon',
        'net.xp_framework.unittest.ioc.stub.weapon.Sword',
        'melee'
      );

      $sharedBow= new Bow();
      $this->fixture->bindType(
        'net.xp_framework.unittest.ioc.stub.IWeapon',
        'net.xp_framework.unittest.ioc.stub.weapon.Bow',
        'ranged'
      );

      $this->assertClass(
        $this->fixture->resolve('net.xp_framework.unittest.ioc.stub.IWeapon', 'melee'),
        'net.xp_framework.unittest.ioc.stub.weapon.Sword'
      );

      $this->assertClass(
        $this->fixture->resolve('net.xp_framework.unittest.ioc.stub.IWeapon', 'ranged'),
        'net.xp_framework.unittest.ioc.stub.weapon.Bow'
      );
    }

    /**
     * Test resolve() class field inject (can only inject Constants)
     *
     */
    #[@test]
    public function resolveClassFieldInject() {
      $this->fixture->bindConstant('dagger.damage', 150);

      $weapon= $this->fixture->resolve('net.xp_framework.unittest.ioc.stub.weapon.Dagger');
      $this->assertClass($weapon, 'net.xp_framework.unittest.ioc.stub.weapon.Dagger');
      $this->assertEquals(150, $weapon->getDamage());
    }

    /**
     * Test resolve() class field inject with missing Constant Binding
     *
     */
    #[@test]
    public function resolveClassFieldInjectAndIgnoreMissingConstants() {
      $weapon= $this->fixture->resolve('net.xp_framework.unittest.ioc.stub.weapon.Dagger');
      $this->assertClass($weapon, 'net.xp_framework.unittest.ioc.stub.weapon.Dagger');

      // Should have default damage
      $this->assertEquals(create(new Dagger())->getDamage(), $weapon->getDamage());
    }

    /**
     * Test resolve() setter inject Constant
     *
     */
    #[@test]
    public function resolveSetterConstantInject() {
      $this->fixture->bindConstant('sword.damage', 400);

      $weapon= $this->fixture->resolve('net.xp_framework.unittest.ioc.stub.weapon.Sword');
      $this->assertClass($weapon, 'net.xp_framework.unittest.ioc.stub.weapon.Sword');
      $this->assertEquals(400, $weapon->damage);
    }

    /**
     * Test resolve() setter inject Constant with missing Constant Binding
     *
     */
    #[@test]
    public function resolveSetterConstantInjectAndIgnoreMissingConstants() {
      $weapon= $this->fixture->resolve('net.xp_framework.unittest.ioc.stub.weapon.Sword');
      $this->assertClass($weapon, 'net.xp_framework.unittest.ioc.stub.weapon.Sword');

      // Should have default damage
      $this->assertEquals(create(new Sword())->damage, $weapon->damage);
    }

    /**
     * Test resolve() setter inject Type
     *
     */
    #[@test]
    public function resolveSetterTypeInject() {
      $weapon= new Sword();
      $this->fixture->bindType('net.xp_framework.unittest.ioc.stub.IWeapon', $weapon);

      $unit= $this->fixture->resolve('net.xp_framework.unittest.ioc.stub.unit.Peon');
      $this->assertClass($unit, 'net.xp_framework.unittest.ioc.stub.unit.Peon');
      $this->assertEquals($weapon, $unit->getWeapon());
    }

    /**
     * Test resolve() setter inject Type with missing Type Binding
     *
     */
    #[@test]
    public function resolveSetterTypeInjectAndIgnoreMissingConstants() {
      $unit= $this->fixture->resolve('net.xp_framework.unittest.ioc.stub.unit.Peon');
      $this->assertClass($unit, 'net.xp_framework.unittest.ioc.stub.unit.Peon');
      $this->assertEquals(NULL, $unit->getWeapon());
    }

    /**
     * Test resolve() constructor inject Type
     *
     */
    #[@test]
    public function resolveConstructorInject() {
      $weapon= new Sword();
      $this->fixture->bindType('net.xp_framework.unittest.ioc.stub.IWeapon', $weapon);

      $unit= $this->fixture->resolve('net.xp_framework.unittest.ioc.stub.unit.Infantry');
      $this->assertClass($unit, 'net.xp_framework.unittest.ioc.stub.unit.Infantry');
      $this->assertEquals($weapon, $unit->getWeapon());
    }

    /**
     * Test resolve() constructor inject Type with missing Type Binding
     *
     */
    #[@test, @expect('ioc.DependecyInjectionException')]
    public function cantResolveConstructorInjectWithMissingTypeBinding() {
      $this->fixture->resolve('net.xp_framework.unittest.ioc.stub.unit.Infantry');
    }

    /**
     * Test resolve() constructor inject Type with context
     *
     */
    #[@test]
    public function resolveConstructorInjectWithContext() {
      $dagger= new Dagger();
      $this->fixture->bindType('net.xp_framework.unittest.ioc.stub.IWeapon', $dagger, 'stealth');

      $bow= new Bow();
      $this->fixture->bindType('net.xp_framework.unittest.ioc.stub.IWeapon', $bow, 'ranged');

      $ninja= $this->fixture->resolve('net.xp_framework.unittest.ioc.stub.unit.Ninja');
      $this->assertEquals($dagger, $ninja->getWeapon());

      $archer= $this->fixture->resolve('net.xp_framework.unittest.ioc.stub.unit.Archer');
      $this->assertEquals($bow, $archer->getWeapon());
    }

    /**
     * Test resolve() recursive resolve
     *
     */
    #[@test]
    public function resolveRecursive() {
      $this->fixture->bindType(
        'net.xp_framework.unittest.ioc.stub.IWeapon',
        'net.xp_framework.unittest.ioc.stub.weapon.Sword'
      );

      $this->fixture->bindType(
        'net.xp_framework.unittest.ioc.stub.IUnit',
        'net.xp_framework.unittest.ioc.stub.unit.Infantry'
      );

      // Get an Infantry equipped with a mighty Sword
      $unit= $this->fixture->resolve('net.xp_framework.unittest.ioc.stub.IUnit');
      $this->assertClass($unit, 'net.xp_framework.unittest.ioc.stub.unit.Infantry');
      $this->assertClass($unit->getWeapon(), 'net.xp_framework.unittest.ioc.stub.weapon.Sword');
    }

    /**
     * Test resolve() recursive resolve with context
     *
     */
    #[@test]
    public function resolveRecursiveWithContext() {
      $this->fixture->bindType(
        'net.xp_framework.unittest.ioc.stub.IWeapon',
        'net.xp_framework.unittest.ioc.stub.weapon.Dagger',
        'stealth'
      );
      $this->fixture->bindType(
        'net.xp_framework.unittest.ioc.stub.IWeapon',
        'net.xp_framework.unittest.ioc.stub.weapon.Bow',
        'ranged'
      );

      $this->fixture->bindType(
        'net.xp_framework.unittest.ioc.stub.IUnit',
        'net.xp_framework.unittest.ioc.stub.unit.Ninja',
        'stealth'
      );
      $this->fixture->bindType(
        'net.xp_framework.unittest.ioc.stub.IUnit',
        'net.xp_framework.unittest.ioc.stub.unit.Archer',
        'ranged'
      );

      // Get a Ninja equipped with a sharp Dagger
      $ninja= $this->fixture->resolve('net.xp_framework.unittest.ioc.stub.IUnit', 'stealth');
      $this->assertClass($ninja, 'net.xp_framework.unittest.ioc.stub.unit.Ninja');
      $this->assertClass($ninja->getWeapon(), 'net.xp_framework.unittest.ioc.stub.weapon.Dagger');

      // Get an Archer equipped with a pointy Bow
      $archer= $this->fixture->resolve('net.xp_framework.unittest.ioc.stub.IUnit', 'ranged');
      $this->assertClass($archer, 'net.xp_framework.unittest.ioc.stub.unit.Archer');
      $this->assertClass($archer->getWeapon(), 'net.xp_framework.unittest.ioc.stub.weapon.Bow');
    }
  }
?>
