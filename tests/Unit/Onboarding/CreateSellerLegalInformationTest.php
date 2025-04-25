<?php

namespace Tests\Application\Actions\Onboarding;

use App\Application\Actions\Onboarding\CreateSellerLegalInformation;
use App\Application\Shared\Exceptions\ConflictHttpException;
use App\Domain\Onboarding\Dtos\CreateSellerLegalInformationDto;
use App\Domain\Onboarding\Interfaces\Repositories\SellerLegalInformationRepositoryInterface;
use App\Infrastructure\Models\SellerLegalInformation;
use App\Infrastructure\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Mockery;

beforeEach(function () {
    $this->sellerLegalRepo = Mockery::mock(SellerLegalInformationRepositoryInterface::class);
    $this->user = User::factory()->create();
    $this->sellerLegalDto = new CreateSellerLegalInformationDto(
        $this->user->id,
        'Barrister John Doe',
        'johndoe@xyz.com'
    );

    $this->legalInformation = SellerLegalInformation::factory()->create([
        'user_id' => $this->sellerLegalDto->getUserId(),
        'fullname' => $this->sellerLegalDto->getFullName(),
        'email' => $this->sellerLegalDto->getEmail(),
    ]);

    $this->createSellerLegalInformation = new CreateSellerLegalInformation($this->sellerLegalRepo);
});

it('should throw an exception if legal name exist for another seller', function () {
    $this->sellerLegalRepo->shouldReceive('findOtherLegal')
        ->once()
        ->with(
            'email',
            $this->sellerLegalDto->getEmail(),
            $this->sellerLegalDto->getUserId(),
        )
        ->andReturn($this->legalInformation);

    $this->createSellerLegalInformation->execute($this->sellerLegalDto);
})->throws(ConflictHttpException::class, 'Legal email address exist for another seller');

it('should create a new legal information record if no existing record and legal certificate is not uploaded',
    function () {
        Storage::fake('local');

        $this->sellerLegalRepo->shouldReceive('findOtherLegal')
            ->once()
            ->with(
                'email',
                $this->sellerLegalDto->getEmail(),
                $this->sellerLegalDto->getUserId(),
            )
            ->andReturn(null);

        $this->sellerLegalRepo->shouldReceive('findLegal')
            ->once()
            ->with(
                'email',
                $this->sellerLegalDto->getEmail(),
            )
            ->andReturn(null);

        $this->sellerLegalRepo->shouldReceive('create')
            ->once()
            ->with($this->sellerLegalDto)
            ->andReturn($this->legalInformation);

        $sellerLegalInformation = $this->createSellerLegalInformation->execute($this->sellerLegalDto);

        expect($sellerLegalInformation)->toBeInstanceOf(SellerLegalInformation::class)
            ->and($sellerLegalInformation->user_id)->toBe($this->sellerLegalDto->getUserId())
            ->and($sellerLegalInformation->fullname)->toBe($this->sellerLegalDto->getFullName())
            ->and($sellerLegalInformation->email)->toBe($this->sellerLegalDto->getEmail())
            ->and($sellerLegalInformation->certificate_path)->toBeNull();
    });

it('should create a new legal information record if no existing record and legal certificate is uploaded',
    function () {
        Storage::fake('local');

        $sellerLegalDto = new CreateSellerLegalInformationDto(
            $this->legalInformation->user_id,
            $this->legalInformation->fullname,
            $this->legalInformation->email,
            UploadedFile::fake()->image('legal-certificate.jpg')
        );

        $this->sellerLegalRepo->shouldReceive('findOtherLegal')
            ->once()
            ->with(
                'email',
                $sellerLegalDto->getEmail(),
                $sellerLegalDto->getUserId(),
            )
            ->andReturn(null);

        $this->sellerLegalRepo->shouldReceive('findLegal')
            ->once()
            ->with(
                'email',
                $this->sellerLegalDto->getEmail(),
            )
            ->andReturn(null);

        $this->legalInformation->certificate_path = 'sellers/legal/certificates/'.$this->legalInformation->uuid.'.jpg';

        $this->sellerLegalRepo->shouldReceive('create')
            ->once()
            ->with($sellerLegalDto)
            ->andReturn($this->legalInformation);

        $response = $this->createSellerLegalInformation->execute($sellerLegalDto);

        expect($response)->toBeInstanceOf(SellerLegalInformation::class)
            ->and($response->user_id)->toBe($this->sellerLegalDto->getUserId())
            ->and($response->fullname)->toBe($this->sellerLegalDto->getFullName())
            ->and($response->email)->toBe($this->sellerLegalDto->getEmail())
            ->and($response->certificate_path)->toBeString();
    });
