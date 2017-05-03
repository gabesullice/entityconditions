<?php

namespace Drupal\jsonapi\Normalizer;

use Drupal\jsonapi\Query\EntityCondition;
use Drupal\jsonapi\Query\EntityConditionGroup;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * The normalizer used for entity conditions.
 */
class EntityConditionGroupNormalizer implements DenormalizerInterface {

  /**
   * The interface or class that this Normalizer supports.
   *
   * @var string
   */
  protected $supportedInterfaceOrClass = EntityConditionGroup::class;

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
    return new EntityConditionGroup($data['conjunction'], $data['members']);
  }

}
