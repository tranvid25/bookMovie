<?php
namespace App\Services;

use App\Models\FeedBack;
use App\Repositories\FeedBackRepository;
use Illuminate\Support\Facades\Mail;

class FeedBackService
{
    protected $FeedBackReposity;
    public function __construct(FeedBackRepository $FeedBackReposity){
      $this->FeedBackReposity=$FeedBackReposity;
    }
    public function getAll(){
        return $this->FeedBackReposity->getAll();
    }
    public function findByFeedBack($id){
        return $this->FeedBackReposity->findByFeedBack($id);
    }
    public function create(array $data){
        return $this->FeedBackReposity->create($data);
    }
    public function update($id,array $data)
    {
        $FeedBack=$this->FeedBackReposity->find($id);
        if($FeedBack && isset($data['email'])){
            Mail::send('mail.sendEmailFeedback',[
                'tieuDe'=>$data['tieuDe'] ?? '',
                'noiDung'=>$data['noiDung'] ?? '',
                'ngayXuLy'=>$data['ngayXuLy'] ?? '',
                'noiDungXuLy'=>$data['noiDungXuLy'] ?? '',
            ],function($message) use($data){
                $message->to($data['email'])->subject('Thông tin feedback');
            });
        }
        return $this->FeedBackReposity->update($id,$data);
    }
    public function delete($id){
        $FeedBack=$this->FeedBackReposity->find($id);
        return $this->FeedBackReposity->delete($FeedBack);
    }


}
