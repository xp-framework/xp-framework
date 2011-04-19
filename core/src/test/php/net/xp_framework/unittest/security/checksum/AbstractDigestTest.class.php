<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'security.checksum.MessageDigest'
  );

  /**
   * TestCase for MD5 digest
   *
   * @see      xp://security.checksum.MD5Digest
   */
  abstract class AbstractDigestTest extends TestCase {
    protected $fixture;
    
    /**
     * Creates a new message digest object
     *
     * @return  security.checksum.MessageDigest
     */
    protected abstract function newDigest();

    /**
     * Returns a checksum for a given input string
     *
     * @param   string data
     * @return  security.checksum.Checksum
     */
    protected abstract function checksumOf($data);
  
    /**
     * Sets up test case
     *
     */
    public function setUp() {
      $this->fixture= $this->newDigest();
    }
    
    /**
     * Test calling update once
     *
     */
    #[@test]
    public function singleUpdate() {
      $this->fixture->update('Hello');
      $this->assertEquals(
        $this->checksumOf('Hello'),
        $this->fixture->digest()
      );
    }

    /**
     * Test calling update() multiple times
     *
     */
    #[@test]
    public function multipleUpdates() {
      $this->fixture->update('Hello');
      $this->fixture->update('World');
      $this->assertEquals(
        $this->checksumOf('HelloWorld'),
        $this->fixture->digest()
      );
    }

    /**
     * Test not calling update() results in the MD5 of an empty string
     *
     */
    #[@test]
    public function noUpdate() {
      $this->assertEquals(
        $this->checksumOf(''),
        $this->fixture->digest()
      );
    }

    /**
     * Test not calling update() but instead digest with optional parameter
     *
     */
    #[@test]
    public function digestOnly() {
      $this->assertEquals(
        $this->checksumOf('Hello'),
        $this->fixture->digest('Hello')
      );
    }

    /**
     * Test not calling update() results in the MD5 of an empty string
     *
     */
    #[@test, @expect('lang.IllegalStateException')]
    public function callingUpdateAfterFinalization() {
      $this->fixture->update('...');
      $this->fixture->digest();
      $this->fixture->update('...');
    }

    /**
     * Test not calling update() results in the MD5 of an empty string
     *
     */
    #[@test, @expect('lang.IllegalStateException')]
    public function callingDigestAfterFinalization() {
      $this->fixture->update('...');
      $this->fixture->digest();
      $this->fixture->digest();
    }
  }
?>
