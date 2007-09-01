<?php
/* This class is part of the XP framework
 *
 * $Id: BugConstants.class.php 2691 2003-11-12 13:52:55Z friebe $ 
 */

  namespace org::bugzilla;

  // Severities
  define('BUG_SEVERITY_BLOCKER',      'blocker');
  define('BUG_SEVERITY_CRITICAL',     'critical');
  define('BUG_SEVERITY_MAJOR',        'major');
  define('BUG_SEVERITY_NORMAL',       'normal');
  define('BUG_SEVERITY_MINOR',        'minor');
  define('BUG_SEVERITY_TRIVIAL',      'trivial');
  define('BUG_SEVERITY_ENHANCEMENT',  'enhancement');
  
  // Stati
  define('BUG_STATUS_UNCONFIRMED',    'UNCONFIRMED');
  define('BUG_STATUS_NEW',            'NEW');
  define('BUG_STATUS_ASSIGNED',       'ASSIGNED');
  define('BUG_STATUS_REOPENED',       'REOPENED');
  define('BUG_STATUS_RESOLVED',       'RESOLVED');
  define('BUG_STATUS_VERIFIED',       'VERIFIED');
  define('BUG_STATUS_CLOSED',         'CLOSED');
  
  // Resolutions
  define('BUG_RESOLUTION_NONE',       '');
  define('BUG_RESOLUTION_FIXED',      'FIXED');
  define('BUG_RESOLUTION_INVALID',    'INVALID');
  define('BUG_RESOLUTION_WONTFIX',    'WONTFIX');
  define('BUG_RESOLUTION_LATER',      'LATER');
  define('BUG_RESOLUTION_REMIND',     'REMIND');
  define('BUG_RESOLUTION_DUPLICATE',  'DUPLICATE');
  define('BUG_RESOLUTION_WORKSFORME', 'WORKSFORME');
  define('BUG_RESOLUTION_MOVED',      'MOVED');
  
  // Priorities
  define('BUG_PRIORITY_1',            'P1');
  define('BUG_PRIORITY_2',            'P2');
  define('BUG_PRIORITY_3',            'P3');
  define('BUG_PRIORITY_4',            'P4');
  define('BUG_PRIORITY_5',            'P5');
  
  /**
   * Bugzilla bug constants
   *
   * @purpose  Wrap ENUMs
   */
  class BugConstants extends lang::Object {
  
  }
?>
