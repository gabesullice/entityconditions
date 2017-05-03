<?php

namespace Drupal\Tests\entityconditions\Unit\Entity;

use Drupal\entityconditions\Entity\Condition;
use Drupal\Tests\UnitTestCase;

/**
 * @coversDefaultClass \Drupal\entityconditions\Entity\Condition
 * @group entityconditions
 */
class ConditionTest extends UnitTestCase {

  /**
   * @covers ::field
   * @dataProvider fieldProvider
   */
  public function testField($field) {
    $condition = new Condition($field, 'value');
    $this->assertEquals($field, $condition->field());
  }

  /**
   * @covers ::value
   * @dataProvider valueProvider
   */
  public function testValue($value) {
    $condition = new Condition('field', $value);
    $this->assertEquals($value, $condition->value());
  }

  /**
   * @covers ::operator
   * @dataProvider operatorProvider
   */
  public function testOperator($operator, $shouldFail) {
    if ($shouldFail) {
      $this->setExpectedException(\InvalidArgumentException::class, "The '{$operator}' operator is not allowed.");
    }
    $condition = new Condition('field', 'value', $operator);
    $this->assertEquals($operator, $condition->operator());
  }

  /**
   * Provides data for the field test.
   */
  public function fieldProvider() {
    return [
      ['field_example'],
    ];
  }

  /**
   * Provides data for the value test.
   */
  public function valueProvider() {
    return [
      [-1], [0], [1],
      ['value_example'],
      [['value_example']],
    ];
  }

  /**
   * Provides data for the operator test.
   */
  public function operatorProvider() {
    return [
      ['fail', TRUE],
      ['!=', TRUE],
      ['=', FALSE],
      ['<>', FALSE],
      ['>', FALSE],
      ['>=', FALSE],
      ['<', FALSE],
      ['<=', FALSE],
      ['STARTS_WITH', FALSE],
      ['CONTAINS', FALSE],
      ['ENDS_WITH', FALSE],
      ['IN', FALSE],
      ['NOT IN', FALSE],
      ['BETWEEN', FALSE],
      ['NOT BETWEEN', FALSE],
    ];
  }

}
