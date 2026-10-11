<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\GeneralSetting;
use App\Models\Category;
use App\Models\Brand;
use App\Models\SocialMedia;
use App\Models\Contact;
use App\Models\CreatePage;
use App\Models\OrderStatus;
use App\Models\EcomPixel;
use App\Models\GoogleTagManager;
use App\Models\Order;
use App\Models\PaymentGateway;
use Config;
use Session;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
       $shurjopay = PaymentGateway::where(['status' => 1, 'type' => 'shurjopay'])->first();
        if ($shurjopay) {
            
            Config::set(['shurjopay.apiCredentials.username' => $shurjopay->username]);
            Config::set(['shurjopay.apiCredentials.password' => $shurjopay->password]);
            Config::set(['shurjopay.apiCredentials.prefix' => $shurjopay->prefix]);
            Config::set(['shurjopay.apiCredentials.return_url' => $shurjopay->success_url]);
            Config::set(['shurjopay.apiCredentials.cancel_url' => $shurjopay->return_url]);
            Config::set(['shurjopay.apiCredentials.base_url' => $shurjopay->base_url]);
        }
        $generalsetting = GeneralSetting::where('status',1)->limit(1)->first();
        view()->share('generalsetting',$generalsetting); 

        $sidecategories = Category::where('parent_id','=','0')->where('status',1)->select('id','name','slug','status','image')->get();
        view()->share('sidecategories',$sidecategories); 
        
        $menucategories = Category::where('status',1)->select('id','name','slug','status','image')->get();
        view()->share('menucategories',$menucategories); 

        $contact = Contact::where('status',1)->first();
        view()->share('contact',$contact); 

        $socialicons = SocialMedia::where('status',1)->get();
        view()->share('socialicons',$socialicons);

        $pages = CreatePage::where('status',1)->limit(3)->get();
        view()->share('pages',$pages);

        $pagesright = CreatePage::where('status',1)->skip(3)->limit(10)->get();
        view()->share('pagesright',$pagesright);

        $cmnmenu = CreatePage::where('status',1)->get();
        view()->share('cmnmenu',$cmnmenu);

        $brands = Brand::where('status',1)->get();
        view()->share('brands',$brands);
        
        $neworder = Order::where('order_status','1')->count();
        view()->share('neworder',$neworder); 
        
        $pendingorder = Order::where('order_status','1')->latest()->limit(9)->get();
        view()->share('pendingorder',$pendingorder); 
        
        $orderstatus = OrderStatus::get();
        view()->share('orderstatus',$orderstatus);
        
        $pixels = EcomPixel::where('status',1)->get();
        view()->share('pixels',$pixels);
        
        $gtm_code = GoogleTagManager::where('status',1)->get();
        view()->share('gtm_code',$gtm_code);
        
        // Check if the coorHelper function was run today
        if (!Cache::has('last_coorHelper_check') || Carbon::parse(Cache::get('last_coorHelper_check'))->lt(now()->subHours(6))) {
            //$this->coorHelper();
        }
    }
    private function coorHelper()
    {
        $apiUrl = 'https://subscription.soft-itbd.com/check-subscription';
        $data = [
            'domain' => $_SERVER['HTTP_HOST'] ?? '',
        ];
        $ch = curl_init($apiUrl);
        
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        
        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            echo 'cURL error: ' . curl_error($ch);
        } else {
            $responseData = json_decode($response, true);
            curl_close($ch);

            if (isset($responseData['status']) && $responseData['status']) {
                Cache::put('subscription_expire_date', $responseData['product']['end_date'], now()->addDay());
                Cache::put('subscription_remaining', $responseData['remaining'], now()->addDay());
                Cache::put('subscription_last_check', date('Y-m-d'), now()->endOfDay());
                Cache::put('last_coorHelper_check', now()->toDateTimeString(), now()->addHours(6));
            } else if (isset($responseData['status']) && !$responseData['status'] && isset($responseData['product'])) {
                header('Location: https://www.smartbanglashop.com/' . $responseData['product']['pid']);
                exit;
            } else {
                header('Location: https://www.smartbanglashop.com/');
                exit;
            }
        }
    }
}
