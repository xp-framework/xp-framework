<?php

  /* This class is part of the XP framework
   *
   */

  uses(
    'lang.Type',
    'unittest.mock.MockProxy',
    'unittest.mock.MockProxyBuilder'
  );

  /**
   * Class for creating mock/stub instances of arbitrary types
   *
   * @purpose  Mocking
   */
  class MockRepository extends Object {
    private
      $mocks= array();

    /**
     * Builds a stub instance for the specified type.
     *
     * @param   string typeName
     * @param   boolean overrideAll
     * @return  Object
     */
    public function createMock($typeName, $overrideAll= TRUE) {
      $type= Type::forName($typeName);

      if (!($type instanceof XPClass)) {
        throw new IllegalArgumentException('Cannot mock other types than XPClass types.');
      }

      $parentClass= NULL;
      $interfaces= array(XPClass::forName('unittest.mock.IMock'));
      if($type->isInterface()) {
        $interfaces[]= $type;
      } else {
        $parentClass= $type;
      }
      
      $defaultCL= ClassLoader::getDefault();

      $proxy= new MockProxyBuilder();
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
      foreach($this->mocks as $mock) {
        $mock->_replayMock();
      }
    }

    /**
     * Verifies all mocks.
     */
    public function verifyAll() {
      foreach($this->mocks as $mock) {
        $mock->_verifyMock();
      }
    }

  }

?>