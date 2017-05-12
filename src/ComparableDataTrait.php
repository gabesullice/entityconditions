<?php

namespace Drupal\typed_data_conditions;

use Drupal\Core\TypedData\Plugin\DataType\StringData;
use Drupal\Core\TypedData\TypedDataInterface;
use Drupal\Core\TypedData\ListInterface;

trait ComparableDataTrait {

  /**
   * {@inheritdoc}
   */
  public function compare(TypedDataInterface $data, $operator = ComparableDataInterface::EQUAL) {
    $this->checkComparable($this, $data, $operator);

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
      case ComparableListDataInterface::CONTAINS:
        return $this->contains($data);
      case ComparableStringDataInterface::STARTS_WITH:
        return $this->startsWith($data);
      case ComparableStringDataInterface::ENDS_WITH:
        return $this->endsWith($data);
      default:
        $message = "The '%s' operator is not supported. See %s.";
        throw new InvalidComparisonOperator(
          sprintf($message, $operator, ComparableDataInterface::class)
        );
    }
  }

  protected function checkComparable(TypedDataInterface $value, TypedDataInterface $data, $operator) {
    $list_comparisons = [
      ComparableDataInterface::BETWEEN => ComparableDataInterface::LESS_THAN,
      ComparableDataInterface::NOT_BETWEEN => ComparableDataInterface::LESS_THAN,
      ComparableDataInterface::IN => ComparableDataInterface::EQUAL,
      ComparableDataInterface::NOT_IN => ComparableDataInterface::EQUAL,
    ];

    if (in_array($operator, array_keys($list_comparisons))) {
      // If the data does not implement ListInterface, then we can't compare it.
      if (!($data instanceof ListInterface)) {
        $message = "Cannot compare data of type '%s' with data of type '%s'.";
        throw new IncomparableDataType(
          sprintf($message, get_class($value), get_class($data))
        );
      }

      // If the list is empty, we're fine. Otherwise, make sure the inner values
      // are comparable.
      if (!$data->isEmpty()) {
        $this->checkComparable($value, $data->first(), $list_comparisons[$operator]);
      }
    }
    elseif ($operator == ComparableListDataInterface::CONTAINS) {
      if (!($this instanceof ListInterface)) {
        $message = "The '%s' operator cannot be used with non-list data. Have: '%s'";
        throw new InvalidComparisonOperator(sprintf($message, $operator, get_class($this)));
      }
    }
    else {
      // Check that the data is of the same type.
      if (get_class($value) !== get_class($data)) {
        $message = "Cannot compare data of type '%s' with data of type '%s'.";
        throw new IncomparableDataType(
          sprintf($message, get_class($value), get_class($data))
        );
      }
    }

    $string_comparisons = [
      ComparableStringDataInterface::STARTS_WITH,
      ComparableStringDataInterface::ENDS_WITH,
    ];

    if (in_array($operator, $string_comparisons)) {
      if (!is_string($this->getValue())) {
        $message = "The '%s' operator cannot be used with non-string data.";
        throw new InvalidComparisonOperator(sprintf($message, $operator));
      }
    }
  }

  public function isEqualTo($data) {
    return $this->getValue() === $data->getValue();
  }

  public function isNotEqualTo($data) {
    return $this->getValue() !== $data->getValue();
  }

  public function isLessThan($data) {
    return $this->getValue() < $data->getValue();
  }

  public function isLessThanEqualTo($data) {
    return $this->getValue() <= $data->getValue();
  }

  public function isGreaterThan($data) {
    return $this->getValue() > $data->getValue();
  }

  public function isGreaterThanEqualTo($data) {
    return $this->getValue() >= $data->getValue();
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

  public function contains($data) {
    if ($data instanceof StringData && $this instanceof ListInterface) {
      return $this->upcast($data)->compare($this, ComparableDataInterface::IN);
    }
    elseif ($data instanceof StringData) {
      return strpos($this->getValue(), $data->getValue()) !== FALSE;
    }
    else {
      return $this->upcast($data)->compare($this, ComparableDataInterface::IN);
    }
  }

  public function startsWith($data) {
    return strpos($this->getValue(), $data->getValue()) === 0;
  }

  public function endsWith($data) {
    $haystack = $this->getValue();
    $needle = $data->getValue();
    return strpos($haystack, $needle) === strlen($haystack) - strlen($needle);
  }

  protected function upcast($data) {
    if (!($data instanceof ComparableDataInterface)) {
      $data = ComparableDataValue::create($data);
    }
    return $data;
  }

}
