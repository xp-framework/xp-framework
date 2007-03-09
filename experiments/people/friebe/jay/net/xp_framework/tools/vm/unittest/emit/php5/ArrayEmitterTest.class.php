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
  class ArrayEmitterTest extends AbstractEmitterTest {

    /**
     * Tests array declaration
     *
     */
    #[@test]
    public function numericArrayDeclaration() {
      $this->assertSourcecodeEquals(
        '$a= array(0 => 1, 1 => 2, 2 => 3, );',
        $this->emit('$a= array(1, 2, 3);')
      );
    }

    /**
     * Tests array declaration
     *
     */
    #[@test]
    public function associativeArrayDeclaration() {
      $this->assertSourcecodeEquals(
        '$a= array(\'Foo\' => \'Bar\', );',
        $this->emit('$a= array("Foo" => "Bar");')
      );
    }

    /**
     * Tests array declaration
     *
     */
    #[@test]
    public function mixedTypeAssociativeArrayDeclaration() {
      $this->assertSourcecodeEquals(
        '$a= array(\'Foo\' => \'Bar\', \'Second\' => 2, 3 => \'Three\', );',
        $this->emit('$a= array("Foo" => "Bar", "Second" => 2, 3 => "Three");')
      );
    }

    /**
     * Tests array access
     *
     */
    #[@test]
    public function arrayAccess() {
      $this->assertSourcecodeEquals(
        '$a[$key]++;',
        $this->emit('$a[$key]++;')
      );
    }

    /**
     * Tests array access
     *
     */
    #[@test]
    public function nestedArrayAccess() {
      $this->assertSourcecodeEquals(
        '$a[$key][$subkey]++;',
        $this->emit('$a[$key][$subkey]++;')
      );
    }

    /**
     * Tests array access
     *
     */
    #[@test]
    public function nestedMemberArrayAccess() {
      $this->assertSourcecodeEquals(
        '$this->a[$key][$subkey]++;',
        $this->emit('$this->a[$key][$subkey]++;')
      );
    }

    /**
     * Tests array append operator "[]"
     *
     */
    #[@test]
    public function arrayAppendOperator() {
      $this->assertSourcecodeEquals(
        '$a[]= 1;',
        $this->emit('$a[]= 1;')
      );
    }

    /**
     * Tests array append operator "[]"
     *
     */
    #[@test]
    public function arrayAppendOperatorAfterArrayOffset() {
      $this->assertSourcecodeEquals(
        '$a[$key][]= 1;',
        $this->emit('$a[$key][]= 1;')
      );
    }

    /**
     * Tests list assignment
     *
     */
    #[@test]
    public function listAssignment() {
      $this->assertSourcecodeEquals(
        'list($o, $t, )= array(0 => 1, 1 => 2, );',
        $this->emit('list($o, $t)= array(1, 2);')
      );
    }
  }
?>
