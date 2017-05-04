<?php

namespace Drupal\entityconditions\Plugin\DataType;

use Drupal\Core\TypedData\Plugin\DataType\Map;

/**
 * @DataType(
 *   id = "condition",
 *   label = @Translation("Condition"),
 *   definition_class = "\Drupal\entityconditions\TypedData\ConditionDefinition"
 * )
 */
class Condition extends Map {

  /*
   * The allowed condition operators.
   *
   * @var string[]
   */
  public static $allowedOperators = [
    '=', '<>',
    '>', '>=', '<', '<=',
    'STARTS_WITH', 'CONTAINS', 'ENDS_WITH',
    'IN', 'NOT IN',
    'BETWEEN', 'NOT BETWEEN',
  ];

  public static function create($property, $value, $operator = NULL) {
    $typed_data_manager = Drupal::typeDataManager();
    $definition = $typed_data_manager->createDataDefinition('condition');
    $values = ['operator' => '='] + $values;
    return $typed_data_manager->create($definition, $values);
  }

  /**
   * The field to be evaluated.
   *
   * @return string
   */
  public function getProperty() {
    return $this->get('property')->getCastedValue();
  }

  /**
   * The value against which the condition should be evaluated.
   *
   * @return mixed
   */
  public function getComparison() {
    return $this->get('comparison')->getValue();
  }

  /**
   * The comparison operator to use for the evaluation.
   *
   * Can be '=', '<', '>', '>=', '<=', 'BETWEEN', 'NOT BETWEEN', 'IN', 'NOT IN',
   * 'IS NULL', 'IS NOT NULL', 'LIKE', 'NOT LIKE', 'EXISTS', 'NOT EXISTS'
   *
   * @return string
   */
  public function getOperator() {
    return $this->get('operator')->getCastedValue();
  }

}
