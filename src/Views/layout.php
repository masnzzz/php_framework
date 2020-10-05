<!DOCTYPE>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title><?php if (isset($title)): echo $this->escape($title) . ' - '; endif; ?>Sample Page</title>
</head>
<body>
    <header>
        <h1><a href="<?php echo $base_url; ?>/">Sample Page</a></h1>
    </header>
    <main>
        <?php echo $_content; ?>
    </main>
</body>
</html>
