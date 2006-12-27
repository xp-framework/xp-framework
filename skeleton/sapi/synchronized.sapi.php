<?php
/* This file provides the synchronized sapi for the XP framework
 * 
 * $Id$
 */

  // {{{ int ftok(string pathname, string id)
  //     Convert a pathname and a project identifier to a System V IPC key
  if (!function_exists('ftok')) { function ftok($pathname, $id) {
    if (!($s= stat($pathname))) return -1;
    return sprintf('%u', ord($id{0}) << 24 | ($s['dev'] & 0xFF) << 16 | $s['ino'] & 0xFFFF);
  }}
  // }}}

  // {{{ internal void unlock(void)
  //     Shutdown function
  function __unlock() {
    if (isset($_SERVER['sync'])) {
      shmop_delete($_SERVER['sync']);
      shmop_close($_SERVER['sync']);
      unset($_SERVER['sync']);
    }
  }
  // }}}

  if (!extension_loaded('shmop')) {
    xp::error('[sapi::synchronized] Shmop extension not available');
    // Bails out
  }

  $identifier= ftok($_SERVER['argv'][0], 'x');
  if (!($shm= shmop_open($identifier, 'n', 0664, 0xA))) {
    $shm= shmop_open($identifier, 'a', 0, 0);
    $pid= shmop_read($shm, 0x0, 0xA);
    shmop_close($shm);
    xp::error(sprintf(
      '[sapi::synchronized] Already running under pid %d [key=%s]',
      $pid,
      $identifier
    ));
    // Bails out
  }
  shmop_write($shm, getmypid(), 0x0);
  $_SERVER['sync']= $shm;
  register_shutdown_function('__unlock');
?>
