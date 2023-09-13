<?php

final readonly class Address
{
    public function __construct(
        public string $street,
        public string $postCode,
        public string $city,
        public string $country,
    ) {
    }
}
