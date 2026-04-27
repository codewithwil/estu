<?php
require_once 'functions/auth.php';
logout();

header('Location: login.php');
exit;