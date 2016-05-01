<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Config for the CodeIgniter Redis library
 *
 * @see ../libraries/Redis.php
 */

// Default connection group
$config['redis_default']['host'] = '50.30.35.9';		// IP address or host
$config['redis_default']['port'] = '3342';			// Default Redis port is 6379
$config['redis_default']['password'] = 'bb73af28d62b5aa38b36cd642d5b0877';			// Can be left empty when the server does not require AUTH

// $config['redis_slave']['host'] = '50.30.35.9';
// $config['redis_slave']['port'] = '3342';
// $config['redis_slave']['password'] = 'bb73af28d62b5aa38b36cd642d5b0877';


// $config['redis_default']['host'] = 'pub-redis-17623.us-east-1-2.4.ec2.garantiadata.com';		// IP address or host
// $config['redis_default']['port'] = '17623';			// Default Redis port is 6379
// $config['redis_default']['password'] = 'boboboibotcom';			// Can be left empty when the server does not require AUTH