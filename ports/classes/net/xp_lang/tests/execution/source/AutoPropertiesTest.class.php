<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  $package= 'net.xp_lang.tests.execution.source';

  uses('net.xp_lang.tests.execution.source.ExecutionTest');

  /**
   * Tests automatic properties
   *
   */
  class net·xp_lang·tests·execution·source·AutoPropertiesTest extends ExecutionTest {
    protected static $fixture= NULL;

    /**
     * Sets up test case
     *
     */
    public function setUp() {
      parent::setUp();
      if (NULL !== self::$fixture) return;

      try {
        self::$fixture= $this->define('class', 'FixtureForAutoPropertiesTest', NULL, '{
          public int id { get; set; }
        }');
      } catch (Throwable $e) {
        throw new PrerequisitesNotMetError($e->getMessage(), $e);
      }
    }
    
    /**
     * Test reading the id property
     *
     */
    #[@test]
    public function initiallyNull() {
      $instance= self::$fixture->newInstance();
      $this->assertEquals(NULL, $instance->id);
    }

    /**
     * Test writing and reading the id property
     *
     */
    #[@test]
    public function roundTrip() {
      $instance= self::$fixture->newInstance();
      $instance->id= 1;
      $this->assertEquals(1, $instance->id);
    }
  }
?>
