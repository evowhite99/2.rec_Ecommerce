<?php

namespace Tests\Browser;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Image;
use App\Models\Product;
use App\Models\Subcategory;
use App\Models\User;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Spatie\Permission\Models\Role;
use Tests\DuskTestCase;
use Illuminate\Support\Facades\Log;

class Ejercicio3Test extends DuskTestCase
{
    use DatabaseMigrations;

    /*
    * A basic browser test example.
    *
    * @return void
    */
    public function test_ejercicio3_test() {
        $brand = Brand::factory()->create();
        $category = Category::factory()->create([
            'name' => 'Informatica',
            'slug' => 'Informatica',
            'icon' => 'Informatica',
        ]);
        $category->brands()->attach($brand->id);
        $subcategory = Subcategory::factory()->create([
            'category_id' => $category->id,
            'name' => 'PC',
            'slug' => 'PC',
        ]);
        $p1 = Product::factory()->create([
            'subcategory_id' => $subcategory->id,
        ]);
        Image::factory()->create([
            'imageable_id' => $p1->id,
            'imageable_type' => Product::class
        ]);
        $role = Role::create(['name' => 'admin']);
        $usuario = User::factory()->create([
            'name' => 'Rubén',
            'email' => 'algo1234@gmail.com',
            'password' => bcrypt('algo1234')
        ])->assignRole('admin');
        $this->browse(function (Browser $browser) use ($usuario, $p1) {
            $browser->loginAs($usuario)
                ->visitRoute('products.show', $p1)
                ->press('@comprar')
                ->pause(200)
                ->assertSee($p1);
            $p1 = Product::find($p1->id);
            $browser->click('.carrito')
                ->pause(300)
                ->screenshot('3a')
                ->click('.perfilUsuario')
                ->pause(200)
                ->click('@apagar')
                ->pause(200)
                ->screenshot('3b');
            $browser->visit('/')
                ->click('.perfilUsuario')
                ->pause(200)
                ->click('@encender')
                ->pause(200)
                ->type('email', 'algo1234@gmail.com')
                ->type('password', 'algo1234')
                ->pause(300)
                ->screenshot('3c')
                ->press('INICIAR SESIÓN')
                ->pause(200)
                ->click('.carrito')
                ->pause(300)
                ->screenshot('3d');


        });


    }


}
