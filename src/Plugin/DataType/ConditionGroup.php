<?php

namespace Drupal\typed_data_conditions\Plugin\DataType;

use Drupal\Core\TypedData\Plugin\DataType\Map;

/**
 * @DataType(
 *   id = "condition_group",
 *   label = @Translation("Condition Group"),
 *   definition_class = "\Drupal\typed_data_conditions\TypedData\ConditionGroupDefinition"
 * )
 */
class ConditionGroup extends Map {

  /**
   * The AND conjunction value.
   */
  public static $allowedConjunctions = ['AND', 'OR'];

  /**
   * The condition group conjunction.
   *
   * @return string
   */
  public function getConjunction() {
    return $this->get('conjunction')->getCastedValue();
  }

  /**
   * The members which belong to the the condition group.
   *
   * @return \Drupal\typed_data_conditions\Plugin\DataType\Condition[]|\Drupal\typed_data_conditions\Plugin\DataType\ConditionGroup[]
   */
  public function getMembers() {
    return $this->get('members')->getValue();
  }

}
