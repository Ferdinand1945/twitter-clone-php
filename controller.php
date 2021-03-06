<?php
require("model.php");
require("router.php");
//en enkel class flash för att displaya errors och välkomen medelnadem
class Flash{
    
    public $msg;
    public $type;
    
    function __construct($msg, $type)
    {
        $this->msg = $msg;
        $this->type = $type;
    }
    
    public function display(){
        echo "<div class=\"flash " . $this->type . "\">" . $this->msg . "</div>";
    }
}
//Class controller innehåller funktionerna för att få korrekta och förkortade URI, samt funktion som laddar upp de olika sidor och själva "sidor"
class Controller{
	
//--------Variables------------
	private $model;
	private $router;
//--------Functions------------
	
	//Constructor
	function __construct(){
		//startar private variabler model och router
		$this->model = new Model();
		$this->router = new Router();
		
        //Proccess Query String
        if(strlen($_GET['query']) > 0)
        {
            $queryParams = explode("/", $_GET['query']);
        }
        else{
            $queryParams = false;
        }
        
        $page = $_GET['page'];
        
		//Handle Page Load// den här del tar hand av loading av varje sida 
		$endpoint = $this->router->lookup($page);
		if($endpoint === false)
		{
			header("HTTP/1.0 404 Not Found");//error 404 för page not found
		}
		else
		{
            $this->$endpoint($queryParams);
            
		}
      
       $this->upload_path = ($params['upload_path'] != "" ? $params['upload_path'] : 'uploads/');
        $this->max_file_size = ($params['max_file_size'] != "" ? $params['max_file_size'] : 1000000);
        $this->allowed = ($params['allowed'] != "" ? $params['allowed'] : array('images/jpg','images/gif','images/jpeg','images/png'));
        $this->site_url = 'http://www.' . $_SERVER["SERVER_NAME"] . '/';
      
	}
  
    private function redirect($url){
        header("Location: /" . $url);
    }
	
	//--- Framework funktionen
	private function loadView($view, $data = null){
		if(is_array($data))
		{
			extract($data);
		}
		require("Views/" . $view . ".php");
	}
	private function loadPage($user, $view, $data = null, $flash = false){
        $this->loadView("header", array('User' => $user));
        if($flash !== false)
        {
            $flash->display();
        }
        $this->loadView($view, $data);
        $this->loadView("footer");
    }
	//--- mmmm säkerhet är inte min starkaste sida så här finns förmodligen ett par errors 
	private function checkAuth(){
        if(isset($_COOKIE['Auth']))
        {
            return $this->model->userForAuth($_COOKIE['Auth']);
        }
        else
        {
            return false;
        }
	}
	//Index Page är funktionen som laddar sidan home. här gör jag användning av classen flash för att displaya validering av login och registrering i home page
	private function indexPage($params){
        $user = $this->checkAuth();
        if($user !== false){ $this->redirect("buddies"); }
        else
        {
            $flash = false;
            if($params !== false)
            {
                $flashArr = array(
                    "0" => new Flash("Your Username and/or Password was incorrect.", "error"),
                    "1" => new Flash("There's already a user with that email address.", "error"),
                    "2" => new Flash("That username has already been taken.", "error"),
                    "3" => new Flash("Passwords don't match.", "error"),
                    "4" => new Flash("Your Password must be at least 6 characters long.", "error"),
                    "5" => new Flash("You must enter a valid Email address.", "error"),
                    "6" => new Flash("You must enter a username.", "error"),
                    "7" => new Flash("You have to be signed in to acces that page.", "warning")
                );
                $flash = $flashArr[$params[0]];
            }
            $this->loadPage($user, "home", array(), $flash);
        }
	}
  //validering av register form
	private function signUp(){
        if($_POST['email'] == "" || strpos($_POST['email'], "@") === false){
            $this->redirect("home/5");
        }
        else if($_POST['username'] == ""){
            $this->redirect("home/6");
        }
        else if(strlen($_POST['password']) < 6)
        {
            $this->redirect("home/4");
        }
        else if($_POST['password'] != $_POST['password2'])
        {
            $this->redirect("home/3");
        }
        else{
            $pass = hash('sha256', $_POST['password']);
            $signupInfo = array(
                'username' => $_POST['username'],
                'email' => $_POST['email'],
                'password' => $pass,
                'name' => $_POST['name']
            );
            $resp = $this->model->signupUser($signupInfo);
            if($resp === true)
            {
                $this->redirect("buddies/1");
            }
            else
            {
                $this->redirect("home/" . $resp); 
            }
        }
    }
    
