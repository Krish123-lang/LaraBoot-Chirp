1. Initating Project

## === === === === === === === CREATING CHIRPS === === === === === === ===

2. Create Model, Migrations and controllers => php artisan make:model -mrc Chirp

3. It will create
   a) app/Models/Chirp.php => The Eloquent model.
   b)database/migrations/<timestamp>\_create_chirps_table.php => The database migration that will create your database table.
   c) app/Http/Controller/ChirpController.php => The HTTP controller that will take incoming requests and return responses.

4. Routing
   => The `index` route will display our form and a listing of Chirps.
   => The `store` route will be used for saving new Chirps.

    - We are also going to place these routes behind two middleware:

    The `auth` middleware ensures that only logged-in users can access the route.
    The `verified` middleware will be used if you decide to enable email verification.

    You may view all of the routes for your application by running the `php artisan route:list` command.

    => `php artisan tinker`


## === === === === === === === SHOWING CHIRPS === === === === === === ===

** All in code

## === === === === === === === EDITING CHIRPS === === === === === === ===

<pre>
Verb	    |    URI	                |        Action	    |    Route Name

GET	        |    /chirps	            |        index	    |    chirps.index

POST	    |    /chirps	            |        store	    |    chirps.store

GET	        |    /chirps/{chirp}/edit	|        edit	    |    chirps.edit

PUT/PATCH	|    /chirps/{chirp}	    |        update	    |    chirps.update

</pre>
## *  Authorization
`php artisan make:policy ChirpPolicy --model=Chirp` => This will create a policy class at app/Policies/ChirpPolicy.php which we can update to specify that only the author is authorized to update a Chirp.

## === === === === === === === DELETING CHIRPS === === === === === === ===
All  in code

## === === === === === === === NOTIFICATIONS & EVENTS === === === === === === ===

a) `php artisan make:notification NewChirp`
=> This will create a new notification at app/Notifications/NewChirp.php

b) Creating an Event
`php artisan make:event ChirpCreated`

c) Creating an event listener
`php artisan make:listener SendChirpCreatedNotifications --event=ChirpCreated`

=> The new listener will be placed at app/Listeners/SendChirpCreatedNotifications.php


![Screenshot (126)](https://github.com/Krish123-lang/LaraBoot-Chirp/assets/56486342/0e781dbc-0a22-4f16-bb81-2380e1a69d2b)
