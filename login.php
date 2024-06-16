<?php

$is_invalid = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    
    $mysqli = require __DIR__ . "/database.php";
    
    $sql = sprintf("SELECT * FROM user
                    WHERE email = '%s'",
                   $mysqli->real_escape_string($_POST["email"]));
    
    $result = $mysqli->query($sql);
    
    $user = $result->fetch_assoc();
    
    if ($user) {
        
        if (password_verify($_POST["password"], $user["password_hash"])) {
            
            session_start();
            
            session_regenerate_id();
            
            $_SESSION["user_id"] = $user["id"];
            
            header("Location: index.php");
            exit;
        }
    }
    
    $is_invalid = true;
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
</head>
<style>
            body {
                display: flex;
                flex-direction: column; 
                justify-content: center; 
                align-items:  flex-start;; 
                min-height: 50vh;
                padding-top: 20vh;
                background-image: url('image/bg.jpg');
                background-size: cover;
                background-repeat: no-repeat;
                background-position: center;
                max-height: 1000px;
                 background-attachment: fixed;
                margin: 0; 
            }
            .glow-box {
                width: 300px;
                max-width: 800px; 
                padding: 20px; 
                background-color: transparent ;
                border-radius: 8px; 
                box-shadow: 0 2px 10px rgb(0, 0, 0); 
                margin-left: 450px;
                margin-right: auto;
                margin-top: 100px;
                margin: 0 auto;
                border: 2px solid transparent; /* Add a transparent border as a base */
                box-shadow:
                            0 0 40px rgb(0, 0, 0);
                            
                animation: glowEffect 4s linear infinite; /* Animation */
            }
           

            /* Define the glow effect using keyframes */
            @keyframes glowEffect {
                
                100% {
                    box-shadow: 0 0 50px rgb(34, 131, 249),
                    0 0 40px #ff58cd,
                    0 0 40px rgb(218, 0, 237);
                }
            }

            label {
                color: rgb(252, 252, 252); 
            }

            h1 {
                margin-left: 100px;
                margin-top: 10px;
                margin-bottom: 10px;
                color: rgb(4, 201, 255);
               
            }

            button {
                background: rgb(218, 0, 237);
                background-color: rgb(4, 201, 255);
                margin-top: 20px;
                margin-left: 90px;
            }

            input {
                margin-top: 20px;
                width: 280px;
                border: 2px solid transparent;
                
            }
            p{
                margin-left: 30px;
                color: azure;
            }
             @media (max-width: 600px) {
            .glow-box {
                max-width: 90%; /* Set form width to 90% of viewport width on small screens */
            }
        }

</style>
<body>
    
    
    
    <?php if ($is_invalid): ?>
        <em>Invalid login</em>
    <?php endif; ?>
    
    <form method="post" class="glow-box" novalidate>
        <h1>Login</h1>
        <input type="email" name="email" id="email" placeholder="Email"
               value="<?= htmlspecialchars($_POST["email"] ?? "") ?>">
        
      
        <input type="password" name="password" id="password" placeholder="Password">
        
        <button>Log in</button>
        <p>Dont have an account yet?<a href = "signup.html"> Signup</a></p>
    </form>
    
</body>
</html>








