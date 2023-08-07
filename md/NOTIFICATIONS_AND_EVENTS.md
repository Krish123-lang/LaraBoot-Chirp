# Notifications and Events
> `php artisan make:notification NewChirp`

1. > app/Notifications/NewChirp.php
```
use App\Models\Chirp;
use Illuminate\Support\Str;

public function __construct(public Chirp $chirp) // Adding public Chirp $chirp
    {
        //
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject("New Chirp from {$this->chirp->user->name}")
                    ->greeting("New Chirp from {$this->chirp->user->name}")
                    ->line(Str::limit($this->chirp->message, 50))
                    ->action('Go to Chirper', url('/'))
                    ->line('Thank you for using our application!');
    }

```
### Creating an event
2. > `app/Events/ChirpCreated.php`
```
use App\Models\Chirp;
public function __construct(public Chirp $chirp) // Added public Chirp $chirp
    {
        //
    }
```

### Dispatching an Event
```
Now that we have our event class, we're ready to dispatch it any time a Chirp is created. You may dispatch events anywhere in your application lifecycle, but as our event relates to the creation of an Eloquent model, we can configure our Chirp model to dispatch the event for us.
```
> app/Models/Chirp.php
```
use App\Events\ChirpCreated;

protected $dispatchesEvents = [
        'created' => ChirpCreated::class,
];
```

### Creating an event listener
> php artisan make:listener SendChirpCreatedNotifications --event=ChirpCreated

3. > `app/Listeners/SendChirpCreatedNotifications.php`
```
use App\Models\User;
use App\Notifications\NewChirp;

class SendChirpCreatedNotifications implements ShouldQueue
{
public function handle(ChirpCreated $event): void
    {
        foreach (User::whereNot('id', $event->chirp->user_id)->cursor() as $user) {
            $user->notify(new NewChirp($event->chirp));
        }
    }
}
```

### Registering the event listener
4. > `App\Providers\EventServiceProvider.php`
```
use App\Events\ChirpCreated;
use App\Listeners\SendChirpCreatedNotifications;

protected $listen = [
        ChirpCreated::class => [
            SendChirpCreatedNotifications::class,
        ],
 
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];
```
