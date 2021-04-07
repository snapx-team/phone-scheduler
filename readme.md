# XKanban

XKanban is a Kanban Dashboard for Laravel projects. This is a single page application that supports the creation of modular grid-style Kanban boards.
Once integrated, it'll create it's necessary db tables


## Installation

*eventually* all we would need to do is require this package with composer using the following command:


```bash
composer require --dev todo/todo/todo
```

## Getting Started

**Firstly → drag the phone-scheduler folder inside your package folder at root level. Create a "package" folder if you don't have one**


Then, add this to your root composer to add the class to your project.
```json
"psr-4": {
    //...
    "Xguard\\PhoneScheduler\\": "package/phone-scheduler/src/"
},
```
Add the Kanban Service provider to the config/app.php

```php
return [
    //...
    //...
    "providers" => [
        //...
        //...
        Xguard\PhoneScheduler\PhoneSchedulerServiceProvider::class,
    ]
];

```

Navigate to the kanban folder in your command line and perform the following commands:
```bash
composer install
npm run dev
```

Aaaand now go back to your root folder in your command line and publish the package with the following command:
```bash
php artisan vendor:publish --provider="Xguard\PhoneScheduler\PhoneSchedulerServiceProvider" --force
```

To run fresh package migrations
```bash
php artisan migrate:fresh --path=package/phone-scheduler/src/database/migrations
```




## Features
* Multiple Kanban Support
* Modular Grid-Like layout
    * Determine the numbers of row and the number of columns per each row
* drag and drop task cards
    * move a task's index within a column
    * move a task between columns
    * move a task between rows
    
* Create, Read, ~~Update, Delete:~~
    * Tasks cards
    * Columns
    * Rows 
    * Kanbans
    
* Single page application

## License
Lets go ahead and say we make it open source? Liscensed under the [MIT license](https://choosealicense.com/licenses/mit/)
