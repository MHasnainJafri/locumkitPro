<?php

namespace App\Console;

use App\Jobs\CronApproveFeedback as JobsCronApproveFeedback;
use App\Jobs\CronCloseJob as JobsCronCloseJob;
use App\Jobs\CronFeedback as JobsCronFeedback;
use App\Jobs\CronJobReminder as JobsCronJobReminder;
use App\Jobs\CronJobSummary as JobsCronJobSummary;
use App\Jobs\CronJobTimeline as JobsCronJobTimeline;
use App\Jobs\CronOnDay as JobsCronOnDay;
use App\Jobs\CronOnDayExpense as JobsCronOnDayExpense;
use App\Jobs\CronPackageStatus as JobsCronPackageStatus;
use App\Jobs\CronResetFreeze as JobsCronResetFreeze;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\OnDaySendNotifications;

class Kernel extends ConsoleKernel
{

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->job(new JobsCronJobReminder)->name("CronJobReminder")->dailyAt("09:00")->timezone("Europe/London");
        // $schedule->job(new JobsCronOnDay)->name("CronOnDay")->dailyAt("11:00")->timezone("Europe/London");
        // $schedule->job(new JobsCronApproveFeedback)->name("CronApproveFeedback")->daily()->timezone("Europe/London");
        // $schedule->job(new JobsCronResetFreeze)->name("CronResetFreeze")->hourly()->timezone("Europe/London");
        // $schedule->job(new JobsCronPackageStatus)->name("CronPackageStatus")->daily()->timezone("Europe/London");
        
        $schedule->job(new JobsCronApproveFeedback)->name("CronApproveFeedback")->everyMinute()->timezone("Europe/London");
        $schedule->job(new JobsCronPackageStatus)->name("CronPackageStatus")->everyMinute()->timezone("Europe/London");
        $schedule->job(new JobsCronFeedback)->name("CronFeedback")->dailyAt("09:00")->timezone("Europe/London");
        $schedule->job(new JobsCronJobReminder)->name("CronJobReminder")->everyMinute()->timezone("Europe/London");
        $schedule->job(new JobsCronJobSummary)->name("CronJobSummary")->dailyAt("09:00")->timezone("Europe/London");
        $schedule->job(new JobsCronCloseJob)->name("CronCloseJob")->dailyAt("10:50")->timezone("Europe/London");
        $schedule->job(new JobsCronOnDay)->name("CronOnDay")->everyMinute()->timezone("Europe/London");
        $schedule->job(new JobsCronOnDayExpense)->name("CronOnDayExpense")->dailyAt("14:00")->timezone("Europe/London");
        $schedule->job(new JobsCronJobTimeline)->name("CronJobTimeline")->hourly()->timezone("Europe/London");
        $schedule->job(new JobsCronResetFreeze)->name("CronResetFreeze")->everyMinute()->timezone("Europe/London");
        
        $schedule->command('app:job-time-line-update')->everyMinute();
        $schedule->command('app:previous-day-send-notifications')->everyMinute();
        $schedule->command('app:on-day-send-notifications')->everyMinute();
        $schedule->command('app:next-day-send-notifications')->everyMinute();
        $schedule->command('app:job-un-freeze-check')->everyMinute();
        
                $schedule->command('app:locumlogbook-reminder')->dailyAt("09:00")->timezone("Europe/London");

        
        /* Cron Job for Servers
        * * * * * /usr/local/bin/php /home/techrepublic/locumkit.techrepublica.com/artisan schedule:run >> /dev/null 2>&1
         */
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
