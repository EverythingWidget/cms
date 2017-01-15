<?php

namespace ew;

/**
 * Description of CRUDRepository
 *
 * @author Eeliya
 */
interface CRUDRepository {

  public function create($input);

  public function read($input);

  public function update($input);

  public function delete($input);
}
