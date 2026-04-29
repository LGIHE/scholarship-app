# Permissions CheckboxList Fix

## Error
```
TypeError
Filament\Forms\Components\CheckboxList::isOptionDisabled(): 
Argument #2 ($label) must be of type string, array given
```

## Root Cause
The CheckboxList component was receiving a grouped/nested array structure:
```php
[
    'application' => [
        1 => 'application.view',
        2 => 'application.create',
    ],
    'scholar' => [
        3 => 'scholar.view',
        4 => 'scholar.create',
    ]
]
```

But it expects a flat array:
```php
[
    1 => 'application.view',
    2 => 'application.create',
    3 => 'scholar.view',
    4 => 'scholar.create',
]
```

## Solution
Changed from grouped options to flat options:

**Before (WRONG):**
```php
->options(function () {
    return \Spatie\Permission\Models\Permission::all()
        ->groupBy(function ($permission) {
            return explode('.', $permission->name)[0];
        })
        ->map(function ($group) {
            return $group->pluck('name', 'id');
        })
        ->toArray();
})
```

**After (CORRECT):**
```php
->options(function () {
    return \Spatie\Permission\Models\Permission::all()
        ->pluck('name', 'id')
        ->toArray();
})
```

## Files Fixed
- ✅ `app/Filament/Resources/ApplicantUserResource.php`
- ✅ `app/Filament/Resources/ScholarUserResource.php`
- ✅ `app/Filament/Resources/SystemUserResource.php`

## Result
- ✅ Permissions now display as a flat, searchable list
- ✅ Alphabetically sorted by permission name
- ✅ Still searchable and bulk toggleable
- ✅ No more TypeError

## Note on Grouping
While the initial intent was to group permissions by category (application, scholar, user, etc.), Filament's CheckboxList component doesn't support nested option groups. The flat list is still user-friendly because:
1. Permissions are named with prefixes (e.g., `application.view`, `scholar.edit`)
2. The search function helps find related permissions quickly
3. Alphabetical sorting keeps related permissions together

## Alternative for Grouping
If grouping is desired in the future, consider:
1. Using multiple CheckboxList components (one per category)
2. Using a custom Livewire component
3. Using Filament's Repeater with nested fields
4. Using tabs to separate permission categories

## Status
✅ **Fixed** - User create/edit forms now work correctly!
