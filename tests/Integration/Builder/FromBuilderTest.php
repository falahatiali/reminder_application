<?php

namespace Tests\Integration\Builder;

use App\Builders\Telegram\From\From;
use App\Builders\Telegram\From\FromBuilder;
use Tests\TestCase;

class FromBuilderTest extends TestCase
{
    private array $from;

    public function setUp(): void
    {
        parent::setUp();
        $data = file_get_contents(__DIR__ . '/../../data/message.json');
        $data = json_decode($data, true);
        $this->from = $data['message']['from'];
    }

    /** @test */
    public function test_has_right_data_for_input()
    {
        $this->assertArrayHasKey('id', $this->from);
        $this->assertArrayHasKey('first_name', $this->from);
        $this->assertArrayHasKey('username', $this->from);
        $this->assertArrayHasKey('is_bot', $this->from);
        $this->assertArrayHasKey('language_code', $this->from);
    }

    /** @test */
    public function test_create_chat_dvo_by_builder()
    {
        $from = (new FromBuilder())
            ->setId($this->from['id'])
            ->setFirstName($this->from['first_name'])
            ->setUsername($this->from['username'])
            ->setLanguageCode($this->from['language_code'])
            ->setIsBot($this->from['is_bot'])
            ->build();

        $this->assertInstanceOf(From::class, $from);
    }
}
