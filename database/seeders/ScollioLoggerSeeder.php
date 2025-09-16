<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Scollio\Facades\Logger;

class ScollioLoggerSeeder extends Seeder
{
    public function run(): void
    {
        $levels = ['emergency', 'alert', 'critical', 'error', 'warning', 'notice', 'info', 'debug'];

        $locations = [
            'OrderController::processPayment',
            'AuthController::login',
            'SystemMonitor',
            'DB::connect',
            'CacheService::get',
            'FeatureToggle',
            'MailService::send',
            'ReportService::generate',
            'QueueWorker',
            'NotificationService'
        ];

        $messages = [
            'Payment failed',
            'User logged in',
            'System down!',
            'High memory usage detected',
            'Database connection lost',
            'Cache miss on key',
            'Email delivery failed',
            'Invoice generation failed',
            'Order shipped successfully',
            'New feature flag enabled'
        ];

        for ($i = 0; $i < 100; $i++) {
            $level = $levels[array_rand($levels)];
            $message = $messages[array_rand($messages)];
            $location = $locations[array_rand($locations)];

            $context = [
                'user_id' => rand(1, 50),
                'order_id' => rand(100, 999),
                'ip' => fake()->ipv4(),
            ];

            Logger::$level($message, $context, $location);
        }
    }
}
