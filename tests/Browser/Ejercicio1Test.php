<?php

namespace Tests\Browser;

use App\Models\Size;
use Facebook\WebDriver\WebDriverKeys;
use Gloudemans\Shoppingcart\Cart;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Log;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Spatie\Permission\Models\Role;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\User;
use App\Models\Product;
use App\Models\Brand;
use App\Models\Color;

class Ejercicio1Test extends DuskTestCase
{
    use DatabaseMigrations;

    /*
    * A basic browser test example.
    *
    * @return void
    */
    public function test_ejercicio1_test() {
        $brand = Brand::factory()->create();
        $category = Category::factory()->create([
            'name' => 'categoria',
            'slug' => 'categoria',
            'icon' => 'categoria',
        ]);
        $category->brands()->attach($brand->id);
        $subcategory1 = Subcategory::factory()->create([
            'category_id' => $category->id,
            'name' => 'ropa',
            'slug' => 'ropa',
            'color' => true,
            'size' => true
        ]);
        $colors = ['azul'];
        foreach ($colors as $color) {
            Color::create([
                'name' => $color
            ]);
        }
        $sizes = Size::all();
        foreach ($sizes as $size) {
            $size->colors()
                ->attach([
                    1 => ['quantity' => 10],
                ]);
        }
        $role = Role::create(['name' => 'admin']);
        $usuario = User::factory()->create([
            'name' => 'Rubén',
            'email' => 'algo1234@gmail.com',
            'password' => bcrypt('algo1234')
        ])->assignRole('admin');
        $usuario2 = User::factory()->create([
            'name' => 'aaaaa',
            'email' => 'algo12346@gmail.com',
            'password' => bcrypt('algo12346')
        ]);
        $products = Product::whereHas('subcategory', function (Builder $query) {
            $query->where('color', true)
                ->where('size', false);
        })->get();
        foreach ($products as $product) {
            $product->colors()->attach([
                1 => [
                    'quantity' => 10
                ],
            ]);
        }
        $this->browse(function (Browser $browser) use ($usuario) {

            $browser->loginAs($usuario)
                ->visit('admin')
                ->pause(300)
                ->click('@agregarProducto')
                ->pause(500)
                ->select('@categoria', 1)
                ->pause(250)
                ->select('@subcategoria', 1)
                ->pause(300)
                ->type('@nombre', 'aaaaa')
                ->pause(300)
                ->select('@marca', 1)
                ->pause(250)
                ->type('@precio', 10)
                ->pause(350)
                ->screenshot('1a');
        });
    }
}
