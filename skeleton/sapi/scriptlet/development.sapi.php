<?php
/* This file provides the development scriptlet sapi for the XP framework
 * 
 * $Id$
 */

  xp::sapi('scriptlet.runner');

  // {{{ final class scriptlet
  class scriptlet extends scriptlet·runner {
  
    // {{{ abstract void except(&scriptlet.HttpScriptletResponse response, lang.Throwable e)
    //     Acts on exceptions
    function except(&$response, &$e) {
      $response->setContent(str_replace(
        '<xp:value-of select="reason"/>',
        $e->toString(),
        file_get_contents(
          dirname(__FILE__).
          DIRECTORY_SEPARATOR.
          'error'.
          $response->statusCode.
          '.html'
        )
      ));
    }
    // }}}

  }
  // }}}
?>
