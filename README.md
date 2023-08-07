# Creating Chirps
### create a model, migration, and resource controller
1. `php artisan make:model -mrc Chirp`
2. Install laravel breeze by:
```
> composer require laravel/breeze --dev
> php artisan breeze:install blade
> npm run dev
> php artisan migrate
```
### Routing
2. > web.php
```
> index - route will display our form and a listing of Chirps. => chirps.index
> store  -route will be used for saving new Chirps.=> chirps.store

> The auth - middleware ensures that only logged-in users can access the route.
> The verified - middleware will be used if you decide to enable email verification.
```

```
Route::resource('chirps', ChirpController::class)
    ->only(['index', 'store'])
    ->middleware(['auth', 'verified']);
```
### chirps named database
3. > .env file
```
    DB_DATABASE=chirps
```
4. `php artisan migrate`
### Showing Index page
3. > ChirpController.php
```
     public function index()
    {
        return view('chirps.index');
    }
```

4. create `views/chirps/index.blade.php`
```
<x-app-layout>
    <div class="max-w-2xl mx-auto p-4 sm:p-6 lg:p-8">
        <form method="POST" action="{{ route('chirps.store') }}">
            @csrf
            <textarea name="message" placeholder="{{ __('What\'s on your mind?') }}"
                class="block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">{{ old('message') }}</textarea>
            <x-input-error :messages="$errors->get('message')" class="mt-2" />
            <x-primary-button class="mt-4">{{ __('Chirp') }}</x-primary-button>
        </form>
    </div>
</x-app-layout>
```
7. > ChirpController.php
```
Creating Chirps by validating and updating the Store
```

```
    use Illuminate\Http\RedirectResponse;
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'message' => 'required|string|max:255',
        ]);

        $request->user()->chirps()->create($validated);

        return redirect(route('chirps.index'));
    }

```
### Creating Relationship (Hasmany)
8. > Models/User.php

```
    use Illuminate\Database\Eloquent\Relations\HasMany;

    public function chirps(): HasMany
    {
        return $this->hasMany(Chirp::class);
    }
```
### Mass Assignment Protection
8. > Models/Chirp.php
```
    protected $fillable = [
            'message',
        ];
```
### Updatin the migration
9. > database/migrations

```
$table->foreignId('user_id')->constrained()->cascadeOnDelete();
$table->string('message');

```
# Showing Chirps

1. > ChirpController.php
```
return view('chirps.index', [
            'chirps' => Chirp::with('user')->latest()->get(),
        ]);
```
2. > app/Models.php
```
use Illuminate\Database\Eloquent\Relations\BelongsTo;

public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
```
### Updating view
3. >index.blade.php
```
<div class="mt-6 bg-white shadow-sm rounded-lg divide-y">
            @foreach ($chirps as $chirp)
                <div class="p-6 flex space-x-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600 -scale-x-100" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                    <div class="flex-1">
                        <div class="flex justify-between items-center">
                            <div>
                                <span class="text-gray-800">{{ $chirp->user->name }}</span>
                                <small class="ml-2 text-sm text-gray-600">{{ $chirp->created_at->format('j M Y, g:i a') }}</small>
                            </div>
                        </div>
                        <p class="mt-4 text-lg text-gray-900">{{ $chirp->message }}</p>
                    </div>
                </div>
            @endforeach
        </div>
```

# EDIT CHIRPS

1. > web.php
```
The chirps.edit route will display the form for editing a Chirp, while the chirps.update route will accept the data from the form and update the model:
```
| Verb          | URI           | Action        | Route Name    |
| ------------- | ------------- | ------------- | ------------- |
| GET           | /chirps  | index  | chirps.index   |
| POST           | /chirps  | store  | chirps.store   |
| GET           | /chirps/{chirp}/edit  | edit  | chirps.edit    |
| PUT/PATCH            | /chirps/{chirp}  | update  | chirps.update   |

