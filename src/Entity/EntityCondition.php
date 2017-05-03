<?php

namespace Drupal\jsonapi\Query;

class EntityCondition {

  /**
   * The allowed condition operators.
   *
   * @var string[]
   */
  protected static $allowedOperators = [
    '=', '<>',
    '>', '>=', '<', '<=',
    'STARTS_WITH', 'CONTAINS', 'ENDS_WITH',
    'IN', 'NOT IN',
    'BETWEEN', 'NOT BETWEEN',
  ];

  /**
   * The field to be evaluated.
   *
   * @var string
   */
  protected $field;

  /**
   * The condition operator.
   *
   * @var string
   */
  protected $operator;

  /**
   * The value against which the field should be evaluated.
   *
   * @var mixed
   */
  protected $value;

  /**
   * Constructs a new EntityCondition object.
   */
  public function __construct($field, $value, $operator = NULL) {
    if (!is_null($operator) && !in_array($operator, static::$allowedOperators)) {
      throw new \InvalidArgumentException("The '{$operator}' operator is not allowed.");
    }
    $this->field = $field;
    $this->value = $value;
    $this->operator = ($operator) ? $operator : '=';
  }

  /**
   * The field to be evaluated.
   *
   * @return string
   */
  public function field() {
    return $this->field;
  }

  /**
   * The comporison operator to use for the evaluation.
   *
   * Can be '=', '<', '>', '>=', '<=', 'BETWEEN', 'NOT BETWEEN', 'IN', 'NOT IN',
   * 'IS NULL', 'IS NOT NULL', 'LIKE', 'NOT LIKE', 'EXISTS', 'NOT EXISTS'
   *
   * @return string
   */
  public function operator() {
    return $this->operator;
  }

  /**
   * The value against which the condition should be evaluated.
   *
   * @return mixed
   */
  public function value() {
    return $this->value;
  }

}
