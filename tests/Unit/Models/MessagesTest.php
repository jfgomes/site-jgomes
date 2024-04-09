<?php

namespace Tests\Unit\Models;

use App\Models\Messages;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MessagesTest extends TestCase
{
    use RefreshDatabase;

    public function testValidateData()
    {
        // Criar dados de exemplo para validar
        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'subject' => 'Test Subject',
            'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
        ];

        // Chamar o método de validação
        $validator = Messages::validateData($data);

        // Verificar se a validação passou
        $this->assertFalse($validator->fails());

        // Verificar se todos os campos foram aceitos
        $this->assertEquals($data['name'], $validator->validated()['name']);
        $this->assertEquals($data['email'], $validator->validated()['email']);
        $this->assertEquals($data['subject'], $validator->validated()['subject']);
        $this->assertEquals($data['content'], $validator->validated()['content']);
    }
}
