<?php 
   /*
     @author efen
     @first-writing 07/11/2017 d/m/y
     @doc the application entry page
   */

    // enable sessions
    session_start();
    
    // allow cors requests
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS'); 
    header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token');
    header("Access-Control-Allow-Headers: content-type, authorization");
    header("Access-Control-Allow-Credentials: true");

    /*header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    header("Access-Control-Allow-Headers: content-type, authorization");
    */

    // include downloaded libraries
    require_once("./vendor/autoload.php");

    // include slim classes from their namespaces
    use Slim\Http\Request;
    use Slim\Http\Response;
    use Slim\Http\UploadedFile;

    // include app classes 
    require_once("sority/models/User.php");
    require_once("sority/models/EmergencyContact.php");
    require_once("sority/models/SecurityHub.php");
    require_once("sority/models/Alert.php");
    require_once("sority/models/Utility.php");

    // setup slim
    $app = new Slim\App([
           'settings' => [ 'displayErrorDetails' => true, 'debug' => true, ]
    ]);

    $container = $app->getContainer();

    // add view to the container
    // using slim template lang
    $container['view'] = function($cont){
        $template_dir = __DIR__ . "/sority/views/";
        $cache = false;
        
        return new Slim\Views\Twig($template_dir, compact('cache')); 
    };

    
    // middleware to protect routes that need users to have sessions
    $session_protector_middleware = function ($request, $response, $next) {
      
      if( has_session() ){
        $response = $next($request, $response); // next($request, $response);
      }else{
        $response->getBody()->write(json_encode( array( 'error'=> 'NO_SESSION' ) ));
      }  

      return $response;
    };


    // start handling routes
    // include routing files
    include_once("./sority/routes/api/user_routes.php");
    include_once("./sority/routes/api/emergency_contact_routes.php");
    include_once("./sority/routes/api/security_hub_routes.php");
    include_once("./sority/routes/api/alert_routes.php");

    
    // check if a session exists 
    function has_session(){
       return (!empty($_SESSION['app_session']) && !empty($_SESSION['app_session']['user_id']));
    }// end of has_session() 
    
    // return the session
    function get_session(){
       return $_SESSION;
    }// end of get_session()
    
    /* 
      check if a session is valid
      validity is determined by existence of a session and.. 
      a host whose data coincides with the session
    **/
    function is_session_valid(){
      $return = false;
      
      if(has_session()){
         $user_id = get_session()['app_session']['user_id'];
         $user_type = get_session()['app_session']['user_type'];
         $return = true; // return exists status for the entity 
      }
      return $return;
    }
    
    // delete a session if it exists
    function delete_session(){
        unset($_SESSION['app_session']);
    }// end of delete_session()
    

    // moves an upload file to the passed directory
    function moveUploadedFile($directory, UploadedFile $uploadedFile){
      $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
      $basename = bin2hex(random_bytes(8)); // see http://php.net/manual/en/function.random-bytes.php
      $filename = sprintf('%s.%0.8s', $basename, $extension);

      $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $filename);

      return $filename;
    }// end of get_and_return_data() 


    // run the app
    $app->run();

?>