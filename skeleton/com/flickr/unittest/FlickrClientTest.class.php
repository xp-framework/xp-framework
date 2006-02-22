<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'util.profiling.unittest.TestCase',
    'com.flickr.FlickrClient'
  );

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class FlickrClientTest extends TestCase {

    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function setUp() {
      $this->client= &new FlickrClient(new XmlRpcHttpTransport(FLICKR_XMLRPC_ENDPOINT));
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    #[@test]
    function unserializeScalar() {
      $this->assertEquals(
        array('foo' => 'bar'),
        $this->client->unserialize('<foo>bar</foo>')
      );
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    #[@test]
    function unserializeArray() {
      $this->assertEquals(
        array('foo' => 'bar', 'bar' => 'baz'),
        $this->client->unserialize('<foo>bar</foo><bar>baz</bar>')
      );
    }

    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    #[@test]
    function unserializeComplex() {
      $this->assertEquals(
        array('foo' => array('bar' => 'baz')),
        $this->client->unserialize('<foo><bar>baz</bar></foo>')
      );
    }

    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
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
