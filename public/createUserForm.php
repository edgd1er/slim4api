<!DOCTYPE html>
<html>
<head>
    <title>Slim4API</title>
    <link href="favicon.ico" rel="icon"/>
    <link href="css/slim4api.css" rel="stylesheet" type="text/css"/>
    <script type="text/javascript" src="js/veriform.js"></script>
</head>
<body>
<div class="flex-container">
    <div class="container">

        <h1>Slim4API - User Create</h1>

        <form method="post" action="/users"
              onsubmit="if(verifForm([username,password,first_name,last_name,email,profile],'Error : fields marked with an asterisk are mandatory!')) return validateEmail(email,Error : email format invalid!')); return false;">

            <p>
                <label for="username">* Username:</label><br/>
                <input type="text" class="form" name="username" placeholder="username"/><br/>

                <label for="password">* Password:</label><br/>
                <input type="password" class="form" name="password" placeholder="password"/><br/>

                <label for="first_name">* First name:</label><br/>
                <input type="text" class="form" name="first_name" placeholder="firstname"/><br/>

                <label for="last_name">* Last name:</label><br/>
                <input type="text" class="form" name="last_name" placeholder="lastname"/><br/>

                <label for="email">* Email:</label><br/>
                <input type="email" class="form" name="email" placeholder="user1@domain.com"/><br/>

                <label for="profile">* Profile:</label><br/>
                <input type="text" class="form" name="profile" placeholder="customers users"/><br/>

                <br/>
                <input type="button" name="buttonEml" value="check email"
                       onclick="validateEmail(email,'Error : email format invalid!');">
                <input type="button" name="buttonFlds" value="check fields"
                       onclick="verifForm([username,password,first_name,last_name,email,profile],'Error : fields marked with an asterisk are mandatory!');">
                <input name="save" type="submit" value="save">
            </p>
        </form>
    </div>
</div>
</body>
</html>