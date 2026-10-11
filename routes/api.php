<?php

use App\Http\Controllers\Ai\ChatbotController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\FrontendController;


Route::group(['namespace' => 'Api','prefix'=>'v1','middleware' => 'api'], function(){
    
     Route::get('app-config', [FrontendController::class, 'appconfig']);
     Route::get('slider', [FrontendController::class, 'slider']);
     Route::get('category-menu', [FrontendController::class, 'categorymenu']);
     Route::get('hotdeal-product', [FrontendController::class, 'hotdealproduct']);
     Route::get('homepage-product', [FrontendController::class, 'homepageproduct']);
     Route::get('footer-menu-left', [FrontendController::class, 'footermenuleft']);
     Route::get('footer-menu-right', [FrontendController::class, 'footermenuright']);
     Route::get('social-media', [FrontendController::class, 'socialmedia']);
     Route::get('contactinfo', [FrontendController::class, 'contactinfo']);
     Route::get('product_details/{slug}', [FrontendController::class, 'productDetails']);
     Route::get('product_review/{slug}', [FrontendController::class, 'product_review']);
     Route::get('all_review', [FrontendController::class, 'all_review']);
     
    //  Home Page Api End =================================
    
    Route::get('category/{id}', [FrontendController::class, 'catproduct']);
    

});


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


//n8n
Route::post('/chatbot/message', [ChatbotController::class, 'message']);
Route::post('/chatbot/product-search', [ChatbotController::class, 'productSearch']);
Route::post('/chatbot/order-save', [ChatbotController::class, 'apiOrderSave']);
