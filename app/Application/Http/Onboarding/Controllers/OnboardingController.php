<?php

namespace App\Application\Http\Onboarding\Controllers;

use App\Application\Actions\Onboarding\CreateSellerContactDetails;
use App\Application\Http\Onboarding\Requests\SellerContactDetailsRequest;
use App\Application\Shared\Responses\ApiResponse;
use App\Domain\Onboarding\Dtos\SellerContactDto;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class OnboardingController
{
    public function __construct(
        private readonly CreateSellerContactDetails $createSellerContactDetails,
    ) {}

    public function contact(SellerContactDetailsRequest $request): JsonResponse
    {
        $data = (object) $request->validated();

        $sellerContactDto = new SellerContactDto(
            $data->contact_name,
            $data->contact_email,
            $data->contact_phone_number,
            $data->country,
            $data->state,
            $data->city,
            $data->address
        );

        $data = $this->createSellerContactDetails->execute($sellerContactDto);

        return ApiResponse::success('Seller contact details successfully updated', $data, Response::HTTP_CREATED);
    }
}
