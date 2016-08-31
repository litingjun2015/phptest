<header class="bar bar-nav">
    <h1 class="title">测试</h1>
</header>

<div class="box-center content" style="height: 80%">
    <?php

    echo time();
    echo "</br>";
    echo date('Y-m-d', time());
    
    echo "echo test";
    echo 'Current PHP version: ' . phpversion();
    echo function_exists('curl_version');
    echo ' ';
    echo phpinfo();
    
    
    ?>


</div>