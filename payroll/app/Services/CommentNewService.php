<?php
namespace App\Services;

use App\Repositories\CommentNewRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class CommentNewService
{
    protected $commentReposity;
    public function __construct(CommentNewRepository $commentReposity)
    {
       $this->commentReposity=$commentReposity;
    }
    public function getAllNew()
    {
        return $this->commentReposity->getAllNew();
    }
    public function getByNew($newId)
    {
        return $this->commentReposity->getByNew($newId);
    }
    public function create(array $data)
    {
        $user=Auth::user();
        //thêm thông tin user
        $data['userId']=$user->id;
        $data['userName']=$user->name;
        $data['userAvatar']=$user->avatar ?? null;
        $data['level']=0;
        $data['time']=Carbon::now();
        //Nếu là reply->tăng level dựa trên parent
        if(!empty($data['parent_id'])){
          $parent=$this->commentReposity->find($data['parent_id']);
          if($parent){
            $data['level']=$parent->level+1;
          }
        }
        return $this->commentReposity->create($data);
    }
    public function update($id,array $data)
    {
        $comment=$this->commentReposity->find($id);
        if(!$comment){
            return false;
        }
        $data['time']=Carbon::now();
        return $this->commentReposity->update($comment,$data);
    }
    public function delete($id)
    {
        $comment=$this->commentReposity->find($id);
        if(!$comment)
        {
            return false;
        }
        return $this->commentReposity->delete($comment);
    }
}
