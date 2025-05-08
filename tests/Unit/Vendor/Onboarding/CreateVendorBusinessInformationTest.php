<?php

namespace Tests\Unit\Vendor\Onboarding;

use App\Application\Shared\Exceptions\ConflictHttpException;
use App\Domain\Vendor\Onboarding\Actions\CreateVendorBusinessInformation;
use App\Domain\Vendor\Onboarding\Dtos\CreateVendorBusinessInformationDto;
use App\Domain\Vendor\Onboarding\Interfaces\VendorBusinessInformationRepositoryInterface;
use App\Infrastructure\Models\User;
use App\Infrastructure\Models\VendorBusinessInformation;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Mockery;

beforeEach(function () {
    $this->vendorBusinessRepo = Mockery::mock(VendorBusinessInformationRepositoryInterface::class);
    $this->user = User::factory()->create();
    $this->vendorBusinessDto = new CreateVendorBusinessInformationDto(
        $this->user->id,
        'Company ABC',
        'Company description',
        'REG 123',
        'TAX 1456'
    );

    $this->businessInformation = VendorBusinessInformation::factory()->create([
        'user_id' => $this->vendorBusinessDto->getUserId(),
        'name' => $this->vendorBusinessDto->getCompanyName(),
        'description' => $this->vendorBusinessDto->getDescription(),
        'registration_number' => $this->vendorBusinessDto->getRegistrationNumber(),
        'tax_identification_number' => $this->vendorBusinessDto->getTaxIdentificationNumber(),
    ]);

    $this->createVendorBusinessInformation = new CreateVendorBusinessInformation($this->vendorBusinessRepo);
});

it('should throw an exception if business name exist for another vendor', function () {
    $this->vendorBusinessRepo->shouldReceive('findOtherBusiness')
        ->once()
        ->with(
            'name',
            $this->vendorBusinessDto->getCompanyName(),
            $this->vendorBusinessDto->getUserId(),
        )
        ->andReturn($this->businessInformation);

    $this->createVendorBusinessInformation->execute($this->vendorBusinessDto);
})->throws(ConflictHttpException::class, 'Business name exist for another vendor');

it('should throw an exception if business registration number exist for another vendor', function () {
    $this->vendorBusinessRepo->shouldReceive('findOtherBusiness')
        ->once()
        ->with(
            'name',
            $this->vendorBusinessDto->getCompanyName(),
            $this->vendorBusinessDto->getUserId(),
        )
        ->andReturn(null);

    $this->vendorBusinessRepo->shouldReceive('findOtherBusiness')
        ->once()
        ->with(
            'registration_number',
            $this->vendorBusinessDto->getRegistrationNumber(),
            $this->vendorBusinessDto->getUserId(),
        )
        ->andReturn($this->businessInformation);

    $this->createVendorBusinessInformation->execute($this->vendorBusinessDto);
})->throws(ConflictHttpException::class, 'Registration number exist for another vendor');

it('should throw an exception if business tax identification number exist for another vendor', function () {
    $this->vendorBusinessRepo->shouldReceive('findOtherBusiness')
        ->once()
        ->with(
            'name',
            $this->vendorBusinessDto->getCompanyName(),
            $this->vendorBusinessDto->getUserId(),
        )
        ->andReturn(null);

    $this->vendorBusinessRepo->shouldReceive('findOtherBusiness')
        ->once()
        ->with(
            'registration_number',
            $this->vendorBusinessDto->getRegistrationNumber(),
            $this->vendorBusinessDto->getUserId(),
        )
        ->andReturn(null);

    $this->vendorBusinessRepo->shouldReceive('findOtherBusiness')
        ->once()
        ->with(
            'tax_identification_number',
            $this->vendorBusinessDto->getTaxIdentificationNumber(),
            $this->vendorBusinessDto->getUserId(),
        )
        ->andReturn($this->businessInformation);

    $this->createVendorBusinessInformation->execute($this->vendorBusinessDto);
})->throws(ConflictHttpException::class, 'Tax identification number exist for another vendor');

