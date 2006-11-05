<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses(
    'MsXslProcessor',
    'net.xp_framework.unittest.xml.AbstractProcessorTest'
  );

  /**
   * Test XSL processor
   *
   * @see      xp://xml.IXSLProcessor
   * @purpose  Unit Test
   */
  class MsXslProcessorTest extends AbstractProcessorTest {

    /**
     * Returns the PHP extension needed for this processor test to work
     *
     * @access  protected
     * @return  string
     */
    function neededExtension() { 
      return 'com';
    }
  
    /**
     * Returns the XSL processor instance to be used
     *
     * @access  protected
     * @return  &xml.IXSLProcessor
     */
    function &processorInstance() {
      return new MsXslProcessor();
    }

    /**
     * Returns the XSL processor's default output charset
     *
     * @access  protected
     * @return  string
     */
    function processorCharset() { 
      return 'UTF-16';
    }
  }
?>
