<?php namespace xp\codegen;

use util\Observer;
use util\Observable;

/**
 * Output for generation
 */
abstract class AbstractOutput extends Observable {
    
  /**
   * Store data
   *
   * @param   string name
   * @param   string data
   */
  protected abstract function store($name, $data);

  /**
   * Append a file and its data to the output
   *
   * @param   string name
   * @param   string data
   */
  public function append($name, $data) {
    $this->setChanged();
    $this->notifyObservers($name);
    $this->store($name, $data);
  }
  
  /**
   * Commit output
   *
   */
  public abstract function commit();
}
