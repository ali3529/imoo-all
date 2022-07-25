<?php

namespace App\Http\Controllers\Api;

use App\Enums\PropertyStatusEnum;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\City;
use App\Models\Country;
use App\Models\Facility;
use App\Models\Feature;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ApiController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('throttle:60,1');
    }

    public function categories(Request $request)
    {
        $categories = Category::all();
        return Response::success('', $categories);
    }

    public function countries(Request $request)
    {
        $countries = Country::all();
        return Response::success('', $countries);
    }

    public function cities(Request $request)
    {
        $cities = City::ofCountry($request->get('countryId', 0))
            ->whereRaw('LOWER(`name`) like ?',
                ['%'.strtolower($request->get('search', '')).'%'])
            ->get();
        return Response::success('', $cities);
    }

    public function features(Request $request)
    {
        $features = Feature::all();
        return Response::success('', $features);
    }

    public function facilities(Request $request)
    {
        $facilities = Facility::all();
        return Response::success('', $facilities);
    }

    public function propertyStatus(Request $request)
    {
        $status = collect(PropertyStatusEnum::toArray(true));
        $items = collect([]);
        $status->each(function ($item, $key) use ($items) {
            $row = array();
            $row['name'] = $item;
            $row['value'] = trans('enums.'.$item);
            $items->push($row);
        });

//        $status->pluck('name');

        return Response::success('', $items->all());
    }

    public function propertyPackages(Request $request)
    {
        $packages = Package::with(['currency'])->get();
        return Response::success('', $packages);
    }
}
