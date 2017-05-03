<?php

namespace Drupal\Tests\entityconditions\Unit\Entity;

use Drupal\entityconditions\Entity\Condition;
use Drupal\entityconditions\Entity\ConditionGroup;
use Drupal\Tests\UnitTestCase;

/**
 * @coversDefaultClass \Drupal\entityconditions\Entity\ConditionGroup
 * @group entityconditions
 */
class ConditionGroupTest extends UnitTestCase {

  /**
   * @covers ::conjunction
   * @dataProvider conjunctionProvider
   */
  public function testConjunction($conjunction, $shouldFail) {
    if ($shouldFail) {
      $this->setExpectedException(\InvalidArgumentException::class, "'{$conjunction}' is not a valid conjunction. Allowed conjunctions are: 'AND', 'OR'");
    }
    $condition = new ConditionGroup($conjunction, []);
    $this->assertEquals($conjunction, $condition->conjunction());
  }

  /**
   * @covers ::members
   * @dataProvider membersProvider
   */
  public function testMembers($members) {
    foreach (['AND', 'OR'] as $conjunction) {
      $group = new ConditionGroup($conjunction, $members);
      $actuals = $group->members();
      for ($i = 0; $i < count($members); $i++) {
        if ($actuals[$i] instanceof ConditionGroup) {
          $this->assertEquals(
            $members[$i]->conjunction(),
            $actuals[$i]->conjunction()
          );
        } else {
          $this->assertEquals($members[$i]->field(), $actuals[$i]->field());
          $this->assertEquals($members[$i]->value(), $actuals[$i]->value());
          $this->assertEquals($members[$i]->operator(), $actuals[$i]->operator());
        }
      }
    }
  }

  /**
   * Provides data for the operator test.
   */
  public function conjunctionProvider() {
    return [
      ['fail', TRUE],
      ['AND', FALSE],
      ['OR', FALSE],
    ];
  }

  /**
   * Provides data for the members test.
   */
  public function membersProvider() {
    return [
      [[new Condition('field', 'value')]],
      [[new Condition('field', 'value'), new Condition('field', 'value')]],
      [[new Condition('field', 'value'), new Condition('field', 'value'), new ConditionGroup('AND', [])]],
      [[new ConditionGroup('AND', [])]],
    ];
  }

}
