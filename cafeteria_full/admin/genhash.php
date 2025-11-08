<?php
$nuevaPass = 'MiNuevoPass123'; // tu nueva contraseña
$hash = password_hash($nuevaPass, PASSWORD_DEFAULT);
echo $hash;
