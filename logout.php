<?php

require_once('functions.php');

session_unset();
session_destroy();
header("Location:login.php");
