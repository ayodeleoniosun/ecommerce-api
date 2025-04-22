<?php

namespace Tests\Application\Actions\Onboarding;

use App\Application\Actions\Onboarding\CreateSellerBusinessInformation;
use App\Application\Shared\Exceptions\ConflictHttpException;
use App\Domain\Onboarding\Dtos\SellerBusinessInformationDto;
use App\Domain\Onboarding\Interfaces\Repositories\SellerBusinessInformationRepositoryInterface;
use App\Infrastructure\Models\SellerBusinessInformation;
use App\Infrastructure\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Mockery;

beforeEach(function () {
    $this->sellerBusinessRepo = Mockery::mock(SellerBusinessInformationRepositoryInterface::class);
    $this->user = User::factory()->create();
    $this->sellerBusinessDto = new SellerBusinessInformationDto(
        $this->user->id,
        'Company ABC',
        'Company description',
        'REG 123',
        'TAX 1456'
    );

    $this->businessInformation = SellerBusinessInformation::factory()->create([
        'user_id' => $this->sellerBusinessDto->getUserId(),
        'name' => $this->sellerBusinessDto->getCompanyName(),
        'description' => $this->sellerBusinessDto->getDescription(),
        'registration_number' => $this->sellerBusinessDto->getRegistrationNumber(),
        'tax_identification_number' => $this->sellerBusinessDto->getTaxIdentificationNumber(),
    ]);

    $this->createSellerBusinessInformation = new CreateSellerBusinessInformation($this->sellerBusinessRepo);
});

it('should throw an exception if business name exist for another seller', function () {
    $this->sellerBusinessRepo->shouldReceive('findOtherBusiness')
        ->once()
        ->with(
            'name',
            $this->sellerBusinessDto->getCompanyName(),
            $this->sellerBusinessDto->getUserId(),
        )
        ->andReturn($this->businessInformation);

    $this->createSellerBusinessInformation->execute($this->sellerBusinessDto);
})->throws(ConflictHttpException::class, 'Business name exist for another seller');

it('should throw an exception if business registration number exist for another seller', function () {
    $this->sellerBusinessRepo->shouldReceive('findOtherBusiness')
        ->once()
        ->with(
            'name',
            $this->sellerBusinessDto->getCompanyName(),
            $this->sellerBusinessDto->getUserId(),
        )
        ->andReturn(null);

    $this->sellerBusinessRepo->shouldReceive('findOtherBusiness')
        ->once()
        ->with(
            'registration_number',
            $this->sellerBusinessDto->getRegistrationNumber(),
            $this->sellerBusinessDto->getUserId(),
        )
        ->andReturn($this->businessInformation);

    $this->createSellerBusinessInformation->execute($this->sellerBusinessDto);
})->throws(ConflictHttpException::class, 'Registration number exist for another seller');

it('should throw an exception if business tax identification number exist for another seller', function () {
    $this->sellerBusinessRepo->shouldReceive('findOtherBusiness')
        ->once()
        ->with(
            'name',
            $this->sellerBusinessDto->getCompanyName(),
            $this->sellerBusinessDto->getUserId(),
        )
        ->andReturn(null);

    $this->sellerBusinessRepo->shouldReceive('findOtherBusiness')
        ->once()
        ->with(
            'registration_number',
            $this->sellerBusinessDto->getRegistrationNumber(),
            $this->sellerBusinessDto->getUserId(),
        )
        ->andReturn(null);

    $this->sellerBusinessRepo->shouldReceive('findOtherBusiness')
        ->once()
        ->with(
            'tax_identification_number',
            $this->sellerBusinessDto->getTaxIdentificationNumber(),
            $this->sellerBusinessDto->getUserId(),
        )
        ->andReturn($this->businessInformation);

    $this->createSellerBusinessInformation->execute($this->sellerBusinessDto);
})->throws(ConflictHttpException::class, 'Tax identification number exist for another seller');

