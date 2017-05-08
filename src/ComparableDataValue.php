<?php

namespace Drupal\typed_data_conditions;

use Drupal\Core\TypedData\TypedDataInterface;
use Drupal\Core\TypedData\TraversableTypedDataInterface;

/**
 * Shim class for TypedData that have not implemented ComparableDataInterface.
 */
class ComparableDataValue implements ComparableDataInterface, TypedDataInterface {

  use ComparableDataTrait {
    compare as protected traitCompare;
    checkComparable as protected traitCheckComparable;
  }

  /**
   * A wrapped typed data object.
   *
   * @var \Drupal\Core\TypedData\TypedDataInterface
   */
  protected $wrapped;

  /**
   * Creates a new ComparableDataValue from an instance of TypedData.
   */
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

  /**
   * {@inheritdoc}
   */
  protected function checkComparable(TypedDataInterface $value, TypedDataInterface $data, $operator) {
    $this->traitCheckComparable($this->wrapped, $data, $operator);
  }

  /**
   * Passes all method calls to the wrapped object.
   */
  public function __call($method, $arguments) {
    return call_user_func_array([$this->wrapped, $method], $arguments);
  }

  /**
   * {@inheritdoc}
   */
  public static function createInstance($definition, $name = NULL, TraversableTypedDataInterface $parent = NULL) {
    return $this->__call(__FUNCTION__, func_get_args());
  }

  /**
   * {@inheritdoc}
   */
  public function getDataDefinition() {
    return $this->__call(__FUNCTION__, []);
  }

  /**
   * {@inheritdoc}
   */
  public function getValue() {
    return $this->__call(__FUNCTION__, []);
  }

  /**
   * {@inheritdoc}
   */
  public function setValue($value, $notify = TRUE) {
    return $this->__call(__FUNCTION__, func_get_args());
  }

  /**
   * {@inheritdoc}
   */
  public function getString() {
    return $this->__call(__FUNCTION__, []);
  }

  /**
   * {@inheritdoc}
   */
  public function getConstraints() {
    return $this->__call(__FUNCTION__, []);
  }

  /**
   * {@inheritdoc}
   */
  public function validate() {
    return $this->__call(__FUNCTION__, []);
  }

  /**
   * {@inheritdoc}
   */
  public function applyDefaultValue($notify = TRUE){
    return $this->__call(__FUNCTION__, func_get_args());
  }

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return $this->__call(__FUNCTION__, []);
  }

  /**
   * {@inheritdoc}
   */
  public function getParent() {
    return $this->__call(__FUNCTION__, []);
  }

  /**
   * {@inheritdoc}
   */
  public function getRoot() {
    return $this->__call(__FUNCTION__, []);
  }

  /**
   * {@inheritdoc}
   */
  public function getPropertyPath() {
    return $this->__call(__FUNCTION__, []);
  }

  /**
   * {@inheritdoc}
   */
  public function setContext($name = NULL, TraversableTypedDataInterface $parent = NULL) {
    return $this->__call(__FUNCTION__, func_get_args());
  }

}
