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
  class net·xp_lang·tests·execution·source·FinallyTest extends ExecutionTest {
    
    /**
     * Test try ... finally
     *
     */
    #[@test]
    public function tryFinallyNoException() {
      $this->assertEquals(array('Try', 'Finally'), $this->run('
        $r= [];
        try {
          $r[]= "Try";
        } finally {
          $r[]= "Finally";
        }
        return $r;
      '));
    }

    /**
     * Test try ... finally
     *
     */
    #[@test]
    public function tryFinallyWithException() {
      $this->assertEquals(array('Try', 'Finally', 'Catch'), $this->run('
        try {
          $r= [];
          try {
            $r[]= "Try";
            throw new FormatException("Error");
          } finally {
            $r[]= "Finally";
          }
        } catch (FormatException $e) {
          $r[]= "Catch";
        }
        return $r;
      '));
    }

    /**
     * Test try ... finally
     *
     */
    #[@test]
    public function tryFinallyWithReturn() {
      $this->assertEquals(array('Try', 'Finally'), $this->run('
        $r= [];
        try {
          $r[]= "Try";
          return $r;
        } finally {
          $r[]= "Finally";
        }
      '));
    }

    /**
     * Test try ... catch ... finally
     *
     */
    #[@test]
    public function tryCatchFinallyWithException() {
      $this->assertEquals(array('Try', 'Catch', 'Finally'), $this->run('
        $r= [];
        try {
          $r[]= "Try";
          throw new FormatException("Error");
        } catch (FormatException $e) {
          $r[]= "Catch"; 
        } finally {
          $r[]= "Finally";
        }
        return $r;
      '));
    }

    /**
     * Test try ... catch ... finally
     *
     */
    #[@test]
    public function tryCatchFinallyWithReturnInsideCatch() {
      $this->assertEquals(array('Try', 'Catch', 'Finally'), $this->run('
        $r= [];
        try {
          $r[]= "Try";
          throw new FormatException("Error");
        } catch (FormatException $e) {
          $r[]= "Catch"; 
          return $r;
        } finally {
          $r[]= "Finally";
        }
      '));
    }

    /**
     * Test try ... catch ... finally
     *
     */
    #[@test]
    public function tryCatchFinallyNoException() {
      $this->assertEquals(array('Try', 'Finally'), $this->run('
        $r= [];
        try {
          $r[]= "Try";
        } catch (FormatException $e) {
          $r[]= "Catch"; 
        } finally {
          $r[]= "Finally";
        }
        return $r;
      '));
    }
  }
?>
