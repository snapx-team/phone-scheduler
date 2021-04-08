# Phone Scheduler

This is a phone scheduling application that generates data to be fetched by twilio. 

## Installation

Use the following commands to install

```bash
composer require xguard/phone-scheduler
php artisan migrate
php artisan vendor:publish --provider="Xguard\PhoneScheduler\PhoneSchedulerServiceProvider" --force
```

## Develpment 

**Follow these steps to make modifications to the package**

**1:** Firstly, download and drag the phone-scheduler folder inside your package folder at root level. 
Create a "package" folder if you don't have one.


**2:** Then, add line of code in the psr-4 of your root composer.json
```json
"psr-4": {
    ...
    "Xguard\\PhoneScheduler\\": "package/phone-scheduler/src/"
},
```
**3:** Add the Phone Scheduler Service provider to the config/app.php

```php
return [
    //...
    "providers" => [
        //...
        Xguard\PhoneScheduler\PhoneSchedulerServiceProvider::class,
    ]
];

```

**4:** Navigate to the phone-scheduler package folder in your command line and perform the following commands:
```bash
composer install
npm install
npm run dev
```

**5:** Return to the  and publish the package with the following command:
```bash
php artisan vendor:publish --provider="Xguard\PhoneScheduler\PhoneSchedulerServiceProvider" --force
```

**6:** To run package migrations
```bash
php artisan migrate --path=package/phone-scheduler/src/database/migrations
```



## License
Lets go ahead and say we make it open source? Liscensed under the [MIT license](https://choosealicense.com/licenses/mit/)
