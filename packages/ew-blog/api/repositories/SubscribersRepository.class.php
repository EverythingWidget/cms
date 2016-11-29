<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace ew_blog;

/**
 * Description of SubscribersRepository
 *
 * @author Eeliya
 */
class SubscribersRepository extends \ew\SimpleRepository {

  protected $path_to_model = '/ew-blog/api/models/ew_blog_subscribers.php';
  protected $model_name = 'ew_blog\ew_blog_subscribers';
  protected $name = 'subscriber';
}
