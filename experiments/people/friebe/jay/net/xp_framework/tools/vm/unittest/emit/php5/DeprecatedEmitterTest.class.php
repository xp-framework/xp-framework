<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses('net.xp_framework.tools.vm.unittest.emit.php5.AbstractEmitterTest');

  /**
   * Tests PHP5 emitter
   *
   * @purpose  Unit Test
   */
  class DeprecatedEmitterTest extends AbstractEmitterTest {

    /**
     * Tests old foreach: endforeach syntax is deprecated
     *
     * @access  public
     */
    #[@test, @expect('lang.FormatException')]
    function deprecatedForeach() {
      $this->emit('foreach ($array as $key => $value): endforeach');
    }

    /**
     * Tests old for: endfor syntax is deprecated
     *
     * @access  public
     */
    #[@test, @expect('lang.FormatException')]
    function deprecatedFor() {
      $this->emit('for ($i= 0; $i < 10; $i++): endfor');
    }

    /**
     * Tests old if: endif syntax is deprecated
     *
     * @access  public
     */
    #[@test, @expect('lang.FormatException')]
    function deprecatedIf() {
      $this->emit('if ($i): endif');
    }

    /**
     * Tests old while: endwhile syntax is deprecated
     *
     * @access  public
     */
    #[@test, @expect('lang.FormatException')]
    function deprecatedWhile() {
      $this->emit('while ($i): endwhile');
    }

    /**
     * Tests old if: endswitch syntax is deprecated
     *
     * @access  public
     */
    #[@test, @expect('lang.FormatException')]
    function deprecatedSwitch() {
      $this->emit('switch ($foo):
        case 1:
        case 2:
        default: break;
      endswitch;');
    }


    /**
     * Tests "new Class;" - without (...) - is deprecated
     *
     * @access  public
     */
    #[@test, @expect('lang.FormatException')]
    function deprecatedConstructorWithoutBraces() {
      $this->emit('class Foo { } new Foo;');
    }
  }
?>
