<?php
/* Demonstrate the usage of NotifiedException
 * 
 * $Id$
 */

  require('lang.base.php');
  uses('util.notify.NotifiedException');
  
  try(); {
    throw(new NotifiedException(
      'Test-Notify',
      array(
        'mail' => 'thekid@localhost',
        'digest' => array(
          'after'       => 10,
          'mail'        => 'thekid@localhost',
          'subject'     => '[PT.WH2.DE] Session error',
          'stor'        => '/tmp/notify.digest'
        ),
        'escalated' => array(
          'rules' => array(
            'c(%5) && c(<100)' => 'mail',
            'c(=5)'            => 'sms'
          ),
          'mail'        => 'thekid@localhost',
          'sms'         => 'friebe@schlund.de',
          'stor'        => '/tmp/notify.escalate'
        )
      ),
      'Details'
    ));
  } if (catch('Exception', $e)) {
    $e->printStackTrace();
  }
?>
