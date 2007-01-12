<?php
/* This file is part of the XP framework's experiments
 *
 * $Id$ 
 */
  require('lang.base.php');
  xp::sapi('scriptlet.production', 'cgi');
  uses(
    'scriptlet.xml.workflow.WorkflowXMLScriptlet', 
    'scriptlet.xml.workflow.routing.MethodRouter', 
    'scriptlet.xml.workflow.routing.StaticRouter', 
    'scriptlet.xml.workflow.routing.ClassRouter', 
    'scriptlet.xml.workflow.routing.DelegatingRouter', 
    'scriptlet.xml.workflow.routing.FacadeRouter', 
    'xml.DomXSLProcessor',
    'rdbms.ConnectionManager',
    'util.Properties',
    'util.log.Logger',
    'util.log.FileAppender'
  );
  
  // {{{ main
  ConnectionManager::getInstance()->configure(new Properties(dirname(__FILE__).'/../etc/datasource.ini'));

  // $cat= Logger::getInstance()->getCategory();
  // $cat->addAppender(new FileAppender('/tmp/scriptlet.log'));

  scriptlet::run(newinstance('scriptlet.xml.workflow.WorkflowXMLScriptlet', array('classes'), '{
    protected function routerFor($request) { return new DelegatingRouter(new ClassRouter(), array(
      "static"    => new StaticRouter(),
      "news"      => new MethodRouter(),
      "jobs"      => new FacadeRouter(),
    )); }
  }'));
  // }}}
?>
