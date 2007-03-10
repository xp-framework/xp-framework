<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses('net.xp_framework.tools.vm.unittest.migrate.AbstractRewriterTest');

  /**
   * Tests class rewriting
   *
   * @purpose  Unit Test
   */
  class ClassTest extends AbstractRewriterTest {

    /**
     * Tests the most basic form of a class
     *
     */
    #[@test]
    public function basicClass() {
      $this->assertClassRewritten(
        'package lang { class Object { } }', 
        'class', 'lang.Object'
      );
    }

    /**
     * Tests the most basic form of an interface
     *
     */
    #[@test]
    public function basicInterface() {
      $this->assertClassRewritten(
        'package util.log { interface Traceable { } }', 
        'interface', 'util.log.Traceable'
      );
    }

    /**
     * Tests extending a class in the same package
     *
     */
    #[@test]
    public function extendingClassInSamePackage() {
      $this->assertClassRewritten(
        'package lang.types { class Integer extends Number { } }', 
        'class', 'lang.types.Integer', 'lang.types.Number'
      );
    }

    /**
     * Tests extending a class in a different package
     *
     */
    #[@test]
    public function extendingClass() {
      $this->assertClassRewritten(
        'package rdbms.sybase { class SybaseConnection extends rdbms.DBConnection { } }', 
        'class', 'rdbms.sybase.SybaseConnection', 'rdbms.DBConnection'
      );
    }

    /**
     * Tests extending the Object class
     *
     */
    #[@test]
    public function extendingObjectClass() {
      $this->assertClassRewritten(
        'package lang.types { class Number { } }', 
        'class', 'lang.types.Number', 'lang.Object'
      );
    }
  }
?>
