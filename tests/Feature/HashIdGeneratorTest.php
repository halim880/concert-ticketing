<?php

namespace Tests\Feature;

use App\Models\HashIdTicketCodeGenerator;
use App\Models\Ticket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class HashIdGeneratorTest extends TestCase
{
    /**
     * A basic feature test example.
     * @test
     * @return void
     */
    public function tickets_code_are_atleast_6_characters_long()
    {
        $ticketCodeGenerator = new HashIdTicketCodeGenerator();

        $code = $ticketCodeGenerator->generateFor(new Ticket(['id'=>1]));

        $this->assertTrue(strlen($code) >= 6);
    }

    /** @test */
    public function ticket_code_should_only_be_uppercase_letter()
    {
        $ticketCodeGenerator = new HashIdTicketCodeGenerator();

        $code = $ticketCodeGenerator->generateFor(new Ticket(['id'=>1]));

        $this->assertMatchesRegularExpression('/^[A-Z]+$/', $code);

    }

    /** @test */
    public function ticket_code_for_the_same_ticket_id_is_same()
    {
        $ticketCodeGenerator = new HashIdTicketCodeGenerator();

        $code1 = $ticketCodeGenerator->generateFor(new Ticket(['id'=>1]));
        $code2 = $ticketCodeGenerator->generateFor(new Ticket(['id'=>1]));


        $this->assertEquals($code1, $code2);
    }

    /** @test */
    public function ticket_code_for_different_ticket_id_is_different()
    {
        $ticketCodeGenerator = new HashIdTicketCodeGenerator();

        $code1 = $ticketCodeGenerator->generateFor(new Ticket(['id'=>1]));
        $code2 = $ticketCodeGenerator->generateFor(new Ticket(['id'=>2]));


        $this->assertNotEquals($code1, $code2);
    }
}
