<?php
/* This class is part of the XP framework's experiments
 *
 * $Id$ 
 */
  
  require('__xp__.php');
  
  // {{{ Original
  // function openFile($file) {
  //   throw new xp~io~IOException('File "'.$file.'" cannot be opened');
  // }
  // 
  // try {
  //   throw new xp~io~IOException('Message');
  // } catch (xp~io~IOException $e) {
  //   echo '*** I/O: ', $e->toString(), "\n";
  // } catch (xp~lang~Throwable $e) {
  //   echo '*** Generic: ', $e->toString(), "\n";
  // } catch (php~Exception $e) {
  //   echo '!!! Unknown: ', $e, "\n";
  // }
  // }}}

  // {{{ Generated version
  function openFile($file) {
    throw xp::exception(new xp·io·IOException('File "'.$file.'" cannot be opened'));
  }
  
  try {
    openFile('/non-existant.file');
  } catch (XPException $__e) {
    if ($__e->cause instanceof xp·io·IOException) {
      $e= $__e->cause;
      echo '*** I/O: ', $e->toString(), "\n";
    } else if ($e->cause instanceof xp·lang·Throwable) {
      $e= $__e->cause;
      echo '*** Generic: ', $e->toString(), "\n";
    } else {
      throw $__e;
    }
  } catch (Exception $e) {
    echo '!!! Unknown: ', $e, "\n";
  }
  // }}}
?>
