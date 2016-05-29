<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace ew;

/**
 * Description of CRUDRepository
 *
 * @author Eeliya
 */
interface CRUDRepository {

  //put your code here
  public function create($input);

  public function read($input);

  public function update($input);

  public function delete($input);
}
