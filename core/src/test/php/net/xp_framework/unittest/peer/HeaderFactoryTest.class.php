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
     * Get Request Header for Response
     *
     */
    #[@test, @expect(class= 'lang.IllegalArgumentException', withMessage= '/A response only header may not be used in a request/')]
    public function getRequestHeaderForResponse() {
      $requestHeader= HeaderFactory::getRequestHeader(HeaderFactory::TYPE_CONTENT_LOCATION, '/var/log/apache2/error.log');
    }

    /**
     * Get header for empty type classname
     *
     */
    #[@test, @expect(class= 'lang.IllegalArgumentException', withMessage= '/A header type has to be given/')]
    public function getRequestHeaderForEmptyType() {
      $requestHeader= HeaderFactory::getRequestHeader('', '/var/log/apache2/error.log');
    }

    /**
     * Get header for invalid type classname
     *
     */
    #[@test, @expect(class= 'lang.IllegalArgumentException', withMessage= '/Invalid type \'de.schlund.alohahe.testonly.NoGoodHeaderClass\' given. Class not found./')]
    public function getRequestHeaderForInvalidType() {
      $requestHeader= HeaderFactory::getRequestHeader('de.schlund.alohahe.testonly.NoGoodHeaderClass', '/var/log/apache2/error.log');
    }

    /**
     * Get standard header
     *
     */
    #[@test]
    public function getIllegalRequestHeader() {
      $requestHeader= HeaderFactory::getRequestHeader('x-mycustom-header', 'random');
      $requestHeaderExpected= new Header('x-mycustom-header', 'random');
      $this->assertEquals($requestHeaderExpected->toString(), $requestHeader->toString());
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
