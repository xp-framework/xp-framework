<?php
/* Diese Klasse ist Teil des XP-Frameworks
 *
 * $Id$
 */

  uses('util.Properties');

  /**
   * Will notify after a given amount of occurences
   *
   * Example params parameter for NotifiedException:
   * <pre>
   *   'escalated' => array(
   *      'rules' => array(
   *        'c(%5) && c(<100)'                  => 'mail',      // Every 5 times until 100
   *        'c(%10) && c(>100) && d(-5,6)'      => 'sms',       // As of #100 every 10 times, only on weekends
   *        'c(=5)'                             => 'sms'        // Fifth occurence
   *        'c(=61) || t(-19,24)'               => 'sms'        // 61st occurence or between 7 PM and midnight
   *      ),
   *      'mail'        => 'foo@bar.baz',
   *      'sms'         => 'foo@sms-gate.bar',
   *      'stor'        => '/tmp/notify.escalated'
   *    )
   * </pre>
   *
   * In most cases, we'd like to reset error counts after a while (e.g.,
   * when the error stops occuring). This notifier will need a cronjob or 
   * other code to clean up the "stor" file (or else counting will continue 
   * forever) since - of course - it is only called when things go wrong:-)
   */
  class EscalatedNotifier extends Object {
  
    /**
     * Notify
     *
     * @access  public
     * @param   string messages
     * @param   array params Parameter, associative array, see classdoc example
     * @param   string detail
     * @param   string stack
     * @return  bool success
     */
    function notify($message, $params, $details, $stack) {
    
      // Create a unique id for this exception if none specified
      // md5(message) might not be accurate if message is dynamically
      // constructed
      if (empty($params['id'])) $params['id']= md5($message);

      // Save exceptions to file      
      $p= &new Properties($params['stor']);
      $result= TRUE;
      try(); {
      
        // Create if necessary
        if (!$p->exists()) $p->create();
        
        // Read and increase count
        $p->writeInteger(
          $params['id'], 
          'count', 
          $count= $p->readInteger($params['id'], 'count', 0)+ 1
        );
       
        // Go through rules
        foreach ($params['rules'] as $rule=> $config) {
          $notify= '';
          
          // Go through list of expressions
          foreach (explode(' ', $rule) as $expr) {
            $dat= substr($expr, 3, -1);
            $notify.= ' ';
            
            // Switch on operator: Detect functions
            switch ($expr{0}) {
              case 'c': $cmp= $count; break;                    // Occurences count 
              case 't': $cmp= date('H'); break;                 // Hour
              case 'd': $cmp= (date('w') + 6) % 7; break;       // Day of week 0..6
              default: $notify.= $expr; continue 2;
            }
            
            // Function operators
            switch ($expr{2}) {
              case '%': $n= ($cmp % $dat == 0); break;
              case '=': $n= ($cmp == $dat); break;
              case '>': $n= ($cmp > $dat); break;
              case '<': $n= ($cmp < $dat); break;
              case '-': list($s, $e)= explode(',', $dat); $n= ($cmp >= $s && $cmp <= $e); break;
              default: $n= FALSE;
            }
            $notify.= $n ? 'TRUE' : 'FALSE';
          }
          eval('$notify='.$notify.';');
          if (!$notify) continue;
          
          // Send mail
          $status= mail(
            $params[$config],
            'Escalation for '.$message.' ['.$config.']',
            '--Occurance #'.$count.' matched by rule '.$rule."\n\n".
            $details."\n\n*** Stack Trace: ***\n".$stack,
            'X-Sender: '.$this->getClassName()
          );
          
          // Log notifications
          $p->writeInteger(
            $params['id'], 
            'notify.'.$config.'.count', 
            $p->readInteger($params['id'], 'notify.'.$config.'.count', 0)+ 1
          );
          $p->writeString($params['id'], 'notify.'.$config.'.rule', $rule);
          $p->writeString($params['id'], 'notify.'.$config.'.datetime', date('r'));
          $p->writeString($params['id'], 'notify.'.$config.'.recipient', $params[$config]);
          $p->writeBool($params['id'], 'notify.'.$config.'.result', $status);
          
          $result= $result & $status;
        }
        
        // Save message to make file "human readable"
        $p->writeString($params['id'], 'message', $message);
        
        // Save to disk
        $p->save();
      } if (catch('Exception', $e)) {
        return FALSE;
      }
      
      return $result;
    }
  }
?>
