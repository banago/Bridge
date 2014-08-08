Bridge
==============

A PHP class to transfer data using different protocols (sftp, ftp, http, etc). Utilizes PHPs ssh2, ftp and curl functions if available.

Example
-------
```php
<?php
require __DIR__.'/vendor/autoload.php';

use Banago\Bridge\Bridge;

$conn = new Bridge('ftp://ftp.funet.fi');
//List directory contents
print_r($conn->ls());
//Display contents of the README file
echo $conn->get('README');
```

License
-------
LGPL v3
