<?php
namespace App\Services;

use App\Repositories\CommentMovieRepository;
use Carbon\Carbon;

class CommentMovieService
{
   protected $comment;
   public function __construct(CommentMovieRepository $comment)
   {
     $this->comment=$comment;
   }
   public function getAllWithMovie()
   {
    return $this->comment->allWithMovie();
   }
   public function getByMovie($movieId)
   {
    return $this->comment->findByMovie($movieId);
   }
   public function create(array $data, $user)
   {
    $data['userId']=$user->id;
    $data['userName']=$user->name;
    $data['userAvatar']=$user->avatar ?? null;
    //Tính level dựa vào comment cha
    if(!empty($data['parent_id']))
    {
        $parent=$this->comment->find($data['parent_id']);
        $data['level']=$parent ? $parent->level+1:1;
    }
    else{
        $data['level']=0;
    }
    $data['time']=Carbon::now();
    return $this->comment->create($data);
   }
   public function update($id,array $data)
   {
    $comment=$this->comment->find($id);
    if($comment)
    {
        return false;
    }
    $data['time']=Carbon::now();
    return $this->comment->update($comment,$data);
   }
   public function delete($id)
   {
    $comment=$this->comment->find($id);
    if(!$comment){
        return false;
    }
    return $this->comment->delete($comment);
   }
}
