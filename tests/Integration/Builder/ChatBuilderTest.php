<?php

namespace Tests\Integration\Builder;

use App\Builders\Telegram\Chat\Chat;
use App\Builders\Telegram\Chat\ChatBuilder;
use Tests\TestCase;

class ChatBuilderTest extends TestCase
{
    private array $chat;

    public function setUp(): void
    {
        parent::setUp();
        $data = file_get_contents(__DIR__ . '/../../data/message.json');
        $data = json_decode($data, true);
        $this->chat = $data['message']['chat'];
    }

    /** @test */
    public function test_has_right_data_for_input()
    {
        $this->assertArrayHasKey('id', $this->chat);
        $this->assertArrayHasKey('first_name', $this->chat);
        $this->assertArrayHasKey('username', $this->chat);
        $this->assertArrayHasKey('type', $this->chat);
    }

    /** @test */
    public function test_create_chat_dvo_by_builder()
    {
        $chat = (new ChatBuilder())
            ->setId($this->chat['id'])
            ->setFirstName($this->chat['first_name'])
            ->setUsername($this->chat['username'])
            ->setType($this->chat['type'])
            ->build();

        $this->assertInstanceOf(Chat::class, $chat);
    }
}
