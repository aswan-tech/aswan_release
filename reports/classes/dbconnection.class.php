<?php 
  class DbConnection {
	var $cn;
    public $host, $username, $password;
    public function __construct($hostname, $username, $password){
        $this->host = $hostname;
        $this->username = $username;
        $this->password = $password;
    }
    public function connectdb(){
        mysql_connect($this->host, $this->username, $this->password);
      
       
    }
    public function select($database){
        mysql_select_db($database);
    }
}
$obj = new DbConnection('localhost', 'root', 'root');
$obj->connectdb();
$obj->select('a2xantianxiety.com');
?>
