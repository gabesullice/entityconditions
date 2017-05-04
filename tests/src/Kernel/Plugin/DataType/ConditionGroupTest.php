<?php

namespace Drupal\Tests\typed_data_conditions\Kernel\Plugin\DataType;

use Drupal\typed_data_conditions\Plugin\DataType\Condition;
use Drupal\typed_data_conditions\Plugin\DataType\ConditionGroup;

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
   * Provides data for the getMembers test.
   */
  public function getMembersProvider() {
    return [
      [[]],
      [[['property' => 'p', 'comparison' => 'c', 'operator' => '=']]],
    ];
  }

}
