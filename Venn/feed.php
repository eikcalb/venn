<?php 
session_name("cuser");
session_start();
include './feedback.php';
$user = new User();
$fb = new Feedback();
if(!isset($_SESSION['login'])||!$_SESSION['login']){
    header("location:./login.php");
}
if(filter_input(INPUT_POST, "add")!==null&&!empty(filter_input(INPUT_POST, "answer"))){    
    if($fb->give_feedback($_SESSION['id'],filter_input(INPUT_POST, "idd"),filter_input(INPUT_POST, "answer"))){
        header("location:./index.php");
    }
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
                background: rgba(100,100,100,1);width: 100%;color: #fff;margin: 0;padding: 1em 0;
            }
            header>*{margin: 0;display: inline-block;}
            .linked{
                position: absolute;right: 0em; top: 0;margin: 0;
                font-size: 80%;
                display: inline-block;
                max-width: 15%;
            }
            #an{
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
            .chair{
                margin: 0;
                color:#222;
                padding: 2em 1em;
            }
            .chairly{
                background: #ddd;color:#111;padding: 1em;
            }
            .chairly:hover{
                background: #ccc;
            }
            
        </style>
    </head>
    <body>
        <?php if(!isset($_GET['all'])): ?>
        <header class="text-center">
            <h2>ADD A FEEDBACK!</h2>
            <a href="./feed.php?all" class="button alert linked">View Feedback History</a>
        </header>
        <main class="row expanded">
            <?php
            if(filter_input(INPUT_GET, "id")===null||($quest = $fb->question(filter_input(INPUT_GET, 'id')))===false){
                header("location:./index.php");
            }
           
 if($quest){
    echo "<div class='small-12 column chair'><div class='small-12 column text-center'><B>TITLE: ".$quest["title"];
    if(!empty($quest['question'])){echo "</B></div><div class='small-12 column text-center'>question: ".$quest["question"];}
    echo "</b></div><div class='small-12 column text-center small' style='font-size:70%'>".gmdate('r',$quest["asked_time"])."</div></div>";
 }
?>
            
        </main>
        <form method="post" action="" id="answer" class="row text-center expanded">
            <span class="small-12 column">
            <input type="text" name="answer" placeholder="answer" />
            </span>
            <input type="hidden" name="idd" value="<?= $_GET['id']?>"/>
            <span class="small-2 small-pull-5 column">
                <input type="submit" class="button" name="add" value="ANSWER!"/>
            </span>
        </form>
        <?php else: ?>
        <header class="text-center">
            <h4>FEEDBACK PROVIDED BY <?= strtoupper($_SESSION['first_name']." ".$_SESSION['last_name']) ?></h4>
            <a href="./index.php" class="button alert linked">Home</a>
        </header>
        <main class="row expanded">
            <?php
            $ans = $fb->list_user_answers($_SESSION['id']);
 if($ans){
     foreach($ans as $quest){
    echo "<div class='small-6 column chairly'><div class='small-12 column text-center'><B>TITLE:<br/> ".$quest["title"];
    if(!empty($quest['question'])){echo "</B></div><div class='small-12 column text-center'>question:  ".$quest["question"];}
    echo "</B></div><div class='small-12 column text-center'>your answer:  ".$quest["answer"];
    echo "</b></div><div class='small-12 column text-center' style='font-size:70%'>answered on ".gmdate('r',$quest["answered_time"])."</div></div>";
 }
 }
?>
            
        </main>
        <?php endif; ?>
    </body>
    <script>
        
       
    </script>
</html>
