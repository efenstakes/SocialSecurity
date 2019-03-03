<?php
  
  include_once("DbManager.php");
  include_once("SocialEntity.php");
  
  /*
    contain data that has to do with a user.. 
    acts as the superclass for user-related classes
  */
  class User extends SocialEntity{
    private $emergency_contacts = array();

    private $db_handle = NULL;


    // set the default values for the object variables
    public function __construct($name = "", $id = "", $password = ""){
      	// super::__construct($name, $id, $password);
   	    
        $db_manager = new DbManager();
        $this->db_handle = $db_manager->getHandle();
    }  

    // 
    public function __toString(){
       return "id :". $this->getID() ." , name :". $this->getName();
    } 
      
    /**
        setter methods
    **/

    // update the object's about me property
    public function setEmergencyContacts($ems){
      	$this->emergency_contacts = $ems;
      	return $this;
    }

     

    /** 
        the getter methods
    **/


    // get the object's about me property
    public function getEmergencyContacts(){
      return $this->emergency_contacts;
    }






    // save a photo instance
    public function save(){
      $save_result = array('saved'=> false, 'id'=> NULL);

      try{
        $this->db_handle->beginTransaction();

        $query = "insert into users " 
                  ." ( name, password, phone, user_type ) "
                  ." values(?, ?, ?, ?)";
 
        $query_data = array(
                        $this->getName(),
                        $this->getPassword(),
                        $this->getContacts()['phone'],
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
      $query = "select * from users where name = ?";

      $stmt = $this->db_handle->prepare($query);
      $stmt->execute(array( $this->getName() ));

      $details = $stmt->fetchAll(PDO::FETCH_ASSOC);

      if( $stmt->rowCount() > 0 ){
        $name = $details[0]['id'];
      }

      return $name;
    }// public function exists(){ .. }


    // check if a patient exists
    public function nameUsed(){
      $exists = false;
      $query = "select * from users where name = ?";

      $stmt = $this->db_handle->prepare($query);
      $stmt->execute(array( $this->getName() ));

      if( $stmt->rowCount() > 0 ){
         $exists = true;
      }

      return $exists;
    }// public function exists(){ .. }

    // check if a patient exists
    public function accountExists(){
      $exists = false;
      $query = "select * from users where password = ? AND name = ?";

      $stmt = $this->db_handle->prepare($query);
      $stmt->execute(array( $this->getPassword(), $this->getName() ));

      if( $stmt->rowCount() > 0 ){
         $exists = true;
      }

      return $exists;
    }// public function exists(){ .. }

    // check if a patient exists
    public function exists(){
      $exists = false;
      $query = "select * from users where id = ?";

      $stmt = $this->db_handle->prepare($query);
      $stmt->execute(array( $this->getID() ));

      if( $stmt->rowCount() > 0 ){
         $exists = true;
      }

      return $exists;
    }// public function exists(){ .. }

    // delete a patient given their id 
    public function delete(){
       $query = "delete from users where id = ?";
       $stmt = $this->db_handle->prepare($query);
       $stmt->execute(array( $this->getID() ));
    }// end of public function delete(){ .. }




    public function setProperties(){
      $user = new User();
      try{

        //get primary photo details like name etc
        $primary_query = "select * from users where id = ?";
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
        $combined_properties = array( 'user'=> $primary_results );

        $user = User::make($combined_properties);

        
        # $this->db_handle->commit();
      }catch(PDOException $e){
        echo($e->getMessage());
      }

      return $user;
    }

    // make the details of a photo
    // database data is passed in an array then this method returns a photo instance
    public static function make($patient_properties){
      $this_user = new User();

      // unpack the properties from the photo_properties array
      $user = $patient_properties['user'];

      $this_user->setID($user['id'])
                 ->setPassword($user['password'])
                 ->setName($user['name'])
                 ->setContacts(array( 'phone' => $user['phone'] ))
                 ->setType($user['user_type']);

      return $this_user;
    } // end of public static function make($photo_properties){ .. }

    // return an array with the photo details
    public function getProperties(){
      $properties = array();
      
      $properties['id'] = $this->getID();
      $properties['name'] = $this->getName();
      $properties['password'] = $this->getPassword();
      $properties['contacts'] = $this->getContacts();
      $properties['user_type'] = $this->getType();

      return $properties;
    }// end of public function getProperties(){ .. }



    public function getAll(){
      $all = array();
      $query = "select * from users";

      $stmt = $this->db_handle->prepare($query);
      $stmt->execute();
      $all = $stmt->fetchAll(PDO::FETCH_ASSOC);

      return $all;
    }




    // free resources that this object is using
    public function __destruct(){ }


  }// end of class


?>