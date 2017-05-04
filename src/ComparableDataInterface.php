<?php

namespace Drupal\typed_data_conditions;

use Drupal\Core\TypedData\TypedDataInterface;

interface ComparableDataInterface {

  const EQUAL = '=';

  const NOT_EQUAL = '<>';

  const LESS_THAN = '<';

  const LESS_THAN_EQUAL = '<=';

  const GREATER_THAN = '>';

  const GREATER_THAN_EQUAL = '>=';

  const BETWEEN = 'BETWEEN';

  const NOT_BETWEEN = 'NOT BETWEEN';

  const IN = 'IN';

  const NOT_IN = 'NOT IN';

  /**
   * Allows an TypedData instance to be compared.
   *
   * @param TypedDataInterface $comparison
   *   The data to compare against.
   * @param string $operator
   *   The comparison operator.
   *
   * @throws IncomparableDataType
   *   When the comparison type can't be compared against.
   * @throws InvalidComparisonOperator
   *   When the data type cannot be compared by the given operator.
   *
   * @return bool
   */
  public function compare(TypedDataInterface $data, $operator = ComparableDataInterface::EQUAL);

}
