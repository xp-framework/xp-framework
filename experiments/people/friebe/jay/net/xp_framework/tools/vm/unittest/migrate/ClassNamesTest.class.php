<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses(
    'unittest.TestCase',
    'net.xp_framework.tools.vm.util.NameMapping'
  );

  /**
   * Tests class names
   *
   * @purpose  Unit Test
   */
  class ClassNamesTest extends TestCase {
    var
      $names= NULL;
      
    /**
     * Setup method
     *
     * @access  public
     */
    function setUp() {
      $this->names= &new NameMapping();
      $this->names->addMapping('date', 'util.Date');
      $this->names->setNamespaceSeparator('.');
    }

    /**
     * Tests special class names ("xp", "parent", "self") stay unqualified
     *
     * @access  public
     */
    #[@test]
    function specialClassesStayUnqualified() {
      foreach (array('xp', 'parent', 'self') as $short) {
        $this->assertEquals(
          $short, 
          $this->names->packagedNameOf($this->names->qualifiedNameOf($short))
        );
      }
    }

    /**
     * Tests net.* does not get prefixed
     *
     * @access  public
     */
    #[@test]
    function noPrefixForXpFrameworkClasses() {
      $this->assertEquals(
        'net.xp_framework.unittest.DemoTest', 
        $this->names->packagedNameOf('net.xp_framework.unittest.DemoTest')
      );
    }

    /**
     * Tests com.* does not get prefixed
     *
     * @access  public
     */
    #[@test]
    function noPrefixForGoogleContributionClasses() {
      $this->assertEquals(
        'com.google.soap.search.GoogleSearchClient', 
        $this->names->packagedNameOf('com.google.soap.search.GoogleSearchClient')
      );
    }

    /**
     * Tests qualified & packaged name of date class
     *
     * @access  public
     */
    #[@test]
    function nameOfDateClass() {
      $this->assertEquals(
        'util.Date', 
        $this->names->packagedNameOf($this->names->qualifiedNameOf('Date'))
      );
    }

    /**
     * Tests qualified & packaged name of date class
     *
     * @access  public
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    function unknownClass() {
      $this->names->qualifiedNameOf('@@NON_EXISTANT_CLASS@@');
    }
  }
?>
