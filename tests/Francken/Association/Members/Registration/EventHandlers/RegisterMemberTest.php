<?php

declare(strict_types=1);

namespace Francken\Tests\Association\Members\Registration\EventHandlers;

use DateTimeImmutable;
use Francken\Association\Boards\Board;
use Francken\Association\Boards\BoardMember;
use Francken\Association\Boards\BoardMemberStatus;
use Francken\Association\LegacyMember;
use Francken\Association\Members\Address;
use Francken\Association\Members\Birthdate;
use Francken\Association\Members\ContactDetails;
use Francken\Association\Members\Fullname;
use Francken\Association\Members\Gender;
use Francken\Association\Members\PaymentDetails;
use Francken\Association\Members\PersonalDetails;
use Francken\Association\Members\Registration\EventHandlers\RegisterMember;
use Francken\Association\Members\Registration\Events\MemberWasRegistered;
use Francken\Association\Members\Registration\Events\RegistrationWasApproved;
use Francken\Association\Members\Registration\Events\RegistrationWasSubmitted;
use Francken\Association\Members\Registration\Registration;
use Francken\Association\Members\StudyDetails;
use Francken\Features\TestCase;
use Francken\Shared\Email;
use Illuminate\Support\Facades\Event;

class RegisterMemberTest extends TestCase
{
    /** @test */
    public function a_registration_can_be_submitted() : void
    {
        Event::fake([RegistrationWasSubmitted::class, MemberWasRegistered::class]);

        $registration = $this->submitRegistration();
        $approvedAt = new DateTimeImmutable('2020-02-02');
        $board = factory(Board::class)->create(['installed_at' => '2020-01-01']);
        $boardMember = BoardMember::create([
            'board_id' => $board->id,
            'member_id' => 0,
            'name' => 'Mark',
            'title' => 'Mark',
            'board_member_status' => BoardMemberStatus::BOARD_MEMBER,
            'installed_at' => new DateTimeImmutable(),
        ]);
        $registration->approve($boardMember, $approvedAt);
        $approveEvent = new RegistrationWasApproved($registration, $boardMember);
        $handler = new RegisterMember();
        $handler->handle($approveEvent);

        $legacyMember = LegacyMember::orderBy('id', 'desc')->first();
        $this->assertEquals($legacyMember->firstname, 'Mark');
        $legacyMember->delete();

        Event::assertDispatched(MemberWasRegistered::class);
    }

    private function submitRegistration() : Registration
    {
        return Registration::submit(
            new PersonalDetails(
                Fullname::fromFirstnameAndSurname('Mark', 'Redeman'),
                'M. S.',
                Gender::MALE(),
                Birthdate::fromString('1993-04-26'),
                'Netherlands',
                true
            ),
            new ContactDetails(
                new Email('markredeman@gmail.com'),
                new Address(
                    'Groningen',
                    '9742 GS',
                    'Nijenborgh 9',
                    'Netherlands'
                ),
                null
            ),
            new StudyDetails('s1111111'),
            new PaymentDetails('NL91 ABNA 0417 1643 00', null, true),
            true,
            ''
        );
    }
}
