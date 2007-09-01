<?php
/* This class is part of the XP framework
 *
 * $Id: AbstractDriverTest.class.php 8971 2006-12-27 15:27:10Z friebe $
 */

  namespace net::xp_framework::unittest::rdbms::drivers;
 
  ::uses('unittest.TestCase');

  /**
   * Driver test
   *
   * @purpose  Unit Test
   */
  class AbstractDriverTest extends unittest::TestCase {
  
    /**
     * Returns driver name in subclasses, following the form:
     * <pre>
     *   scheme://string 
     * </pre>
     * where scheme is one of the following:
     * <ul>
     *   <li>ext - tests that PHP extension is available via extension_loaded()</li>
     * </ul>
     *
     * @return  string
     */
    public function driverName() { }
    
    /**
     * Tests driver is available in current PHP setup
     *
     */
    #[@test]
    public function driverAvailable() {
      extract(parse_url($this->driverName()));
      switch ($scheme) {
        case 'ext': {
          $ok= extension_loaded($host);
          break;
        }
        
        case 'pdo': {   // Being forward-compatible to PHP X (where X > 4):)
          $ok= extension_loaded('pdo') && in_array($host, PDO::availableDrivers());
          break;
        }

        default: {
          throw(new PrerequisitesNotMetError('Test error, unknown scheme "'.$scheme.'"'));
        }
      }
      
      $ok || $this->fail('Driver "'.$this->driverName().'" not loaded', FALSE, TRUE);
    } 
  }
?>
