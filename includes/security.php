<?php
function csrf_token(){if(empty($_SESSION['csrf'])) $_SESSION['csrf']=bin2hex(random_bytes(32));return $_SESSION['csrf'];}
function csrf_check(){if(!isset($_POST['csrf']) || !hash_equals(csrf_token(),(string)$_POST['csrf'])){http_response_code(403);exit('Invalid form token. Reload and try again.');}}
