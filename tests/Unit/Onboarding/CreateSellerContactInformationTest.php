<?php

namespace Tests\Application\Actions\Onboarding;

use App\Application\Shared\Exceptions\ConflictHttpException;
use App\Domain\Onboarding\Actions\CreateSellerContactInformation;
use App\Domain\Onboarding\Dtos\CreateSellerContactInformationDto;
use App\Domain\Onboarding\Interfaces\Repositories\SellerContactInformationRepositoryInterface;
use App\Infrastructure\Models\SellerContactInformation;
use App\Infrastructure\Models\User;
use Mockery;

beforeEach(function () {
    $this->sellerContactRepo = Mockery::mock(SellerContactInformationRepositoryInterface::class);
    $this->user = User::factory()->create();
    $this->sellerContactDto = new CreateSellerContactInformationDto(
        $this->user->id,
        'John Doe',
        'johndoe@xyz.com',
        '0123456789',
        'Nigeria',
        'Oyo',
        'Ibadan',
        'Bodija, Ibadan'
    );

    $this->contactInformation = SellerContactInformation::factory()->create([
        'user_id' => $this->sellerContactDto->getUserId(),
        'name' => $this->sellerContactDto->getName(),
        'email' => $this->sellerContactDto->getEmail(),
        'phone_number' => $this->sellerContactDto->getPhoneNumber(),
        'country' => $this->sellerContactDto->getCountry(),
        'state' => $this->sellerContactDto->getState(),
        'city' => $this->sellerContactDto->getCity(),
        'address' => $this->sellerContactDto->getAddress(),
    ]);

    $this->createSellerContactInformation = new CreateSellerContactInformation($this->sellerContactRepo);
});

it('should throw an exception if contact email exist for another seller', function () {
    $this->sellerContactRepo->shouldReceive('findOtherContact')
        ->once()
        ->with(
            'email',
            $this->sellerContactDto->getEmail(),
            $this->sellerContactDto->getUserId(),
        )
        ->andReturn($this->contactInformation);

    $this->createSellerContactInformation->execute($this->sellerContactDto);
})->throws(ConflictHttpException::class, 'Email address exist for another seller');

it('should throw an exception if contact phone number exist for another seller', function () {
    $this->sellerContactRepo->shouldReceive('findOtherContact')
        ->once()
        ->with(
            'email',
            $this->sellerContactDto->getEmail(),
            $this->sellerContactDto->getUserId(),
        )
        ->andReturn(null);

    $this->sellerContactRepo->shouldReceive('findOtherContact')
        ->once()
        ->with(
            'phone_number',
            $this->sellerContactDto->getPhoneNumber(),
            $this->sellerContactDto->getUserId(),
        )
        ->andReturn($this->contactInformation);

    $this->createSellerContactInformation->execute($this->sellerContactDto);
})->throws(ConflictHttpException::class, 'Phone number exist for another seller');

it('should create a new seller contact information if not exist', function () {
    $this->sellerContactRepo->shouldReceive('findOtherContact')
        ->once()
        ->with(
            'email',
            $this->sellerContactDto->getEmail(),
            $this->sellerContactDto->getUserId(),
        )
        ->andReturn(null);

    $this->sellerContactRepo->shouldReceive('findOtherContact')
        ->once()
        ->with(
            'phone_number',
            $this->sellerContactDto->getPhoneNumber(),
            $this->sellerContactDto->getUserId(),
        )
        ->andReturn(null);

    $this->sellerContactRepo->shouldReceive('create')
        ->once()
        ->with($this->sellerContactDto)
        ->andReturn($this->contactInformation);

    $response = $this->createSellerContactInformation->execute($this->sellerContactDto);

    expect($response)->toBeInstanceOf(SellerContactInformation::class)
        ->and($response->user_id)->toBe($this->sellerContactDto->getUserId())
        ->and($response->name)->toBe($this->sellerContactDto->getName())
        ->and($response->email)->toBe($this->sellerContactDto->getEmail())
        ->and($response->phone_number)->toBe($this->sellerContactDto->getPhoneNumber())
        ->and($response->country)->toBe($this->sellerContactDto->getCountry())
        ->and($response->city)->toBe($this->sellerContactDto->getCity())
        ->and($response->address)->toBe($this->sellerContactDto->getAddress());
});
