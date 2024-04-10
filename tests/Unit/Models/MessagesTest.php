<?php

namespace Tests\Unit\Models;

use App\Models\Messages;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MessagesTest extends TestCase
{
    use RefreshDatabase;

    public function testValidateData()
    {
        // Create example data for validation
        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'subject' => 'Test Subject',
            'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
        ];

        // Call the validation method
        $validator = Messages::validateData($data);

        // Assert that validation passes
        $this->assertFalse($validator->fails());

        // Assert that all fields were accepted
        $this->assertEquals($data['name'], $validator->validated()['name']);
        $this->assertEquals($data['email'], $validator->validated()['email']);
        $this->assertEquals($data['subject'], $validator->validated()['subject']);
        $this->assertEquals($data['content'], $validator->validated()['content']);
    }
}
