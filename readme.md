# Bridge

Brige is a common interface to comuncate with a server through FTP and SFTP. SFTP support requires the `php_ssh2` PHP extension.

##Installation

The recommended way to install Bridge is through Composer.

```sh
composer require banago/bridge
```

Or

```json
{
    "require": {
        "banago/bridge": "dev-master",
    }
}
```

## Example

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

## License

LGPL v3

## Credits

Brige is a fork of [Connection.php](https://github.com/tangervu/Connection.php) project created by [Tuomas Angervuori](http://anger.kapsi.fi/links/). Brige is further developed and actively maintained by [Baki Goxhaj](https://twitter.com/banago) as part of the [PHPloy](https://github.com/banago/PHPloy) project.
