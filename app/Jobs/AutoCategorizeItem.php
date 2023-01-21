<?php

namespace App\Jobs;

use App\Models\Item;
use App\Models\ShoppingList;
use App\Models\ShoppingListItem;
use App\Models\ShoppingListVersion;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use OpenAI\Laravel\Facades\OpenAI;

class AutoCategorizeItem implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(public Item $item)
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->item->load('users');

        $toSync = collect();
        $suggestedTags = collect();

        ray()->showQueries();

        $shoppingListUsers = $this->getUsersFromActiveShoppingLists();

        $users = $this->item->users->concat($shoppingListUsers)->unique('id');

        $users->load('stores.tags');

        $users->each(function ($user) use ($toSync, $suggestedTags) {
            $user->stores->each(function ($store) use ($toSync, $suggestedTags, $user) {
                $tags = $store->tags->pluck('name');
                $tagList = $tags->map(fn ($t) => str_replace('&', '/', $t))->join(', ');

                $existingItemStoreTags = DB::table('item_store_tag')
                    ->where('item_id', $this->item->id)
                    ->whereIn('store_tag_id', $store->tags->pluck('id'))
                    ->exists();

                if ($existingItemStoreTags) {
                    ray('found existing item store tag, skipping');
                    return;
                }

                ray($tags)->label('tags');

                ray($suggestedTags)->label('suggested tags');

                $existingTag = $store->tags->first(fn ($t) => $suggestedTags->contains(strtolower($t->name)));

                ray($existingTag)->label('existing tag');

                if ($existingTag) {
                    ray('found existing tag');
                    $toSync->offsetSet($existingTag->id, ['user_id' => $user->id]);
                    return;
                }

                $prompt = "given the following categories, what is the best fit for \"{$this->item->name}\": {$tagList}";

                ray($prompt)->label('prompt');

                $result = OpenAI::completions()->create([
                    'model'  => 'text-davinci-003',
                    'prompt' => $prompt,
                ]);

                ray($result)->label('result');

                $suggestedTag = collect(explode("\n", $result['choices'][0]['text']))->map(fn ($t) => strtolower(trim($t, ' .')))->last();

                ray($suggestedTag)->label('suggested tag');

                $existingTag = $store->tags->first(fn ($t) => strtolower($t->name) === $suggestedTag);

                ray($existingTag)->label('existing tag from suggested');

                $toSync->offsetSet($existingTag->id, ['user_id' => $user->id]);

                $suggestedTags->push($suggestedTag);
            });
        });

        if ($toSync->isNotEmpty()) {
            $this->item->storeTags()->sync($toSync);
        }
    }

    protected function getUsersFromActiveShoppingLists()
    {


        $shoppingListVersionIds = ShoppingListItem::where('item_id', $this->item->id)->pluck('shopping_list_version_id');

        ray($shoppingListVersionIds);

        if (!$shoppingListVersionIds->count()) {
            return collect();
        }

        $shoppingListIds = ShoppingListVersion::whereIn('id', $shoppingListVersionIds)->pluck('shopping_list_id');

        if (!$shoppingListIds->count()) {
            return collect();
        }

        return ShoppingList::with('users')->whereIn('id', $shoppingListIds)->get()->map(fn ($s) => $s->users)->flatten();
    }
}
