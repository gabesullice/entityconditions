<?php

namespace Drupal\typed_data_conditions;

use Drupal\Core\TypedData\TypedDataInterface;

interface ComparableStringDataInterface extends ComparableListDataInterface {

  const STARTS_WITH = 'STARTS_WITH';

  const ENDS_WITH = 'ENDS_WITH';

}
