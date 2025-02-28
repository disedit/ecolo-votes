<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AttendeeCheckinStatusChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The attendee instance.
     *
     * @var \App\Models\Attendee
     */
    public $attendee;

    /**
     * Wheter checking in or out
     *
     * @var \App\Models\Attendee
     */
    public $mode;

    /**
     * Create a new event instance.
     */
    public function __construct($attendee, $mode)
    {
        $this->attendee = $attendee;
        $this->mode = $mode;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('Attendee.Status.' . $this->attendee->id),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'attendee.checked_in_status_changed';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return ['mode' => $this->mode];
    }
}
