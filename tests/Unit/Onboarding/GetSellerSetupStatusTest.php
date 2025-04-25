<?php

namespace Tests\Application\Actions\Onboarding;

use App\Domain\Onboarding\Actions\GetSellerSetupStatus;
use App\Domain\Onboarding\Interfaces\Repositories\SellerBusinessInformationRepositoryInterface;
use App\Domain\Onboarding\Interfaces\Repositories\SellerContactInformationRepositoryInterface;
use App\Domain\Onboarding\Interfaces\Repositories\SellerLegalInformationRepositoryInterface;
use App\Domain\Onboarding\Interfaces\Repositories\SellerPaymentInformationRepositoryInterface;
use App\Infrastructure\Models\User;
use Mockery;

beforeEach(function () {
    $this->sellerContactRepo = Mockery::mock(SellerContactInformationRepositoryInterface::class);
    $this->sellerLegalRepo = Mockery::mock(SellerLegalInformationRepositoryInterface::class);
    $this->sellerPaymentRepo = Mockery::mock(SellerPaymentInformationRepositoryInterface::class);
    $this->sellerBusinessRepo = Mockery::mock(SellerBusinessInformationRepositoryInterface::class);
    $this->user = User::factory()->create();
});

it('should get seller setup status', function () {
    $this->sellerContactRepo->shouldReceive('isCompleted')
        ->once()
        ->with($this->user->id)
        ->andReturn(true);

    $this->sellerBusinessRepo->shouldReceive('isCompleted')
        ->once()
        ->with($this->user->id)
        ->andReturn(true);

    $this->sellerPaymentRepo->shouldReceive('isCompleted')
        ->once()
        ->with($this->user->id)
        ->andReturn(true);

    $this->sellerLegalRepo->shouldReceive('isCompleted')
        ->once()
        ->with($this->user->id)
        ->andReturn(true);

    $status = new GetSellerSetupStatus(
        $this->sellerContactRepo,
        $this->sellerBusinessRepo,
        $this->sellerPaymentRepo,
        $this->sellerLegalRepo
    );

    $response = $status->execute($this->user->id);

    expect($response['completed_contact_information'])->toBe(true)
        ->and($response['completed_business_information'])->toBe(true)
        ->and($response['completed_payment_information'])->toBe(true)
        ->and($response['completed_legal_information'])->toBe(true);
});
