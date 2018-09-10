<?php
namespace controllers ;
use models\Blog;
use PDO;
class BlogController 
{
    public function index(){
       $blog = new Blog;
       $data = $blog->search_page();
        view("blogs.index",[
            "blog"=>$data[0],
            "str"=>$data[1]
        ]);
    }
    public function staticHtml(){
        $blog = new Blog;
        $arr = $blog->static_content();
        ob_start();
        foreach($arr as $v){
            if($v['is_show']==1){
            view("blogs.content",[
                'blogs'=>$v,
            ]);
            $str = ob_get_contents();

            file_put_contents(ROOT.'/public/contents/'.$v['id'].".html",$str);
            ob_clean();
            }
        }
    }
    public function get_index(){
        $blog = new Blog;
        $blog->index2htlm();
    }
   public function update_display(){
       $id = $_GET['id'];
       $blog = new Blog;
       $num = $blog->update_id($id);
    //    return $num;
       echo json_encode([
           'num'=>$num,
           "email"=>isset($_SESSION['email'])?$_SESSION['email']:""
       ]);
   }
   public function tomysql(){
       $blog = new Blog;
       $blog->redistomysql();
   }
   public function created(){
       view('blogs.create');
   }
   public function docreated(){
       $title =  $_POST['title'];
       $content = $_POST['content'];
       $is_show  = $_POST['is_show'];
       $blog = new Blog;
       $blog->add($title,$content,$is_show);
       if($is_show==1){
          $blog->makeHtml($id);
       }
        message('发表成功','/blog/index',2);
   }
   public function delete(){
       $id = $_POST['id'];
       $blog = new Blog;
       $blog->delete($id);
       $blog->delHtml($id);
       message('删除成功','/blog/index',2);

   }
   public function eidt(){
       $id = $_GET['id'];
       $blog = new Blog;
       $b  = $blog->find($id);
       view('blogs/update',[
            'blog'=>$b
         ]);
   }
   public function update(){
       $title = $_POST['title'];
       $content = $_POST['content'];
       $is_show = $_POST['is_show'];
       $id = $_POST['id'];
       $blog = new Blog;
       if($is_show==1){
           $blog->makeHtml($id);
       }else{
        $blog->delHtml($id);
       }
       $reg = $blog ->update($title,$content,$is_show,$id);
       if($reg){
        message("修改成功",'/blog/index',2);
       }else{
        message("修改失败",'/blog/edit?id='.$id,2);
           
       }
   }
   public function content(){
       $id = $_GET['id'];
       $blog = new Blog;
       $b = $blog ->find($id);
       if($b['user_id'] != $_SESSION['id'] ){
           die("无权访问");
       }
       view('blogs.content',[
           "blogs"=>$b
       ]);
   }
}