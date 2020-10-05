# custom email provider

[![Latest Version on Packagist](https://img.shields.io/packagist/v/tepuilabs/sendinblue.svg?style=flat-square)](https://packagist.org/packages/tepuilabs/sendinblue)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/tepuilabs/sendinblue/run-tests?label=tests)](https://github.com/tepuilabs/sendinblue/actions?query=workflow%3Arun-tests+branch%3Amaster)
[![Total Downloads](https://img.shields.io/packagist/dt/tepuilabs/sendinblue.svg?style=flat-square)](https://packagist.org/packages/tepuilabs/sendinblue)



### Installation

You can install the package via composer:

```bash
composer require tepuilabs/sendinblue
```

Then add:

- `SENDINBLUE_API_KEY` to your env

Add this to your `config/mail.php` file

```php
    'mailers' => [
		'sendinblue' => [
			'transport' => 'sendinblue',
		],
    ],
```
### Sending Mail Via A Specific Mailer

```php
Mail::mailer('sendinblue')
    ->to($request->user())
    ->send(new OrderShipped($order));
```


### Testing

``` bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

### Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

### Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

### Credits

- [tepuiLABS](https://github.com/tepuiLABS)
- [All Contributors](../../contributors)

### License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
