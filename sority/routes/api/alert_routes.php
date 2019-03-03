<?php
  /**
    keep alerts routing code .ex
    /alert, /alert/[..] 
  **/
    
  // get the details that the user entered in the form about the emergency
  // start saving security hub data to the db and return an array
  // array( 'saved'=> boolean(), 'errors'=> array() )
  // @todo should ensure there is a session
  $app->post("/api/alert/save[/]", function($request, $response, $args){
    $return = array('saved'=> false, 'id'=> NULL, 'errors'=> array());

    // should get this from the session
    $from_user = trim($request->getParam('from_user')); //get_session()['app_session']['user_id']; 
    
    $lat = trim($request->getParam('lat'));
    $lng = trim($request->getParam('lng'));
    $text = trim($request->getParam('text'));

    $new_alert = new Alert();
    $new_alert->setUser(array( 'id'=> $from_user ))
              ->SetLocation(array( 'lat'=> $lat, 'lng'=> $lng ))
              ->setText($text);

    $return = $new_alert->save();

    return json_encode($return);
    
  });


  // get all alerts   
  $app->get("/api/alerts/all[/]", function($request, $response, $args){
    $all = array();
    $all = (new Alert())->getAll();
    return json_encode($all);
  });


  // delete an alert given their id
  // @todo should check for session first and check the user deleting is the alert owner
  $app->post("/api/alert/{id}/delete[/]", function($request, $response, $args){
  	$return = array('deleted'=> true);
    
    $id = $args['id'];
    $alert = (new Alert())->setID($id);
    $alert->delete();

    return json_encode($return);

  }); // ->add($session_check_middleware)



?>