<?php

namespace Tests\Unit\Vendor\Onboarding;

use App\Application\Shared\Exceptions\ConflictHttpException;
use App\Domain\Vendor\Onboarding\Actions\CreateVendorContactInformationAction;
use App\Domain\Vendor\Onboarding\Dtos\CreateVendorContactInformationDto;
use App\Domain\Vendor\Onboarding\Interfaces\VendorContactInformationRepositoryInterface;
use App\Infrastructure\Models\User\User;
use App\Infrastructure\Models\Vendor\VendorContactInformation;
use Mockery;

beforeEach(function () {
    $this->vendorContactRepo = Mockery::mock(VendorContactInformationRepositoryInterface::class);
    $this->user = User::factory()->create();
    $this->vendorContactDto = new CreateVendorContactInformationDto(
        $this->user->id,
        'John Doe',
        'johndoe@xyz.com',
        '0123456789',
        'Nigeria',
        'Oyo',
        'Ibadan',
        'Bodija, Ibadan'
    );

    $this->contactInformation = VendorContactInformation::factory()->create([
        'user_id' => $this->vendorContactDto->getUserId(),
        'name' => $this->vendorContactDto->getName(),
        'email' => $this->vendorContactDto->getEmail(),
        'phone_number' => $this->vendorContactDto->getPhoneNumber(),
        'country' => $this->vendorContactDto->getCountry(),
        'state' => $this->vendorContactDto->getState(),
        'city' => $this->vendorContactDto->getCity(),
        'address' => $this->vendorContactDto->getAddress(),
    ]);

    $this->createVendorContactInformation = new CreateVendorContactInformationAction($this->vendorContactRepo);
});

it('should throw an exception if contact email exist for another vendor', function () {
    $this->vendorContactRepo->shouldReceive('findOtherContact')
        ->once()
        ->with(
            'email',
            $this->vendorContactDto->getEmail(),
            $this->vendorContactDto->getUserId(),
        )
        ->andReturn($this->contactInformation);

    $this->createVendorContactInformation->execute($this->vendorContactDto);
})->throws(ConflictHttpException::class, 'Email address exist for another vendor');

it('should throw an exception if contact phone number exist for another vendor', function () {
    $this->vendorContactRepo->shouldReceive('findOtherContact')
        ->once()
        ->with(
            'email',
            $this->vendorContactDto->getEmail(),
            $this->vendorContactDto->getUserId(),
        )
        ->andReturn(null);

    $this->vendorContactRepo->shouldReceive('findOtherContact')
        ->once()
        ->with(
            'phone_number',
            $this->vendorContactDto->getPhoneNumber(),
            $this->vendorContactDto->getUserId(),
        )
        ->andReturn($this->contactInformation);

    $this->createVendorContactInformation->execute($this->vendorContactDto);
})->throws(ConflictHttpException::class, 'Phone number exist for another vendor');

it('should create a new vendor contact information if not exist', function () {
    $this->vendorContactRepo->shouldReceive('findOtherContact')
        ->once()
        ->with(
            'email',
            $this->vendorContactDto->getEmail(),
            $this->vendorContactDto->getUserId(),
        )
        ->andReturn(null);

    $this->vendorContactRepo->shouldReceive('findOtherContact')
        ->once()
        ->with(
            'phone_number',
            $this->vendorContactDto->getPhoneNumber(),
            $this->vendorContactDto->getUserId(),
        )
        ->andReturn(null);

    $this->vendorContactRepo->shouldReceive('create')
        ->once()
        ->with($this->vendorContactDto)
        ->andReturn($this->contactInformation);

    $response = $this->createVendorContactInformation->execute($this->vendorContactDto);

    expect($response)->toBeInstanceOf(VendorContactInformation::class)
        ->and($response->user_id)->toBe($this->vendorContactDto->getUserId())
        ->and($response->name)->toBe($this->vendorContactDto->getName())
        ->and($response->email)->toBe($this->vendorContactDto->getEmail())
        ->and($response->phone_number)->toBe($this->vendorContactDto->getPhoneNumber())
        ->and($response->country)->toBe($this->vendorContactDto->getCountry())
        ->and($response->city)->toBe($this->vendorContactDto->getCity())
        ->and($response->address)->toBe($this->vendorContactDto->getAddress());
});
