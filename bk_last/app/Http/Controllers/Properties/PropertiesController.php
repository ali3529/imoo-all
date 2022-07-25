<?php

namespace App\Http\Controllers\Properties;

use App\Enums\ModerationStatusEnum;
use App\Enums\PaymentMethodEnum;
use App\Enums\PaymentStatusEnum;
use App\Enums\PropertyStatusEnum;
use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Currency;
use App\Models\Package;
use App\Models\Payment;
use App\Models\Property;
use App\Models\Transaction;
use Auth;
use Exception;
use GuzzleHttp\Psr7\Uri;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Payrexx\Models\Request\Gateway;
use Payrexx\Payrexx;
use Payrexx\PayrexxException;
use URL;
use Intervention\Image\ImageManagerStatic as Image;


class PropertiesController extends Controller
{

    public function __construct()
    {
        $this->middleware(['throttle:60,1'])->only('create');
        $this->middleware('auth:api')->only([
            'details', 'update', 'myProperties'
        ]);
    }

    public function create(Request $request)
    {
        try {
            $this->validateCreate($request);

            $user = Auth::user();
            $userId = $user ? $user->id : null;
            $data = [
                'type'              => $request->get('type'),
                'advertising_type'  => $request->get('advertisingType')['value'],
                'name'              => Str::title(($request->get('type').' - '
                        .$request->get('category')['name'])).' - '
                    .$request->get('postCode')['name'],
                'neworedit'         => '0',
                'package'           => '0',
                'never_expired'     => 1,
                'expire_date'       => now()->addDays(30),
                'author_type'       => 'Botble\RealEstate\Models\Account'
                /*Account::class*/,
                'author_id'         => $userId,
                'category_id'       => $request->get('category')['id'],
                'city_id'           => $request->get('postCode')['id'],
                'moderation_status' => ModerationStatusEnum::PENDING,
                'status'            => $request->get('type') == 'sale'
                    ? PropertyStatusEnum::SELLING
                    : PropertyStatusEnum::RENTING
            ];
            $property = Property::create($data);

            //reference
            $property->makeReference();

            return Response::success('', $property);
        } catch (Exception $exception) {
            return Response::error($exception);
        }
    }

    public function details(Request $request)
    {
        $user = $request->user();
        $property = Property::with([
            'city', 'category', 'features', 'facilities'
        ])
            ->where('reference', $request->get('reference', '_'))
            ->where(function ($q) use ($user) {
                $q->whereNull('author_id')
                    ->orWhere('author_id', $user->id);
            })
            ->first();

        if ($property && !$property->author_id) {
            $property->author_id = $user->id;
            $property->save();
        }

        return Response::success('', $property);
    }

    public function update(Request $request)
    {
        $user = $request->user();
        if (!$user->confirmed_at) {
            return Response::error(trans('messages.not_email_verified'));
        }
        $property = Property::find($request->get('id', 0));

        if (!$property || $property->author_id != $user->id) {
            return Response::error(trans('messages.error_occurred'));
        }

        if ($property->payed) {
            $property->update($request->only('status'));
            return Response::success('update status');
        }

        $this->validateUpdate($request);

        $currency = Currency::where('symbol', '=', 'CHF')->first();

        $request->merge(['currency_id' => $currency->id]);

        $property->update($request->all());

        $property->features()->sync($request->get('features', []));

//        $property->facilities()->sync($request->get('facilities', []));
        $property->facilities()->detach();
        $property->facilities()->attach($request->get('facilities', []));
        return Response::success('');
    }

    public function updateFiles(Request $request)
    {
        $user = $request->user();
        if (!$user->confirmed_at) {
            return Response::error(trans('messages.not_email_verified'));
        }
        $property = Property::find($request->get('id', 0));

        if (!$property || $property->author_id != $user->id) {
            return Response::error(trans('messages.error_occurred'));
        }

        $this->validateFiles($request);

//        $videos = $request->get('videos', []);
        $images = $request->get('images', []);
        if ($request->hasfile('pFiles')) {
            $images = array_merge($images,
                $this->saveFile($request->file('pFiles')));
        }
        if ($request->hasfile('pVideos')) {
            $images = array_merge($images,
                $this->saveFile($request->file('pVideos'), 'videos'));
        }
        if ($request->hasfile('pImages')) {
            $images = array_merge($images,
                $this->saveFile($request->file('pImages'), 'images', true));
        }

        $request->merge([
            'images' => $images/*, 'videos' => $videos*/
        ]);

        $property->update($request->all());
        return Response::success('');
    }

    public function packageUpdate(Request $request)
    {
        $user = $request->user();
        $property = Property::where('reference', '=',
            $request->get('reference', '-'))->first();

        if (!$property || $property->author_id != $user->id) {
            return Response::error(trans('messages.error_occurred'));
        }
        //
        $p = Package::find($request->get('packageId', 0));
//        $user->packages()->attach($p);
        $property->package = $p->id;
        $property->save();
        return Response::success('', $p);
    }

