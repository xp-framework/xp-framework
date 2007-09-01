<?php
/* This class is part of the XP framework
 *
 * $Id: ExceptionsTest.class.php 8971 2006-12-27 15:27:10Z friebe $
 */

  namespace net::xp_framework::unittest::core;

  ::uses('unittest.TestCase');

  /**
   * Test the XP exception mechanism
   *
   * @purpose  Testcase
   */
  class ExceptionsTest extends unittest::TestCase {

    /**
     * Basics: Tests nothing is caught when nothing is thrown
     *
     */
    #[@test]
    public function noException() {
      try {
        // Nothing
      } catch (::Exception $caught) {
        return $this->fail('Caught an exception but none where thrown', $caught);
      }
    }

    /**
     * Basics: Tests thrown exception is caught
     *
     */
    #[@test]
    public function thrownExceptionCaught() {
      try {
        throw(new lang::XPException('Test'));
      } catch (::Exception $caught) {
        $this->assertSubclass($caught, 'Exception');
        ::delete($caught);
        return TRUE;
      }

      $this->fail('Thrown Exception not caught');
    }

    /**
     * Basics: Tests thrown exception is caught in the correct catch
     * block.
     *
     */
    #[@test]
    public function multipleCatches() {
      try {
        throw(new lang::XPException('Test'));
      } catch (lang::IllegalArgumentException $caught) {
        return $this->fail('Exception should have been caught in Exception block', 'IllegalArgumentException');
      } catch (::Exception $caught) {
        return TRUE;
      } catch (lang::Throwable $caught) {
        return $this->fail('Exception should have been caught in Exception block', 'Throwable');
      }

      $this->fail('Thrown Exception not caught');
    }
  }
?>
