<?php

require 'Deploy.php';

$deploy = new Deploy('/var/www/nouse/', [
	'branch' => 'master',
    'log'    => './nouse.log'
    ]);

$deploy->execute();