    public function propertyPackage(Request $request)
    {
        $user = $request->user();
        $property = Property::where('reference', '=',
            $request->get('reference', '-'))->first();

        if (!$property || $property->author_id != $user->id) {
            return Response::error(trans('messages.error_occurred'));
        }

        $package = $property->package()->first();
        return Response::success('', $package);
    }

    public function publish(Request $request)
    {
        $user = $request->user();
        $property = Property::where('reference', '=',
            $request->get('reference', '-'))->first();

        if (!$property || $property->author_id != $user->id) {
            return Response::error(trans('messages.error_occurred'));
        }

        $package = $property->package()->first();
        $package->load('currency');
        if ($package) {

            $payment = new Payment();
            $payment->amount = $package->price;
            $payment->currency = $package->currency->symbol;
            $payment->user_id = $user->id;
            $payment->payment_channel = PaymentMethodEnum::PAYREXX();
            $payment->status = PaymentStatusEnum::PENDING();
            $payment->property_id = $property->id;
            $payment->save();

            $transaction = new Transaction;
            $transaction->credits = 1;
            $transaction->account_id = $user->id;
            $transaction->type = 'add';
            $transaction->payment_id = $payment->id;
            $transaction->save();

            if ($package->price != 0) {
                $payrexx = new Payrexx(config('services.payrexx.instance'),
                    config('services.payrexx.key'));
                $gateway = $this->createPayrexxInvoice($property, $user,
                    $payment->id);

                try {
                    $response = $payrexx->create($gateway);
                    $payment->charge_id = $response->getHash();
                    $payment->order_id = $response->getId();
                    $payment->save();
//                var_dump($response);
//                $user->packages()->attach($package);

                    return Response::success('', [
                        'url'    => $response->getLink(),
                        'status' => $response->getStatus()
                    ]);


                } catch (PayrexxException $e) {
                    return Response::error($e->getMessage());
                }
            } else {
                $payment->status = PaymentStatusEnum::COMPLETED;
                $payment->save();
                $property = $payment->property()->first();
                return Response::success('', [
                    'url'    => $property->reference,
                    'status' => 'free'
                ]);
            }
        }
        return Response::error(trans('messages.package_not_find'));
    }

    public function myProperties(Request $request)
    {
        $user = $request->user();
        $properties = $user->properties()
            ->orderBy('created_at', 'desc')->get();

        return Response::success('', $properties);
    }

    public function delete(Request $request, Property $property)
    {
        $delete = $property->delete();
        if ($delete) {
            return Response::success('');
        }
        return Response::error(trans('messages.error_occurred'));
    }

    protected function validateCreate(Request $request)
    {
        $request->validate([
            'type'            => 'required',
            'category'        => 'required',
            'country'         => 'required',
            'postCode'        => 'required',
            'advertisingType' => 'required',
            /*
                        'name'              => 'required',
                        'description'       => 'max:300|required',
                        'content'           => 'min:10|max:300|required',
                        'number_bedroom'    => 'numeric|min:0|max:10000|required',
                        'consent_protection' => 'required',
                        'package'            => 'required',

                        // edited  v2
                        'additional_costs'  => 'nullable',
                        'net_rent'  => 'nullable',
                        'contact_name'  => 'max:200',
                        //edited  v2
                        'post_code' => 'numeric|min:1000|max:9999|required',
                        'house_number' => 'numeric|min:0|required',
                        'house_name' => 'required|required',
                        'gross_rent' => 'numeric|min:0',
                        'garage' => 'numeric|min:0|required',
                        'parking_space' => 'numeric|min:0|required',
                        'contact_phone_number' => 'numeric|required',
                        // End Edited v2


                        /// Edited V.1  nullable
                        'square'       => 'numeric|min:1|required',
                        'number_wc'       => 'numeric|min:0|max:1000|required',
                        'construction_year'=> 'numeric|min:1600|max:2021|required',
                        'last_renovation'=> 'numeric|min:2000|max:2021|nullable',
                        'last_reconstruction'=> 'numeric|min:2000|max:2021|nullable',
                        'available_date'  => 'max:100',
                        //END Edited

                        'number_bathroom'   => 'numeric|min:0|max:10000|required',
                        'number_floor'      => 'numeric|min:0|max:10000|required',
                        'price'             => 'numeric|min:0|required',
                        'status'            => Rule::in(PropertyStatusEnum::values()),
                        'moderation_status' => Rule::in(ModerationStatusEnum::values()),*/
        ]);
    }

