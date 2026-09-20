<?php

namespace App\Console\Commands;

use App\Models\UserModel\CallRequest;
use App\Models\UserModel\ChatRequest;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CallChatDeleteCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'call-chat:delete';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Call Chat Delete started';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        ChatRequest::where('chatStatus', 'Pending')->whereNotNull('validated_till')->where('validated_till','<',Carbon::now())->delete();
        CallRequest::where('callStatus', 'Pending')->whereNotNull('validated_till')->where('validated_till','<',Carbon::now())->delete();

    }
}
