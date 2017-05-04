<?php

namespace Drupal\Tests\typed_data_conditions\Kernel;

use Drupal\Tests\typed_data_conditions\Kernel\KernelTestBase;
use Drupal\typed_data_conditions\Evaluator;

/**
 * @coversDefaultClass \Drupal\typed_data_conditions\Evaluator
 * @group typed_data_conditions
 */
class EvaluatorTest extends KernelTestBase {

  /**
   * The evaluator.
   *
   * @var \Drupal\typed_data_conditions\EvaluatorInterface
   */
  protected $evaluator;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();
    $this->evaluator = new Evaluator();
  }

  /**
   * @covers ::evaluate
   * @dataProvider evaluateProvider
   */
  public function testEvaluate_condition($data, $condition, $expect) {
    $condition = $this->createData('condition', $condition);
    $data = $this->createData($data['datatype'], $data['value']);
    $this->assertEqual($expect, $this->evaluator->evaluate($data, $condition));
  }

  public function evaluateProvider() {
    return [
      [['datatype' => 'string', 'value' => 'test'], ['property' => 'value', 'comparison' => 'test'], TRUE],
      [['datatype' => 'string', 'value' => 'test'], ['property' => 'value', 'comparison' => 'fail'], FALSE],
      [['datatype' => 'string', 'value' => 'test'], ['property' => 'value', 'comparison' => 'fail', 'operator' => '<>'], TRUE],

      [['datatype' => 'integer', 'value' => 5], ['property' => 'value', 'comparison' => 5, 'operator' => '='], TRUE],
      [['datatype' => 'integer', 'value' => 5], ['property' => 'value', 'comparison' => 6, 'operator' => '='], FALSE],

      [['datatype' => 'integer', 'value' => 5], ['property' => 'value', 'comparison' => 5, 'operator' => '<>'], FALSE],
      [['datatype' => 'integer', 'value' => 5], ['property' => 'value', 'comparison' => 6, 'operator' => '<>'], TRUE],

      [['datatype' => 'integer', 'value' => 5], ['property' => 'value', 'comparison' => 4, 'operator' => '<'], FALSE],
      [['datatype' => 'integer', 'value' => 5], ['property' => 'value', 'comparison' => 5, 'operator' => '<'], FALSE],
      [['datatype' => 'integer', 'value' => 5], ['property' => 'value', 'comparison' => 6, 'operator' => '<'], TRUE],

      [['datatype' => 'integer', 'value' => 5], ['property' => 'value', 'comparison' => 4, 'operator' => '>'], TRUE],
      [['datatype' => 'integer', 'value' => 5], ['property' => 'value', 'comparison' => 5, 'operator' => '>'], FALSE],
      [['datatype' => 'integer', 'value' => 5], ['property' => 'value', 'comparison' => 6, 'operator' => '>'], FALSE],

      [['datatype' => 'integer', 'value' => 5], ['property' => 'value', 'comparison' => 4, 'operator' => '>='], TRUE],
      [['datatype' => 'integer', 'value' => 5], ['property' => 'value', 'comparison' => 5, 'operator' => '>='], TRUE],
      [['datatype' => 'integer', 'value' => 5], ['property' => 'value', 'comparison' => 6, 'operator' => '>='], FALSE],

      [['datatype' => 'integer', 'value' => 5], ['property' => 'value', 'comparison' => 4, 'operator' => '<='], FALSE],
      [['datatype' => 'integer', 'value' => 5], ['property' => 'value', 'comparison' => 5, 'operator' => '<='], TRUE],
      [['datatype' => 'integer', 'value' => 5], ['property' => 'value', 'comparison' => 6, 'operator' => '<='], TRUE],

      [['datatype' => 'integer', 'value' => 4], ['property' => 'value', 'comparison' => [4, 6], 'operator' => 'BETWEEN'], FALSE],
      [['datatype' => 'integer', 'value' => 5], ['property' => 'value', 'comparison' => [4, 6], 'operator' => 'BETWEEN'], TRUE],
      [['datatype' => 'integer', 'value' => 6], ['property' => 'value', 'comparison' => [4, 6], 'operator' => 'BETWEEN'], FALSE],

      [['datatype' => 'integer', 'value' => 4], ['property' => 'value', 'comparison' => [4, 6], 'operator' => 'NOT BETWEEN'], TRUE],
      [['datatype' => 'integer', 'value' => 5], ['property' => 'value', 'comparison' => [4, 6], 'operator' => 'NOT BETWEEN'], FALSE],
      [['datatype' => 'integer', 'value' => 6], ['property' => 'value', 'comparison' => [4, 6], 'operator' => 'NOT BETWEEN'], TRUE],

      [['datatype' => 'integer', 'value' => 5], ['property' => 'value', 'comparison' => [4, 5, 6], 'operator' => 'IN'], TRUE],
      [['datatype' => 'integer', 'value' => 5], ['property' => 'value', 'comparison' => [4, 6], 'operator' => 'IN'], FALSE],

      [['datatype' => 'integer', 'value' => 5], ['property' => 'value', 'comparison' => [4, 5, 6], 'operator' => 'NOT IN'], FALSE],
      [['datatype' => 'integer', 'value' => 5], ['property' => 'value', 'comparison' => [4, 6], 'operator' => 'NOT IN'], TRUE],

      [['datatype' => 'integer', 'value' => 5000], ['property' => 'value', 'operator' => 'IS NULL'], FALSE],
      [['datatype' => 'integer', 'value' => NULL], ['property' => 'value', 'operator' => 'IS NULL'], TRUE],

      [['datatype' => 'integer', 'value' => 5000], ['property' => 'value', 'operator' => 'IS NOT NULL'], TRUE],
      [['datatype' => 'integer', 'value' => NULL], ['property' => 'value', 'operator' => 'IS NOT NULL'], FALSE],

      [
        ['datatype' => 'condition', 'value' => ['property' => 'foo', 'comparison' => 'bar', 'operator' => '=']],
        ['property' => 'property', 'comparison' => 'foo', 'operator' => '='],
        TRUE
      ],
    ];
  }

  public function createData($type, $value) {
    $definition = $this->typedDataManager->createDataDefinition($type);
    return $this->typedDataManager->create($definition, $value);
  }

}
