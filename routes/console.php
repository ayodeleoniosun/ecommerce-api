<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('auth:clear-resets')->hourly();
Schedule::command('app:restore-abandoned-cart-quantity')->everyFiveMinutes();
