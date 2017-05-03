<?php

namespace Drupal\Tests\jsonapi\Normalizer;

use Drupal\KernelTests\KernelTestBase;
use Drupal\jsonapi\Normalizer\EntityConditionNormalizer;
use Drupal\jsonapi\Query\EntityCondition;

/**
 * @coversDefaultClass \Drupal\jsonapi\Normalizer\EntityConditionNormalizer
 * @group jsonapi
 * @group jsonapi_normalizers
 */
class EntityConditionNormalizerTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = [
    'serialization',
    'system',
    'jsonapi',
  ];

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();
    $this->normalizer = $this->container->get('serializer.normalizer.entity_condition.jsonapi');
  }

  /**
   * @covers ::denormalize
   * @dataProvider denormalizeProvider
   */
  public function testDenormalize($case) {
    $normalized = $this->normalizer->denormalize($case, EntityCondition::class);
    $this->assertEquals($case['path'], $normalized->field());
    $this->assertEquals($case['value'], $normalized->value());
    if (isset($case['operator'])) {
      $this->assertEquals($case['operator'], $normalized->operator());
    }
  }

  /**
   * @covers ::denormalize
   * @expectedException InvalidArgumentException
   * @expectedExceptionMessage The 'NOT_ALLOWED' operator is not allowed.
   */
  public function testDenormalize_exception() {
    $this->normalizer->denormalize([
      'path' => 'some_field',
      'operator' => 'NOT_ALLOWED',
      'value' => 'some_string',
    ], EntityCondition::class);
  }

  public function denormalizeProvider() {
    return [
      [['path' => 'some_field', 'value' => NULL]],
      [['path' => 'some_field', 'operator' => '=', 'value' => 'some_string']],
      [['path' => 'some_field', 'operator' => 'NOT BETWEEN', 'value' => 'some_string']],
      [['path' => 'some_field', 'operator' => NULL, 'value' => 'some_string']],
      [['path' => 'some_field', 'operator' => NULL, 'value' => ['some_string']]],
    ];
  }

}
