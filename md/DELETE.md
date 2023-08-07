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
