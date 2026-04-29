# Application Submission Fix Summary

## Changes Made

### 1. Frontend (Form.jsx)
- Added `forceFormData: true` to the post request to properly handle file uploads
- Added console logging to debug submission
- Added better error handling with user-friendly messages

### 2. Backend (ApplicationController.php)
- Added logging at the beginning of submit() to track requests
- Added logging after successful submission
- Documents are now renamed with applicant name format: `firstname_lastname_document_type.extension`
  - Example: `caleb_nkunze_national_id.pdf`
  - Example: `caleb_nkunze_academic_documents.pdf`
- Added try-catch for email sending to prevent submission failure if email fails
- Made document validation conditional (only validates if documents are present)

### 3. File Naming Convention
Files are renamed using this pattern:
```
{firstname}_{lastname}_{document_type}.{extension}
```

Where:
- `firstname` and `lastname` are converted to lowercase with spaces replaced by underscores
- `document_type` is one of: `academic_documents`, `national_id`, `admission_form`, `provisional_results`
- `extension` is the original file extension (pdf, jpg, jpeg, png)

## Testing Steps

1. **Check Browser Console**
   - Open browser developer tools (F12)
   - Go to Console tab
   - Try submitting the application
   - Look for:
     - "Submitting application..." message
     - Any error messages
     - "Application submitted successfully" message

2. **Check Laravel Logs**
   ```bash
   tail -f storage/logs/laravel.log
   ```
   - Look for "Application submission attempt" log entry
   - Check what keys are being sent
   - Look for "Application submitted successfully" log entry

3. **Check Database**
   ```bash
   php artisan tinker
   ```
   Then run:
   ```php
   $app = App\Models\Application::latest()->first();
   $app->status; // Should be 'submitted'
   $app->documents; // Should show document paths
   ```

4. **Check Uploaded Files**
   ```bash
   ls -la storage/app/public/applications/documents/
   ```
   - Files should be named like: `caleb_nkunze_national_id.pdf`

## Common Issues & Solutions

### Issue 1: Application stays as "draft"
**Possible Causes:**
- JavaScript error preventing form submission
- Validation error on backend
- File upload issue

**Solution:**
1. Check browser console for errors
2. Check Laravel logs for validation errors
3. Ensure all required fields are filled
4. Ensure documents are uploaded

### Issue 2: Files not uploading
**Possible Causes:**
- File size too large (>5MB)
- Wrong file format
- Storage directory doesn't exist

**Solution:**
1. Check file size (must be <5MB)
2. Use only PDF, JPG, JPEG, or PNG files
3. Run: `php artisan storage:link`
4. Check permissions: `chmod -R 775 storage`

### Issue 3: Validation errors
**Check these required fields:**
- Personal Info: first_name, last_name, gender, has_disability, refugee_or_displaced, residence_area, university, program_of_study, cgpa, high_school
- Financial Info: household_income, number_of_dependents, estimated_tuition, estimated_living_expenses, income_sources, funding_gap
- Guardian Info: guardian_name, guardian_phone, guardian_relation
- Essay: personal_statement (min 100 words), commitment (min 100 words)
- Documents: academic_documents (required), national_id (required)

## Debug Commands

### View latest application:
```bash
php artisan tinker
```
```php
$app = App\Models\Application::latest()->first();
dd($app->toArray());
```

### View application status:
```php
$app = App\Models\Application::where('user_id', YOUR_USER_ID)->latest()->first();
echo "Status: " . $app->status;
echo "\nDocuments: " . json_encode($app->documents);
```

### Clear logs:
```bash
> storage/logs/laravel.log
```

### Watch logs in real-time:
```bash
tail -f storage/logs/laravel.log
```

## Next Steps After Testing

1. Try submitting the application
2. Check browser console for any errors
3. Check Laravel logs for submission attempt
4. Verify status changed to "submitted" in database
5. Verify files were uploaded with correct names
6. Report any errors you see

## File Locations

- Frontend Form: `resources/js/Pages/Application/Form.jsx`
- Backend Controller: `app/Http/Controllers/ApplicationController.php`
- Logs: `storage/logs/laravel.log`
- Uploaded Files: `storage/app/public/applications/documents/`
