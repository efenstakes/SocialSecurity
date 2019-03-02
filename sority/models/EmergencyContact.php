<?php
  
  // include database connection script
  include_once("DbManager.php");
  
  /*
    contain emergency contact related functions.
  */
  class EmergencyContact {
    private $id, $user = array( 'id'=> NULL ), $contacts = array(); 

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

    public function setUser($user){
      $this->user = $user;
      return $this;
    }

    public function setContacts($cons){
      $this->contacts = $cons;
      return $this;
    }

    
    /**
      accessor methods
    */

    public function getID(){
      return $this->id;
    }

    public function getUser(){
      return $this->user;
    }

    public function getContacts(){
      return $this->contacts;
    }



    // save a emergency contact instance
    public function save(){
      $save_result = array('saved'=> false, 'id'=> NULL);

      try{
        $this->db_handle->beginTransaction();

        $query = "insert into emergency_contacts " 
                  ." ( user_id, emergency_contact_1, emergency_contact_2 ) "
                  ." values( ?, ?, ? )";
 
        $query_data = array(
                        $this->getUser()['id'],
                        $this->getContacts()[0],
                        $this->getContacts()[1]
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
      

    // check if an emergency contact exists
    public function exists(){
      $exists = false;
      $query = "select * from emergency_contacts where id = ?";

      $stmt = $this->db_handle->prepare($query);
      $stmt->execute(array( $this->getID() ));

      if( $stmt->rowCount() > 0 ){
         $exists = true;
      }

      return $exists;
    }// public function exists(){ .. }

    // delete an emergency contacts given their id 
    public function delete(){
       $query = "delete from emergency_contacts where id = ?";
       $stmt = $this->db_handle->prepare($query);
       $stmt->execute(array( $this->getID() ));
    }// end of public function delete(){ .. }




    public function setProperties(){
      $emergency_contact = new EmergencyContact();
      try{

        //get primary emergency contact details 
        $primary_query = "select * from emergency_contacts where id = ?";
        $primary_stmt = $this->db_handle->prepare($primary_query);
        $primary_stmt->execute(array( $this->getID() ));
        $primary_results = $primary_stmt->fetch(PDO::FETCH_ASSOC);

        // combined properties
        $combined_properties = array( 'emergency_contact'=> $primary_results );

        $emergency_contact = EmergencyContact::make($combined_properties);

        
        # $this->db_handle->commit();
      }catch(PDOException $e){
        echo($e->getMessage());
      }

      return $emergency_contact;
    }

    // make the details of an emergency contacts 
    // database data is passed in an array then this method returns a security hub instance
    public static function make($emergency_contact_properties){
      $this_contact = new EmergencyContact();

      // unpack the properties from the emergency_contact_properties array
      $contact = $emergency_contact_properties['emergency_contact'];

      $this_contact->setID($contact['id'])
                    ->setContacts(array( 
                          $contact['emergency_contact_1'], $contact['emergency_contact_2']
                       ))
                    ->setUser(array( 'id'=> $contact['user_id'] ));

      return $this_contact;
    } // end of public static function make($emergency_contact_properties){ .. }

    // return an array with the emergency contact details
    public function getProperties(){
      $properties = array();
      
      $properties['id'] = $this->getID();
      $properties['contacts'] = $this->getContacts();
      $properties['user'] = $this->getUser();

      return $properties;
    }// end of public function getProperties(){ .. }


    // get all emergency contact 
    public function getAll(){
      $all = array();
      $query = "select * from emergency_contacts";

      $stmt = $this->db_handle->prepare($query);
      $stmt->execute();
      $all = $stmt->fetchAll(PDO::FETCH_ASSOC);

      return $all;
    } // public function getAll(){ .. }

    // get all emergency contacts for a user
    public function getAllForUser($id){
      $all = array();
      $query = "select * from emergency_contacts where id = ?";

      $stmt = $this->db_handle->prepare($query);
      $stmt->execute(array( $id ));
      $all = $stmt->fetchAll(PDO::FETCH_ASSOC);

      return $all;
    } // public function getAll(){ .. }




    // free resources that this object is using
    public function __destruct(){ }


  }// end of class


?>