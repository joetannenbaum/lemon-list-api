<?php

namespace App\Events;

use App\Models\ShoppingList;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ShoppingListUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $list;

    public $user;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(ShoppingList $list, User $user)
    {
        $this->list = $list;
        $this->user = $user;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel(sprintf('shopping-list-%d', $this->list->id));
    }

    public function broadcastWhen()
    {
        return $this->list->is_shared;
    }
}
