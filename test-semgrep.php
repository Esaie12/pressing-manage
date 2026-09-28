<?php

// 1. Devrait déclencher laravel-debug-dd (WARNING)
dd("Ici un debug oublié");

// 2. Devrait déclencher laravel-db-raw-concatenation (ERROR)
$userInput = $_GET['id'];
DB::raw("SELECT * FROM users WHERE id = " . $userInput);

// 3. Devrait déclencher laravel-create-request-all (ERROR)
User::create($request->all());
