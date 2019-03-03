<?php
  /**
    keep the emergency routing routing code .ex
    /patients, /patient[/] , /patient/[..] 
  **/
  
  // get the details that the user entered in the form
  // start saving user emergency data to the db and return an array
  // array( 'saved'=> boolean(), 'errors'=> array() )
  $app->post("/api/emergency-contact/save[/]", function($request, $response, $args){
    $return = array('saved'=> false, 'id'=> NULL, 'errors'=> array());
    
    $user_id = get_session()['app_session']['user_id'];

    $contact_1 = trim($request->getParam('contact_1'));
    $contact_2 = trim($request->getParam('contact_2'));

    $new_contact = new EmergencyContact();
    $new_contact->setUser(array( 'id'=> $user_id ))
                ->setContacts(array( $contact_1, $contact_2 ));

    $return = $new_contact->save();

    return json_encode($return);
    
  })->add($session_protector_middleware);


  // get all user emergency contacts 
  $app->post("/api/emergency-contact/all/for-user/{id}[/]", function($request, $response, $args){
    $all = array();
    $all = (new EmergencyContact())->getAllForUser($args['id']);
    return json_encode($all);
  })->add($session_protector_middleware);

  // get all user emergency contacts 
  $app->post("/api/emergency-contact/all[/]", function($request, $response, $args){
    $all = array();
    $all = (new EmergencyContact())->getAll();
    return json_encode($all);
  })->add($session_protector_middleware);


  // @not-worked
  // delete an emergency-contact given their id
  $app->post("/api/emergency-contact/delete/{id}[/]", function($request, $response, $args){
  	$return = array('deleted'=> true);

    $contact = new EmergencyContact();
    $contact_id = $args['id'];
    $user_id = get_session()['app_session']['user_id'];
    
    $contact->setID($contact_id)->setUser(array( 'id'=> $user_id ));

    $contact->delete();

    return json_encode($return);

  })->add($session_protector_middleware);



?>