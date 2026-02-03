<?php
require_once __DIR__ . '/../../src/config.php';
require_once __DIR__ . '/../../src/auth.php';

requireAdmin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - <?= SITE_NAME ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../assets/css/custom.css">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'sans': ['Inter', 'system-ui', 'sans-serif']
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50">
    <?php include __DIR__ . '/../../src/includes/admin-header.php'; ?>

    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Settings</h1>
            <a href="index.php" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg">← Back to Dashboard</a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Store Settings -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-bold mb-4">Store Information</h2>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Store Name</label>
                        <div class="text-gray-900"><?= SITE_NAME ?></div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Store URL</label>
                        <div class="text-gray-900"><?= SITE_URL ?></div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Admin Email</label>
                        <div class="text-gray-900"><?= ADMIN_EMAIL ?></div>
                    </div>
                </div>
            </div>

            <!-- Email Settings -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-bold mb-4">Email Configuration</h2>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email Debug Mode</label>
                        <div class="text-gray-900">
                            <?= EMAIL_DEBUG ? 'ON (emails logged to file)' : 'OFF (emails sent)' ?>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Use MailHog</label>
                        <div class="text-gray-900">
                            <?= (defined('USE_MAILHOG') && USE_MAILHOG) ? 'ON' : 'OFF' ?>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Use SMTP</label>
                        <div class="text-gray-900">
                            <?= (defined('USE_SMTP') && USE_SMTP) ? 'ON' : 'OFF' ?>
                        </div>
                    </div>
                    <div class="mt-4">
                        <a href="../test-email.php" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm">
                            Test Email System
                        </a>
                    </div>
                </div>
            </div>

            <!-- Database Settings -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-bold mb-4">Database Information</h2>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Database Host</label>
                        <div class="text-gray-900"><?= DB_HOST ?></div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Database Name</label>
                        <div class="text-gray-900"><?= DB_NAME ?></div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Database User</label>
                        <div class="text-gray-900"><?= DB_USER ?></div>
                    </div>
                </div>
            </div>

            <!-- System Tools -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-bold mb-4">System Tools</h2>
                <div class="space-y-3">
                    <a href="../test-db.php" class="block w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-center">
                        Test Database Connection
                    </a>
                    <a href="../test-email.php" class="block w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-center">
                        Test Email System
                    </a>
                    <a href="../check-mailhog.php" class="block w-full bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg text-center">
                        Check MailHog Status
                    </a>
                    <a href="../view-email-logs.php" class="block w-full bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg text-center">
                        View Email Logs
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>