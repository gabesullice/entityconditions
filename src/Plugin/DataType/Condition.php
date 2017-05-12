<?php

namespace Drupal\typed_data_conditions\Plugin\DataType;

use Drupal\Core\TypedData\Plugin\DataType\Map;
use Drupal\Core\TypedData\TypedDataInterface;
use Drupal\data_resolver\DataResolver;
use Drupal\typed_data_conditions\ConditionInterface;
use Drupal\typed_data_conditions\EvaluatorInterface;
use Drupal\typed_data_conditions\ComparableDataInterface;
use Drupal\typed_data_conditions\ComparableDataValue;
use Drupal\typed_data_conditions\ComparableListDataInterface;
use Drupal\typed_data_conditions\ComparableListDataValue;
use Drupal\typed_data_conditions\ComparableStringDataInterface;

/**
 * @DataType(
 *   id = "condition",
 *   label = @Translation("Condition"),
 *   definition_class = "\Drupal\typed_data_conditions\TypedData\ConditionDefinition"
 * )
 */
class Condition extends Map implements ConditionInterface, EvaluatorInterface {

  /*
   * The allowed condition operators.
   *
   * @var string[]
   */
  public static $allowedOperators = [
    ComparableDataInterface::EQUAL, ComparableDataInterface::NOT_EQUAL,
    ComparableDataInterface::LESS_THAN,
    ComparableDataInterface::LESS_THAN_EQUAL,
    ComparableDataInterface::GREATER_THAN,
    ComparableDataInterface::GREATER_THAN_EQUAL,
    ComparableDataInterface::IN, ComparableDataInterface::NOT_IN,
    ComparableDataInterface::BETWEEN, ComparableDataInterface::NOT_BETWEEN,
    ComparableListDataInterface::CONTAINS,
    ComparableStringDataInterface::STARTS_WITH,
    ComparableStringDataInterface::ENDS_WITH,
  ];

  public static function create($property, $value, $operator = NULL) {
    $typed_data_manager = Drupal::typeDataManager();
    $definition = $typed_data_manager->createDataDefinition('condition');
    $values = ['operator' => '='] + $values;
    return $typed_data_manager->create($definition, $values);
  }

  /**
   * The property to be evaluated.
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
    $operator = $this->get('operator')->getCastedValue();
    return empty($operator) ? '=' : $operator;
  }

  /**
   * Returns a boolean result of the condition evaluated against the given data.
   *
   * @param \Drupal\Core\TypeData\TypedDataInterface $data
   *   The data against which the condition should be evaluated.
   */
  public function evaluate(TypedDataInterface $data) {
    $value = DataResolver::create($data)->get($this->getProperty())->resolve();
    $comparable = $this->upcast($value);
    return $comparable->compare($this->getComparison(), $this->getOperator());
  }

  /**
   * Helper function to upcast a resolved data value to something comparable.
   *
   * This is a hopefully a temporary shim until various TypedData more broadly
   * implement ComparableDataInterface.
   */
  protected function upcast($value) {
    if (!($value instanceof ComparableDataInterface)) {
      if (count($value) === 1) {
        $value = ComparableDataValue::create($value->first()->getValue());
      }
      else {
        $value = ComparableListDataValue::create($value);
      }
    }
    return $value;
  }

}
