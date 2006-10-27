<?php
/* This file is part of the XP framework's experiments
 *
 * $Id$ 
 */
  require('lang.base.php');
  xp::sapi('scriptlet.production');
  uses('org.json.rpc.JsonRpcRouter');

  scriptlet::run(new JsonRpcRouter('service'));
?>