it('should create a new business information record if no existing record and business certificate is not uploaded',
    function () {
        Storage::fake('local');

        $this->sellerBusinessRepo->shouldReceive('findOtherBusiness')
            ->once()
            ->with(
                'name',
                $this->sellerBusinessDto->getCompanyName(),
                $this->sellerBusinessDto->getUserId(),
            )
            ->andReturn(null);

        $this->sellerBusinessRepo->shouldReceive('findOtherBusiness')
            ->once()
            ->with(
                'registration_number',
                $this->sellerBusinessDto->getRegistrationNumber(),
                $this->sellerBusinessDto->getUserId(),
            )
            ->andReturn(null);

        $this->sellerBusinessRepo->shouldReceive('findOtherBusiness')
            ->once()
            ->with(
                'tax_identification_number',
                $this->sellerBusinessDto->getTaxIdentificationNumber(),
                $this->sellerBusinessDto->getUserId(),
            )
            ->andReturn(null);

        $this->sellerBusinessRepo->shouldReceive('findBusiness')
            ->once()
            ->with(
                'registration_number',
                $this->sellerBusinessDto->getRegistrationNumber(),
            )
            ->andReturn(null);

        $this->sellerBusinessRepo->shouldReceive('create')
            ->once()
            ->with($this->sellerBusinessDto)
            ->andReturn($this->businessInformation);

        $sellerBusinessInformation = $this->createSellerBusinessInformation->execute($this->sellerBusinessDto);

        expect($sellerBusinessInformation)->toBeInstanceOf(SellerBusinessInformation::class)
            ->and($sellerBusinessInformation->user_id)->toBe($this->sellerBusinessDto->getUserId())
            ->and($sellerBusinessInformation->name)->toBe($this->sellerBusinessDto->getCompanyName())
            ->and($sellerBusinessInformation->description)->toBe($this->sellerBusinessDto->getDescription())
            ->and($sellerBusinessInformation->certificate_path)->toBeNull()
            ->and($sellerBusinessInformation->registration_number)->toBe($this->sellerBusinessDto->getRegistrationNumber())
            ->and($sellerBusinessInformation->tax_identification_number)->toBe($this->sellerBusinessDto->getTaxIdentificationNumber());
    });

it('should create a new business information record if no existing record and business certificate is uploaded',
    function () {
        Storage::fake('local');

        $sellerBusinessDto = new SellerBusinessInformationDto(
            $this->businessInformation->user_id,
            $this->businessInformation->name,
            $this->businessInformation->description,
            $this->businessInformation->registration_number,
            $this->businessInformation->tax_identification_number,
            UploadedFile::fake()->image('business-certificate.jpg')
        );

        $this->sellerBusinessRepo->shouldReceive('findOtherBusiness')
            ->once()
            ->with(
                'name',
                $sellerBusinessDto->getCompanyName(),
                $sellerBusinessDto->getUserId(),
            )
            ->andReturn(null);

        $this->sellerBusinessRepo->shouldReceive('findOtherBusiness')
            ->once()
            ->with(
                'registration_number',
                $sellerBusinessDto->getRegistrationNumber(),
                $sellerBusinessDto->getUserId(),
            )
            ->andReturn(null);

        $this->sellerBusinessRepo->shouldReceive('findOtherBusiness')
            ->once()
            ->with(
                'tax_identification_number',
                $sellerBusinessDto->getTaxIdentificationNumber(),
                $sellerBusinessDto->getUserId(),
            )
            ->andReturn(null);

        $this->sellerBusinessRepo->shouldReceive('findBusiness')
            ->once()
            ->with(
                'registration_number',
                $sellerBusinessDto->getRegistrationNumber(),
            )
            ->andReturn(null);

        $this->businessInformation->certificate_path = 'sellers/business/certificates/'.$this->businessInformation->uuid.'.jpg';

        $this->sellerBusinessRepo->shouldReceive('create')
            ->once()
            ->with($sellerBusinessDto)
            ->andReturn($this->businessInformation);

        $response = $this->createSellerBusinessInformation->execute($sellerBusinessDto);

        expect($response)->toBeInstanceOf(SellerBusinessInformation::class)
            ->and($response->user_id)->toBe($this->sellerBusinessDto->getUserId())
            ->and($response->name)->toBe($this->sellerBusinessDto->getCompanyName())
            ->and($response->description)->toBe($this->sellerBusinessDto->getDescription())
            ->and($response->certificate_path)->toBeString()
            ->and($response->registration_number)->toBe($this->sellerBusinessDto->getRegistrationNumber())
            ->and($response->tax_identification_number)->toBe($this->sellerBusinessDto->getTaxIdentificationNumber());
    });
