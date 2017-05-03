<?php

namespace Drupal\jsonapi\Normalizer;

use Drupal\jsonapi\Query\EntityCondition;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * The normalizer used for entity conditions.
 */
class EntityConditionNormalizer implements DenormalizerInterface {

  /**
   * The field key in the filter condition: filter[lorem][condition][<field>].
   *
   * @var string
   */
  const PATH_KEY = 'path';

  /**
   * The value key in the filter condition: filter[lorem][condition][<value>].
   *
   * @var string
   */
  const VALUE_KEY = 'value';

  /**
   * The operator key in the condition: filter[lorem][condition][<operator>].
   *
   * @var string
   */
  const OPERATOR_KEY = 'operator';

  /**
   * The interface or class that this Normalizer supports.
   *
   * @var string
   */
  protected $supportedInterfaceOrClass = EntityCondition::class;

  /**
   * {@inheritdoc}
   */
  protected $formats = ['api_json'];

  /**
   * {@inheritdoc}
   */
  public function supportsDenormalization($data, $type, $format = null) {
    return $type == $this->supportedInterfaceOrClass;
  }

  /**
   * {@inheritdoc}
   */
  public function denormalize($data, $class, $format = NULL, array $context = []) {
    $field = $data[static::PATH_KEY];
    $value = $data[static::VALUE_KEY];
    $operator = (isset($data[static::OPERATOR_KEY])) ? $data[static::OPERATOR_KEY] : NULL;
    return new EntityCondition($field, $value, $operator);
  }

}
