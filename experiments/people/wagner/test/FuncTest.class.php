<?php
/* This file is part of the XP framework
 *
 * $Id$
 */
  uses(
    'util.log.ColoredConsoleAppender',
    'rdbms.SQLFunctions',
    'de.schlund.db.rubentest.Nmappoint',
    'test.SQLTest'
  );

  /**
   * test SQL function implementation
   *
   * @ext      mysql
   * @see      xp://rdbms.SQLFunctions
   * @purpose  test
   */
  class FuncTest extends SQLTest {

    /**
     * define peer
     *
     * @return rdbms.Peer
     */
    protected function getPeer() {
      return Nmappoint::getPeer();
    }

    /**
     * define criteria
     *
     * @return array<rdbms.Criteria>
     */
    protected function getCrits() {
      $crits= array();
      $crits[]= "create(new Criteria())->setProjection(SQLFunctions::day(SQLFunctions::getdate()), 'dayTest')";
      $crits[]= "create(new Criteria())->setProjection(SQLFunctions::ascii('a'), 'asciiTest')";
      $crits[]= "create(new Criteria())->setProjection(SQLFunctions::char('97'), 'charTest')";
      $crits[]= "create(new Criteria())->setProjection(SQLFunctions::len('aaaaaaa'), 'lentest')";
      $crits[]= "create(new Criteria())->setProjection(SQLFunctions::reverse('abcdefg'), 'reverseTest')";
      $crits[]= "create(new Criteria())->setProjection(SQLFunctions::space('4'), 'spaceTest')";
      $crits[]= "create(new Criteria())->setProjection(SQLFunctions::concat('aa', SQLFunctions::str(SQLFunctions::getdate()), 'cc'), 'concatTest')";
      $crits[]= "create(new Criteria())->setProjection(SQLFunctions::str(SQLFunctions::getdate()), 'getdateTest')";
      $crits[]= "create(new Criteria())->setProjection(SQLFunctions::str(SQLFunctions::dateadd('month', '-4', SQLFunctions::getdate())), 'dateaddTest')";
      $crits[]= "create(new Criteria())->setProjection(SQLFunctions::datediff('second', SQLFunctions::dateadd('day', '-4', SQLFunctions::getdate()), SQLFunctions::getdate()), 'datediffTest')";
      $crits[]= "create(new Criteria())->setProjection(SQLFunctions::datename('hour', SQLFunctions::getdate()), 'datenameTest')";
      $crits[]= "create(new Criteria())->setProjection(SQLFunctions::datepart('hour', new Date()), 'datenameTest')";
      $crits[]= "create(new Criteria())->setProjection(SQLFunctions::abs(-6), 'absTest')";
      $crits[]= "create(new Criteria())->setProjection(SQLFunctions::cot(45), 'cotTest')";
      $crits[]= "create(new Criteria())->setProjection(SQLFunctions::pi(), 'piTest')";
      $crits[]= "create(new Criteria())->setProjection(SQLFunctions::acos(SQLFunctions::cos(0.125)), 'cosAcosTest')";
      $crits[]= "create(new Criteria())->setProjection(SQLFunctions::asin(SQLFunctions::sin(0.125)), 'sinAsinTest')";
      $crits[]= "create(new Criteria())->setProjection(SQLFunctions::atan(SQLFunctions::tan(0.125)), 'tanAtanTest')";
      $crits[]= "create(new Criteria())->setProjection(SQLFunctions::atan(SQLFunctions::tan(0.125), 0), 'tanAtan2Test')";
      $crits[]= "create(new Criteria())->setProjection(SQLFunctions::degrees(SQLFunctions::pi()), 'degreesTest')";
      $crits[]= "create(new Criteria())->setProjection(SQLFunctions::radians(SQLFunctions::degrees(90)), 'radiansTest')";
      $crits[]= "create(new Criteria())->setProjection(SQLFunctions::ceil(5.1), 'ceilTest')";
      $crits[]= "create(new Criteria())->setProjection(SQLFunctions::floor(5.7), 'floorTest')";
      $crits[]= "create(new Criteria())->setProjection(SQLFunctions::exp(SQLFunctions::log(1)), 'expTest')";
      $crits[]= "create(new Criteria())->setProjection(SQLFunctions::log(SQLFunctions::exp(1)), 'logTest')";
      $crits[]= "create(new Criteria())->setProjection(SQLFunctions::log10(SQLFunctions::power(10, 5)), 'powerTest')";
      $crits[]= "create(new Criteria())->setProjection(SQLFunctions::power(10, SQLFunctions::log10(5)), 'log10Test')";
      $crits[]= "create(new Criteria())->setProjection(SQLFunctions::rand(), 'randTest')";
      $crits[]= "create(new Criteria())->setProjection(Projections::ProjectionList()->add(SQLFunctions::round(1.50), 'roundtest1')->add(SQLFunctions::round(1.49), 'roundtest2')->add(SQLFunctions::round(1.49, 1), 'roundtest3'))";
      $crits[]= "create(new Criteria())->setProjection(Projections::ProjectionList()->add(SQLFunctions::sign(-7), 'signTest1')->add(SQLFunctions::sign(0), 'signTest2')->add(SQLFunctions::sign(4), 'signTest3'))";
      $crits[]= "create(new Criteria())->setProjection(SQLFunctions::soundex('kawabanga'), 'soundexTest')";
      $crits[]= "create(new Criteria())->setProjection(SQLFunctions::cast('345', 'decimal'), 'datatypesTest')";
      $crits[]= "create(new Criteria())->setProjection(SQLFunctions::cast(Nmappoint::column('texture_id'), 'char'), 'datatypesTest')";
      $crits[]= "create(new Criteria())->add(Restrictions::equal('texture_id', SQLFunctions::ceil(SQLFunctions::asin(SQLFunctions::sin(0.125)))))";
      $crits[]= "create(new Criteria())->add(Restrictions::equal(Nmappoint::column('texture_id'), SQLFunctions::ceil(SQLFunctions::asin(SQLFunctions::sin(0.125)))))";
      $crits[]= "create(new Criteria())->setProjection(SQLFunctions::locate('foobar', 'bar'))";
      $crits[]= "create(new Criteria())->setProjection(SQLFunctions::locate(NULL, 'bar'))";
      $crits[]= "create(new Criteria())->setProjection(SQLFunctions::locate('foobarfoo', 'foo', 4))";
      $crits[]= "create(new Criteria())->setProjection(SQLFunctions::substring('foobarfoo', 2, 4))";
      return $crits;
    }
  }
?>
