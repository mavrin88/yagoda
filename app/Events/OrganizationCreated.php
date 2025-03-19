<?php

    namespace App\Events;

    use App\Models\Organization;
    use Illuminate\Broadcasting\Channel;
    use Illuminate\Queue\SerializesModels;
    use Illuminate\Broadcasting\InteractsWithSockets;
    use Illuminate\Foundation\Events\Dispatchable;
    use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

    class OrganizationCreated implements ShouldBroadcast
    {
        use Dispatchable, InteractsWithSockets, SerializesModels;

        public $organization;

        public function __construct(Organization $organization)
        {
            $this->organization = $organization;
        }

        public function broadcastOn()
        {
            return new Channel('organizations');
        }

        public function broadcastAs()
        {
            return 'OrganizationCreated';
        }
    }
