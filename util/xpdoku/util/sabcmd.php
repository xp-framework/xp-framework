<?php
/* This file is part of the XP framework
 *
 * $Id$
 */

  require('lang.base.php');
  uses(
    'util.cmd.ParamString',
    'text.PHPSyntaxHighlighter',
    'xml.XSLProcessor'
  );
  
  function schemeHandler($proc, $scheme, $rest) {
    static $s= NULL;
    if (!isset($s)) $s= &new PHPSyntaxHighlighter();

    switch ($scheme) {
      case 'php':
        $s->setSource("<?php\n".substr($rest, 2)."\n?>");
        return '<php>'.strtr(substr($s->getHighlight(), 6, -7), array(
          '<br />'   => "<br />\n", 
          '&lt;?php' => '',
          '?&gt;'    => '<br />'
        )).'</php>';
        break;
    }
    return '<xml/>';
  }
  
  // {{{ main
  $p= &new ParamString();
  if (4 != $p->count) {
    printf("Usage: %s xsl xml area\n", basename($p->value(0)));
    exit(-1);
  }
  
  $proc= &new XSLProcessor();
  $proc->setXSLFile($p->value(1));
  $proc->setXMLFile($p->value(2));
  $proc->setParam('area', $p->value(3));
  $proc->setSchemeHandler(array('get_all' => 'schemeHandler'));
  
  try(); {
    $proc->run();
  } if (catch('Exception', $e)) {
    $e->printStackTrace();
    exit(-2);
  }
  
  echo $proc->output();
  // }}}
?>
