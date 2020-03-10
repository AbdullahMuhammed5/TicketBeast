<?php

namespace Tests\Browser;

use App\Concert;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ViewConcertListingTest extends DuskTestCase
{
    use DatabaseMigrations;
    /**
     * @test
     * @return void
     * @throws \Throwable
     */
    public function user_can_view_a_published_concert_listing()
    {
        $concert = factory(Concert::class)->states('published')->create([
            'title'                  => 'The big concert',
            'subtitle'               => 'The biggest concert in the world!',
            'date'                   => Carbon::parse('September 5, 2020 8:00pm'),
            'ticket_price'           => 2500,
            'venue'                  => 'London main st',
            'venue_address'          => '123 Example Lane',
            'city'                   => 'London',
            'state'                   => 'chelsea',
            'zip'                    => '12584',
            'additional_information' => 'For tickets, call (555) 555-5555',
        ]);

        $this->browse(function (Browser $browser) use($concert) {
            $browser->visit("concerts/$concert->id")
                ->assertSee('The big concert')
                ->assertSee('The biggest concert in the world!')
                ->assertSee('September 5, 2020')
                ->assertSee('8:00pm')
                ->assertSee('25.00')
                ->assertSee('London')
                ->assertSee('chelsea')
                ->assertSee('123 Example Lane')
                ->assertSee('12584')
                ->assertSee('For tickets, call (555) 555-5555');
        });
    }
    /**
     * @test
     * @return void
     * @throws \Throwable
     */
    public function user_cannot_view_unpublished_concert(){
        $concert = factory(Concert::class)->states('unpublished')->create();
        $response = $this->json('get', "concerts/$concert->id");
        $response->assertStatus(404);
    }
}