it('should create a new business information record if no existing record and business certificate is not uploaded',
    function () {
        Storage::fake('local');

        $this->vendorBusinessRepo->shouldReceive('findOtherBusiness')
            ->once()
            ->with(
                'name',
                $this->vendorBusinessDto->getCompanyName(),
                $this->vendorBusinessDto->getUserId(),
            )
            ->andReturn(null);

        $this->vendorBusinessRepo->shouldReceive('findOtherBusiness')
            ->once()
            ->with(
                'registration_number',
                $this->vendorBusinessDto->getRegistrationNumber(),
                $this->vendorBusinessDto->getUserId(),
            )
            ->andReturn(null);

        $this->vendorBusinessRepo->shouldReceive('findOtherBusiness')
            ->once()
            ->with(
                'tax_identification_number',
                $this->vendorBusinessDto->getTaxIdentificationNumber(),
                $this->vendorBusinessDto->getUserId(),
            )
            ->andReturn(null);

        $this->vendorBusinessRepo->shouldReceive('findBusiness')
            ->once()
            ->with(
                'registration_number',
                $this->vendorBusinessDto->getRegistrationNumber(),
            )
            ->andReturn(null);

        $this->vendorBusinessRepo->shouldReceive('create')
            ->once()
            ->with($this->vendorBusinessDto)
            ->andReturn($this->businessInformation);

        $vendorBusinessInformation = $this->createVendorBusinessInformation->execute($this->vendorBusinessDto);

        expect($vendorBusinessInformation)->toBeInstanceOf(VendorBusinessInformation::class)
            ->and($vendorBusinessInformation->user_id)->toBe($this->vendorBusinessDto->getUserId())
            ->and($vendorBusinessInformation->name)->toBe($this->vendorBusinessDto->getCompanyName())
            ->and($vendorBusinessInformation->description)->toBe($this->vendorBusinessDto->getDescription())
            ->and($vendorBusinessInformation->certificate_path)->toBeNull()
            ->and($vendorBusinessInformation->registration_number)->toBe($this->vendorBusinessDto->getRegistrationNumber())
            ->and($vendorBusinessInformation->tax_identification_number)->toBe($this->vendorBusinessDto->getTaxIdentificationNumber());
    });

it('should create a new business information record if no existing record and business certificate is uploaded',
    function () {
        Storage::fake('local');

        $vendorBusinessDto = new CreateVendorBusinessInformationDto(
            $this->businessInformation->user_id,
            $this->businessInformation->name,
            $this->businessInformation->description,
            $this->businessInformation->registration_number,
            $this->businessInformation->tax_identification_number,
            UploadedFile::fake()->image('business-certificate.jpg')
        );

        $this->vendorBusinessRepo->shouldReceive('findOtherBusiness')
            ->once()
            ->with(
                'name',
                $vendorBusinessDto->getCompanyName(),
                $vendorBusinessDto->getUserId(),
            )
            ->andReturn(null);

        $this->vendorBusinessRepo->shouldReceive('findOtherBusiness')
            ->once()
            ->with(
                'registration_number',
                $vendorBusinessDto->getRegistrationNumber(),
                $vendorBusinessDto->getUserId(),
            )
            ->andReturn(null);

        $this->vendorBusinessRepo->shouldReceive('findOtherBusiness')
            ->once()
            ->with(
                'tax_identification_number',
                $vendorBusinessDto->getTaxIdentificationNumber(),
                $vendorBusinessDto->getUserId(),
            )
            ->andReturn(null);

        $this->vendorBusinessRepo->shouldReceive('findBusiness')
            ->once()
            ->with(
                'registration_number',
                $vendorBusinessDto->getRegistrationNumber(),
            )
            ->andReturn(null);

        $this->businessInformation->certificate_path = 'vendors/business/certificates/'.$this->businessInformation->uuid.'.jpg';

        $this->vendorBusinessRepo->shouldReceive('create')
            ->once()
            ->with($vendorBusinessDto)
            ->andReturn($this->businessInformation);

        $response = $this->createVendorBusinessInformation->execute($vendorBusinessDto);

        expect($response)->toBeInstanceOf(VendorBusinessInformation::class)
            ->and($response->user_id)->toBe($this->vendorBusinessDto->getUserId())
            ->and($response->name)->toBe($this->vendorBusinessDto->getCompanyName())
            ->and($response->description)->toBe($this->vendorBusinessDto->getDescription())
            ->and($response->certificate_path)->toBeString()
            ->and($response->registration_number)->toBe($this->vendorBusinessDto->getRegistrationNumber())
            ->and($response->tax_identification_number)->toBe($this->vendorBusinessDto->getTaxIdentificationNumber());
    });
