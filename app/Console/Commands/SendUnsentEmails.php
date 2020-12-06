<?php

namespace App\Console\Commands;

use App\Helpers\{SendEmailHelpers, SettingsHelpers};
use App\Models\{ReadySent, Schedule, Subscribers};
use Illuminate\Console\Command;

class SendUnsentEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:unsent';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send unsent emails to subscribers';

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
     * @throws \PHPMailer\PHPMailer\Exception
     */
    public function handle()
    {
        @set_time_limit(0);

        $fh = fopen(__FILE__, 'r');

        if (!flock($fh, LOCK_EX | LOCK_NB)) {
            exit('Script is already running');
        }

        $this->line('start unsent sending emails');

        $mailcountno = 0;
        $mailcount = 0;

        $schedule = Schedule::where('value_from_start_date', '<=', date('Y-m-d H:i:s'))
            ->where('value_from_end_date', '>=', date('Y-m-d H:i:s'))
            ->get();

        foreach ($schedule as $row) {

            $order = SettingsHelpers::getSetting('RANDOM_SEND') == 1 ? 'RAND()' : 'subscribers.id';
            $limit = SettingsHelpers::getSetting('LIMIT_SEND') == 1 ? SettingsHelpers::getSetting('LIMIT_NUMBER') : null;

            switch (SettingsHelpers::getSetting('INTERVAL_TYPE')) {
                case "minute":
                    $interval = "(subscribers.timeSent < NOW() - INTERVAL '" . SettingsHelpers::getSetting('INTERVAL_NUMBER') . "' MINUTE)";
                    break;
                case "hour":
                    $interval = "(subscribers.timeSent < NOW() - INTERVAL '" . SettingsHelpers::getSetting('INTERVAL_NUMBER') . "' HOUR)";
                    break;
                case "day":
                    $interval = "(subscribers.timeSent < NOW() - INTERVAL '" . SettingsHelpers::getSetting('INTERVAL_NUMBER') . "' DAY)";
                    break;
                default:
                    $interval = null;
            }

            if ($interval) {
                $subscribers = Subscribers::select(['subscribers.email', 'subscribers.id', 'subscribers.token', 'subscribers.name'])
                    ->join('subscriptions', 'subscribers.id', '=', 'subscriptions.subscriberId')
                    ->join('schedule_category', function ($join) use ($row) {
                        $join->on('subscriptions.categoryId', '=', 'schedule_category.categoryId')
                            ->where('schedule_category.scheduleId', '=', $row->id);
                    })
                    ->leftJoin('ready_sent', function ($join) use ($row) {
                        $join->on('subscribers.id', '=', 'ready_sent.subscriberId')
                            ->where('ready_sent.scheduleId', '=', $row->id)
                            ->where(function ($query) {
                                $query->where('ready_sent.success', '=', 0);
                            });
                    })
                    ->whereNull('ready_sent.subscriberId')
                    ->where('subscribers.active', '=', 1)
                    ->whereRaw($interval)
                    ->groupBy('subscribers.id')
                    ->groupBy('subscribers.email')
                    ->groupBy('subscribers.token')
                    ->groupBy('subscribers.name')
                    ->orderByRaw($order)
                    ->take($limit)
                    ->get();
            } else {
                $subscribers = Subscribers::select(['subscribers.email', 'subscribers.id', 'subscribers.token', 'subscribers.name'])
                    ->join('subscriptions', 'subscribers.id', '=', 'subscriptions.subscriberId')
                    ->join('schedule_category', function ($join) use ($row) {
                        $join->on('subscriptions.categoryId', '=', 'schedule_category.categoryId')
                            ->where('schedule_category.scheduleId', '=', $row->id);
                    })
                    ->leftJoin('ready_sent', function ($join) use ($row) {
                        $join->on('subscribers.id', '=', 'ready_sent.subscriberId')
                            ->where('ready_sent.scheduleId', '=', $row->id)
                            ->where(function ($query) {
                                $query->where('ready_sent.success', '=', 0);
                            });
                    })
                    ->whereNull('ready_sent.subscriberId')
                    ->where('subscribers.active', 1)
                    ->groupBy('subscribers.id')
                    ->groupBy('subscribers.email')
                    ->groupBy('subscribers.token')
                    ->groupBy('subscribers.name')
                    ->orderByRaw($order)
                    ->take($limit)
                    ->get();
            }

            foreach ($subscribers as $subscriber) {
                if (SettingsHelpers::getSetting('sleep') > 0)
                    sleep(SettingsHelpers::getSetting('sleep'));

                SendEmailHelpers::setBody($row->template->body);
                SendEmailHelpers::setSubject($row->template->name);
                SendEmailHelpers::setPrior($row->template->prior);
                SendEmailHelpers::setEmail($subscriber->email);
                SendEmailHelpers::setToken($subscriber->token);
                SendEmailHelpers::setSubscriberId($subscriber->id);
                SendEmailHelpers::setName($subscriber->name);

                $result = SendEmailHelpers::sendEmail($row->templateId);

                if ($result['result'] === true) {
                    ReadySent::where('scheduleId', $row->id)->update(['success' => 1]);
                    Subscribers::where('id', $subscriber->id)->update(['timeSent' => date('Y-m-d H:i:s')]);

                    $mailcount++;
                } else {
                    $mailcountno++;
                }

                if (SettingsHelpers::getSetting('LIMIT_SEND') == 1 && SettingsHelpers::getSetting('LIMIT_NUMBER') == $mailcount) {
                    break;
                }
            }

            if (SettingsHelpers::getSetting('LIMIT_SEND') == 1 && SettingsHelpers::getSetting('LIMIT_NUMBER') == $mailcount) {
                break;
            }
        }

        $this->line("sent: " . $mailcount);
        $this->line("no sent: " . $mailcountno);
    }
}
