<?php

namespace Tests\Unit\Vendor\Onboarding;

use App\Application\Shared\Exceptions\ConflictHttpException;
use App\Domain\Vendor\Onboarding\Actions\CreateVendorLegalInformation;
use App\Domain\Vendor\Onboarding\Dtos\CreateVendorLegalInformationDto;
use App\Domain\Vendor\Onboarding\Interfaces\VendorLegalInformationRepositoryInterface;
use App\Infrastructure\Models\User\User;
use App\Infrastructure\Models\Vendor\VendorLegalInformation;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Mockery;

beforeEach(function () {
    $this->vendorLegalRepo = Mockery::mock(VendorLegalInformationRepositoryInterface::class);
    $this->user = User::factory()->create();
    $this->vendorLegalDto = new CreateVendorLegalInformationDto(
        $this->user->id,
        'Barrister John Doe',
        'johndoe@xyz.com'
    );

    $this->legalInformation = VendorLegalInformation::factory()->create([
        'user_id' => $this->vendorLegalDto->getUserId(),
        'fullname' => $this->vendorLegalDto->getFullName(),
        'email' => $this->vendorLegalDto->getEmail(),
    ]);

    $this->createVendorLegalInformation = new CreateVendorLegalInformation($this->vendorLegalRepo);
});

it('should throw an exception if legal name exist for another vendor', function () {
    $this->vendorLegalRepo->shouldReceive('findOtherLegal')
        ->once()
        ->with(
            'email',
            $this->vendorLegalDto->getEmail(),
            $this->vendorLegalDto->getUserId(),
        )
        ->andReturn($this->legalInformation);

    $this->createVendorLegalInformation->execute($this->vendorLegalDto);
})->throws(ConflictHttpException::class, 'Legal email address exist for another vendor');

it('should create a new legal information record if no existing record and legal certificate is not uploaded',
    function () {
        Storage::fake('local');

        $this->vendorLegalRepo->shouldReceive('findOtherLegal')
            ->once()
            ->with(
                'email',
                $this->vendorLegalDto->getEmail(),
                $this->vendorLegalDto->getUserId(),
            )
            ->andReturn(null);

        $this->vendorLegalRepo->shouldReceive('findLegal')
            ->once()
            ->with(
                'email',
                $this->vendorLegalDto->getEmail(),
            )
            ->andReturn(null);

        $this->vendorLegalRepo->shouldReceive('create')
            ->once()
            ->with($this->vendorLegalDto)
            ->andReturn($this->legalInformation);

        $vendorLegalInformation = $this->createVendorLegalInformation->execute($this->vendorLegalDto);

        expect($vendorLegalInformation)->toBeInstanceOf(VendorLegalInformation::class)
            ->and($vendorLegalInformation->user_id)->toBe($this->vendorLegalDto->getUserId())
            ->and($vendorLegalInformation->fullname)->toBe($this->vendorLegalDto->getFullName())
            ->and($vendorLegalInformation->email)->toBe($this->vendorLegalDto->getEmail())
            ->and($vendorLegalInformation->certificate_path)->toBeNull();
    });

it('should create a new legal information record if no existing record and legal certificate is uploaded',
    function () {
        Storage::fake('local');

        $vendorLegalDto = new CreateVendorLegalInformationDto(
            $this->legalInformation->user_id,
            $this->legalInformation->fullname,
            $this->legalInformation->email,
            UploadedFile::fake()->image('legal-certificate.jpg')
        );

        $this->vendorLegalRepo->shouldReceive('findOtherLegal')
            ->once()
            ->with(
                'email',
                $vendorLegalDto->getEmail(),
                $vendorLegalDto->getUserId(),
            )
            ->andReturn(null);

        $this->vendorLegalRepo->shouldReceive('findLegal')
            ->once()
            ->with(
                'email',
                $this->vendorLegalDto->getEmail(),
            )
            ->andReturn(null);

        $this->legalInformation->certificate_path = 'vendors/legal/certificates/'.$this->legalInformation->uuid.'.jpg';

        $this->vendorLegalRepo->shouldReceive('create')
            ->once()
            ->with($vendorLegalDto)
            ->andReturn($this->legalInformation);

        $response = $this->createVendorLegalInformation->execute($vendorLegalDto);

        expect($response)->toBeInstanceOf(VendorLegalInformation::class)
            ->and($response->user_id)->toBe($this->vendorLegalDto->getUserId())
            ->and($response->fullname)->toBe($this->vendorLegalDto->getFullName())
            ->and($response->email)->toBe($this->vendorLegalDto->getEmail())
            ->and($response->certificate_path)->toBeString();
    });
