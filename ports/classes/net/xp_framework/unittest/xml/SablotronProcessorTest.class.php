<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses(
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
     * @access  protected
     * @return  string
     */
    public function neededExtension() { 
      return 'xslt';
    }
  
    /**
     * Returns the XSL processor instance to be used
     *
     * @access  protected
     * @return  &xml.IXSLProcessor
     */
    public function &processorInstance() {
      return new XSLProcessor();
    }

    /**
     * Returns the XSL processor's default output charset
     *
     * @access  protected
     * @return  string
     */
    public function processorCharset() { 
      return 'utf-8';
    }
  }
?>
