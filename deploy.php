<?php

require 'Deploy.php';

$deploy = new Deploy('/var/www/deploy/', [
	'branch' => 'master',
    'log'    => './deploy.log'
    ]);

$deploy->execute();
