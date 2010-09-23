<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  $package= 'net.xp_lang.tests.execution.source';

  uses('net.xp_lang.tests.execution.source.ExecutionTest');

  /**
   * Tests arrays
   *
   */
  class net·xp_lang·tests·execution·source·CatchTest extends ExecutionTest {
    
    /**
     * Test try ... catch
     *
     */
    #[@test]
    public function catchNoException() {
      $this->assertEquals(array('Try'), $this->run('
        $r= [];
        try {
          $r[]= "Try";
        } catch (FormatException $e) {
          $r[]= "Catch";
        }
        return $r;
      '));
    }

    /**
     * Test try ... catch
     *
     */
    #[@test]
    public function catchWithException() {
      $this->assertEquals(array('Try', 'Catch'), $this->run('
        $r= [];
        try {
          $r[]= "Try";
          throw new FormatException("Error");
        } catch (FormatException $e) {
          $r[]= "Catch";
        }
        return $r;
      '));
    }

    /**
     * Test try ... catch
     *
     */
    #[@test]
    public function catchSubclass() {
      $this->assertEquals(array('Try', 'Catch'), $this->run('
        $r= [];
        try {
          $r[]= "Try";
          throw new FormatException("Error");
        } catch (Throwable $e) {
          $r[]= "Catch";
        }
        return $r;
      '));
    }

    /**
     * Test try ... catch ... catch
     *
     */
    #[@test]
    public function catchIAE() {
      $this->assertEquals(array('Try', 'Catch.IAE'), $this->run('
        $r= [];
        try {
          $r[]= "Try";
          throw new IllegalArgumentException("Error");
        } catch (IllegalArgumentException $e) {
          $r[]= "Catch.IAE";
        } catch (FormatException $e) {
          $r[]= "Catch.FE";
        }
        return $r;
      '));
    }

    /**
     * Test try ... catch ... catch
     *
     */
    #[@test]
    public function catchFE() {
      $this->assertEquals(array('Try', 'Catch.FE'), $this->run('
        $r= [];
        try {
          $r[]= "Try";
          throw new FormatException("Error");
        } catch (IllegalArgumentException $e) {
          $r[]= "Catch.IAE";
        } catch (FormatException $e) {
          $r[]= "Catch.FE";
        }
        return $r;
      '));
    }

    /**
     * Test try ... catch (A|B)
     *
     */
    #[@test]
    public function catchMultipleFE() {
      $this->assertEquals(array('Try', 'Catch'), $this->run('
        $r= [];
        try {
          $r[]= "Try";
          throw new FormatException("Error");
        } catch (IllegalArgumentException | FormatException $e) {
          $r[]= "Catch";
        }
        return $r;
      '));
    }

    /**
     * Test try ... catch (A|B) when B is thrown
     *
     */
    #[@test]
    public function catchMultipleIAE() {
      $this->assertEquals(array('Try', 'Catch'), $this->run('
        $r= [];
        try {
          $r[]= "Try";
          throw new IllegalArgumentException("Error");
        } catch (IllegalArgumentException | FormatException $e) {
          $r[]= "Catch";
        }
        return $r;
      '));
    }

    /**
     * Test try ... catch (A|B) when neither A nor B is thrown
     *
     */
    #[@test]
    public function catchMultipleISE() {
      $this->assertEquals(array('Try', 'Catch.ISE'), $this->run('
        $r= [];
        try {
          $r[]= "Try";
          throw new IllegalStateException("Error");
        } catch (IllegalArgumentException | FormatException $e) {
          $r[]= "Catch";
        } catch (IllegalStateException $e) {
          $r[]= "Catch.ISE";
        }
        return $r;
      '));
    }
  }
?>
