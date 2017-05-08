<?php

namespace Drupal\typed_data_conditions;

use Drupal\Core\TypedData\TypedDataInterface;

interface EvaluatorInterface {

  /**
   * Returns a boolean result of the object evaluated against the given data.
   *
   * @param \Drupal\Core\TypeData\TypedDataInterface $data
   *   The data against which the object should be evaluated.
   */
  public function evaluate(TypedDataInterface $data);

}
