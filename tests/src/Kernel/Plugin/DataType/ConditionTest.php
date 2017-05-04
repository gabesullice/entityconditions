<?php

namespace Drupal\Tests\entityconditions\Kernel\Plugin\DataType;

/**
 * @coversDefaultClass \Drupal\entityconditions\Plugin\DataType\Condition
 * @group entityconditions
 */
class ConditionTest extends ConditionKernelTestBase {

  /**
   * @covers ::getProperty
   * @dataProvider conditionProvider
   */
  public function testGetProperty($values) {
    $condition = $this->createCondition($values);
    $property = $condition->getProperty();
    $this->assertEquals($values['property'], $property);
  }

  /**
   * @covers ::getComparison
   * @dataProvider conditionProvider
   */
  public function testGetComparison($values) {
    $condition = $this->createCondition($values);
    $comparison = $condition->getComparison();
    $this->assertEquals($values['comparison'], $comparison);
  }

  /**
   * @covers ::getOperator
   * @dataProvider getOperatorProvider
   */
  public function testGetOperator($operator, $shouldFail = FALSE) {
    $condition = $this->createCondition([
      'property' => 'example',
      'comparison' => 'example',
      'operator' => $operator,
    ]);

    $this->assertEquals($operator, $condition->getOperator());

    $violations = $condition->validate();
    $this->assertTrue($shouldFail xor !$violations->has(0));
  }

  /**
   * Provides data for example conditions.
   */
  public function conditionProvider() {
    return [
      [['property' => 'field_example', 'comparison' => 'value_example']],
      [['property' => 'field_example', 'comparison' => 'value_example', 'operator' => '<>']],
      [['property' => 'field_example', 'comparison' => 'value_example', 'operator' => 'fail'], TRUE],
    ];
  }

  /**
   * Provides data for the operator tests.
   */
  public function getOperatorProvider() {
    return [
      ['fail', TRUE],
      ['!=', TRUE],
      ['='],
      ['<>'],
      ['>'],
      ['>='],
      ['<'],
      ['<='],
      ['STARTS_WITH'],
      ['CONTAINS'],
      ['ENDS_WITH'],
      ['IN'],
      ['NOT IN'],
      ['BETWEEN'],
      ['NOT BETWEEN'],
    ];
  }

}
