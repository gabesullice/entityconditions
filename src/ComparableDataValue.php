<?php

namespace Drupal\typed_data_conditions;

use Drupal\Core\TypedData\TypedDataInterface;

class ComparableDataValue implements ComparableDataInterface {

  use ComparableDataTrait {
    compare as protected traitCompare;
  }

  /**
   * A wrapped typed data object.
   *
   * @var \Drupal\Core\TypedData\TypedDataInterface
   */
  protected $wrapped;

  public function __construct(TypedDataInterface $data) {
    $this->wrapped = $data;
  }

  /**
   * Wraps a TypedData object so it can be used for a ComparableDataInterface. 
   *
   * @param \Drupal\Core\TypedData\TypedDataInterface $data
   *   The data to be wrapped.
   *
   * @return \Drupal\typed_data_conditions\ComparableDataValue
   *   The wrapped object.
   */
  public static function create(TypedDataInterface $data) {
    return new static($data);
  }

  /**
   * {@inheritdoc}
   */
  public function compare(TypedDataInterface $data, $operator = ComparableDataInterface::EQUAL) {
    return $this->traitCompare($data, $operator);
  }

  protected function checkComparable(TypedDataInterface $data) {
    if (get_class($this->wrapped) !== get_class($data)) {
      $message = "Cannot compare data of type '%s' with data of type '%s'";
      throw new IncomparableDataType(
        sprintf($message, get_class($this), get_class($data))
      );
    }
  }

  /**
   * Passes all method calls to the wrapped object.
   */
  public function __call($method, $arguments) {
    return call_user_func_array([$this->wrapped, $method], $arguments);
  }

}
