<?php namespace validation;

use lang\Object;
use lang\reflect\Field;
use lang\XPClass;

/**
 * Class validator
 *
 * @author jzinnau
 *
 */
class EntityValidator extends Object {

  const ASSERT_ANNOTATION_NAME= 'Assert';

  /**
   * Validates object against constraints defined in its annotations
   *
   * @param Object $entity
   * @return ValidationResult
   * @throws EntityValidationException
   */
  public function validate(Object $entity) {
    $result= new ValidationResult();

    /** @var XPClass $class */
    $class= $entity->getClass();
    $fields= $class->getFields();

    /** @var Field $field */
    foreach ($fields as $field) {
      if ($field->hasAnnotations() && $field->hasAnnotation(self::ASSERT_ANNOTATION_NAME)) {
        $fieldName= $field->getName();
        $getterMethodName= 'get'.ucfirst($field->getName());
        $fieldValue= $class->hasMethod($getterMethodName) ?
          $entity->$getterMethodName() :
          $entity->$fieldName;
        $context= new ValidationContext($fieldName);

        $annotation= $field->getAnnotation(self::ASSERT_ANNOTATION_NAME);
        if (!is_array($annotation)) {
          throw new EntityValidationException('Invalid assert annotation format (no array)');
        }
       /* if (isset($annotation['type'])) {
          $annotation= array($annotation);
        }*/

        foreach ($annotation as $assert) {
          $constraintValidator= XPClass::forName($assert['type'])->newInstance(
            $context,
            $assert
          );
          $valid= $constraintValidator->validate($entity->$fieldName);
          if (!$valid && $constraintValidator->continueValidationAfterViolation() == false) {
            break;
          }
        }

        if ($context->hasViolations()) {
          $result->addContextResults($context);
        }
      }
    }

    return $result;
  }

}