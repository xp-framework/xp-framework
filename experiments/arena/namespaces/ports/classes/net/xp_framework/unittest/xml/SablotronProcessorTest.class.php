<?php
/* This class is part of the XP framework
 *
 * $Id: SablotronProcessorTest.class.php 8971 2006-12-27 15:27:10Z friebe $
 */

  namespace net::xp_framework::unittest::xml;
 
  ::uses(
    'xml.XSLProcessor',
    'net.xp_framework.unittest.xml.AbstractProcessorTest'
  );

  /**
   * ProcessorTest implementation that tests the Sablotron-based processor
   *
   * @see      xp://xml.XSLProcessor
   * @ext      xslt
   * @purpose  Unit Test
   */
  class SablotronProcessorTest extends AbstractProcessorTest {

    /**
     * Returns the PHP extension needed for this processor test to work
     *
     * @return  string
     */
    public function neededExtension() { 
      return 'xslt';
    }
  
    /**
     * Returns the XSL processor instance to be used
     *
     * @return  &xml.IXSLProcessor
     */
    public function processorInstance() {
      return new xml::XSLProcessor();
    }

    /**
     * Returns the XSL processor's default output charset
     *
     * @return  string
     */
    public function processorCharset() { 
      return 'utf-8';
    }
  }
?>
