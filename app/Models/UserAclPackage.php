<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAclPackage extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'price',
        'description',
        'user_acl_package_resources_ids_list',
    ];

    static public function getPaidPackagePrices(): array
    {
        $packages = UserAclPackage::query()->where("price", ">", 0)->select("name", "price")->get();
        $gold_price = 0;
        $silver_price = 0;
        $bronze_price = 0;

        foreach ($packages as $package) {
            if (strtolower($package->name) == "gold") {
                $gold_price = set_amount_format($package->price) . "*";
            } elseif (strtolower($package->name) == "silver") {
                $silver_price = set_amount_format($package->price);
            } elseif (strtolower($package->name) == "bronze") {
                $bronze_price = set_amount_format($package->price);
            }
        }

        return [
            "gold_price" => $gold_price,
            "silver_price" => $silver_price,
            "bronze_price" => $bronze_price,
        ];
    }
}
