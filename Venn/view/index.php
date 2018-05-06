<?php 
session_name("cuser");
session_start();
include './feedback.php';
$user = new User();
$fb = new Feedback();
if(!$_SESSION['login']){
    header("location:./login.php");
}
if(filter_input(INPUT_POST, "add")!==null&&!empty(filter_input(INPUT_POST, "title"))){    
    $fb->add_question(filter_input(INPUT_POST, "title"),  filter_input(INPUT_POST, "question"));
}

?>
<!DOCTYPE html>

<html>
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="Cache-Control" content="no-cache"/>
        <meta name="viewport" content="user-scalable=no,width=device-width,initial-scale=0"/>
        <title>Feedback</title>
        <link rel="stylesheet" href="foundation.min.css"/>
        <style>
            body{
                height:100%;
                overflow-x: hidden;
                cursor: default;
                transition: all 4s ease-in;
                user-select:none;-webkit-user-select: none;
            }
            header{
                background: rgba(220,200,205,0.5);width: 100%;margin: 0;
            }
            header>*{margin: 0;}
            .linked{
                position: absolute;right: 0em;top: 0;margin: 0;
                font-size: 80%;
                display: inline-block;
                max-width: 15%;
                background: #900 !important;
            }
            .linked:hover{
                background: #8a8a8a !important;
            }
            #add-question{
                position: fixed;
                width:100%;
                /*height:*/ 
                bottom: 0;
                right: 100%;
                padding: 1em;
                background: #444;
            }
            #add-question>*{
                display: inline-block;
            }
            main{
                min-height: 50%;margin:0;
            }
            .boxed{margin: 0;}
            .boxed:nth-child(odd){
                background: #147ccf;color:#efefef;
                padding: 2em 1em;
            }
            .boxed:nth-child(even){
                background: #3adb76;padding: 2em 1em;color: #f1f1f1;
            }
            .boxed:hover{
                background: #448;
            }
            .champ{
                position: fixed;
                bottom: 4%;
                width: 100%;
                margin: auto;
            }
        </style>
    </head>
    <body>
        <header class="text-center">
            <h2>ADD A FEEDBACK QUESTION!</h2>
            <a href="./feed.php?all" class="button alert linked">View Feedback History</a>
        <div class="button-group champ">
            <?php if(!$_SESSION['login']): ?>
                <div class=" button" style="border-radius: 2em;" onclick="login()">
                    LOGIN
                </div>
            <?php else:?>
            <div class=" button" style="border-radius: 2em;" onclick="logOut()">
                    LOGOUT
                </div>
            <?php endif ?>
                <div class="alert button" style="border-radius: 2em;" onclick="provide()">
                    ASK FOR FEEDBACK
                </div>
            </div>
        </header>
        <main class="row expanded">
            <?php
            $quest = $fb->list_questions();
 if($quest){
     foreach ($quest as $b){
    echo "<a class='small-6 medium-4 column boxed' href='./feed.php?id=".$b['id']."'><div class='small-12 column text-center'><B>TITLE: ".$b["title"];
    echo "</b></div><div class='small-12 column text-center small' style='font-size:70%'>".gmdate('r',$b["asked_time"])."</div></a>";    
 }
 }
?>
            
        </main>
       
         <form method="post" action="" id="add-question" class="row text-center expanded">
            <span class="small-12 column"> 
            <input type="text" name="title" placeholder="Feedback Title"/>
            </span>
            <span class="small-12 column">
            <input type="text" name="question" placeholder="what would you like to ask your clients today?" />
            </span>
            <span class="small-2 small-pull-5 column">
                <input type="submit" class="button" name="add" value="ASK!"/>
                <input type="reset" class="secondary button" value="CANCEL" onclick="unprovide()"/>
            </span>
        </form>
         

    </body>
    <script>
        function login(){
            location.href="/login.php";
        }
        function logOut(){
            location.href="/login.php?logout";
        }
        function provide(){
            quest=document.getElementById("add-question");
            quest.style.right=0;
        }
        function unprovide(){
            quest=document.getElementById("add-question");
            quest.style.right="100%";
        }
    </script>
</html>
