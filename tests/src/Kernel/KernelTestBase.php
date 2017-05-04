<?php

namespace Drupal\Tests\typed_data_conditions\Kernel;

use Drupal\KernelTests\KernelTestBase as CoreKernelTestBase;

/**
 * Provides shared coded for Kernel tests.
 */
class KernelTestBase extends CoreKernelTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = [
    'typed_data_conditions',
  ];

  /**
   * The Type Data Manager.
   *
   * @var Drupal\Core\TypeData\TypedDataManagerInterface
   */
  protected $typedDataManager;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();
    $this->typedDataManager = $this->container->get('typed_data_manager');
  }

}
