<?php
/* This class is part of the XP framework's people's experiments
 *
 * $Id$ 
 */
  class WikiSyntaxParser extends Object {
    var
      $state  = array('initial');
      
    function pushState($s) {
      array_unshift($this->state, $s);
    }
    
    function popState() {
      array_shift($this->state);
    }
    
    function currentState() {
      return $this->state[0];
    }
    
    #[@parser(state = '*', pattern = '°(ht|f)tps?://([^ \)$]+)°')]
    function url($matches) {
      return '<link href="'.$matches[0].'"/>';
    }

    #[@parser(state = 'initial', pattern= '/^== (.+) ==$/')]
    function section($matches) {
      return '<h1 title="'.$matches[1].'"/>';
    }

    #[@parser(state = 'initial', pattern= '/^=== (.+) ===$/')]
    function subsection($matches) {
      return '<h2 title="'.$matches[1].'"/>';
    }

    #[@parser(state = 'initial', pattern= '/^(\*)+ (.*)/')]
    function beginUl($matches) {
      $this->pushState('list');
      return '<ul><li>'.$matches[2].'</li>';
    }
    
    #[@parser(state = 'list', pattern= '/^(\*)+ (.*)/')]
    function li($matches) {
      return '<li>'.$matches[2].'</li>';
    }

    #[@parser(state = 'list', pattern= '/^$/')]
    function endUl($matches) {
      $this->popState();
      return '</ul>';
    }
  }
?>
