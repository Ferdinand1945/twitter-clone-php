<?php
//alla objekten skapat i classen Model.  
class Model{
    
    private $db; // Behåller msqli variabel $db
    
    function __construct(){
        $this->db = new mysqli('youhost', 'user', 'pass', 'dbname');
    }
    
    // Vi skapar funktionen select som kommer att förlätta anropet av data i databasen på de olika funktionen.
    private function select($table, $arr){
        $query = "SELECT * FROM " . $table;
        $pref = " WHERE ";
        foreach($arr as $key => $value)
        {
            $query .= $pref . $key . "='" . $value . "'";
            $pref = " AND ";
        }
        $query .= ";";
        return $this->db->query($query);
    }
    
    // Som SELECT skapar vi en INSERT funktion som kommer att förlätta insertion av data i de olik funktionen.
    private function insert($table, $arr)
    {
        $query = "INSERT INTO " . $table . " (";
        $pref = "";
        foreach($arr as $key => $value)
        {
            $query .= $pref . $key;
            $pref = ", ";
        }
        $query .= ") VALUES (";
        $pref = "";
        foreach($arr as $key => $value)
        {
            $query .= $pref . "'" . $value . "'";
            $pref = ", ";
        }
        $query .= ");";
        return $this->db->query($query);
    }
    
    // DELETE för förlätta redigering av db i en funktion
    private function delete($table, $arr){
        $query = "DELETE FROM " . $table;
        $pref = " WHERE ";
        foreach($arr as $key => $value)
        {
            $query .= $pref . $key . "='" . $value . "'";
            $pref = " AND ";
        }
        $query .= ";";
        return $this->db->query($query);
    }
    // Skapar en funktion som ska kontrollera om en row existerar, här ser vi användning av funktion select för första gången.
    private function exists($table, $arr){
        $res = $this->select($table, $arr);
        return ($res->num_rows > 0) ? true : false;
    }
    
    public function userForAuth($hash){
        $query = "SELECT Users.* FROM Users JOIN (SELECT username FROM UserAuth WHERE hash = '"; 
        $query .= $hash . "' LIMIT 1) AS UA WHERE Users.username = UA.username LIMIT 1";
        $res = $this->db->query($query);
        if($res->num_rows > 0)
        {
            return $res->fetch_object();
        }
        else
        {
            return false;
        }
    }
    
  
  // funktion signupUser, för att registrera en ny User
    public function signupUser($user){
        $emailCheck = $this->exists("Users", array("email" => $user['email']));
        if($emailCheck){
            return 1;
        }
        else{
            $userCheck = $this->exists("Users", array("username" => $user['username']));
            if($userCheck){
                return 2;
            }
            else{
                $user['created_at'] = date( 'Y-m-d H:i:s');
                $user['gravatar_hash'] = md5(strtolower(trim($user['email'])));
                $this->insert("Users", $user);
                $this->authorizeUser($user);
                return true;
            }
        }
    }
    
  
  //authorizeUser fungerar som validering och filter av den data som kommer att sättas i tabelen   
    public function authorizeUser($user){
        $chars = "qazwsxedcrfvtgbyhnujmik,ol.p;/1234567890QAZWSXEDCRFVTGBYHNUJMIKOLP";
        $hash = sha1($user['username']);
        for($i = 0; $i<12; $i++)
        {
            $hash .= $chars[rand(0, 64)]; 
        }
        $this->insert("UserAuth", array("hash" => $hash, "username" => $user['username']));
        setcookie("Auth", $hash);
    }
    
  
  //login funktion
    public function attemptLogin($userInfo){
        if($this->exists("Users", $userInfo))
        {
            $this->authorizeUser($userInfo);
            return true;
        }
        else{
            return false;
        }
    }
    //logout funktion
    public function logoutUser($hash){
        $this->delete("UserAuth", array("hash" => $hash));
        setcookie ("Auth", "", time() - 3600);
    }
    
  
  //GetUserInfo här var jag tvungen att skapa en query för att få UserInfo annars fick jag en error genom $this->select($table, $arr)
    public function getUserInfo($user)
    {
        $query = "SELECT theposts_count, IF(theposts IS NULL, 'You have no Comments', theposts) as theposts, followers, following ";
        $query .= "FROM (SELECT COUNT(*) AS theposts_count FROM Comments WHERE user_id = " . $user->id . ") AS RC ";
        $query .= "LEFT JOIN (SELECT user_id, theposts FROM Comments WHERE user_id = " . $user->id . " ORDER BY created_at DESC LIMIT 1) AS R"; 
        $query .= " ON R.user_id = " . $user->id . " JOIN ( SELECT COUNT(*) AS followers FROM Follows WHERE followee_id = " . $user->id;
        $query .=  ") AS FE JOIN (SELECT COUNT(*) AS following FROM Follows WHERE user_id = " . $user->id . ") AS FR;";
        $res = $this->db->query($query);
        return $res->fetch_object();
    }
    
  
  
