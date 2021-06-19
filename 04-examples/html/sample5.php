<html>
<head>
    <meta charset="UTF-8" />
    <title>HTML Swoole Server</title>
    <link rel="icon" type="image/png" href="/imgs/favicon-32x32.png"/>
</head>
<body>
    <h1><?php echo $main_heading; ?></h1>
    <div>
        <form action="/subscription" method="post">
            <div>
                <p>If you want to subscribe, add your email here:</p>
                <input name="email" id="email" value="" placeholder="johngalt@example.com"/>
                <input type="submit" value="Subscribe"/>
            </div>

            <?php if (isset($error)) { ?>
                <div style="color: red"><?php echo $error; ?></div>
            <?php } ?>
        </form>
    </div>
</body>
</html>
