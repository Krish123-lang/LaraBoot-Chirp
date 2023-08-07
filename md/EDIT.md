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
