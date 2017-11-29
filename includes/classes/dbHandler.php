<?php
class dbHandler {
    
    protected static $connection;
    
    public function dbConnect() {
        
        if(!isset(self::$connection)) {
            $config = parse_ini_file($_SERVER['DOCUMENT_ROOT'].'./eindwerk/includes/objects/config.ini');
            
            self::$connection = new mysqli($config['ip'], $config['username'], $config['password'], $config['databaseName']);
        }
        
        if(self::$connection === false) {
            return mysqli_connect_error(); 
        }
        
        return self::$connection;
    }
    
    public function query($query) {
        $connection = $this -> dbConnect();
        
        $result = $connection -> query($query);
        
        
        return $result;
    }
    
    public function select($query) {
        $rows = array();
        $result = $this -> query($query);
        if($result === false) {
            return false;
        }
        while ($row = $result -> fetch_assoc()) {
            $rows[] = $row;
        }
        return $rows;
    }
    
    public function quote($value) {
        $connection = $this -> dbConnect();
        return "'" . $connection -> real_escape_string($value) . "'";
    }
    public function lastId() {
        $id = self::$connection->insert_id;
        return $id;
    }
    
    public function checkError($query) {
        $connection = $this -> dbConnect();
        
        $result = $connection -> query($query);
        $result = self::$connection->error;
        
        
        return $result;
    }
}
?>