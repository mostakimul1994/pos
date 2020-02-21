<?php

namespace App\Utils;

use \Module;
use App\BusinessLocation;
use App\User;
use App\Product;
use App\Transaction;

use \CarbonPeriod;

class ModuleUtil extends Util
{
    /**
     * This function check if superadmin module is enabled or not.
     *
     * @param int $business_id
     *
     * @return boolean
     */
    public static function isInstalledSuperadmin()
    {
        $is_available = Module::has('Superadmin');

        if ($is_available) {
            //Check if installed by checking the system table superadmin_version
            $superadmin_version = \App\System::getProperty('superadmin_version');
            if(empty($superadmin_version)){
                return false;
            } else {
                return true;
            }
        }
      
        return false;
    }

    /**
     * This function check if a business has active subscription packages
     *
     * @param int $business_id
     *
     * @return boolean
     */
    public static function isSubscribed($business_id)
    {
        $is_available = Module::has('Superadmin');

        if ($is_available) {
            $package = \Modules\Superadmin\Entities\Subscription::active_subscription($business_id);
           
            if (empty($package)) {
                return false;
            }
        }
      
        return true;
    }

    /**
     * Returns the name of view used to display for subscription expired.
     *
     * @return string
     */
    public static function expiredResponse($redirect_url = null)
    {

        $response_array = ['success' => 0,
                        'msg' => __(
                            "superadmin::lang.subscription_expired_toastr",
                            ['app_name' => env('APP_NAME'), 'subscribe_url' => action('\Modules\Superadmin\Http\Controllers\SubscriptionController@index')]
                        )
                    ];

        if (request()->ajax()) {
            if (request()->wantsJson()) {
                return $response_array;
            } else {
                return view('superadmin::subscription.subscription_expired_modal');
            }
        } else {
            if (is_null($redirect_url)) {
                return back()
                    ->with('status', $response_array);
            } else {
                return redirect($redirect_url)
                    ->with('status', $response_array);
            }
        }
    }

    /**
     * This function check if a business has available quota for various types.
     *
     * @param string $type
     * @param int $business_id
     *
     * @return boolean
     */
    public static function isQuotaAvailable($type, $business_id)
    {
        $is_available = Module::has('Superadmin');
        
        if ($is_available) {
            $package = \Modules\Superadmin\Entities\Subscription::active_subscription($business_id);

            if (empty($package)) {
                return false;
            }

            //Start
            $start_dt = $package->start_date->toDateTimeString();
            $end_dt = $package->end_date->endOfDay()->toDateTimeString();

            if ($type == 'locations') {
                //Check for available location and max number allowed.
                $max_allowed = isset($package->package_details['location_count']) ? $package->package_details['location_count'] : 0;
                if ($max_allowed == 0) {
                    return true;
                } else {
                    $count = BusinessLocation::where('business_id', $business_id)
                                ->count();
                    if ($count >= $max_allowed) {
                        return false;
                    }
                }
            } elseif ($type == 'users') {
                //Check for available location and max number allowed.
                $max_allowed = isset($package->package_details['user_count']) ? $package->package_details['user_count'] : 0;
                if ($max_allowed == 0) {
                    return true;
                } else {
                    $count = User::where('business_id', $business_id)
                                        ->count();
                    if ($count >= $max_allowed) {
                        return false;
                    }
                }
            } elseif ($type == 'products') {
                $max_allowed = isset($package->package_details['product_count']) ? $package->package_details['product_count'] : 0;

                if ($max_allowed == 0) {
                    return true;
                } else {
                    $count = Product::where('business_id', $business_id)
                            ->whereBetween('created_at', [$start_dt, $end_dt])
                            ->count();
                    if ($count >= $max_allowed) {
                        return false;
                    }
                }
            } elseif ($type == 'invoices') {
                $max_allowed = isset($package->package_details['invoice_count']) ? $package->package_details['invoice_count'] : 0;
                
                if ($max_allowed == 0) {
                    return true;
                } else {
                    $count = Transaction::where('business_id', $business_id)
                            ->where('type', 'purchase')
                            ->whereBetween('created_at', [$start_dt, $end_dt])
                            ->count();
                    if ($count >= $max_allowed) {
                        return false;
                    }
                }
            }
        }

        return true;
    }

    /**
     * This function returns the response for expired quota
     *
     * @param string $type
     * @param int $business_id
     * @param string $redirect_url = null
     *
     * @return \Illuminate\Http\Response
     */
    public static function quotaExpiredResponse($type, $business_id, $redirect_url = null)
    {
        
        if ($type == 'locations') {
            if (request()->ajax()) {
                $count = BusinessLocation::where('business_id', $business_id)
                                ->count();

                if (request()->wantsJson()) {
                    $response_array = ['success' => 0,
                            'msg' => __("superadmin::lang.max_locations", ['count' => $count])
                        ];
                    return $response_array;
                } else {
                    return view('superadmin::subscription.max_location_modal')
                        ->with('count', $count);
                }
            }
        } elseif ($type == 'users') {
            $count = User::where('business_id', $business_id)
                            ->count();

            $response_array = ['success' => 0,
                        'msg' => __("superadmin::lang.max_users", ['count' => $count])
                    ];
            return redirect($redirect_url)
                    ->with('status', $response_array);
        } elseif ($type == 'products') {
            $count = Product::where('business_id', $business_id)
                        ->count();

            $response_array = ['success' => 0,
                        'msg' => __("superadmin::lang.max_products", ['count' => $count])
                    ];
            return redirect($redirect_url)
                    ->with('status', $response_array);
        } elseif ($type == 'invoices') {
            $count = Transaction::where('business_id', $business_id)
                        ->where('type', 'purchase')
                        ->count();

            $response_array = ['success' => 0,
                        'msg' => __("superadmin::lang.max_invoices", ['count' => $count])
                    ];

            if (request()->wantsJson()) {
                return $response_array;
            } else {
                return redirect($redirect_url)
                    ->with('status', $response_array);
            }
        }
    }
}
