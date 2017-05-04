<?php

namespace Drupal\entityconditions\TypedData;

use \Drupal\Core\TypedData\DataDefinition;
use \Drupal\Core\TypedData\MapDataDefinition;
use \Drupal\entityconditions\Plugin\DataType\Condition;

class ConditionDefinition extends MapDataDefinition {

  /**
   * The condition property definitions.
   *
   * @var \Drupal\Core\TypeData\DataDefinitionInterface[]
   */
  protected $propertyDefinitions;

  /**
   * {@inheritdoc}
   */
  public function getPropertyDefinitions() {
    if (!isset($this->propertyDefinitions)) {
      $this->propertyDefinitions['property'] = DataDefinition::create('string')
        ->setLabel('property')
        ->setRequired(TRUE);
      $this->propertyDefinitions['comparison'] = DataDefinition::create('any')
        ->setLabel('Value')
        ->setRequired(TRUE);
      $this->propertyDefinitions['operator'] = DataDefinition::create('string')
        ->setLabel('Operator')
        ->addConstraint('AllowedValues', Condition::$allowedOperators)
        ->setRequired(TRUE);
    }
    return $this->propertyDefinitions;
  }

}

