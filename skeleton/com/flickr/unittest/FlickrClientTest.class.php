<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'util.profiling.unittest.TestCase',
    'com.flickr.xmlrpc.FlickrClient'
  );

  /**
   * FlickR client test
   *
   * @purpose  Testcase
   */
  class FlickrClientTest extends TestCase {
    var 
      $client= NULL;

    /**
     * Setup method. Creates client member
     *
     * @access  public
     */
    function setUp() {
      $this->client= &new FlickrClient(new XmlRpcHttpTransport(FLICKR_XMLRPC_ENDPOINT));
    }
    
    /**
     * Test deserialization of a scalar
     *
     * @access  public
     */
    #[@test]
    function unserializeScalar() {
      $this->assertEquals(
        array('foo' => 'bar'),
        $this->client->unserialize('<foo>bar</foo>')
      );
    }
    
    /**
     * Test deserialization of a hash
     *
     * @access  public
     */
    #[@test]
    function unserializeArray() {
      $this->assertEquals(
        array('foo' => 'bar', 'bar' => 'baz'),
        $this->client->unserialize('<foo>bar</foo><bar>baz</bar>')
      );
    }

    /**
     * Test deserialization of a complex (hash of hashes)
     *
     * @access  public
     */
    #[@test]
    function unserializeComplex() {
      $this->assertEquals(
        array('foo' => array('bar' => 'baz')),
        $this->client->unserialize('<foo><bar>baz</bar></foo>')
      );
    }

    /**
     * Test deserialization of attributes
     *
     * @access  public
     */
    #[@test]
    function unserializeAttributes() {
      $this->assertEquals(
        array('foo' => array('bar' => 'baz')),
        $this->client->unserialize('<foo bar="baz"/>')
      );
    }
  }
?>