```
Route::resource('chirps', ChirpController::class)
    ->only(['index', 'store']) // === === === Deleted This === === === 
    ->only(['index', 'store', 'edit', 'update']) // === === === Added This === === === 
```
### Linking to the Edit Page
2. > index.blade.php
```
@unless ($chirp->created_at->eq($chirp->updated_at))
    <small class="text-sm text-gray-600"> &middot; {{ __('edited') }}</small>
@endunless

@if ($chirp->user->is(auth()->user()))
    <x-dropdown>
        <x-slot name="trigger">
            <button>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z" />
                </svg>
            </button>
        </x-slot>
        <x-slot name="content">
            <x-dropdown-link :href="route('chirps.edit', $chirp)">
                {{ __('Edit') }}
            </x-dropdown-link>
        </x-slot>
    </x-dropdown>
@endif
```

3. > edit.blade.php
```
<x-app-layout>
    <div class="max-w-2xl mx-auto p-4 sm:p-6 lg:p-8">
        <form method="POST" action="{{ route('chirps.update', $chirp) }}">
            @csrf
            @method('patch')
            <textarea
                name="message"
                class="block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"
            >{{ old('message', $chirp->message) }}</textarea>
            <x-input-error :messages="$errors->get('message')" class="mt-2" />
            <div class="mt-4 space-x-2">
                <x-primary-button>{{ __('Save') }}</x-primary-button>
                <a href="{{ route('chirps.index') }}">{{ __('Cancel') }}</a>
            </div>
        </form>
    </div>
</x-app-layout>
```
4. > ChirpController.php
```
public function edit(Chirp $chirp): View
    {
        $this->authorize('update', $chirp);
 
        return view('chirps.edit', [
            'chirp' => $chirp,
        ]);
    }

public function update(Request $request, Chirp $chirp): RedirectResponse
    {
        $this->authorize('update', $chirp);
 
        $validated = $request->validate([
            'message' => 'required|string|max:255',
        ]);
 
        $chirp->update($validated);
 
        return redirect(route('chirps.index'));
    }
```
### Authorization

```
By default, the authorize method will prevent everyone from being able to update the Chirp. We can specify who is allowed to update it by creating a Model Policy.
```
> php artisan make:policy ChirpPolicy --model=Chirp

# Deleting Chirps

1. > web.php
```
->only(['index', 'store', 'edit', 'update']) // === === Delete this === === 
->only(['index', 'store', 'edit', 'update', 'destroy'])// === === Added Destroy === === 
```
| Verb          | URI           | Action        | Route Name    |
| ------------- | ------------- | ------------- | ------------- |
| GET           | /chirps  | index  | chirps.index   |
| POST           | /chirps  | store  | chirps.store   |
| GET           | /chirps/{chirp}/edit  | edit  | chirps.edit    |
| PUT/PATCH            | /chirps/{chirp}  | update  | chirps.update   |
| DELETE            | /chirps/{chirp}  | destroy  | chirps.destroy   |

2. > ChirpController.php
```
public function destroy(Chirp $chirp): RedirectResponse
    {
        $this->authorize('delete', $chirp);
 
        $chirp->delete();
 
        return redirect(route('chirps.index'));
    }
```
3. > Policies/ChirpPolicy.php
#### As with editing, we only want our Chirp authors to be able to delete their Chirps, so let's update the delete method in our ChirpPolicy class:

```
public function delete(User $user, Chirp $chirp): bool
    {
        return $this->update($user, $chirp);
    }
```
### Updating View
4. > index.blade.php
```
<form method="POST" action="{{ route('chirps.destroy', $chirp) }}">
    @csrf
    @method('delete')
    <x-dropdown-link :href="route('chirps.destroy', $chirp)" onclick="event.preventDefault(); this.closest('form').submit();">
        {{ __('Delete') }}
    </x-dropdown-link>
</form>
```
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
![1](https://github.com/Krish123-lang/LaraBoot-Chirp/assets/56486342/8d1659e9-b53f-4f69-a80f-6451c8bb473b)

![edit_delete_optinos](https://github.com/Krish123-lang/LaraBoot-Chirp/assets/56486342/2f46d7e0-7956-4067-8faf-24f1e1852aba)

![notification_email](https://github.com/Krish123-lang/LaraBoot-Chirp/assets/56486342/66039fbf-7c24-4788-8e5a-4134f8491c1a)

