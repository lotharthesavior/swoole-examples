<html>
<head>
    <title>Admin Page</title>
    <link rel="icon" href="data:,"><!-- This avoids errors for favicon requests -->

    <link rel="stylesheet" href="/css/style.css"/>
</head>
<body>

<div class="login-form-container">
    <h1>Hello <?php echo $user_name; ?></h1>

    <form action="/logout" method="POST" enctype="multipart/form-data"><input type="submit" value="Logout"/></form>
</div>

</body>
</html>