<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  define('ENTITY',     '<? extends DataSet>');
  define('COLLECTION', '<? extends DataSet>[]');

  uses(
    'rdbms.finder.FinderException',
    'rdbms.finder.FinderMethod',
    'rdbms.finder.NoSuchEntityException',
    'lang.MethodNotImplementedException'
  );

  /**
   * A finder is a collection of criteria for a given rdbms.Peer object.
   *
   * Declaration:
   * <code>
   *   class JobFinder extends Finder {
   *     public function getPeer() {
   *       return Job::getPeer();
   *     }
   *
   *     #[@finder(kind= ENTITY)]
   *     public function byPrimary($pk) {
   *       return new Criteria(array('job_id', $pk, EQUAL));
   *     }
   *
   *     #[@finder(kind= COLLECTION)]
   *     public function expiredJobs() {
   *       return new Criteria(array('expire_at', Date::now(), GREATER_EQUAL));
   *     }
   *   }
   * </code>
   *
   * Finding single entities:
   * <code>
   *   $jf= new JobFinder();
   *   $job= $jf->find($jf->byPrimary(10));
   * </code>
   *
   * Reflective use:
   * <code>
   *   $jf= new JobFinder();
   *
   *   Console::writeLine($jf->getClassName(), ' provides the following list methods:');
   *   foreach ($jf->collectionMethods() as $m) {
   *     Console::writeLine('- ', $m->getName());
   *   }
   * </code>
   *
   * Finding a collection of entities:
   * <code>
   *   // Hardcoded version
   *   $jf->findAll($jf->expiredJobs(10));
   *
   *   // Generic access version
   *   $jf->findAll($jf->method('expiredJobs')->invoke(array(10)));
   * </code>
   *
   * Iterating on a collection of entities:
   * <code>
   *   for ($iterator= $jf->iterate($jf->expiredJobs(10)); $iterator->hasNext(); ) {
   *     Console::writeLine($iterator->next());
   *   }
   * </code>
   *
   * @test     xp://net.xp_framework.unittest.rdbms.FinderTest
   * @purpose  Base class for all finder
   */
  abstract class Finder extends Object {
  
    /**
     * Returns the associated peer objects
     *
     * @return  rdbms.Peer
     */
    public abstract function getPeer();
    
    /**
     * Helper methods for *Methods() methods.
     *
     * @param   string kind
     * @return  rdbms.finder.FinderMethod[]
     */
    protected function finderMethods($kind) {
      $r= array();
      foreach ($this->getClass()->getMethods() as $m) {
        if (
          $m->hasAnnotation('finder') &&
          (NULL === $kind || $kind == $m->getAnnotation('finder', 'kind'))
        ) $r[]= new FinderMethod($this, $m);
      }
      return $r;
    }

    /**
     * Returns all finder methods
     *
     * @see     xp://rdbms.finder.Finder#entityMethods
     * @see     xp://rdbms.finder.Finder#collectionMethods
     * @return  rdbms.finder.FinderMethod[]
     */
    public function allMethods() {
      return $this->finderMethods(NULL);
    }

    /**
     * Returns all finder methods that return a single entity
     *
     * @return  rdbms.finder.FinderMethod[]
     */
    public function entityMethods() {
      return $this->finderMethods(ENTITY);
    }

    /**
     * Returns all finder methods that return a colleciton of entities
     *
     * @return  rdbms.finder.FinderMethod[]
     */
    public function collectionMethods() {
      return $this->finderMethods(COLLECTION);
    }

    /**
     * Retrieve a single finder method. Returns the all() method if the 
     * name argument is NULL
     *
     * @param   string name
     * @return  rdbms.finder.FinderMethod
     * @throws  rdbms.finder.FinderException in case the method does not exist or is no finder
     */
    public function method($name) {
      NULL === $name && $name= 'all';

      try {
        $m= $this->getClass()->getMethod($name);
      } catch (ElementNotFoundException $e) {
        throw new FinderException('No such finder', new MethodNotImplementedException('Cannot find finder method', $name));
      }

      if (!$m->hasAnnotation('finder')) {
        throw new FinderException('Not a finder', new IllegalArgumentException($m->getName()));
      }
      
      return new FinderMethod($this, $m);
    }
    
    /**
     * Returns an empty criteria object
     *
     * @return  rdbms.Criteria
     */
    #[@finder(kind= COLLECTION)]
    public function all() {
      return new Criteria();
    }
 
    /**
     * Get a single entity by specified criteria.
     *
     * @param   rdbms.Criteria
     * @return  rdbms.DataSet
     * @throws  rdbms.finder.NoSuchEntityException
     * @throws  rdbms.finder.FinderException
     */
    public function get($criteria) {
      try {
        $it= $this->getPeer()->iteratorFor($criteria);
      } catch (SQLException $e) {
        throw new FinderException('Failed finding '.$this->getPeer()->identifier, $e);
      }
      
      // Check for results. If we cannot find anything, throw a NSEE
      if (!$it->hasNext()) {
        throw new NoSuchEntityException(
          'Entity does not exist', 
          new IllegalStateException('No results for '.$criteria->toString())
        );
      }
      
      // Fetch first value, and if nothing is returned after that, return it,
      // throwing an exception otherwise
      $e= $it->next();
      if ($it->hasNext()) {
        throw new FinderException(
          'Query returned more than one result ('.$s.')', 
          new IllegalStateException('')
        );
      }
      
      return $e;
    }

    /**
     * Get a list of entities by specified criteria..
     *
     * @param   rdbms.Criteria
     * @return  rdbms.DataSet[]
     * @throws  rdbms.finder.NoSuchEntityException
     * @throws  rdbms.finder.FinderException
     */
    public function getAll($criteria) {
      try {
        $list= $this->getPeer()->doSelect($criteria);
      } catch (SQLException $e) {
        throw new FinderException('Failed finding '.$this->getPeer()->identifier, $e);
      }
      
      // Check for results. If we cannot find anything, throw a NSEE
      if (empty($list)) {
        throw new NoSuchEntityException(
          'Entity does not exist', 
          new IllegalStateException('No results for '.$criteria->toString())
        );
      }

      return $list;
    }
   
    /**
     * Find a single entity by specified criteria. Returns NULL if 
     * nothing can be found.
     *
     * @param   rdbms.Criteria
     * @return  rdbms.DataSet
     * @throws  rdbms.finder.FinderException
     */
    public function find($criteria) {
      try {
        $it= $this->getPeer()->iteratorFor($criteria);
      } catch (SQLException $e) {
        throw new FinderException('Failed finding '.$this->getPeer()->identifier, $e);
      }
      
      // Check for results. If we cannot find anything, throw a NSEE
      if (!$it->hasNext()) {
        return NULL;
      }
      
      // Fetch first value, and if nothing is returned after that, return it,
      // throwing an exception otherwise
      $e= $it->next();
      if ($it->hasNext()) {
        throw new FinderException(
          'Query returned more than one result ('.$s.')', 
          new IllegalStateException('')
        );
      }
      
      return $e;
    }

    /**
     * Find a list of entities by specified criteria..
     *
     * @param   rdbms.Criteria
     * @return  rdbms.DataSet[]
     * @throws  rdbms.finder.FinderException
     */
    public function findAll($criteria) {
      try {
        return $this->getPeer()->doSelect($criteria);
      } catch (SQLException $e) {
        throw new FinderException('Failed finding '.$this->getPeer()->identifier, $e);
      }
    }

    /**
     * Iterate on a list of entities by specified criteria..
     *
     * @param   rdbms.Criteria
     * @return  rdbms.ResultIterator
     * @throws  rdbms.finder.FinderException
     */
    public function iterate($criteria) {
      try {
        return $this->getPeer()->iteratorFor($criteria);
      } catch (SQLException $e) {
        throw new FinderException('Failed finding '.$this->getPeer()->identifier, $e);
      }
    }
  }
?>
