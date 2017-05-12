<?php

namespace Drupal\typed_data_conditions;

use Drupal\Core\TypedData\ListInterface;
use Drupal\Core\TypedData\TypedDataInterface;
use Drupal\Core\TypedData\TraversableTypedDataInterface;

/**
 * Shim class for Lists that have not implemented ComparableListDataInterface.
 */
class ComparableListDataValue extends ComparableDataValue implements \IteratorAggregate, ComparableListDataInterface, ListInterface {

  /**
   * Creates a new ComparableListDataValue from an instance of a TypedData list.
   */
  public function __construct(ListInterface $data) {
    $this->wrapped = $data;
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
  public function appendItem($value = NULL) {
    return $this->__call(__FUNCTION__, func_get_args());
  }

  /**
   * {@inheritdoc}
   */
  public function filter($callback) {
    return $this->__call(__FUNCTION__, func_get_args());
  }

  /**
   * {@inheritdoc}
   */
  public function first() {
    return $this->__call(__FUNCTION__, []);
  }

  /**
   * {@inheritdoc}
   */
  public function get($index) {
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
  public function getItemDefinition() {
    return $this->__call(__FUNCTION__, []);
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty() {
    return $this->__call(__FUNCTION__, []);
  }

  /**
   * {@inheritdoc}
   */
  public function removeItem($index) {
    return $this->__call(__FUNCTION__, func_get_args());
  }

  /**
   * {@inheritdoc}
   */
  public function set($index, $value) {
    return $this->__call(__FUNCTION__, func_get_args());
  }

  /**
   * {@inheritdoc}
   */
  public function onChange($name) {
    return $this->__call(__FUNCTION__, func_get_args());
  }

  /**
   * {@inheritdoc}
   */
  public function getIterator() {
    return $this->__call(__FUNCTION__, []);
  }

  /**
   * {@inheritdoc}
   */
  public function offsetExists($offset) {
    return $this->__call(__FUNCTION__, func_get_args());
  }

  /**
   * {@inheritdoc}
   */
  public function offsetGet($offset) {
    return $this->__call(__FUNCTION__, func_get_args());
  }

  /**
   * {@inheritdoc}
   */
  public function offsetSet($offset, $value) {
    return $this->__call(__FUNCTION__, func_get_args());
  }

  /**
   * {@inheritdoc}
   */
  public function offsetUnset($offset) {
    return $this->__call(__FUNCTION__, func_get_args());
  }

  /**
   * {@inheritdoc}
   */
  public function count() {
    return $this->__call(__FUNCTION__, []);
  }

}
