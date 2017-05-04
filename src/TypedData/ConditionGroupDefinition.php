<?php

namespace Drupal\entityconditions\TypedData;

use \Drupal\Core\TypedData\DataDefinition;
use \Drupal\Core\TypedData\MapDataDefinition;
use \Drupal\entityconditions\Plugin\DataType\ConditionGroup;

class ConditionGroupDefinition extends MapDataDefinition {

  /**
   * The condition group property definitions.
   *
   * @var \Drupal\Core\TypeData\DataDefinitionInterface[]
   */
  protected $propertyDefinitions;

  /**
   * {@inheritdoc}
   */
  public function getPropertyDefinitions() {
    if (!isset($this->propertyDefinitions)) {
      $this->propertyDefinitions['conjunction'] = DataDefinition::create('string')
        ->addConstraint('AllowedValues', ConditionGroup::$allowedConjunctions)
        ->setLabel('Conjunction')
        ->setRequired(TRUE);
      $this->propertyDefinitions['members'] = DataDefinition::create('any')
        // @todo figure out how to properly add a constraint here
        ->setLabel('Operator')
        ->setRequired(TRUE);
    }
    return $this->propertyDefinitions;
  }

}

