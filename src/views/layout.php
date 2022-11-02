<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="stylesheets/css/app.css">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Courgette&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="stylesheets/css/stylesheet.css">
        <title><?php echo $title?></title>
    </head>
    <body>
        <div class="wrapper">
            <header>
                <h1 class="mb-4 title pl-5 my-auto text-center"><a href="/" class="text-white text-decoration-none">TOWN SELECT</a></h1>
            </header>
            <div class="main">
                <?php include $content ?>
            </div>
            <footer class="mt-auto  d-flex align-items-center justify-content-end">
                <h5><a class="text-white pr-5" href="inquiry">お問い合わせ</a></h5>
            </footer>
        </div>
    </body>
</html>
