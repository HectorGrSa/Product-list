<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

//Declaramos la tabla "Product", que este contendra los datos de los productos.
class Product extends Model
{
    protected $fillable = ['title', 'price', 'image_url', 'data_link', 'searchurl_id','company_id', 'category_id'];
    

    //Asociamos "Product" con la tabla "searchURL"
    public function searchurl()
    {
        return $this->belongsTo(Searchurl::class);
    }
        

    //Asociamos "Product" con la tabla "Company"
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    
    //Asociamos "Product" con la tabla "Category"
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}