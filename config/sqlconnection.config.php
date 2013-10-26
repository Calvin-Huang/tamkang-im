<?php

return array(
    /*
     * atch out for putting spaces in the DSN
     * mysql:host=localhost;dbname=test works
     * mysql: host = localhost; dbname=test works
     * mysql: host = localhost; dbname = test doesn't work...
     */
    "dsn" => "mysql:host=localhost;dbname=im",
    "username" => "root",
    "password" => ""
);
?>