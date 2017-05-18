<?php

namespace Drupal\Tests\typed_data_conditions\Kernel\Plugin\DataType;

use Drupal\Core\TypedData\TypedDataInterface;
use Drupal\typed_data_conditions\EvaluatorInterface;
use Drupal\typed_data_conditions\Plugin\DataType\Condition;
use Drupal\typed_data_conditions\Plugin\DataType\ConditionGroup;
use \Prophecy\Argument;

/**
 * @coversDefaultClass \Drupal\typed_data_conditions\Plugin\DataType\ConditionGroup
 * @group typed_data_conditions
 */
class ConditionGroupTest extends ConditionKernelTestBase {

  /**
   * @covers ::getConjunction
   * @dataProvider getConjunctionProvider
   */
  public function testConjunction($conjunction, $shouldFail = FALSE) {
    $group = $this->createConditionGroup(['conjunction' => $conjunction, 'members' => []]);
    $this->assertEquals($conjunction, $group->getConjunction());
    $this->assertTrue($shouldFail XOR !$group->validate()->has(0));
  }

  /**
   * Provides data for the getConjunction test.
   */
  public function getConjunctionProvider() {
    return [
      ['fail', TRUE],
      ['AND'],
      ['OR'],
    ];
  }

  /**
   * @covers ::getMembers
   * @dataProvider getMembersProvider
   */
  public function testMembers($members) {
    foreach (['AND', 'OR'] as $conjunction) {
      $group = $this->createConditionGroup([
        'conjunction' => $conjunction,
        'members' => array_map(function ($member) {
          return $this->createCondition($member);
        }, $members),
      ]);

      $actuals = $group->getMembers();

      for ($i = 0; $i < count($members); $i++) {
        $this->assertEquals($members[$i]['property'], $actuals[$i]->getProperty());
        $this->assertEquals($members[$i]['comparison'], $actuals[$i]->getComparison());
        $this->assertEquals($members[$i]['operator'], $actuals[$i]->getOperator());
      }
    }
  }

  /**
   * @covers ::evaluate
   * @dataProvider evaluateProvider
   */
  public function testEvaluate($conjunction, $results, $expect) {
    $conditions = array_map(function ($result) {
      $condition = $this->prophesize(EvaluatorInterface::class);
      $condition->evaluate(Argument::type(TypedDataInterface::class))->willReturn($result);
      return $condition->reveal();
    }, $results);

    $group = $this->createConditionGroup([
      'conjunction' => $conjunction,
      'members' => $conditions,
    ]);

    $data = $this->prophesize(TypedDataInterface::class);

    $this->assertEquals($expect, $group->evaluate($data->reveal()));
  }

  /**
   * Provides data for the getMembers test.
   */
  public function getMembersProvider() {
    return [
      [[]],
      [[['property' => 'p', 'comparison' => 'c', 'operator' => '=']]],
    ];
  }

  /**
   * Provides data for the getMembers test.
   */
  public function evaluateProvider() {
    return [
      ['OR',  [], FALSE],
      ['AND', [], FALSE],
      ['AND', [TRUE, TRUE], TRUE],
      ['AND', [TRUE, FALSE], FALSE],
      ['AND', [FALSE, FALSE], FALSE],
      ['OR',  [TRUE, TRUE], TRUE],
      ['OR',  [TRUE, FALSE], TRUE],
      ['OR',  [FALSE, FALSE], FALSE],
    ];
  }

}
