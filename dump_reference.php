<?php

require_once __DIR__ . '/vendor/autoload.php';

use Bankiru\Api\JsonRpc\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\Dumper\YamlReferenceDumper;

$dumper = new YamlReferenceDumper();

echo $dumper->dump(new Configuration());


