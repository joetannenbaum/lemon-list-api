<?php

namespace App\Console\Commands;

use App\Jobs\AutoCategorizeItem;
use App\Models\User;
use Illuminate\Console\Command;

class AuthCategorizeAllItems extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'categorize:all-items';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        User::with('items')->find([1, 6])->each(
            fn ($u) => $u->items->each(
                fn ($i, $index) => dispatch(new AutoCategorizeItem($i))->delay(now()->addSeconds($index + 1))
            )
        );

        return Command::SUCCESS;
    }
}
