<?php
  
  include_once("DbManager.php");
  
  /*
    model an alert made by a user for their emergency contacts
  */
  class Alert {
    private $id, $user = array( 'id'=> NULL ), $text = "";
    private $location = array( 'lat'=> NULL, 'lng'=> NULL );

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
        setter methods
    **/

    public function setID($id){
    	$this->id = $id;
    	return $this;
    }

    public function setUser($user){
      $this->user = $user;
      return $this;
    }
     
    public function setText($text){
      $this->text = $text;
      return $this;
    }
     
    public function setLocation($loc){
      $this->location = $loc;
      return $this;
    }

    /** 
        the getter methods
    **/


    public function getID(){
      return $this->id;
    }
    public function getUser(){
      return $this->user;
    }
    public function getText(){
      return $this->text;
    }
    public function getLocation(){
      return $this->location;
    }



    // save an alert instance
    public function save(){
      $save_result = array('saved'=> false, 'id'=> NULL);

      try{
        $this->db_handle->beginTransaction();

        $query = "insert into alerts " 
                  ." ( from_user, from_lat, from_lng, alert_text ) "
                  ." values(?, ?, ?, ?)";
 
        $query_data = array(
                        $this->getUser()['id'],
                        $this->getLocation()['lat'],
                        $this->getLocation()['lng'],
                        $this->getText()
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


    // delete an alert given their id 
    public function alert(){
       $query = "delete from alerts where id = ?";
       $stmt = $this->db_handle->prepare($query);
       $stmt->execute(array( $this->getID() ));
    }// end of public function delete(){ .. }




    public function setProperties(){
      $alert = new Alert();
      try{

        //get primary alert details from alert table
        $primary_query = "select * from alerts where id = ?";
        $primary_stmt = $this->db_handle->prepare($primary_query);
        $primary_stmt->execute(array( $this->getID() ));
        $primary_results = $primary_stmt->fetch(PDO::FETCH_ASSOC);

        // combined properties
        $combined_properties = array( 'alert'=> $primary_results );

        $alert = Alert::make($combined_properties);

      }catch(PDOException $e){
        echo($e->getMessage());
      }

      return $alert;
    }

    // make the details of an alert
    // database data is passed in an array then this method returns an alert instance
    public static function make($alert_properties){
      $this_alert = new Alert();

      // unpack the properties from the alert_properties array
      $alert = $alert_properties['alert'];

      $this_alert->setID($alert['id'])
                 ->setUser(array( 'id'=> $alert['from_user'] ))
                 ->setLocation(array( 'lat' => $alert['lat'], 'lng' => $alert['lng'] ))
                 ->setText($alert['text']);

      return $this_alert;
    } // end of public static function make($alert_properties){ .. }

    // return an array with the alert details
    public function getProperties(){
      $properties = array();
      
      $properties['id'] = $this->getID();
      $properties['from_user'] = $this->getUser();
      $properties['location'] = $this->getLocation();
      $properties['text'] = $this->getText();

      return $properties;
    }// end of public function getProperties(){ .. }



    public function getAll(){
      $all = array();
      $query = "select * from alerts";

      $stmt = $this->db_handle->prepare($query);
      $stmt->execute();
      $db_all = $stmt->fetchAll(PDO::FETCH_ASSOC);

      $all = array_map(function($alrt){
               return Alert::make($alrt)->getProperties();
      }, $db_all);

      return $all;
    } // public function getAll(){ .. }

    public function getAllForUser($id){
      $all = array();
      $query = "select * from alerts where from_user = ?";

      $stmt = $this->db_handle->prepare($query);
      $stmt->execute(array( $id ));
      $db_all = $stmt->fetchAll(PDO::FETCH_ASSOC);

      $all = array_map(function($alrt){
               return Alert::make($alrt)->getProperties();
      }, $db_all);

      return $all;
    }// public function getAllForUser($id){ .. }




    // free resources that this object is using
    public function __destruct(){ }


  }// end of class


?>