    private function login(){
        $pass = hash('sha256', $_POST['password']);
		$loginInfo = array(
			'username' => $_POST['username'],
			'password' => $pass
		);
		if($this->model->attemptLogin($loginInfo))
        {
            $this->redirect("buddies/0");
        }
        else
        {
            $this->redirect("home/0");
        }
	}
	
	private function logout(){
        $this->model->logoutUser($_COOKIE['Auth']);
        $this->redirect("home");
    }
    
    private function buddies($params){
        $user = $this->checkAuth();
        if($user === false){ $this->redirect("home/7"); }
        else
        {
            $userData = $this->model->getUserInfo($user);
            $tposts = $this->model->getFollowers($user);
            $flash = false;
            if(isset($params[0]))
            {
                $flashArr = array(
                    "0" => new Flash("Welcome Back, " . $user->name, "notice"),
                    "1" => new Flash("Welcome to Twitter Clone by Fernando och Dagmawi, Thanks for signing up.", "notice"),
                    "2" => new Flash("You have exceeded the 140 character limit for Posts lenght", "error")
                );
                $flash = $flashArr[$params[0]];
            }
            $this->loadPage($user, "buddies", array('User' => $user, "userData" => $userData, "tposts" => $tposts), $flash);
        }
    }
    
    private function newPost($params){
        $user = $this->checkAuth();
        if($user === false){ $this->redirect("home/7"); }
        else{
           $text = ($_POST['text']);
            if(strlen($text) > 140)
            {
                $this->redirect("buddies/2");
            }
            else
            {
                $this->model->postComments($user, $text);
                $this->redirect("buddies");
            }
        }
        
    }
    
    private function publicPage($params){
        $user = $this->checkAuth();
        if($user === false){ $this->redirect("home/7"); }
        else
        {
            $q = false;
            if(isset($_POST['query']))
            {
                $q = $_POST['query'];
            }
            $comments = $this->model->getPublicComments($q);
            $this->loadPage($user, "public", array('comments' => $comments));
        }
    }
    
    private function profiles($params){
        $user = $this->checkAuth();
        if($user === false){ $this->redirect("home/7"); }
        else{
            $q = false;
            if(isset($_POST['query']))
            {
                $q = $_POST['query'];
            }
            $profiles = $this->model->getPublicProfiles($user, $q);
            $this->loadPage($user, "profiles", array('profiles' => $profiles));
        }
    }
    private function follow($params){
        $user = $this->checkAuth();
        if($user === false){ $this->redirect("home/7"); }
        else{
            $this->model->follow($user, $params[0]);
            $this->redirect("profiles");
        }
    }
    private function unfollow($params){
        $user = $this->checkAuth();
        if($user === false){ $this->redirect("home/7"); }
        else{
            $this->model->unfollow($user, $params[0]);
            $this->redirect("profiles");
        }
    }
  
  private function uploadPic($params) {
     $user = $this->checkAuth();
    if($user === false) { $this->redirect("home/7"); } else {
        $uploadPic = $this->model->uploadPic($user, $r);
        $this->loadPage($user, "uploadPic", array('uploadPic' => $uploadPic));
    }
  }
  
  private function like(){
  
  }

}

