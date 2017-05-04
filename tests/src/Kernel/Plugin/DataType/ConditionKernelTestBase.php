<?php

namespace Drupal\Tests\typed_data_conditions\Kernel\Plugin\DataType;

use Drupal\Tests\typed_data_conditions\Kernel\KernelTestBase;

/**
 * Provides shared coded for the condition and condition group kernel tests.
 */
class ConditionKernelTestBase extends KernelTestBase {

  /**
   * Helper function to create condition instances.
   */
  protected function createCondition($values) {
    $definition = $this->typedDataManager->createDataDefinition('condition');
    $values = $values + ['operator' => '='];
    return $this->typedDataManager->create($definition, $values);
  }

  /**
   * Helper function to create condition instances.
   */
  protected function createConditionGroup($values) {
    $definition = $this->typedDataManager->createDataDefinition('condition_group');
    return $this->typedDataManager->create($definition, $values);
  }

}
