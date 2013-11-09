<?php namespace net\xp_framework\unittest\rdbms\mock;

/**
 * Register mock connection
 *
 * @see   xp://net.xp_framework.unittest.rdbms.mock.MockConnection
 */
class RegisterMockConnection extends \lang\Object implements \unittest\TestClassAction {
  const MOCK_CONNECTION_CLASS = 'net.xp_framework.unittest.rdbms.mock.MockConnection';

  /**
   * This method gets invoked before any test method of the given class is
   * invoked, and before any methods annotated with beforeTest.
   *
   * @param  lang.XPClass $c
   * @return void
   * @throws unittest.PrerequisitesNotMetError
   */
  public function beforeTestClass(\lang\XPClass $c) {
    \rdbms\DriverManager::register('mock', \lang\XPClass::forName(self::MOCK_CONNECTION_CLASS));
  }

  /**
   * This method gets invoked after all test methods of a given class have
   * executed, and after any methods annotated with afterTest
   *
   * @param  lang.XPClass $c
   * @return void
   */
  public function afterTestClass(\lang\XPClass $c) {
    \rdbms\DriverManager::remove('mock');
  }
}