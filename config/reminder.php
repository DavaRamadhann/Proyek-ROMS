<?php

return [
    // Waktu scheduler untuk mengirim reminder (format HH:MM, 24 jam)
    // Admin dapat mengubah nilai ini di file konfigurasi atau melalui UI admin di masa depan
    'schedule_time' => env('REMINDER_SCHEDULE_TIME', '09:00'),
];
