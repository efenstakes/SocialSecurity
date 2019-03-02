<?php
   
   /** 
     keep properties of a social person; a user with social handles like instagram, fb, twitter and the like and comments etc.
     a subclass of User class
   **/

  class SocialEntity{
    private $id = "", $name = "", $password = "", $description = "", $type;
    private $pic, $profile_pic, $contacts, $comments;
    private $location;
    private $social_preferences = array('likes' => 0, 'dislikes' => 0, 'views' => 0);
    private $social_handles = array('fb' => null, 'twitter' => null, 'ig' => null);
    private $charges = array('min'=> 0, 'max'=> 0);
    private $photos = array();
      
    private $operates_in, $is_enabled = false;
    private $bookings = array();


    // set the object properties to their defaults
    public function __construct($name = "", $id = "", $password = ""){
      	 $this->name = $name;
      	 $this->id = $id;
         $this->password = $password;
    }

    /** 
        @@Mutators
    **/

    // update the object's name property
    public function setName($name){
      	$this->name = $name;
      	return $this;
    }

    // update the object's id property
    public function setID($id){
      	$this->id = $id;
      	return $this;
    }

    // update the object's password property
    public function setPassword($password){
        $this->password = $password;
        return $this;
    }

    // toggle the is_enabled property
    public function toggleIsEnabled(){
        $this->is_enabled = (! $this->is_enabled);
        return $this;
    }
  
    // update the object's profile pic property
    public function setProfilePic($pic){
      	$this->profile_pic = $pic;
      	return $this;
    }

    // update the object's pic property
    public function setPic($pic){
      	$this->pic = $pic;
      	return $this;
    }

    // update the object's type property
    public function setType($type){
      	$this->type = $type;
      	return $this;
    }

    // update the object's location property
    public function setLocation($loc){
      	$this->location = $loc;
      	return $this;
    }

    // @@set the contacts property of this object
    public function setDescription($description){
      	$this->description = $description;
      	return $this;
    }

    // @@set the contacts property of this object
    public function setContacts($conts){
      	$this->contacts = $conts;
      	return $this;
    }

    // @@set the social handles property of this object
    public function setSocialHandles($handles){
      	$this->social_handles = $handles;
      	return $this;
    } 

    // @@set the social_preferences property of this object
    public function setSocialPreferences($prefs){
        $this->social_preferences = $prefs;
        return $this;
    }       

    // @@set the comments property of this object
    public function setComments($comments){
        $this->comments = $comments;
        return $this;
    }

    // set the bookings
    public function setBookings($bookings){
    	$this->bookings = $bookings;
        return $this;
    }

    // set the operates in property
    public function setOperatesIn($ops_in){
    	$this->operates_in = $ops_in;
        return $this;
    }

    // set the charges property
    public function setCharges($charges){
    	$this->charges = $charges;
        return $this;
    }

    // set the photos property
    public function setPhotos($photos){
      $this->photos = $photos;
      return $this;
    }

    /** 
      @accessors
    **/

    // get the object's name property
    public function getName(){
   	  return $this->name;
    }

    // get the object's id property
    public function getID(){
   	  return $this->id;
    }

    // get the object's password property
    public function getPassword(){
          return $this->password;
    }

    // get the is_enabled property
    public function isEnabled(){
      return $this->is_enabled;
    }

    // get the object's profile pic property
    public function getProfilePic(){
   	  return $this->profile_pic;
    }

    // get the object's pic property
    public function getPic(){
  	  return $this->pic;
    }

    // get the object's type property
    public function getType(){
     	 return $this->type;
    }

    // get the object's description property
    public function getDescription(){
      	 return $this->description;
    }

    // get the object's pic property
    // @return type array('city'=> city, 'country'=> country)
    public function getLocation(){
      	  return $this->location;
    }

    // @return type array('phone'=> phone, 'email'=> email)
    public function getContacts(){
    	return $this->contacts;
    }

    // @return type array('fb'=> fb, 'twitter'=> twitter, 'ig'=> ig)
    public function getSocialHandles(){
    	return $this->social_handles;
    }
        
    // @return type array('likes'=>likes, 'dislikes'=>dislikes, 'views'=>views)
    public function getSocialPreferences(){
      return $this->social_preferences;
    }
        
    // @return type array()
    public function getComments(){
       return $this->comments;
    }


    // get the bookings 
    public function getBookings(){
    	return $this->bookings;
    }

    // get the operates in property
    public function getOperatesIn(){
    	return $this->operates_in;
    }
    
    // get the charges in property
    public function getCharges(){
    	return $this->charges;
    }

    // get the photos property
    public function getPhotos(){
      return $this->photos;
    }





   }// end of class



?>