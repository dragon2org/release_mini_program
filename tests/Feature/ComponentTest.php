<?php
/**
 * Created by PhpStorm.
 * User: harlen
 * Date: 2019/1/21
 * Time: 9:33 PM
 */

namespace Tests\Feature;


use App\Models\Component;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ComponentTest extends TestCase
{
    use RefreshDatabase;

    public function testComponentCreate()
    {
        $component = make(Component::class);

        $form = array_merge($component->toArray(), [
            'validate' => [
                'filename' => '5555239629.txt',
                'content' => '4d549fb1a927ca9d89d965cfe07301ec',
            ]
        ]);
        $response = $this->postJson('/api/v1/component', $form, [
            'Content-Type' => 'application/json'
        ]);

        $response->assertStatus(200);
        //$this->assertDatabaseHas(Component::getTabale(), $component);
    }
}