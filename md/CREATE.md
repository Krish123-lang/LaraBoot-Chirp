# Creating Chirps
### create a model, migration, and resource controller
1. `php artisan make:model -mrc Chirp`
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
