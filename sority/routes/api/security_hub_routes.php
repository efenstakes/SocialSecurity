<?php
  /**
    keep the security hub routing code .ex
    /security-hub, /security-hub/[..] 
  **/
  
  // get the details that the user entered in the form
  // start saving security hub data to the db and return an array
  // array( 'saved'=> boolean(), 'errors'=> array() )
  $app->post("/api/security-hub/save[/]", function($request, $response, $args){
    $return = array('saved'=> false, 'id'=> NULL, 'errors'=> array());

    $name = trim($request->getParam('name'));
    $lat = trim($request->getParam('lat'));
    $lng = trim($request->getParam('lng'));
    $type = trim($request->getParam('type'));

    $new_hub = new SecurityHub();
    $new_hub->setName($name)
            ->SetLocation(array( 'lat'=> $lat, 'lng'=> $lng ))
            ->setType($type);

    $return = $new_hub->save();

    return json_encode($return);
    
  });


  // get all security hubs  
  $app->get("/api/security-hub/all[/]", function($request, $response, $args){
    $all = array();
    $all = (new SecurityHub())->getAll();
    return json_encode($all);
  });

  // get all security hubs  
  $app->get("/api/security-hub/all/{type}[/]", function($request, $response, $args){
    $all = array();
    $type = $args['type'];

    if( $type == 'POLICE' || $type == 'police' ){
       $all = (new SecurityHub())->getAllOfType('POLICE_STATION');  
    }else{
      $all = (new SecurityHub())->getAllOfType('HOSPITAL');
    }
    
    return json_encode($all);
  });


  // delete an security-hub given their id
  $app->post("/api/security-hub/{id}/delete[/]", function($request, $response, $args){
  	$return = array('deleted'=> true);
    
    $hub_id = $args['id'];
    $hub = (new SecurityHub())->setID($hub_id);
    $hub->delete();

    return json_encode($return);

  });



?>