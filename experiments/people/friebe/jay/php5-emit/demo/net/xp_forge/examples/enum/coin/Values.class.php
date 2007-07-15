<?php
  uses('util.cmd.Command', 'net.xp_forge.examples.enum.coin.Coin');$package= 'net.xp_forge.examples.enum.coin'; class net·xp_forge·examples·enum·coin·Values extends util·cmd·Command{
/**
 * @return  function
 */
public function run(){foreach (net·xp_forge·examples·enum·coin·Coin::values() as $coin) {$this->out->writeLine($coin->name, ': ', $coin->value(), '¢ (', $coin->color(), ')');
  };
  }};
  
?>