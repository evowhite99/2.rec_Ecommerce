<?php

namespace Tests\Browser;

use App\Models\Order;
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
use App\Models\Image;
use App\Models\Product;
use App\Models\Brand;

class Ejercicio2Test extends DuskTestCase
{
    use DatabaseMigrations;

    public $order;

    /*
    * A basic browser test example.
    *
    * @return void
    */
    public function test_ejericio2_test() {
        $brand = Brand::factory()->create();
        $category = Category::factory()->create([
            'name' => 'categoria',
            'slug' => 'categoria',
            'icon' => 'categoria',
        ]);
        $category->brands()->attach($brand->id);
        $subcategory2 = Subcategory::factory()->create([
            'category_id' => $category->id,
            'name' => 'tele',
            'slug' => 'tele',
        ]);
        $p2 = Product::factory()->create([
            'subcategory_id' => $subcategory2->id,
            'quantity' => 2,
            'name' => 'algo2',
            'slug' => 'algo2',
            'price' => 40,
        ]);
        Image::factory()->create([
            'imageable_id' => $p2->id,
            'imageable_type' => Product::class
        ]);
        $role = Role::create(['name' => 'admin']);
        $usuario = User::factory()->create([
            'name' => 'Rubén',
            'email' => 'algo1234@gmail.com',
            'password' => bcrypt('algo1234')
        ])->assignRole('admin');
        $this->browse(function (Browser $browser) use ($p2, $usuario) {
            $browser->loginAs($usuario)
                ->visit('/products/' . $p2->slug)
                ->pause(400)
                ->click('@comprar')
                ->pause(400)
                ->click('.carrito')
                ->pause(400)
                ->click('@alCarrito')
                ->pause(400)
                ->clickLink('Continuar')
                ->pause(400)
                ->type('@nombreContacto', 'Persona1')
                ->type('@telefonoContacto', '456654456')
                ->click('@continuar')
                ->pause(1500)
                ->assertPathIs('/orders/' . $p2->id . '/payment')
                ->pause(600)
                ->screenshot('2a');
            $order = Order::find($p2->id);
            $order->status = 2;
            $order->save();
            $browser->visit('/orders/' . $p2->id)
                ->pause(600)
                ->screenshot('2b');


        });
    }


}
