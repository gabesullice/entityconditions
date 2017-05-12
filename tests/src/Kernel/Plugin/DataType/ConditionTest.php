<?php

namespace Drupal\Tests\typed_data_conditions\Kernel\Plugin\DataType;

use Drupal\node\Entity\NodeType;
use Drupal\node\Entity\Node;
use Drupal\user\Entity\User;

/**
 * @coversDefaultClass \Drupal\typed_data_conditions\Plugin\DataType\Condition
 * @group typed_data_conditions
 */
class ConditionTest extends ConditionKernelTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = ['system', 'node', 'user'];

  /**
   * Create test node types.
   */
  public function setUp() {
    parent::setUp();
    $this->installEntitySchema('node');
    $this->installEntitySchema('user');
    $this->installSchema('system', ['sequences']);
    $this->installSchema('node', ['node_access']);
    $this->installSchema('user', ['users_data']);

    NodeType::create([
      'type' => 'article',
    ])->save();
  }

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

    $this->assertEqual($expect, $condition->evaluate($data));
  }

  public function evaluateProvider() {
    return [
      [['datatype' => 'string', 'value' => 'barzap'], ['comparison' => 'foo', 'operator' => 'CONTAINS'], FALSE],
      [['datatype' => 'string', 'value' => 'zapfoobar'], ['comparison' => 'foo', 'operator' => 'CONTAINS'], TRUE],

      [['datatype' => 'string', 'value' => 'foobar'], ['comparison' => 'foo', 'operator' => 'STARTS_WITH'], TRUE],
      [['datatype' => 'string', 'value' => 'barzap'], ['comparison' => 'foo', 'operator' => 'STARTS_WITH'], FALSE],
      [['datatype' => 'string', 'value' => 'barfoo'], ['comparison' => 'foo', 'operator' => 'STARTS_WITH'], FALSE],

      [['datatype' => 'string', 'value' => 'foobar'], ['comparison' => 'foo', 'operator' => 'ENDS_WITH'], FALSE],
      [['datatype' => 'string', 'value' => 'barzap'], ['comparison' => 'foo', 'operator' => 'ENDS_WITH'], FALSE],
      [['datatype' => 'string', 'value' => 'barfoo'], ['comparison' => 'foo', 'operator' => 'ENDS_WITH'], TRUE],

      [['datatype' => 'integer', 'value' => 5], ['comparison' => 5, 'operator' => '='], TRUE],
      [['datatype' => 'integer', 'value' => 5], ['comparison' => 6, 'operator' => '='], FALSE],

      [['datatype' => 'string', 'value' => 'foo'], ['comparison' => 'foo', 'operator' => '='], TRUE],
      [['datatype' => 'string', 'value' => 'foo'], ['comparison' => 'bar', 'operator' => '='], FALSE],

      [['datatype' => 'integer', 'value' => 5], ['comparison' => 5, 'operator' => '<>'], FALSE],
      [['datatype' => 'integer', 'value' => 5], ['comparison' => 6, 'operator' => '<>'], TRUE],

      [['datatype' => 'string', 'value' => 'foo'], ['comparison' => 'foo', 'operator' => '<>'], FALSE],
      [['datatype' => 'string', 'value' => 'foo'], ['comparison' => 'bar', 'operator' => '<>'], TRUE],

      [['datatype' => 'integer', 'value' => 5], ['comparison' => 4, 'operator' => '<'], FALSE],
      [['datatype' => 'integer', 'value' => 5], ['comparison' => 5, 'operator' => '<'], FALSE],
      [['datatype' => 'integer', 'value' => 5], ['comparison' => 6, 'operator' => '<'], TRUE],

      [['datatype' => 'string', 'value' => 'foo'], ['comparison' => 'bar', 'operator' => '<'], FALSE],
      [['datatype' => 'string', 'value' => 'foo'], ['comparison' => 'foo', 'operator' => '<'], FALSE],
      [['datatype' => 'string', 'value' => 'foo'], ['comparison' => 'zap', 'operator' => '<'], TRUE],

      [['datatype' => 'integer', 'value' => 5], ['comparison' => 4, 'operator' => '>'], TRUE],
      [['datatype' => 'integer', 'value' => 5], ['comparison' => 5, 'operator' => '>'], FALSE],
      [['datatype' => 'integer', 'value' => 5], ['comparison' => 6, 'operator' => '>'], FALSE],

      [['datatype' => 'string', 'value' => 'foo'], ['comparison' => 'bar', 'operator' => '>'], TRUE],
      [['datatype' => 'string', 'value' => 'foo'], ['comparison' => 'foo', 'operator' => '>'], FALSE],
      [['datatype' => 'string', 'value' => 'foo'], ['comparison' => 'zap', 'operator' => '>'], FALSE],

      [['datatype' => 'integer', 'value' => 5], ['comparison' => 4, 'operator' => '<='], FALSE],
      [['datatype' => 'integer', 'value' => 5], ['comparison' => 5, 'operator' => '<='], TRUE],
      [['datatype' => 'integer', 'value' => 5], ['comparison' => 6, 'operator' => '<='], TRUE],

      [['datatype' => 'string', 'value' => 'foo'], ['comparison' => 'bar', 'operator' => '<='], FALSE],
      [['datatype' => 'string', 'value' => 'foo'], ['comparison' => 'foo', 'operator' => '<='], TRUE],
      [['datatype' => 'string', 'value' => 'foo'], ['comparison' => 'zap', 'operator' => '<='], TRUE],

      [['datatype' => 'integer', 'value' => 5], ['comparison' => 4, 'operator' => '>='], TRUE],
      [['datatype' => 'integer', 'value' => 5], ['comparison' => 5, 'operator' => '>='], TRUE],
      [['datatype' => 'integer', 'value' => 5], ['comparison' => 6, 'operator' => '>='], FALSE],

      [['datatype' => 'string', 'value' => 'foo'], ['comparison' => 'bar', 'operator' => '>='], TRUE],
      [['datatype' => 'string', 'value' => 'foo'], ['comparison' => 'foo', 'operator' => '>='], TRUE],
      [['datatype' => 'string', 'value' => 'foo'], ['comparison' => 'zap', 'operator' => '>='], FALSE],

      [['datatype' => 'integer', 'value' => 4], ['comparison' => [4, 6], 'operator' => 'BETWEEN'], FALSE],
      [['datatype' => 'integer', 'value' => 5], ['comparison' => [4, 6], 'operator' => 'BETWEEN'], TRUE],
      [['datatype' => 'integer', 'value' => 6], ['comparison' => [4, 6], 'operator' => 'BETWEEN'], FALSE],

      [['datatype' => 'string', 'value' => 'bar'], ['comparison' => ['bar', 'zap'], 'operator' => 'BETWEEN'], FALSE],
      [['datatype' => 'string', 'value' => 'foo'], ['comparison' => ['bar', 'zap'], 'operator' => 'BETWEEN'], TRUE],
      [['datatype' => 'string', 'value' => 'zap'], ['comparison' => ['bar', 'zap'], 'operator' => 'BETWEEN'], FALSE],

      [['datatype' => 'integer', 'value' => 4], ['comparison' => [4, 6], 'operator' => 'NOT BETWEEN'], TRUE],
      [['datatype' => 'integer', 'value' => 5], ['comparison' => [4, 6], 'operator' => 'NOT BETWEEN'], FALSE],
      [['datatype' => 'integer', 'value' => 6], ['comparison' => [4, 6], 'operator' => 'NOT BETWEEN'], TRUE],

      [['datatype' => 'string', 'value' => 'bar'], ['comparison' => ['bar', 'zap'], 'operator' => 'NOT BETWEEN'], TRUE],
      [['datatype' => 'string', 'value' => 'foo'], ['comparison' => ['bar', 'zap'], 'operator' => 'NOT BETWEEN'], FALSE],
      [['datatype' => 'string', 'value' => 'zap'], ['comparison' => ['bar', 'zap'], 'operator' => 'NOT BETWEEN'], TRUE],

      [['datatype' => 'integer', 'value' => 5], ['comparison' => [4, 5, 6], 'operator' => 'IN'], TRUE],
      [['datatype' => 'integer', 'value' => 5], ['comparison' => [4, 6], 'operator' => 'IN'], FALSE],

      [['datatype' => 'string', 'value' => 'foo'], ['comparison' => ['bar', 'foo', 'zap'], 'operator' => 'IN'], TRUE],
      [['datatype' => 'string', 'value' => 'foo'], ['comparison' => ['bar', 'zap'], 'operator' => 'IN'], FALSE],

      [['datatype' => 'integer', 'value' => 5], ['comparison' => [4, 5, 6], 'operator' => 'NOT IN'], FALSE],
      [['datatype' => 'integer', 'value' => 5], ['comparison' => [4, 6], 'operator' => 'NOT IN'], TRUE],

      [['datatype' => 'string', 'value' => 'foo'], ['comparison' => ['bar', 'foo', 'zap'], 'operator' => 'NOT IN'], FALSE],
      [['datatype' => 'string', 'value' => 'foo'], ['comparison' => ['bar', 'zap'], 'operator' => 'NOT IN'], TRUE],

      [
        ['datatype' => 'condition', 'value' => ['property' => 'foo', 'comparison' => 'bar', 'operator' => '=']],
        ['comparison' => ['property' => 'foo', 'comparison' => 'bar', 'operator' => '='], 'operator' => '='],
        TRUE
      ],
    ];
  }

  /**
   * Tests conditions against data with nested properties.
   *
   * @covers ::evaluate
   */
  public function testEvaluate_entity() {
    $user0 = User::create([
      'name' => 'user0',
      'mail' => 'test@example.com',
    ]);
    $user0->save();

    $node0 = Node::create([
      'type' => 'article',
      'title' => 'node0',
      'uid' => $user0->id(),
    ]);
    $node0->save();

    $node_data = $node0->getTypedData();

    $condition0 = $this->createCondition([
      'property' => 'uid.0.entity.name.0.value',
      'comparison' => $this->createData('string', 'user0'),
    ]);

    $this->assertEqual(TRUE, $condition0->evaluate($node_data));

    $condition1 = $this->createCondition([
      'property' => 'uid.0.entity.name.0.value',
      'comparison' => $this->createData('string', 'us'),
      'operator' => 'STARTS_WITH',
    ]);

    $this->assertEqual(TRUE, $condition1->evaluate($node_data));

    $condition2 = $this->createCondition([
      'property' => 'uid.0.entity.name.0.value',
      'comparison' => $this->createData('string', 'us'),
      'operator' => 'ENDS_WITH',
    ]);

    $this->assertEqual(FALSE, $condition2->evaluate($node_data));

    $condition3 = $this->createCondition([
      'property' => 'uid.entity.name.value',
      'comparison' => $this->createData('string', 'user0'),
    ]);

    $this->assertEqual(TRUE, $condition3->evaluate($node_data));

    $condition4 = $this->createCondition([
      'property' => 'uid.entity.name.value',
      'comparison' => $this->createData('string', 'should_be_false'),
    ]);

    $this->assertEqual(FALSE, $condition4->evaluate($node_data));

    $condition5 = $this->createCondition([
      'property' => 'uid.entity.name.value',
      'comparison' => $this->createData('string', 'should_be_false'),
    ]);

    $this->assertEqual(FALSE, $condition5->evaluate($node_data));

    $condition6 = $this->createCondition([
      'property' => 'uid.entity.name.value',
      'comparison' => $this->createData('string', 'user0'),
    ]);

    $this->assertEqual(TRUE, $condition6->evaluate($node_data));
  }

}
