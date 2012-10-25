<!DOCTYPE html>
<html class="no-js">
<?php echo $head; ?>
<body>

<div class="header-container">
    <header class="wrapper clearfix">
        <h1 class="title">h1.title</h1>
        <nav>
            <ul>
                <li><a href="#">nav ul li a</a></li>
                <li><a href="#">nav ul li a</a></li>
                <li><a href="#">nav ul li a</a></li>
            </ul>
        </nav>
    </header>
</div>

<?php echo $content; ?>

<div class="footer-container">
    <footer class="wrapper">
        <h3>© <?php echo (date('Y') == 2012) ? date('Y') : '2012 - ' . date('Y') ?> Anton Kuzmenko</h3>
    </footer>
</div>

<?php echo $javascript; ?>
</body>
</html>