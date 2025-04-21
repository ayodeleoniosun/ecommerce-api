<?php

namespace Tests\Application\Actions\Onboarding;

use App\Application\Actions\Onboarding\CreateSellerPaymentInformation;
use App\Application\Shared\Exceptions\ConflictHttpException;
use App\Domain\Onboarding\Dtos\SellerPaymentInformationDto;
use App\Domain\Onboarding\Interfaces\Repositories\SellerPaymentInformationRepositoryInterface;
use App\Infrastructure\Models\SellerPaymentInformation;
use App\Infrastructure\Models\User;
use Mockery;

beforeEach(function () {
    $this->sellerPaymentRepo = Mockery::mock(SellerPaymentInformationRepositoryInterface::class);
    $this->user = User::factory()->create();
    $this->sellerPaymentDto = new SellerPaymentInformationDto(
        $this->user->id,
        'John Doe',
        '0123456789',
        '033',
        'Central Bank of Nigeria',
        'O12345'
    );

    $this->contactInformation = SellerPaymentInformation::factory()->create([
        'user_id' => $this->sellerPaymentDto->getUserId(),
        'account_name' => $this->sellerPaymentDto->getAccountName(),
        'account_number' => $this->sellerPaymentDto->getAccountNumber(),
        'bank_code' => $this->sellerPaymentDto->getBankCode(),
        'bank_name' => $this->sellerPaymentDto->getBankName(),
        'swift_code' => $this->sellerPaymentDto->getSwiftCode(),
    ]);

    $this->createSellerPaymentInformation = new CreateSellerPaymentInformation($this->sellerPaymentRepo);
});

it('should throw an exception if payment information exist for another seller', function () {
    $this->sellerPaymentRepo->shouldReceive('findOtherPayment')
        ->once()
        ->with(
            $this->sellerPaymentDto->getAccountNumber(),
            $this->sellerPaymentDto->getBankCode(),
            $this->sellerPaymentDto->getUserId(),
        )
        ->andReturn($this->contactInformation);

    $this->createSellerPaymentInformation->execute($this->sellerPaymentDto);
})->throws(ConflictHttpException::class, 'Payment information exist for another seller');

it('should create a new seller contact information if not exist', function () {
    $this->sellerPaymentRepo->shouldReceive('findOtherPayment')
        ->once()
        ->with(
            $this->sellerPaymentDto->getAccountNumber(),
            $this->sellerPaymentDto->getBankCode(),
            $this->sellerPaymentDto->getUserId(),
        )
        ->andReturn(null);

    $this->sellerPaymentRepo->shouldReceive('create')
        ->once()
        ->with($this->sellerPaymentDto)
        ->andReturn($this->contactInformation);

    $response = $this->createSellerPaymentInformation->execute($this->sellerPaymentDto);

    expect($response)->toBeInstanceOf(SellerPaymentInformation::class)
        ->and($response->user_id)->toBe($this->sellerPaymentDto->getUserId())
        ->and($response->account_name)->toBe($this->sellerPaymentDto->getAccountName())
        ->and($response->account_number)->toBe($this->sellerPaymentDto->getAccountNumber())
        ->and($response->bank_code)->toBe($this->sellerPaymentDto->getBankCode())
        ->and($response->bank_name)->toBe($this->sellerPaymentDto->getBankName())
        ->and($response->swift_code)->toBe($this->sellerPaymentDto->getSwiftCode());
});
