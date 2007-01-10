<?php
/* This file is part of the XP framework's experiments
 *
 * $Id$ 
 */
  require('lang.base.php');
  xp::sapi('scriptlet.production');
  uses(
    'scriptlet.xml.workflow.WorkflowXMLScriptlet', 
    'scriptlet.xml.workflow.MethodRouter', 
    'scriptlet.xml.workflow.StaticRouter', 
    'scriptlet.xml.workflow.ClassRouter', 
    'scriptlet.xml.workflow.DelegatingRouter', 
    'xml.DomXSLProcessor'
  );
  
  // {{{ main
  scriptlet::run(newinstance('scriptlet.xml.workflow.WorkflowXMLScriptlet', array('classes'), '{
    function &processorInstance() { return new DomXSLProcessor(); }
    function &routerFor(&$request) { return new DelegatingRouter(new ClassRouter(), array(
      "static" => new StaticRouter(),
      "news"   => new MethodRouter()
    )); }
  }'));
  // }}}
?>
