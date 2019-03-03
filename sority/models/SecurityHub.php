<?php
  
  // include database connection script
  include_once("DbManager.php");
  
  /*
    contain security hub related functions.
    a hub is either a hospital or police station
  */
  class SecurityHub {
    private $id, $name = "", $type = NULL; 
    private $location = array( 'city'=> NULL, 'lat'=> NULL, 'lng'=> NULL );

    private $db_handle = NULL;


    // set the default values for the object variables
    public function __construct(){
        $db_manager = new DbManager();
        $this->db_handle = $db_manager->getHandle();
    }  

    // 
    public function __toString(){
       return "id :". $this->getID() ." , name :". $this->getName();
    } 

    
    /**
       mutator methods
    */

    public function setID($id){
      $this->id = $id;
      return $this;
    }

    public function setName($nam){
      $this->name = $nam;
      return $this;
    }

    public function setType($ty){
      $this->type = $ty;
      return $this;
    }

    public function setLocation($loc){
      $this->location = $loc;
      return $this;
    }

    
    /**
      accessor methods
    */

    public function getID(){
      return $this->id;
    }

    public function getName(){
      return $this->name;
    }

    public function getType(){
      return $this->type;
    }

    public function getLocation(){
      return $this->location;
    }



    // save a hub instance
    public function save(){
      $save_result = array('saved'=> false, 'id'=> NULL);

      try{
        $this->db_handle->beginTransaction();

        $query = "insert into security_hubs " 
                  ." ( name, city, lat, lng, hub_type ) "
                  ." values( ?, ?, ?, ?, ? )";
 
        $query_data = array(
                        $this->getName(),
                        $this->getLocation()['city'],
                        $this->getLocation()['lat'],
                        $this->getLocation()['lng'],
                        $this->getType()
                      );

        $stmt = $this->db_handle->prepare($query);
        $stmt->execute($query_data);
        
        $id = $this->db_handle->lastInsertId();

        if($stmt->rowCount() > 0){
           $save_result['id'] = $id;
           $save_result['saved'] = true;
        }

        $this->db_handle->commit();
      }catch(PDOException $e){
        echo($e->getMessage());
        $this->db_handle->rollBack();
      }

       return $save_result;
    }// end of public function save(){ .. {}
      

    // check if a patient exists
    public function findID(){
      $name = NULL;
      $query = "select * from security_hubs where name = ?";

      $stmt = $this->db_handle->prepare($query);
      $stmt->execute(array( $this->getName() ));

      $details = $stmt->fetchAll(PDO::FETCH_ASSOC);

      if( $stmt->rowCount() > 0 ){
        $name = $details[0]['id'];
      }

      return $name;
    }// public function exists(){ .. }


    // check if a hub name is used
    public function nameUsed(){
      $exists = false;
      $query = "select * from security_hubs where name = ?";

      $stmt = $this->db_handle->prepare($query);
      $stmt->execute(array( $this->getName() ));

      if( $stmt->rowCount() > 0 ){
         $exists = true;
      }

      return $exists;
    }// public function exists(){ .. }



    // check if a hub exists
    public function exists(){
      $exists = false;
      $query = "select * from security_hubs where id = ?";

      $stmt = $this->db_handle->prepare($query);
      $stmt->execute(array( $this->getID() ));

      if( $stmt->rowCount() > 0 ){
         $exists = true;
      }

      return $exists;
    }// public function exists(){ .. }

    // delete a hub given their id 
    public function delete(){
       $query = "delete from security_hubs where id = ?";
       $stmt = $this->db_handle->prepare($query);
       $stmt->execute(array( $this->getID() ));
    }// end of public function delete(){ .. }




    public function setProperties(){
      $hub = new SecurityHub();
      try{

        //get primary photo details like name etc
        $primary_query = "select * from security_hubs where id = ?";
        $primary_stmt = $this->db_handle->prepare($primary_query);
        $primary_stmt->execute(array( $this->getID() ));
        $primary_results = $primary_stmt->fetch(PDO::FETCH_ASSOC);

        //get primary photo details like name etc
        /*
        $titles_query = "select specialty from specialty where STAFFID = ?";
        $titles_stmt = $this->db_handle->prepare($titles_query);
        $titles_stmt->execute(array($this->getID()));
        $titles_results = $titles_stmt->fetch(PDO::FETCH_ASSOC);
        */

        // combined properties
        $combined_properties = array( 'security_hub'=> $primary_results );

        $hub = SecurityHub::make($combined_properties);

        
        # $this->db_handle->commit();
      }catch(PDOException $e){
        echo($e->getMessage());
      }

      return $hub;
    }

    // make the details of a security hub
    // database data is passed in an array then this method returns a security hub instance
    public static function make($hub_properties){
      $this_hub = new SecurityHub();

      // unpack the properties from the hub_properties array
      $hub = $hub_properties['security_hub'];

      $this_hub->setID($hub['id'])
                 ->setName($hub['name'])
                 ->setLocation(array( 
                      'city' => $hub['city'], 'lat' => $hub['lat'], 'lng' => $hub['lng']
                   ))
                 ->setType($hub['hub_type']);

      return $this_hub;
    } // end of public static function make($hub_properties){ .. }

    // return an array with the security hub details
    public function getProperties(){
      $properties = array();
      
      $properties['id'] = $this->getID();
      $properties['name'] = $this->getName();
      $properties['type'] = $this->getType();
      $properties['location'] = $this->getLocation();

      return $properties;
    }// end of public function getProperties(){ .. }


    // get all security hubs
    public function getAll(){
      $all = array();
      $query = "select * from security_hubs";

      $stmt = $this->db_handle->prepare($query);
      $stmt->execute();
      $hubs = $stmt->fetchAll(PDO::FETCH_ASSOC);

      $all = array_map(function($hub){
                return SecurityHub::make(array( 'security_hub'=> $hub ))->getProperties();
      }, $hubs);

      return $all;
    }


    // get all security hubs
    public function getAllOfType($type){
      $all = array();
      $query = "select * from security_hubs where hub_type like ?";

      $stmt = $this->db_handle->prepare($query);
      $stmt->execute(array( $type ));
      $hubs = $stmt->fetchAll(PDO::FETCH_ASSOC);

      $all = array_map(function($hub){
                return SecurityHub::make(array( 'security_hub'=> $hub ))->getProperties();
      }, $hubs);

      return $all;
    }



    // free resources that this object is using
    public function __destruct(){ }


  }// end of class


?>