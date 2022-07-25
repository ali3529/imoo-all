<?php

namespace App\Notifications;

//use App\Channel\NotificationChannel;
//use App\Channel\SMSChannel;
use App\Models\Account;
use Carbon\Carbon;
use Illuminate\Auth\Notifications\VerifyEmail as Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\URL;

//use NotificationChannels\Fcm\FcmMessage;
//use NotificationChannels\Fcm\Resources\AndroidConfig;
//use NotificationChannels\Fcm\Resources\AndroidFcmOptions;
//use NotificationChannels\Fcm\Resources\AndroidNotification;
//use NotificationChannels\Fcm\Resources\ApnsConfig;
//use NotificationChannels\Fcm\Resources\ApnsFcmOptions;

class VerifySms extends Notification implements ShouldQueue
{
    use Queueable;

    private $message = '';
    private $sendByEmail = false;

    public function __construct($message, $viaEmail = false)
    {
        $this->message = $message;
        $this->sendByEmail = $viaEmail;
        //
    }


    /**
     * Get the notification's delivery channels.
     *
     * @param  Account  $user
     *
     * @return array
     */
    public function via($user)
    {
        $via = [];
        if ($user->phone) {
//            $via[] = SMSChannel::class;
//            $via[] = NotificationChannel::class;
        }

        if ($user->email && filter_var($user->email, FILTER_VALIDATE_EMAIL)
            && $this->sendByEmail
        ) {
            $via[] = 'mail';
        }

//        if ($user->FCMToken && $user->FCMToken != 'undefined') {
//            $via[] = FcmChannel::class;
//        }

        return $via;
    }


    /**
     * Send Push Notification
     *
     * @param  Account  $user
     *
//     * //     * @return FcmMessage
     */
    public function toFcm($notifiable)
    {
//        return FcmMessage::create()
//            // ->setData(['data1' => 'value', 'data2' => 'value2'])
//            ->setNotification(\NotificationChannels\Fcm\Resources\Notification::create()
//                ->setTitle('')
//                ->setBody()
//            // ->setImage('http://example.com/url-to-image-here.png')
//            )->setAndroid(
//                AndroidConfig::create()
//                    ->setFcmOptions(AndroidFcmOptions::create()
//                        ->setAnalyticsLabel('analytics'))
//                    ->setNotification(AndroidNotification::create()
//                        ->setColor('#0A0A0A'))
//            )->setApns(
//                ApnsConfig::create()
//                    ->setFcmOptions(ApnsFcmOptions::create()
//                        ->setAnalyticsLabel('analytics_ios')));
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  Account  $user
     *
     * @return MailMessage
     */
    public function toMail($user)
    {
        $greeting = 'Dear '.$user->getFullName();

        return (new MailMessage)
            ->greeting($greeting)
            ->salutation(config('app.name'))
            ->subject('activation code')
            ->line('Verification code :'.$this->message);
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  Account  $user
     *
     * //     * @return string
     */
    public function toSMS($user)
    {
        //        $basic  = new \Nexmo\Client\Credentials\Basic('Nexmo key', 'Nexmo secret');
//        $client = new \Nexmo\Client($basic);

//       return $message = $client->message()->send([
//            'to' => '9197171****',
//            'from' => 'John Doe',
//            'text' => 'A simple hello message sent from Vonage SMS API'
//        ]);

    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     *
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }


    /**
     * @param $user
     *
     * @return string[]
     */
    public function toNotification($user)
    {
        return [
        ];
    }

}