    protected function validateUpdate(Request $request)
    {
        $request->validate([
            'name'                => 'required',
            'description'         => 'max:300|required',
            'content'             => 'min:10|max:300|required',
            'number_bedroom'      => 'numeric|min:0|max:10000|required',
//            'consent_protection'   => 'required',
            'package'             => 'required',

            // edited  v2
            'additional_costs'    => 'nullable',
            'net_rent'            => 'nullable',
//            'contact_name'         => 'max:200',

            //edited  v2
//            'post_code'            => 'numeric|min:1000|max:9999|required',
//            'house_number'         => 'numeric|min:0|required',
//            'house_name'           => 'required|required',
//            'gross_rent'           => 'numeric|min:0',
//            'garage'               => 'numeric|min:0|required',
//            'parking_space'        => 'numeric|min:0|required',
//            'contact_phone_number' => 'numeric|required',
            // End Edited v2


            /// Edited V.1  nullable
            'square'              => 'numeric|min:1|required',
            'number_wc'           => 'numeric|min:0|max:1000|required',
            'construction_year'   => 'numeric|min:1600|max:2021|required',
            'last_renovation'     => 'numeric|min:2000|max:2021|nullable',
            'last_reconstruction' => 'numeric|min:2000|max:2021|nullable',
            'available_date'      => 'max:100',
            //END Edited

            'number_bathroom' => 'numeric|min:0|max:10000|required',
            'number_floor'    => 'numeric|min:0|max:10000|required',
//            'price'             => 'numeric|min:0|required',
            'status'          => 'required|'
                .Rule::in(PropertyStatusEnum::values()),
//            'moderation_status' => Rule::in(ModerationStatusEnum::values())
            ////
//            'Location'             => 'required'

        ]);
    }

    protected function validateFiles(Request $request)
    {
        $request->validate([
            'pFiles.*'  => 'mimetypes:application/pdf|max:8192',
            'pImages.*' => 'max:50192|mimes:jpeg,jpg,png',
            'pVideos.*' => 'mimetypes:video/mp4|max:81920',

        ], [], [
            'pFiles.*'  => trans('validation.pdfs'),
            'pImages.*' => trans('validation.photos'),
            'pVideos.*' => trans('validation.videos'),
        ]);
    }

    private function saveFile($files, $path = 'files', $isImage = false)
    {
        $collect = collect();
        $url = URL::to('/');
        foreach ($files as $file) {
            $fileName = time().rand(0, 1000);
            $fileName150x150 = $fileName.'-150x150.'
                .$file->getClientOriginalExtension();
            $fileName410x270 = $fileName.'-410x270.'
                .$file->getClientOriginalExtension();
            $fileName = $fileName.'.'
                .Str::lower($file->getClientOriginalExtension());
            $image_resize = Image::make($file->getRealPath());
            $file->move(public_path().'/properties/'.$path, $fileName);
            $collect->push($url.'/properties/'.$path.'/'.$fileName);
            if ($isImage) {
                $image_resize->resize(150, 150);
                $image_resize->save(public_path('/properties/'
                    .$path.'/'.$fileName150x150));
                $image_resize->resize(410, 270);
                $image_resize->save(public_path('/properties/'
                    .$path.'/'.$fileName410x270));
            }
        }
        return $collect->all();
    }

    private function createPayrexxInvoice(
        Property $property,
        Account $account,
        $paymentId
    ) {

        $gateway = new Gateway();
        $package = $property->package()->first();
        $amount = $package->price;

        // amount multiplied by 100
        $gateway->setAmount($amount * 100);

        // VAT rate percentage (nullable)
//        $gateway->setVatRate(7.70);


        //Product SKU
        $gateway->setSku($package->name);


        // currency ISO code
        $gateway->setCurrency($package->currency->symbol);


        //success and failed url in case that merchant redirects to payment site instead of using the modal view
        $gateway->setSuccessRedirectUrl(config('app.url')
            ."/api/v1/payrexx/callback/success?c=".$paymentId);
        $gateway->setFailedRedirectUrl(config('app.url')
            ."/api/v1/payrexx/callback/failed?c=".$paymentId);
        $gateway->setCancelRedirectUrl(config('app.url')
            ."/api/v1/payrexx/callback/cancel?c=".$paymentId);

        // optional: payment service provider(s) to use (see http://developers.payrexx.com/docs/miscellaneous)
        // empty array = all available psps
        $gateway->setPsp([config('services.payrexx.psp')]);
        //$gateway->setPsp(array(4));
        //$gateway->setPm(['mastercard']);

        // optional: whether charge payment manually at a later date (type authorization)
        $gateway->setPreAuthorization(false);

        // optional: if you want to do a pre authorization which should be charged on first time
        //$gateway->setChargeOnAuthorization(true);

        // optional: whether charge payment manually at a later date (type reservation)
        $gateway->setReservation(false);


        // subscription information if you want the customer to authorize a recurring payment.
        // this does not work in combination with pre-authorization payments.
        //$gateway->setSubscriptionState(true);
        //$gateway->setSubscriptionInterval('P1M');
        //$gateway->setSubscriptionPeriod('P1Y');
        //$gateway->setSubscriptionCancellationInterval('P3M');

        // optional: reference id of merchant (e. g. order number)
        $gateway->setReferenceId($property->reference);
        $gateway->setId($paymentId);
        //$gateway->setValidity(5);
        //$gateway->setLookAndFeelProfile('144be481');

        // optional: add contact information which should be stored along with payment
        $gateway->addField($type = 'email', $value = $account->email);

        return $gateway;
    }
}
