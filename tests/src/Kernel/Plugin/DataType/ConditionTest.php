<?php

namespace Drupal\Tests\typed_data_conditions\Kernel\Plugin\DataType;

/**
 * @coversDefaultClass \Drupal\typed_data_conditions\Plugin\DataType\Condition
 * @group typed_data_conditions
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

  /**
   * @covers ::evaluate
   * @dataProvider evaluateProvider
   */
  public function testEvaluate($data, $condition, $expect) {
    if (!isset($condition['comparison'])) {
      $comparison = NULL;
    }
    elseif ($data['datatype'] == 'condition') {
      $comparison = $this->createData($data['datatype'], $condition['comparison']);
    }
    elseif (is_array($condition['comparison'])) {
      $comparison = $this->createListData($data['datatype'], $condition['comparison']);
    }
    else {
      $comparison = $this->createData($data['datatype'], $condition['comparison']);
    }

    $condition = $this->createData('condition', [
      'comparison' => $comparison,
    ] + $condition);

    $data = $this->createData($data['datatype'], $data['value']);

    $this->assertEqual($expect, $condition->evaluate($data, $condition));
  }

  public function evaluateProvider() {
    return [
      [['datatype' => 'string', 'value' => 'barzap'], ['property' => 'value', 'comparison' => 'foo', 'operator' => 'CONTAINS'], FALSE],
      [['datatype' => 'string', 'value' => 'zapfoobar'], ['property' => 'value', 'comparison' => 'foo', 'operator' => 'CONTAINS'], TRUE],

      [['datatype' => 'string', 'value' => 'foobar'], ['property' => 'value', 'comparison' => 'foo', 'operator' => 'STARTS_WITH'], TRUE],
      [['datatype' => 'string', 'value' => 'barzap'], ['property' => 'value', 'comparison' => 'foo', 'operator' => 'STARTS_WITH'], FALSE],
      [['datatype' => 'string', 'value' => 'barfoo'], ['property' => 'value', 'comparison' => 'foo', 'operator' => 'STARTS_WITH'], FALSE],

      [['datatype' => 'string', 'value' => 'foobar'], ['property' => 'value', 'comparison' => 'foo', 'operator' => 'ENDS_WITH'], FALSE],
      [['datatype' => 'string', 'value' => 'barzap'], ['property' => 'value', 'comparison' => 'foo', 'operator' => 'ENDS_WITH'], FALSE],
      [['datatype' => 'string', 'value' => 'barfoo'], ['property' => 'value', 'comparison' => 'foo', 'operator' => 'ENDS_WITH'], TRUE],

      [['datatype' => 'integer', 'value' => 5], ['property' => 'value', 'comparison' => 5, 'operator' => '='], TRUE],
      [['datatype' => 'integer', 'value' => 5], ['property' => 'value', 'comparison' => 6, 'operator' => '='], FALSE],

      [['datatype' => 'string', 'value' => 'foo'], ['property' => 'value', 'comparison' => 'foo', 'operator' => '='], TRUE],
      [['datatype' => 'string', 'value' => 'foo'], ['property' => 'value', 'comparison' => 'bar', 'operator' => '='], FALSE],

      [['datatype' => 'integer', 'value' => 5], ['property' => 'value', 'comparison' => 5, 'operator' => '<>'], FALSE],
      [['datatype' => 'integer', 'value' => 5], ['property' => 'value', 'comparison' => 6, 'operator' => '<>'], TRUE],

      [['datatype' => 'string', 'value' => 'foo'], ['property' => 'value', 'comparison' => 'foo', 'operator' => '<>'], FALSE],
      [['datatype' => 'string', 'value' => 'foo'], ['property' => 'value', 'comparison' => 'bar', 'operator' => '<>'], TRUE],

      [['datatype' => 'integer', 'value' => 5], ['property' => 'value', 'comparison' => 4, 'operator' => '<'], FALSE],
      [['datatype' => 'integer', 'value' => 5], ['property' => 'value', 'comparison' => 5, 'operator' => '<'], FALSE],
      [['datatype' => 'integer', 'value' => 5], ['property' => 'value', 'comparison' => 6, 'operator' => '<'], TRUE],

      [['datatype' => 'string', 'value' => 'foo'], ['property' => 'value', 'comparison' => 'bar', 'operator' => '<'], FALSE],
      [['datatype' => 'string', 'value' => 'foo'], ['property' => 'value', 'comparison' => 'foo', 'operator' => '<'], FALSE],
      [['datatype' => 'string', 'value' => 'foo'], ['property' => 'value', 'comparison' => 'zap', 'operator' => '<'], TRUE],

      [['datatype' => 'integer', 'value' => 5], ['property' => 'value', 'comparison' => 4, 'operator' => '>'], TRUE],
      [['datatype' => 'integer', 'value' => 5], ['property' => 'value', 'comparison' => 5, 'operator' => '>'], FALSE],
      [['datatype' => 'integer', 'value' => 5], ['property' => 'value', 'comparison' => 6, 'operator' => '>'], FALSE],

      [['datatype' => 'string', 'value' => 'foo'], ['property' => 'value', 'comparison' => 'bar', 'operator' => '>'], TRUE],
      [['datatype' => 'string', 'value' => 'foo'], ['property' => 'value', 'comparison' => 'foo', 'operator' => '>'], FALSE],
      [['datatype' => 'string', 'value' => 'foo'], ['property' => 'value', 'comparison' => 'zap', 'operator' => '>'], FALSE],

      [['datatype' => 'integer', 'value' => 5], ['property' => 'value', 'comparison' => 4, 'operator' => '<='], FALSE],
      [['datatype' => 'integer', 'value' => 5], ['property' => 'value', 'comparison' => 5, 'operator' => '<='], TRUE],
      [['datatype' => 'integer', 'value' => 5], ['property' => 'value', 'comparison' => 6, 'operator' => '<='], TRUE],

      [['datatype' => 'string', 'value' => 'foo'], ['property' => 'value', 'comparison' => 'bar', 'operator' => '<='], FALSE],
      [['datatype' => 'string', 'value' => 'foo'], ['property' => 'value', 'comparison' => 'foo', 'operator' => '<='], TRUE],
      [['datatype' => 'string', 'value' => 'foo'], ['property' => 'value', 'comparison' => 'zap', 'operator' => '<='], TRUE],

      [['datatype' => 'integer', 'value' => 5], ['property' => 'value', 'comparison' => 4, 'operator' => '>='], TRUE],
      [['datatype' => 'integer', 'value' => 5], ['property' => 'value', 'comparison' => 5, 'operator' => '>='], TRUE],
      [['datatype' => 'integer', 'value' => 5], ['property' => 'value', 'comparison' => 6, 'operator' => '>='], FALSE],

      [['datatype' => 'string', 'value' => 'foo'], ['property' => 'value', 'comparison' => 'bar', 'operator' => '>='], TRUE],
      [['datatype' => 'string', 'value' => 'foo'], ['property' => 'value', 'comparison' => 'foo', 'operator' => '>='], TRUE],
      [['datatype' => 'string', 'value' => 'foo'], ['property' => 'value', 'comparison' => 'zap', 'operator' => '>='], FALSE],

      [['datatype' => 'integer', 'value' => 4], ['property' => 'value', 'comparison' => [4, 6], 'operator' => 'BETWEEN'], FALSE],
      [['datatype' => 'integer', 'value' => 5], ['property' => 'value', 'comparison' => [4, 6], 'operator' => 'BETWEEN'], TRUE],
      [['datatype' => 'integer', 'value' => 6], ['property' => 'value', 'comparison' => [4, 6], 'operator' => 'BETWEEN'], FALSE],

      [['datatype' => 'string', 'value' => 'bar'], ['property' => 'value', 'comparison' => ['bar', 'zap'], 'operator' => 'BETWEEN'], FALSE],
      [['datatype' => 'string', 'value' => 'foo'], ['property' => 'value', 'comparison' => ['bar', 'zap'], 'operator' => 'BETWEEN'], TRUE],
      [['datatype' => 'string', 'value' => 'zap'], ['property' => 'value', 'comparison' => ['bar', 'zap'], 'operator' => 'BETWEEN'], FALSE],

      [['datatype' => 'integer', 'value' => 4], ['property' => 'value', 'comparison' => [4, 6], 'operator' => 'NOT BETWEEN'], TRUE],
      [['datatype' => 'integer', 'value' => 5], ['property' => 'value', 'comparison' => [4, 6], 'operator' => 'NOT BETWEEN'], FALSE],
      [['datatype' => 'integer', 'value' => 6], ['property' => 'value', 'comparison' => [4, 6], 'operator' => 'NOT BETWEEN'], TRUE],

      [['datatype' => 'string', 'value' => 'bar'], ['property' => 'value', 'comparison' => ['bar', 'zap'], 'operator' => 'NOT BETWEEN'], TRUE],
      [['datatype' => 'string', 'value' => 'foo'], ['property' => 'value', 'comparison' => ['bar', 'zap'], 'operator' => 'NOT BETWEEN'], FALSE],
      [['datatype' => 'string', 'value' => 'zap'], ['property' => 'value', 'comparison' => ['bar', 'zap'], 'operator' => 'NOT BETWEEN'], TRUE],

      [['datatype' => 'integer', 'value' => 5], ['property' => 'value', 'comparison' => [4, 5, 6], 'operator' => 'IN'], TRUE],
      [['datatype' => 'integer', 'value' => 5], ['property' => 'value', 'comparison' => [4, 6], 'operator' => 'IN'], FALSE],

      [['datatype' => 'string', 'value' => 'foo'], ['property' => 'value', 'comparison' => ['bar', 'foo', 'zap'], 'operator' => 'IN'], TRUE],
      [['datatype' => 'string', 'value' => 'foo'], ['property' => 'value', 'comparison' => ['bar', 'zap'], 'operator' => 'IN'], FALSE],

      [['datatype' => 'integer', 'value' => 5], ['property' => 'value', 'comparison' => [4, 5, 6], 'operator' => 'NOT IN'], FALSE],
      [['datatype' => 'integer', 'value' => 5], ['property' => 'value', 'comparison' => [4, 6], 'operator' => 'NOT IN'], TRUE],

      [['datatype' => 'string', 'value' => 'foo'], ['property' => 'value', 'comparison' => ['bar', 'foo', 'zap'], 'operator' => 'NOT IN'], FALSE],
      [['datatype' => 'string', 'value' => 'foo'], ['property' => 'value', 'comparison' => ['bar', 'zap'], 'operator' => 'NOT IN'], TRUE],

      [
        ['datatype' => 'condition', 'value' => ['property' => 'foo', 'comparison' => 'bar', 'operator' => '=']],
        ['property' => 'property', 'comparison' => ['property' => 'foo', 'comparison' => 'bar', 'operator' => '='], 'operator' => '='],
        TRUE
      ],
    ];
  }

}
