<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\SearchUrl;
use App\Models\Product;

class ShowProductList extends Command
{
    protected $signature = 'app:show-product-list {--url= : url of products}';

    protected $description = 'Command that allows given a url to show all the products we have stored in our database';

    public function handle(){
        try{
            //Obtenemos la url del usuario y verificamos que la variable url contenga los datos, si no hacemos saltar un error
            $url = $this->option('url');

            if (!$url) {
                $this->error('You must provide a --url option');
                return;
            }

            $this->getData($url);

        } catch (\Exception $e) {
            error_log('Error ' . $e->getMessage());
        }
    }

    protected function getData($URL){   
        try{
            //Buscamos si esta buscaqueda existe en la base de datos
            $searchUrl = SearchUrl::where('searchURL', $URL)->first();

            if ($searchUrl) {

                $this->info("A recent search has been found with this url!");

                //Buscamos los productos asociados a esta busqueda
                $products = Product::where('searchurl_id', $searchUrl->id)->get();
                
                //printamos los resultados
                foreach ($products as $product) {
                    $this->info(json_encode($product, JSON_PRETTY_PRINT) . "\n");
                }

            } else {
                //Si no se ha encontrado la busqueda se avisa al usaurio.
                $this->info("No search has been done with this url yet.");
            }
        } catch (\Exception $e) {
            error_log('Error' . $e->getMessage());
        }
    }
}
 