<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DriverTracking implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private $bookingId;
    public $lat;
    public $long;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($bookingId, $lat, $long)
    {
        $this->bookingId = $bookingId;
        $this->lat = $lat;
        $this->long = $long;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel("booking.$this->bookingId");
    }
}
