<?php

namespace App\Application\Http\Onboarding\Controllers;

use App\Application\Actions\Onboarding\CreateSellerBusinessInformation;
use App\Application\Actions\Onboarding\CreateSellerContactInformation;
use App\Application\Actions\Onboarding\CreateSellerLegalInformation;
use App\Application\Actions\Onboarding\CreateSellerPaymentInformation;
use App\Application\Actions\Onboarding\GetSellerSetupStatus;
use App\Application\Http\Onboarding\Requests\SellerBusinessInformationRequest;
use App\Application\Http\Onboarding\Requests\SellerContactInformationRequest;
use App\Application\Http\Onboarding\Requests\SellerLegalInformationRequest;
use App\Application\Http\Onboarding\Requests\SellerPaymentInformationRequest;
use App\Application\Shared\Responses\ApiResponse;
use App\Domain\Onboarding\Dtos\SellerBusinessInformationDto;
use App\Domain\Onboarding\Dtos\SellerContactInformationDto;
use App\Domain\Onboarding\Dtos\SellerLegalInformationDto;
use App\Domain\Onboarding\Dtos\SellerPaymentInformationDto;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OnboardingController
{
    public function __construct(
        private readonly CreateSellerContactInformation $createSellerContactInformation,
        private readonly CreateSellerBusinessInformation $createSellerBusinessInformation,
        private readonly CreateSellerLegalInformation $createSellerLegalInformation,
        private readonly CreateSellerPaymentInformation $createSellerPaymentInformation,
        private readonly GetSellerSetupStatus $setupStatus,
    ) {}

    public function status(Request $request): JsonResponse
    {
        try {
            $data = $this->setupStatus->execute(auth()->user()->id);

            return ApiResponse::success('Setup status successfully retrieved', $data);
        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage(), $e->getCode());
        }
    }

    public function contact(SellerContactInformationRequest $request): JsonResponse
    {
        $data = (object) $request->validated();

        $sellerContactDto = new SellerContactInformationDto(
            $data->user_id,
            $data->contact_name,
            $data->contact_email,
            $data->contact_phone_number,
            $data->country,
            $data->state,
            $data->city,
            $data->address
        );

        try {
            $data = $this->createSellerContactInformation->execute($sellerContactDto);

            return ApiResponse::success('Seller contact information successfully updated', $data);
        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage(), $e->getCode());
        }
    }

    public function business(SellerBusinessInformationRequest $request): JsonResponse
    {
        $data = (object) $request->validated();

        $sellerBusinessDto = new SellerBusinessInformationDto(
            $data->user_id,
            $data->company_name,
            $data->description,
            $data->registration_number,
            $data->tax_identification_number,
            $data->business_certificate_path ?? null
        );

        try {
            $data = $this->createSellerBusinessInformation->execute($sellerBusinessDto);

            return ApiResponse::success('Seller business information successfully updated', $data);
        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage(), $e->getCode());
        }
    }

    public function legal(SellerLegalInformationRequest $request): JsonResponse
    {
        $data = (object) $request->validated();

        $sellerLegalDto = new SellerLegalInformationDto(
            $data->user_id,
            $data->fullname,
            $data->email,
            $data->legal_certificate_path
        );

        try {
            $data = $this->createSellerLegalInformation->execute($sellerLegalDto);

            return ApiResponse::success('Seller legal information successfully updated', $data);
        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage(), $e->getCode());
        }
    }

    public function payment(SellerPaymentInformationRequest $request): JsonResponse
    {
        $data = (object) $request->validated();

        $sellerPaymentDto = new SellerPaymentInformationDto(
            $data->user_id,
            $data->account_name,
            $data->account_number,
            $data->bank_code,
            $data->bank_name,
            $data->swift_code
        );

        try {
            $data = $this->createSellerPaymentInformation->execute($sellerPaymentDto);

            return ApiResponse::success('Seller payment information successfully updated', $data);
        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage(), $e->getCode());
        }
    }
}
