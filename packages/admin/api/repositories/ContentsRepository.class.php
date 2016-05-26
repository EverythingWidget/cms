<?php

namespace admin;

/**
 * Description of ContentsRepository
 *
 * @author Eeliya
 */
class ContentsRepository implements \ew\CRUDRepository {

  public function create($input, $response) {
    $response->set_status_code(501);
    return ['message' => 'ContentsRepository: REST create functionality is not implemented'];
  }

  public function delete($input, $response) {
    $response->set_status_code(501);
    return ['message' => 'ContentsRepository: REST delete functionality is not implemented'];
  }

  public function read($input, $response) {
    
  }

  public function update($input, $response) {
    $response->set_status_code(501);
    return ['message' => 'ContentsRepository: REST update functionality is not implemented'];
  }

}
