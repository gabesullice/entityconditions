<?php

namespace Drupal\typed_data_conditions;

use Drupal\Core\TypedData\TypedDataInterface;
use Drupal\Core\TypedData\ComplexDataInterface;
use Drupal\typed_data_conditions\ConditionInterface;
/**
 * Evaluates data conditions against data.
 */
class Evaluator {

  /**
   * Returns a boolean result of the condition evaluated against the given data.
   *
   * @param \Drupal\Core\TypeData\TypedDataInterface $data
   *   The data against which the condition should be evaluated.
   * @param \Drupal\typed_data_conditions\ConditionInterface|\Drupal\typed_data_conditions\ConditionGroupInterface
   */
  public function evaluate(TypedDataInterface $data, $condition) {
    assert($condition instanceof ConditionInterface);

    if ($data instanceof ComplexDataInterface) {
      $value = $data->get($condition->getProperty())->getValue();
    }
    else {
      $value = $data->getValue();
    }

    $comparison = $condition->getComparison();

    switch ($condition->getOperator()) {
      case '=':
        return $value == $comparison;
      case '<>':
        return $value != $comparison;
      case '<':
        return $value < $comparison;
      case '>':
        return $value > $comparison;
      case '>=':
        return $value >= $comparison;
      case '<=':
        return $value <= $comparison;
      case 'BETWEEN':
        return $comparison[0] < $value && $value < $comparison[1];
      case 'NOT BETWEEN':
        return !($comparison[0] < $value && $value < $comparison[1]);
      case 'IN':
        return in_array($value, $comparison);
      case 'NOT IN':
        return !in_array($value, $comparison);
      case 'IS NULL':
        return is_null($value);
      case 'IS NOT NULL':
        return !is_null($value);
    }
  }

}
