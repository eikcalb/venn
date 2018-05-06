<?php 
session_name("cuser");
session_start();
include './feedback.php';
$user = new User();
$fb = new Feedback();

if(isset($_SESSION['login'])&&$_SESSION['login']){
    header("location:/index.php");
}
if(filter_input(INPUT_POST, "login")!==null){    
    if($user->login_user(filter_input(INPUT_POST, "token"), filter_input(INPUT_POST, "pass"))){        
        header("location:/index.php");
    }        
}
if(filter_input(INPUT_GET, "logout")!==null){    
    session_destroy();       
}
if(filter_input(INPUT_POST, "reg")!==null){    
    if($user->add_user(filter_input(INPUT_POST, "mail"), filter_input(INPUT_POST, "first"),filter_input(INPUT_POST, "last"),filter_input(INPUT_POST, "pass"),filter_input(INPUT_POST, "tel"))){
        header("location:/index.php");
    }
}

?>
<!DOCTYPE html>

<html>
    <head>
        <meta charset="UTF-8">
        <title>Login</title>
        <meta http-equiv="Cache-Control" content="no-cache"/>
        <meta name="viewport" content="width=device-width"/>
        <link rel="stylesheet" href="foundation.min.css"/>
        <style>
            body{
                height:100%;
                overflow-x: hidden;
                cursor: default;
                background: #444;
                user-select:none;-webkit-user-select: none;
            }
            header>*{margin: 0;}
            #login{
                /*position: absolute;*/
                top: 0;
                width:100%;
                height: 100%;
                padding: 25%;
/*                background: #444;*/
            }
            #reg{
                height: 100%;
             padding: 15%;

            }
            #add-question>*{
                display: inline-block;
            }
        </style>

    </head>
    <body>
        <?php 
        if(!isset($_GET['reg'])):?>
        <form method="post" action="" id="login" class="row text-center expanded">
            <span class="small-12 column"> 
                <input type="text" name="token" placeholder="Email or Phone Number" required=""/>
            </span>
            <span class="small-12 column">
                <input type="password" name="pass" placeholder="Password" required=""/>
            </span>
            <span class="small-2 small-pull-5 column">
                <input type="submit" class="button" name="login" value="login"/>
                <input type="reset" class="secondary button" value="Register" onclick="unprovide()"/>

            </span>
        </form>
        <?php else :?>
        <form method="post" action="" id="reg" class="row text-center expanded">
            <span class="small-12 column"> 
                <input type="email" name="mail" placeholder="Email " required=""/>
            </span>
            <span class="small-12 column"> 
                <input type="tel" name="tel" placeholder="Phone Number" required=""/>
            </span>
            <span class="small-12 column"> 
                <input type="text" name="first" placeholder="first name" required=""/>
            </span>
            <span class="small-12 column"> 
                <input type="text" name="last" placeholder="last name" required=""/>
            </span>
            <span class="small-12 column">
                <input type="password" name="pass" placeholder="Password" required=""/>
            </span>
            <span class="small-2 small-pull-5 column">
                <input type="submit" class="button" name="reg" value="Register"/>
                <input type="button" class="secondary button" value="Login" onclick="reprovide()"/>
            </span>
        </form>
        <?php endif; ?>
        <script defer="">
        
        function unprovide(){
           location.href="?reg";
        }
        function reprovide(){
           location.href="?";
        }
    </script>
    </body>
</html>
