<?php

  /* This class is part of the XP framework
   *
   * $Id$
   */

  uses('lang.Type',
       'unittest.mock.MockProxy');

  /**
   * Class for creating mock/stub instances of arbitrary types
   *
   * @purpose  Mocking
   */
  class Mockery extends Object {
    private
      $mocks= array();

    /**
     * Builds a stub instance for the specified type.
     *
     * @param   string typeName
     * @param   boolean overrideAll
     * @return  Object
     */
    public function createMock($typeName, $overrideAll=false) {
      $type = Type::forName($typeName);

      if (!($type instanceof XPClass)) {
        throw new IllegalArgumentException('Cannot mock other types than XPClass types.');
      }

      $parentClass=null;
      $interfaces=array(XPClass::forName('unittest.mock.IMock'));
      if($type->isInterface())
        $interfaces[]=$type;
      else 
        $parentClass=$type;

      $defaultCL= ClassLoader::getDefault();

      $proxy= new Proxy();
      $proxy->setOverwriteExisting($overrideAll);
      $proxyClass= $proxy->createProxyClass($defaultCL, $interfaces, $parentClass);
      $mock= $proxyClass->newInstance(new MockProxy());
      $this->mocks[]= $mock;
      return $mock;
    }
    /**
     * Replays all mocks.
     */
    public function replayAll() {
      foreach($this->mocks as $mock)
        $mock->_replayMock();
    }

    /**
     * Verifies all mocks.
     */
    public function verifyAll() {
      foreach($this->mocks as $mock)
        $mock->_verifyMock();
    }

  }

?>