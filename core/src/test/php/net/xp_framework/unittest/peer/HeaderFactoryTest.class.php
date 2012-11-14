<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestCase',
    'peer.HeaderFactory',
    'peer.header.ContentLocationHeader',
    'peer.header.ContentMD5Header',
    'peer.header.ContentRangeHeader',
    'peer.header.ContentTypeHeader'
  );

  /**
   * TestCase
   *
   * @see      xp://peer.HeaderFactory
   */
  class HeaderFactoryTest extends TestCase {

    /**
     * Get Request Header
     *
     */
    #[@test]
    public function getRequestHeader() {
      $requestHeader= HeaderFactory::getRequestHeader(HeaderFactory::TYPE_CONTENT_TYPE, 'application/excel', 'latin1');
      $requestHeaderExpected= new ContentTypeHeader('application/excel', 'latin1');
      $this->assertEquals($requestHeaderExpected->toString(), $requestHeader->toString());
    }

    /**
     * Get Response Header
     *
     */
    #[@test]
    public function getResponseHeader() {
      $responseHeader= HeaderFactory::getResponseHeader(HeaderFactory::TYPE_CONTENT_MD5, 'myown-md5');
      $responseHeaderExpected= new ContentMD5Header('myown-md5');
      $this->assertEquals($responseHeaderExpected->toString(), $responseHeader->toString());
    }

    /**
     * Get invalid Request Header
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function getIllegalRequestHeader() {
      $requestHeader= HeaderFactory::getRequestHeader(HeaderFactory::TYPE_CONTENT_LOCATION, '/var/log/apache2/error.log');
    }

    /**
     * will retrieve the name for a header
     *
     */
    #[@test]
    public function getNameForType() {
      $retrievedName= HeaderFactory::getNameForType(HeaderFactory::TYPE_CONTENT_RANGE);
      $expectedName= create(new ContentRangeHeader('egal', 'auch', 'nochmehr'))->getName();
      $this->assertEquals($expectedName, $retrievedName);
    }
  }
?>
