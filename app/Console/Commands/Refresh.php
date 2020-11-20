<?php

namespace App\Console\Commands;

use App\Models\ShoppingList;
use App\Models\Store;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class Refresh extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'refresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->call('migrate:refresh');
        $this->call('passport:client', [
            '--password' => true,
            '--name' => 'Password Grant Client',
            '--provider' => 'users',
        ]);

        DB::table('oauth_clients')->where('id', 1)->update(['secret' => 'rT6WXpKEHp3Kg05BDLizezpi6f96PGb9C3mTrfiL']);

        $user = User::create([
            'name'     => 'Joe Tannenbaum',
            'email'    => 'joe@joe.codes',
            'password' => Hash::make('asdfasdf'),
        ]);

        $list = ShoppingList::make([
            'name' => 'Grocery List',
        ]);

        $user->shoppingLists()->save($list);

        $store = Store::make([
            'name' => 'Westside Market',
        ]);

        $user->stores()->save($store);
    }
}
