<?php namespace xp\codegen;

/**
 * Generator
 */
abstract class AbstractGenerator extends \lang\Object {
  public 
    $storage = null,
    $output  = null;
 
  /**
   * Returns storage
   *
   * @return  xp.codegen.AbstractStorage
   */
  #[@target]
  public function storage() {
    return $this->storage;
  } 

  /**
   * Returns output
   *
   * @return  xp.codegen.AbstractStorage
   */
  #[@target]
  public function output() {
    return $this->output;
  } 
}
