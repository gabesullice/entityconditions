<?php

namespace Drupal\typed_data_conditions;

use Drupal\Core\TypedData\TypedDataInterface;

trait ComparableDataTrait {

  /**
   * {@inheritdoc}
   */
  public function compare(TypedDataInterface $data, $operator = ComparableDataInterface::EQUAL) {
    $this->checkComparable($data);

    switch ($operator) {
      case ComparableDataInterface::EQUAL:
        return $this->isEqualTo($data);
      case ComparableDataInterface::NOT_EQUAL:
        return $this->isNotEqualTo($data);
      case ComparableDataInterface::LESS_THAN:
        return $this->isLessThan($data);
      case ComparableDataInterface::LESS_THAN_EQUAL:
        return $this->isLessThanEqualTo($data);
      case ComparableDataInterface::GREATER_THAN:
        return $this->isGreaterThan($data);
      case ComparableDataInterface::GREATER_THAN_EQUAL:
        return $this->isGreaterThanEqualTo($data);
      case ComparableDataInterface::BETWEEN:
        return $this->isBetween($data);
      case ComparableDataInterface::NOT_BETWEEN:
        return $this->isNotBetween($data);
      case ComparableDataInterface::IN:
        return $this->isIn($data);
      case ComparableDataInterface::NOT_IN:
        return $this->isNotIn($data);
      default:
        $message = "The '%s' operator is not supported. See %s.";
        throw new InvalidComparisonOperator(
          sprintf($message, $operator, ComparableDataInterface::class)
        );
    }
  }

  protected function checkComparable(TypedDataInterface $data) {
    if (get_class($this) !== get_class($data)) {
      $message = "Cannot compare data of type '%s' with data of type '%s'";
      throw new IncomparableDataType(
        sprintf($message, get_class($this), get_class($data))
      );
    }
  }

  public function isEqualTo($data) {
    return $this->getValue() === $data->getValue();
  }

  public function isNotEqualTo($data) {
    return $this->getValue() !== $data->getValue();
  }

  public function isLessThan($data) {
    return $this->getValue() < $this->getValue();
  }

  public function isLessThanEqualTo($data) {
    return $this->getValue() <= $this->getValue();
  }

  public function isGreaterThan($data) {
    return $this->getValue() > $this->getValue();
  }

  public function isGreaterThanEqualTo($data) {
    return $this->getValue() >= $this->getValue();
  }

  public function isBetween(ListInterface $data) {
    return (
      $this->isGreaterThan($data->offsetGet(0))
      && $this->isLessThan($data->offsetGet(1))
    );
  }

  public function isNotBetween(ListInterface $data) {
    return !$this->isBetween($data);
  }

  public function isIn(ListInterface $data) {
    return !$this->isNotIn($data);
  }

  public function isNotIn(ListInterface $data) {
    foreach ($data as $datum) {
      if ($this->isEqualTo($datum)) {
        return FALSE;
      }
    }
    return TRUE;
  }

}
