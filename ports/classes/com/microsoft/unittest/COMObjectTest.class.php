<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'com.microsoft.com.COMObject',
    'lang.Runtime'
  );

  /**
   * TestCase
   *
   * @ext   com_dotnet
   * @see   xp://com.microsoft.com.COMObject
   */
  class COMObjectTest extends TestCase {
  
    /**
     * Sets up test case
     *
     */
    #[@beforeClass]
    public static function verifyExtensionAvailable() {
      if (!Runtime::getInstance()->extensionAvailable('com_dotnet')) {
        throw new PrerequisitesNotMetError('No COM extension available', NULL, array('ext/com_dotnet'));
      }
    }

    /**
     * Test creating a non-existant COM object
     *
     */
    #[@test]
    public function create() {
      new COMObject('WScript.Shell');
    }
    
    /**
     * Test creating a non-existant COM object
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function createNonExistant() {
      new COMObject('WScript.NONEXISTANT');
    }
    
    /**
     * Test reading the WshShell.CurrentDirectory property
     *
     * @see   php://getcwd
     * @see   http://msdn.microsoft.com/en-us/library/3cc5edzd(v=VS.85).aspx
     */
    #[@test]
    public function readProperty() {
      $this->assertEquals(getcwd(), create(new COMObject('WScript.Shell'))->CurrentDirectory);
    }

    /**
     * Test reading the WshShell.Environment property
     *
     * @see   php://getenv
     * @see   http://msdn.microsoft.com/en-us/library/fd7hxfdd(v=VS.85).aspx
     */
    #[@test]
    public function variantsAreWrappedInCOMObjects() {
      $this->assertInstanceOf(
        'com.microsoft.com.COMObject', 
        create(new COMObject('WScript.Shell'))->Environment
      );
    }

    /**
     * Test reading an offset of the WshShell.Environment property
     *
     * @see   php://getenv
     * @see   http://msdn.microsoft.com/en-us/library/6s7w15a0(v=VS.85).aspx
     */
    #[@test]
    public function supportsIndexers() {
      $this->assertEquals(
        getenv('OS'), 
        create(new COMObject('WScript.Shell'))->Environment['OS']
      );
    }

    /**
     * Test reading an offset of the WshShell.Environment property
     *
     * @see   php://getenv
     * @see   http://msdn.microsoft.com/en-us/library/6s7w15a0(v=VS.85).aspx
     */
    #[@test]
    public function supportsIteration() {
      $env= create(new COMObject('WScript.Shell'))->Environment;
      $i= 0;
      foreach ($env as $pair) {
        $i++;
      }
      $this->assertEquals($env->Length, $i);
    }

    /**
     * Test reading non-existant property
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function readNonExistantProperty() {
      create(new COMObject('WScript.Shell'))->NonExistant;
    }

    /**
     * Test reading an offset of the WshShell object itself
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function objectWithoutIndexers() {
      $sh= new COMObject('WScript.Shell');
      $read= $sh[0];
    }

    /**
     * Test calling the WshShell.RegRead method
     *
     * @see   http://msdn.microsoft.com/en-us/library/x05fawxd(v=VS.85).aspx
     */
    #[@test]
    public function callMethod() {
      $this->assertNotEquals(NULL, create(new COMObject('WScript.Shell'))->RegRead('HKLM\\SOFTWARE\\Microsoft\\Windows\\CurrentVersion\\ProgramFilesDir'));
    }

    /**
     * Test calling non-existant method
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function callNonExistantMethod() {
      create(new COMObject('WScript.Shell'))->NonExistant();
    }

    /**
     * Test calling method Run() with not enough arguments
     *
     * @see   http://msdn.microsoft.com/en-us/library/d5fk67ky(v=VS.85).aspx
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function callMethodWithNotEnoughArguments() {
      create(new COMObject('WScript.Shell'))->Run();
    }

    /**
     * Test calling method RegRead() with too many arguments
     *
     * @see   http://msdn.microsoft.com/en-us/library/x05fawxd(v=VS.85).aspx
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function callMethodWithTooManyArguments() {
      create(new COMObject('WScript.Shell'))->RegRead('A', 'exceed');
    }

    /**
     * Test calling method RegRead() with type conflict
     *
     * @see   http://msdn.microsoft.com/en-us/library/x05fawxd(v=VS.85).aspx
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function callMethodWithWrongArguments() {
      create(new COMObject('WScript.Shell'))->RegRead(NULL);
    }
  }
?>
