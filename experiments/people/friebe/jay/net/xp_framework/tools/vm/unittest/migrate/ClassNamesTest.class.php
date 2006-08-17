<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses(
    'util.profiling.unittest.TestCase'
  );

  /**
   * Tests class names
   *
   * @purpose  Unit Test
   */
  class ClassNamesTest extends TestCase {

    /**
     * Retrieves qualified name of a given short name
     *
     * @access  public
     * @param   string
     * @return  string
     */
    function qualifiedNameOf($short) {
      $key= strtolower($short);
      if (!isset($this->mapping[$key])) {
        return throw(new IllegalArgumentException('Mapping for "'.$short.'" not found'));
      }
      
      return ($this->getClassName() == $this->mapping[$key] 
        ? 'self' 
        : $this->mapping[$key]
      );
    }

    /**
     * Retrieves prefix for a given package
     *
     * @access  public
     * @param   string package
     * @return  string
     */
    function prefixFor($package) {
      static $ports= array('com', 'net', 'ch', 'org', 'us');
      
      return (in_array(substr($package, 0, strpos($package, '.')), $ports) ? '' : 'xp~');
    }
    
    /**
     * Retrieves packaged name of a given qualified name
     *
     * @access  public
     * @param   string q qualified class name
     * @return  string
     */
    function packagedNameOf($q) {
      if (strstr($q, '.')) {
        $packaged= $this->prefixFor($q).strtr($q, '.', '~');
      } else {
        $packaged= $q;
      }
      return $packaged;
    }
    
    /**
     * Setup method
     *
     * @access  public
     */
    function setUp() {
      $this->mapping['xp']= 'xp';
      $this->mapping['parent']= 'parent';
      $this->mapping['self']= 'self';
      $this->mapping['date']= 'util.Date';
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
          $this->packagedNameOf($this->qualifiedNameOf($short))
        );
      }
    }

    /**
     * Tests everything except (com.*, net.*, org.*, ch.*) gets prefixed with "xp"
     *
     * @access  public
     */
    #[@test]
    function xpPrefixForFramework() {
      $this->assertEquals(
        'xp~lang~XPClass', 
        $this->packagedNameOf('lang.XPClass')
      );
    }

    /**
     * Tests com.* package does not get prefixed
     *
     * @access  public
     */
    #[@test]
    function noPrefixForXpFrameworkPackage() {
      $this->assertEquals('', $this->prefixFor('net.xp_framework.unittest'));
    }

    /**
     * Tests net.* does not get prefixed
     *
     * @access  public
     */
    #[@test]
    function noPrefixForXpFrameworkClasses() {
      $this->assertEquals(
        'net~xp_framework~unittest~DemoTest', 
        $this->packagedNameOf('net.xp_framework.unittest.DemoTest')
      );
    }

    /**
     * Tests com.* package does not get prefixed
     *
     * @access  public
     */
    #[@test]
    function noPrefixForGoogleContributionPackage() {
      $this->assertEquals('', $this->prefixFor('com.google.soap.search'));
    }

    /**
     * Tests com.* does not get prefixed
     *
     * @access  public
     */
    #[@test]
    function noPrefixForGoogleContributionClasses() {
      $this->assertEquals(
        'com~google~soap~search~GoogleSearchClient', 
        $this->packagedNameOf('com.google.soap.search.GoogleSearchClient')
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
        'xp~util~Date', 
        $this->packagedNameOf($this->qualifiedNameOf('Date'))
      );
    }

    /**
     * Tests qualified & packaged name of date class
     *
     * @access  public
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    function unknownClass() {
      $this->qualifiedNameOf('@@NON_EXISTANT_CLASS@@');
    }
  }
?>
