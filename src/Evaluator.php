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

    $comparison = $condition->getComparison();
    $operator = $condition->getOperator();

    if ($data instanceof ComparableDataInterface) {
      return $data->compare($comparison, $operator);
    }
    else {
      return ComparableDataValue::create($data)->compare($comparison, $operator);
    }
  }

}
