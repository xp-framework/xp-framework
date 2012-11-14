<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'lang.XPClass',
    'unittest.TestCase',
    'peer.Header'
  );

  /**
   * Abstract TestCase used by content
   *
   * @see      xp://peer.Header
   */
  abstract class AbstractHeaderTest extends TestCase {

    // vars set like in peer.Header
    protected
      $isResponse=  TRUE,
      $isRequest=   TRUE,
      $isUnique=    FALSE,
      $className=   NULL;

    /**
     * Check isResponse
     *
     */
    #[@test]
    public function isResponseHeader() {
      $this->assertEquals($this->isResponse, $this->getNewContentHeader()->isResponseHeader());
    }

    /**
     * Check isRequest
     *
     */
    #[@test]
    public function isRequestHeader() {
      $this->assertEquals($this->isRequest, $this->getNewContentHeader()->isRequestHeader());
    }

    /**
     * Check isUnique
     *
     */
    #[@test]
    public function isUnique() {
      $this->assertEquals($this->isUnique, $this->getNewContentHeader()->isUnique());
    }

    /**
     * Method for retrieval of a ContentRangeHeader
     *
     * @return peer.Header
     */
    protected function getNewContentHeader() {
      $class= XPClass::forName($this->className);
      return $class->newInstance('bla', 'bla2', 'bla2', 'bla2');
    }


  }
?>
