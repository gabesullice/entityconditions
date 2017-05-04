<?php

namespace Drupal\typed_data_conditions;

interface ConditionInterface {

  /**
   * The property to be evaluated.
   *
   * @return string
   */
  public function getProperty();

  /**
   * The value against which the condition should be evaluated.
   *
   * @return mixed
   */
  public function getComparison();

  /**
   * The comparison operator to use for the evaluation.
   *
   * Can be '=', '<', '>', '>=', '<=', 'BETWEEN', 'NOT BETWEEN', 'IN', 'NOT IN',
   * 'IS NULL', 'IS NOT NULL', 'LIKE', 'NOT LIKE', 'EXISTS', 'NOT EXISTS'
   *
   * @return string
   */
  public function getOperator();

}
