<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Filter factory
   *
   * @see      io.collections.iterate.IterationFilter
   * @purpose  Factory
   */
  class FilterFactory extends Object {
  
    /**
     * Create a filter
     *
     * @model   static
     * @access  public
     * @param   string field
     * @param   string operator
     * @param   string value
     * @return  io.collections.iterate.IterationFilter
     * @throws  lang.IllegalArgumentException in case field / operator combination is unknown
     */
    public static function filterFor($field, $operator, $value) {
      static $lookup= array(
        'name='         => 'NameEquals',
        'name~'         => 'NameMatches',

        'extenstion='   => 'ExtensionEquals',
        
        'size='         => 'SizeEquals',
        'size<'         => 'SizeSmallerThan',
        'size>'         => 'SizeBiggerThan',
        
        'modified<'     => 'ModifiedBefore',
        'modified>'     => 'ModifiedAfter',

        'created<'      => 'CreatedBefore',
        'created>'      => 'CreatedAfter',

        'accessed<'     => 'AccessedBefore',
        'accessed>'     => 'AccessedAfter',
      );
      
      $key= $field.$operator;
      if (!isset($lookup[$key])) {
        throw(new IllegalArgumentException('Unknown filter "'.$key.'"'));
      }

      $constructor= XPClass::forName(sprintf('io.collections.iterate.%sFilter', $lookup[$key]))->getConstructor();
      $arguments= $constructor->getArguments();
      
      switch ($s= sizeof($arguments)) {
        case 0: return $constructor->newInstance();

        case 1: switch ($arguments[0]->getType()) {
          case 'util.Date': {
            try {
              $arg= Date::fromString($value);
            } catch (IllegalArgumentException $e) {
              throw new FormatException('Cannot parse string "'.$value.'", expecting YYYY-MM-DD');
            }
            break;
          }

          default: {
            // Leave value as-is
            $arg= $value;
          }
        }
        return $constructor->newInstance($arg);

        default: throw new IllegalArgumentException('Too many arguments ('.$s.'): '.$constructor->toString());
      }
      
      // Unreachable
    }
  }
?>
