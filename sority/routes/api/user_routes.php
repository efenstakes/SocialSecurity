<?php
  /**
    keep the user routing code .ex
    /users, /user[/] , /user/[..] 
  **/
  
  // get the details that the user entered in the form
  // start saving user data to the db and return an array
  // array( 'added'=> boolean(), 'user'=> (new User()) )
  $app->post("/api/user/save[/]", function($request, $response, $args){
    $return = array('saved'=> false, 'id'=> NULL, 'errors'=> array());

    $name = trim($request->getParam('name'));
    $pwd = trim($request->getParam('password'));
    $confirmation_password = trim($request->getParam('confirmation_password'));

    $phone = trim($request->getParam('phone'));
    $user_type = 'REGULAR';
   
    $new_user = new User();
    $new_user->setName($name)
             ->setPassword($pwd)
             ->setContacts(array(
                'phone' => $phone
             ))
             ->setType($user_type);

   

    if( $pwd === $confirmation_password && !$new_user->nameUsed() ){
        $return = $new_user->save();
    
        //return json_encode(array('got_here'=> true));
               
        // set session
        $_SESSION['app_session']['user_id'] = $return['id'];
        $_SESSION['app_session']['is_new'] = true;
        $_SESSION['app_session']['user_type'] = $user_type;

    }

    return json_encode($return);
    
  });


  // check if a user has a session 
  $app->post("/api/user/has-session[/]", function($request, $response, $args){
    $return = array('has_session'=> false);

    $return['has_session'] = isset($_SESSION['app_session']);
    $return['session'] = $_SESSION;

    return json_encode($return);
  });

  // check if a user exists by id
  $app->post("/api/user/exists[/]", function($request, $response, $args){
    $return = array('exists'=> false);
    $id = $request->getParam('id');

    $exists = (new User())->setID($id)->exists();

    return json_encode($return);
  });

  // check if a user exists by id
  $app->post("/api/user/all[/]", function($request, $response, $args){
    $all = array();
    $all = (new User())->getAll();
    return json_encode($all);
  });

  // check if a user name is used
  $app->post("/api/user/name-used[/]", function($request, $response, $args){
    $name = $request->getParam('name');

    $name_used = (new User())->setName($name)->nameUsed();

    return json_encode(array('name_used'=> $name_used));
  });

  // check if a user account exists
  $app->post("/api/user/account-exists[/]", function($request, $response, $args){
    $return = array('exists'=> false);

    $name = $request->getParam('name');
    $password = $request->getParam('password');

    $return['exists'] = (new User())->setName($name)
                                    ->setPassword($password)
                                    ->accountExists();
    
    if($return['exists']) {
      $id = (new User())->setName($name)->findID();
      $_SESSION['app_session']['user_id'] = $id;      
    }

    return json_encode($return);
  });


  // get details of a user given their id
  $app->post("/api/user/session-data[/]", function($request, $response, $args){
    $id = get_session()['app_session']['user_id'];

    $details = (new User())->setID($id)->setProperties()->getProperties();

    return json_encode(array('user'=> $details));
  });

  // get details of a user given their id
  $app->map(['GET', 'POST'], "/api/user/details/{id}[/]", function($request, $response, $args){
    $id = $args['id'];

    $details = (new User())->setID($id)->setProperties()->getProperties();

    return json_encode(array('user'=> $details));
  });


  // delete a user given their id
  $app->post("/api/user/delete/{id}[/]", function($request, $response, $args){
  	$return = array('deleted'=> false);
    $user = new User();
    $user_id = $args['id'];
    
    if(has_session()){
        $user_session_id = get_session()['app_session']['user_id'];

        $user_details = $user->setID($user_id)->setProperties()->getProperties(); 
        $user_session_details = $user->setID($user_session_id)->setProperties()->getProperties(); 

        if($user_details['id'] == $user_session_details['id']){
            $user->setID($user_id)->delete();
            $return['deleted'] = true;
        }else if( $user_session_details['type'] == 'ADMIN' ){
            $user->setID($user_id)->delete();
            $return['deleted'] = true;
        }else{
            $return['deleted'] = false;
        }
  
    }

    return json_encode($return);

  });


  // this authenticates user before they are taken to their account page
  $app->post("/api/user/signin[/]", function($request, $response, $args){
    $return = array('authenticated'=> false, 'user'=> array());

    $name = trim($request->getParam("name"));
    $password = trim($request->getParam("password"));
        
    $user = new User();
    $exists = $user->setName($name)->setPassword($password)->accountExists();

    if($exists){ 
        $_SESSION['app_session']['user_id'] = $user->findID();
        $_SESSION['app_session']['user_type'] ='user';
        $return['authenticated'] = true;
        $return['session'] = $_SESSION;
        $return['user'] = $user->setID($user->findID())
                                     ->setProperties()
                                     ->getProperties();
    }
           
    return json_encode($return);      
  });
  
    
  // log a user out
  $app->map(['POST', 'GET'], "/api/user/logout[/]", function($request, $response, $args){
    $return = array('loged_out'=> false);

    delete_session();
    $return['loged_out'] = true;

    return json_encode($return);
  });




?>