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
            view("blogs.content",[
                'blogs'=>$v,
            ]);
            $str = ob_get_contents();

            file_put_contents(ROOT.'/public/contents/'.$v['id'].".html",$str);
            ob_clean();
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
       echo $num;
       return $num;
   }
   public function tomysql(){
       $blog = new Blog;
       $blog->redistomysql();
   }
}