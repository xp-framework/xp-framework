<?php
/* This file is part of the XP framework
 *
 * $Id$
 */
  uses(
    'util.log.ColoredConsoleAppender',
    'rdbms.criterion.Projections',
    'de.schlund.db.rubentest.Ncolor',
    'test.SQLTest'
  );

  /**
   * test SQL projection implementation
   *
   * @ext      mysql
   * @see      xp://rdbms.SQLFunctions
   * @purpose  test
   */
  class ProjectionTest extends SQLTest {

    /**
     * main method
     *
     */
    public function run() {
      parent::run();
      if ($this->showSource) $this->logger->debug('$crit'."= create(new Criteria())->add(Restrictions::like(Ncolor::column('name'), '%green'))");
      $crit= Criteria::newInstance()->add(Restrictions::like(Ncolor::column('name'), '%green'));

      Console::writeLine(xp::stringOf('\n\nwithProjection count:'));
      if ($this->showSource) $this->logger->debug("Ncolor::getPeer()->iteratorFor(".'$crit'."->withProjection(Projections::count()))->next()->get('count')");
      Console::writeLine(xp::stringOf(Ncolor::getPeer()->iteratorFor($crit->withProjection(Projections::count()))->next()->get('count')));

      Console::writeLine(xp::stringOf('\n\nwithout projection:'));
      if ($this->showSource) $this->logger->debug("Ncolor::getPeer()->doSelect(".'$crit'.")");
      Console::writeLine(xp::stringOf(Ncolor::getPeer()->doSelect($crit)));
    }


    /**
     * define peer
     *
     * @return rdbms.Peer
     */
    protected function getPeer() {
      return Ncolor::getPeer();
    }

    /**
     * define criteria
     *
     * @return array<rdbms.Criteria>
     */
    protected function getCrits() {
      $crits= array();
      $crits['count']= "create(new Criteria())->setProjection(
        Projections::count()
      )";

      $crits['count1']= "create(new Criteria())->setProjection(
        Projections::count(Ncolor::column('color_id'))
      )";

      $crits['count2']= "create(new Criteria())->setProjection(
        Projections::count('*', 'counting all')
      )";

      $crits['count3']= "create(new Criteria())->setProjection(
        Projections::count(Ncolor::column('color_id'), 'counting all')
      )";

      $crits['average']= "create(new Criteria())->setProjection(
        Projections::average(Ncolor::column('color_id'))
      )";

      $crits['max']= "create(new Criteria())->setProjection(
        Projections::max(Ncolor::column('color_id'))
      )";

      $crits['min']= "create(new Criteria())->setProjection(
        Projections::min(Ncolor::column('color_id'))
      )";

      $crits['sum']= "create(new Criteria())->setProjection(
        Projections::sum(Ncolor::column('color_id'))
      )";

      $crits['property']= "create(new Criteria())->setProjection(
        Projections::property(Ncolor::column('name'))
      )";

      $crits['column']= "create(new Criteria())->setProjection(
        Ncolor::column('name')
      )";

      $crits['projectionList']= "create(new Criteria())->setProjection(
        Projections::projectionList()
        ->add(Projections::property(Ncolor::column('color_id'), 'id'))
        ->add(Projections::property(Ncolor::column('name')))
      )";

      $crits['columnlist']= "create(new Criteria())->setProjection(
        Projections::projectionList()
        ->add(Ncolor::column('color_id'), 'id')
        ->add(Ncolor::column('name'))
      )";

      $crits['plain']= "new Criteria()";

      return $crits;
    }
  }
?>
