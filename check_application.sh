#!/bin/bash
echo "=== Checking Application Status ==="
echo ""
echo "Latest Application:"
php artisan tinker --execute="
\$app = App\Models\Application::latest()->first();
if (\$app) {
    echo 'ID: ' . \$app->id . PHP_EOL;
    echo 'User ID: ' . \$app->user_id . PHP_EOL;
    echo 'Status: ' . \$app->status . PHP_EOL;
    echo 'Created: ' . \$app->created_at . PHP_EOL;
    echo 'Updated: ' . \$app->updated_at . PHP_EOL;
    echo 'Has Documents: ' . (empty(\$app->documents) ? 'No' : 'Yes') . PHP_EOL;
    if (!empty(\$app->documents)) {
        echo 'Documents: ' . json_encode(\$app->documents, JSON_PRETTY_PRINT) . PHP_EOL;
    }
} else {
    echo 'No applications found' . PHP_EOL;
}
"
echo ""
echo "=== Uploaded Files ==="
ls -lh storage/app/public/applications/documents/ 2>/dev/null || echo "No files uploaded yet"
echo ""
echo "=== Recent Log Entries ==="
tail -n 20 storage/logs/laravel.log 2>/dev/null || echo "No log file found"