  //Den här funktion fick jag olika errors men till slut hittade jag ett par bra exemplar och gick att skapa bra.
    public function getFollowers($user)
    {
        $query = "SELECT name, username, gravatar_hash, theposts, Comments.created_at FROM Comments JOIN (";
        $query .= "SELECT Users.* FROM Users LEFT JOIN (SELECT followee_id FROM Follows WHERE user_id = ";
        $query .= $user->id . " ) AS Follows ON followee_id = id WHERE followee_id = id OR id = " . $user->id;
        $query .= ") AS Users on user_id = Users.id ORDER BY Comments.created_at DESC LIMIT 10;";
        $res = $this->db->query($query);
        $tposts = array();
        while($row = $res->fetch_object())
        {
            array_push($tposts, $row);
        }
        return $tposts;
    }  
    
  
  
  //Post a comment funktion sätter in arrayen theposts i tabelen Comments(texten att skriva) created_at och user_id för att senare dysplaya den i postrutan.
    public function postComments($user, $text){
        $r = array(
            "theposts" => $text,
            "created_at" => date( 'Y-m-d H:i:s'),
            "user_id" => $user->id
        );
        $this->insert("Comments", $r);
    }
    
  
  // Kopplad till Comments tabel, får kommentarerna, datum när posten var skapat och user_id som senare visas i en $row
    public function getPublicComments($q){
        if($q === false)
        {
            $query = "SELECT name, username, gravatar_hash, theposts, Comments.created_at FROM Comments JOIN Users ";
            $query .= "ON user_id = Users.id ORDER BY Comments.created_at DESC LIMIT 10;";
        }
        else{
            $query = "SELECT name, username, gravatar_hash, theposts, Comments.created_at FROM Comments JOIN Users ";
            $query .= "ON user_id = Users.id WHERE theposts LIKE \"%" . $q ."%\" ORDER BY Comments.created_at DESC LIMIT 10;";   
        }
        $res = $this->db->query($query);
        $comments = array();
        while($row = $res->fetch_object())
        {
            array_push($comments, $row);
        }
        return $comments;
    }
    
  
  //Anropar alla profiler som finns i db på tabelen User. Funktionen anropar alla information från tabelen Users (förutom pass) och skriver ut den i en $row  
    public function getPublicProfiles($user, $q){
        if($q === false)
        {
            $query = "SELECT id, name, username, gravatar_hash FROM Users WHERE id != " . $user->id;
            $query .= " ORDER BY created_at DESC LIMIT 10";
        }
        else{
            $query = "SELECT id, name, username, gravatar_hash FROM Users WHERE id != " . $user->id;
            $query .= " AND (name LIKE \"%" . $q . "%\" OR username LIKE \"%" . $q . "%\") ORDER BY created_at DESC LIMIT 10";
        }
        $userRes = $this->db->query($query);
        if($userRes->num_rows > 0){
	        $userArr = array();
	        $query = "";
	        while($row = $userRes->fetch_assoc()){
	            $i = $row['id'];
	            $query .= "SELECT " . $i . " AS id, followers, IF(theposts IS NULL, 'This user has no posts yet :) ', theposts) ";
	            $query .= "AS theposts, followed FROM (SELECT COUNT(*) as followers FROM Follows WHERE followee_id = " . $i . ") ";
	            $query .= "AS F LEFT JOIN (SELECT user_id, theposts FROM Comments WHERE user_id = " . $i;
	            $query .= " ORDER BY created_at DESC LIMIT 1) AS R ON R.user_id = " . $i . " JOIN (SELECT COUNT(*) ";
	            $query .= "AS followed FROM Follows WHERE followee_id = " . $i . " AND user_id = " . $user->id . ") AS F2 LIMIT 1;";
	            $userArr[$i] = $row;
	        }
	        $this->db->multi_query($query);
	        $profiles = array();
	        do{
	            $row = $this->db->store_result()->fetch_object();
	            $i = $row->id;
	            $userArr[$i]['followers'] = $row->followers;
	            $userArr[$i]['followed'] = $row->followed;
	            $userArr[$i]['theposts'] = $row->theposts;
	            array_push($profiles, (object)$userArr[$i]);
	        }while($this->db->next_result());
        return $profiles;
        }
        else
        {
	        return null;
        }
    }
    
  //funktion follow, för att "follow" en user
    public function follow($user, $fId){
        $this->insert("Follows", array("user_id" => $user->id, "followee_id" => $fId));
    }
    
  
  //unfollow, för att "unfollow" en user.
    public function unfollow($user, $fId){
        $this->delete("Follows", array("user_id" => $user->id, "followee_id" => $fId));
    }
  
  public function answer($user, $fId) {
  //Här kommer att vara funktionen för att svara på "theposts" comments. helt enkel svara på kommentarerna.  (still under contruction)
    }
    
  //funktionen uploadPic försöker lägga upp bilder i tabelen upload eller pictures (har inte besämt hur ska det heta ännu.

  //public function uploadPic($user, $pictures) {
    //$this->insert("tbl_uploads", array("id" => $user->id, "file" => $pictures));
       
  //}
  
  
  public function uploadPic($user, $pictures) {
 
  }
  
  
}