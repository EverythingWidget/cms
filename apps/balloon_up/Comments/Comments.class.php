<?php
namespace balloon_up;
use Section;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Comments
 *
 * @author Eeliya
 */
class Comments extends Section
{

   public function get_title()
   {
      return "Comments";
   }

   public function get_description()
   {
      return "Manage comments";
   }

   /** Post a comment on the balloon
    * 

    * @param int $balloon_id
    * @param int $balloons_state
    * @param int $comment_id
    * @param int $comments_state
    * @param type $message
    */
   public function post($balloon_id, $comment_id,$react_to_balloons, $react_to_comments, $message)
   {
      
   }

   public function edit_comment($balloon_comment_id, $balloon_id, $comment_id,$react_to_balloons, $react_to_comments, $message)
   {
      
   }

   public function delete_comment($balloon_comment_id)
   {
      
   }

   public function like_comment($comment_id)
   {
      
   }

   public function dislike_comment($comment_id)
   {
      
   }

   public function get_balloons_comments($balloon_id, $token = 0, $sort = NULL)
   {
      
   }

//put your code here
}
