<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'io.streams.MemoryInputStream',
    'io.streams.MemoryOutputStream',
    'io.streams.Streams'
  );

  /**
   * TestCase
   *
   * @see      xp://io.streams.Streams
   * @purpose  Unittest
   */
  class DomApiStreamsTest extends TestCase {
  
    /**
     * Sets up this unittest 
     *
     * @throws  unittest.PrerequisitesNotMetError
     */
    public function setUp() {
      if (!extension_loaded('dom')) {
        throw new PrerequisitesNotMetError('DOM extension not loaded');
      }
    }
 
    /**
     * Test DOMDocument::loadHTMLFile()
     *
     */
    #[@test]
    public function usableInHTMLFile() {
      $dom= new DOMDocument();
      $this->assertTrue($dom->loadHTMLFile(Streams::readableUri(new MemoryInputStream(trim('
        <?xml version="1.0" encoding="utf-8"?>
        <html>
          <head>
            <title>übercoder</title>
          </head>
          <body>
            <!-- Content here -->
          </body>
        </html>
      ')))));
    }
  }
?>
