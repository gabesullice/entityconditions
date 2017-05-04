<?php

namespace Drupal\entityconditions\Plugin\DataType;

use Drupal\Core\TypedData\Plugin\DataType\Map;

/**
 * @DataType(
 *   id = "condition_group",
 *   label = @Translation("Condition Group"),
 *   definition_class = "\Drupal\entityconditions\TypedData\ConditionGroupDefinition"
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
   * @return \Drupal\entityconditions\Plugin\DataType\Condition[]|\Drupal\entityconditions\Plugin\DataType\ConditionGroup[]
   */
  public function getMembers() {
    return $this->get('members')->getValue();
  }

}
