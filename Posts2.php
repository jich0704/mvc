<?php


class Posts2 extends Controller
{
   public function __construct()
   {
      if(!isLoggedIn() ){
         redirect('users/login');
      }
      $this->postModel = $this->model('Post2');
      $this->userModel = $this->model('User');
   }

   public function index()
    {
       $posts = $this->postModel->getPosts();
       $data = [
          'posts' => $posts
       ];
       $this->view('posts2/index', $data);
    }


    public function add()
    {
       if($_SERVER['REQUEST_METHOD']=='POST'){
          // Sanitize POST Array
          $_POST = filter_input_array(INPUT_POST,FILTER_SANITIZE_STRING);

          $data = [
             'title' => trim($_POST['title']),
             'body' => trim($_POST['body']),
             'user_id' => $_SESSION['user_id'],
             'title_err' => '',
             'body_err' => ''
          ];

          // Validate
          if( empty($data['title']) ){
             $data['title_err'] = 'Please enter the title';
          }
          if( empty($data['body']) ){
             $data['body_err'] = 'Please enter the body';
          }

          // Make sure no errors
          if ( empty($data['title_err']) && empty($data['body_err']) ){
             // Validated
             if( $this->postModel->addPost($data) ){
                flash('post_message', 'Post Added');
                redirect('posts2');
             } else{
                die('Something went wrong');
             }
          } else {
             // Load the view
             $this->view('posts2/add', $data);
          }

       } else{
          $data = [
             'title' => '',
             'body' => ''
          ];
          $this->view('posts2/add', $data);
       }

    }



   public function edit($id)
   {
      if($_SERVER['REQUEST_METHOD']=='POST'){
         // Sanitize POST Array
         $_POST = filter_input_array(INPUT_POST,FILTER_SANITIZE_STRING);

         $data = [
            'id' => $id,
            'title' => trim($_POST['title']),
            'body' => trim($_POST['body']),
            'user_id' => $_SESSION['user_id'],
            'title_err' => '',
            'body_err' => ''
         ];

         // Validate
         if( empty($data['title']) ){
            $data['title_err'] = 'Please enter the title';
         }
         if( empty($data['body']) ){
            $data['body_err'] = 'Please enter the body';
         }

         // Make sure no errors
         if ( empty($data['title_err']) && empty($data['body_err']) ){
            // Validated
            if( $this->postModel->updatePost($data) ){
               flash('post_message', '게시글 업데이트 완료');
               redirect('posts2');
            } else{
               die('Something went wrong');
            }
         } else {
            // Load the view
            $this->view('posts2/edit', $data);
         }

      } else{
         // Get existing post from model
         $post = $this->postModel->getPostById($id);

         //Check for owner
         if( $post->user_id != $_SESSION['user_id'] ){
            redirect('posts2');
         }
         $data = [
            'id' => $post->id,
            'title' => $post->title,
            'body' => $post->body,
            'title_err' => '',
            'body_err' => ''
         ];
         $this->view('posts2/edit', $data);
      }

   }

   public function show($id)
   {
      $post = $this->postModel->getPostById($id);
      $user = $this->userModel->getUserById($post->user_id);
      $data = [
         'post' => $post,
         'user' => $user
      ];
      $this->view('posts2/show', $data);
   }


   public function delete($id)
   {
      if($_SERVER['REQUEST_METHOD']=='POST') {
         // Get existing post from model
         $post = $this->postModel->getPostById($id);

         //Check for owner
         if( $post->user_id != $_SESSION['user_id'] ){
            redirect('posts2');
         }
         if( $this->postModel->deletePost($id) ){
            flash('post_message', 'Post removed');
            redirect('posts2');
         } else {
            die('Something went wrong');
         }

      } else {
         redirect('posts2');
      }
   } //end function


}