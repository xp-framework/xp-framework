<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  $package= 'net.xp_lang.tests.syntax.php';

  uses('net.xp_lang.tests.syntax.php.ParserTestCase');

  /**
   * TestCase
   *
   */
  class net·xp_lang·tests·syntax·php·ExceptionExpressionTest extends net·xp_lang·tests·syntax·php·ParserTestCase {
  
    /**
     * Test try/catch
     *
     */
    #[@test]
    public function singleCatch() {
      $this->assertEquals(array(new TryNode(array(
        'statements' => array(new MethodCallNode(new VariableNode('method'), 'call')),
        'handling'   => array(
          new CatchNode(array(
            'type'       => new TypeName('IllegalArgumentException'),
            'variable'   => 'e',
            'statements' => array(new MethodCallNode(new VariableNode('this'), 'finalize'))
          ))
        )
      ))), $this->parse('
        try {
          $method->call();
        } catch (IllegalArgumentException $e) {
          $this->finalize();
        }
      '));
    }

    /**
     * Test try/finally
     *
     */
    #[@test]
    public function singleThrow() {
      $this->assertEquals(array(new ThrowNode(array(
        'expression' => new InstanceCreationNode(array(
          'type'       => new TypeName('IllegalStateException'),
          'parameters' => NULL
        ))
      ))), $this->parse('
        throw new IllegalStateException();
      '));
    }

    /**
     * Test try w/ multiple catches
     *
     */
    #[@test]
    public function multipleCatches() {
      $this->assertEquals(array(new TryNode(array(
        'statements' => array(
          new ReturnNode(new InstanceCreationNode(array(
            'type'       => new TypeName('HashTable'),
            'parameters' => NULL
          )))
        ), 
        'handling'   => array(
          new CatchNode(array(
            'type'       => new TypeName('IllegalArgumentException'),
            'variable'   => 'e',
            'statements' => NULL, 
          )),
          new CatchNode(array(
            'type'       => new TypeName('SecurityException'),
            'variable'   => 'e',
            'statements' => array(new ThrowNode(array(
              'expression' => new VariableNode('e')
            ))), 
          )),
          new CatchNode(array(
            'type'       => new TypeName('Exception'),
            'variable'   => 'e',
            'statements' => NULL, 
          ))
        )
      ))), $this->parse('
        try {
          return new HashTable();
        } catch (IllegalArgumentException $e) {
        } catch (SecurityException $e) {
          throw $e;
        } catch (Exception $e) {
        }
      '));
    }
  }
?>
