<?php

namespace Drupal\typed_data_conditions\Plugin\DataType;

use Drupal\Core\TypedData\Plugin\DataType\Map;
use Drupal\Core\TypedData\TypedDataInterface;
use Drupal\typed_data_conditions\EvaluatorInterface;

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

  /**
   * Evaluates the condition group.
   *
   * @return boolean
   */
  public function evaluate(TypedDataInterface $data) {
    $conjunction = $this->getConjunction();
    $members = $this->getMembers();

    if ($conjunction == 'OR') {
      return array_reduce($members, function ($result, $member) use ($data) {
        if ($result) return TRUE;
        return $member->evaluate($data);
      }, FALSE);
    }
    elseif ($conjunction == 'AND') {
      return array_reduce($members, function ($result, $member) use ($data) {
        if ($result === FALSE) return FALSE;
        return $member->evaluate($data);
      }, NULL);
    }
  }

}
